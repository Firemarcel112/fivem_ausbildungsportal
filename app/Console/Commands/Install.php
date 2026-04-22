<?php

namespace App\Console\Commands;

use App\Models\Fraction;
use Database\Seeders\ProductionSeeder;
use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Richtet den Produktiv Zustand ein';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('Starte Installierung');

        if (Fraction::count() == 0) {
            $default_fraction_name = $this->ask('Wie soll die Standard-Fraktion heißen?', 'Unbekannt');
            $default_fraction_short_name = $this->ask('Lege einen Kurznamen für die Fraktion: ' . $default_fraction_name . ' fest, Maximal 3 Zeichen');

            Fraction::create([
                'name' => $default_fraction_name,
                'short_name' => $default_fraction_short_name,
                'master' => 1,
            ]);
        }

        $this->info('Seede benötigte Daten');

        $this->call(ProductionSeeder::class);

        $this->info('Daten erfolgreich geseedet');

        $this->line('Installierung beendet');
    }
}
