<?php

namespace App\Console\Commands;

use Database\Seeders\ProductionSeeder;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Deploy extends Command
{
    protected $signature = 'app:deploy';

    protected $description = 'Automatisiertes Deployment';

    public function handle()
    {
        $php_binary = config('app.php', 'php');
        // Schritte für die Progressbar zählen
        $steps = 7;
        $bar = $this->output->createProgressBar($steps);
        $bar->start();

        /* ----------------------------------------------------
         * STEP 1: Cache leeren
         * ----------------------------------------------------
         */
        $this->info("\n🧹 Lösche Cache ...");
        $this->call('optimize:clear');
        $bar->advance();

        /* ----------------------------------------------------
         * STEP 2: git pull
         * ----------------------------------------------------
         */
        $this->info("\n⬇️  Führe git pull aus...");
        $git_output = $this->runGetOutput(['git', 'pull']);
        $this->line($git_output);
        $bar->advance();

        if (str_contains($git_output, 'Already up to date')) {
            $this->info("\n⚡ Optimiere Cache ...");
            $this->call('optimize');
            $this->call('queue:restart');
            $this->info("\n📁 Keine Änderungen vorhanden. Deployment beendet.");
            $bar->finish();

            return true;
        }

        /* ----------------------------------------------------
         * STEP 3: Änderungen ermitteln
         * ----------------------------------------------------
         */
        $this->info("\n📄 Ermittele Änderungen ...");
        $changed_files = $this->getGitChanges();

        foreach ($changed_files as $file) {
            $this->line(" - $file");
        }
        $bar->advance();

        /* ----------------------------------------------------
         * STEP 4: Composer installieren
         * ----------------------------------------------------
         */
        $composer_ran = false;

        if ($this->anyChanged($changed_files, ['composer.lock', 'composer.json'])) {
            $this->info("\n📦 composer.lock/json geändert → composer install ...");
            $composer_ran = true;

            if (config('app.env') != 'local') {
                $this->runProcess([$php_binary, 'composer', 'install', '--no-interaction', '--no-dev', '--prefer-dist', '--optimize-autoloader']);
            } else {
                $this->runProcess([$php_binary, 'composer', 'install', '--no-interaction', '--prefer-dist', '--optimize-autoloader']);
            }
        } else {
            $this->info('📦 Composer unverändert → übersprungen');
        }

        $bar->advance();

        /* ----------------------------------------------------
         * STEP 5: Migrationen
         * ----------------------------------------------------
         */
        if ($this->anyChanged($changed_files, ['database/migrations'])) {
            $this->info("\n🗄 Migrationen geändert → führe migrate aus...");
            $this->call('migrate');
        } else {
            $this->info('🗄 Keine neuen Migrationen → übersprungen');
        }

        $bar->advance();

        /* ----------------------------------------------------
         * STEP 6: Seeder
         * ----------------------------------------------------
         */
        if ($this->anyChanged($changed_files, ['database/seeders'])) {
            $this->info("\n🌱 Seeder geändert → führe ProductionSeeder aus...");
            $this->call('db:seed', [
                '--class' => ProductionSeeder::class,
            ]);
        } else {
            $this->info('🌱 Seeder unverändert → übersprungen');
        }

        $bar->advance();

        /* ----------------------------------------------------
         * STEP 7: Cache optimieren
         * ----------------------------------------------------
         */
        $this->info("\n⚡ Optimiere Cache ...");
        $this->call('optimize');
        $this->call('queue:restart');

        $bar->advance();
        $bar->finish();

        $this->info("\n\n✅ Deployment erfolgreich abgeschlossen!");

        return true;
    }

    /**
     * Gibt die änderungen der Git Dateien zurück
     *
     * @return string[]
     */
    public function getGitChanges(): array
    {
        $process = new Process(['git', 'diff', '--name-only', 'HEAD@{1}', 'HEAD']);
        $process->run();

        return array_filter(explode("\n", trim($process->getOutput())));
    }

    /**
     * Gibt die änderungen an den Dateien zurück
     *
     * @param  array $changed_files
     * @param  array $paths
     * @return bool
     */
    public function anyChanged(array $changed_files, array $paths): bool
    {
        foreach ($paths as $path) {
            foreach ($changed_files as $file) {
                if (str_starts_with($file, $path)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Lässt einen Prozess via CLI laufen
     *
     * @param  array $command
     * @return void
     */
    public function runProcess(array $command)
    {
        $process = new Process($command, base_path());
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });
    }

    /**
     * Lässt einen Prozess via CLI laufen und gibt den output zurück
     *
     * @param  array  $command
     * @return string
     */
    public function runGetOutput(array $command): string
    {
        $process = new Process($command, base_path());
        $process->setTimeout(null);
        $process->run();

        return $process->getOutput();
    }
}
