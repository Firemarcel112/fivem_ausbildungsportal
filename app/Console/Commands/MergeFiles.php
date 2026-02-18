<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MergeFiles extends Command
{
    // Der Name, mit dem du den Befehl aufrufst
    protected $signature = 'code:merge';
    protected $description = 'Fügt alle Controller, Models und Commands in jeweils eine Datei zusammen';

    public function handle()
    {
        $map = [
            'Controllers.txt' => app_path('Http/Controllers'),
            'Models.txt'      => app_path('Models'),
            'Commands.txt'    => app_path('Console/Commands'),
            'Services.txt' => app_path('Services'),
            'Events.txt' => app_path('Events'),
            'Listeners.txt' => app_path('Listeners'),
            'Providers.txt' => app_path('Providers'),
            'Facades.txt' => app_path('Facades'),
            'Enums.txt' => app_path('Enums'),
            'DTO.txt' => app_path('DTO'),
            'Traits.txt' => app_path('Traits'),
        ];

        foreach ($map as $output_name => $source_dir) {
            if (!File::exists($source_dir)) {
                $this->warn("Verzeichnis nicht gefunden: $source_dir");
                continue;
            }

            $files = File::allFiles($source_dir);
            $combined_content = "<?php\n\n/** Sammeldatei: $output_name **/\n\n";
            File::makeDirectory(base_path('utils'), 0755, true, true);
            foreach ($files as $file) {
                // Verhindert, dass der Command sich selbst oder die Sammeldatei einliest
                if ($file->getFilename() === $output_name || $file->getFilename() === 'MergeFiles.php') {
                    continue;
                }

                $content = File::get($file);
                // Entferne das öffnende <?php Tag aus den Quelldateien
                $content = str_replace(['<?php', '<?'], '', $content);

                $combined_content .= "/** Datei: " . $file->getRelativePathname() . " **/\n";
                $combined_content .= trim($content) . "\n\n";
            }

            File::put(base_path('utils' . DIRECTORY_SEPARATOR . $output_name), $combined_content);
            $this->info("Erstellt: $output_name im utils-Verzeichnis.");
        }

        return Command::SUCCESS;
    }
}
