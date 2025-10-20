<?php

namespace App\Console\Commands\Model;

use Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateModelFunctions extends Command
{
    protected $signature = 'model:functions {model}';
    protected $description = 'Generiert Accessors & Mutators für alle Spalten eines Models basierend auf der Datenbanktabelle.';

    public function handle()
    {
        $model_name = $this->argument('model');

        // Model-Klasse ermitteln
        $model_class_name = "App\\Models\\{$model_name}";

        if (!class_exists($model_class_name)) {
            $this->error("Model: {$model_class_name} nicht gefunden.");
            return 1;
        }

        try {
            $model = new $model_class_name;
            $table = $model->getTable();
        } catch (\Exception $e) {
            $this->error("Konnte Modell nicht instanziieren oder Tabelle nicht bestimmen: " . $e->getMessage());
            return 1;
        }

        if (!Schema::hasTable($table)) {
            $this->error("Tabelle [{$table}] existiert nicht in der Datenbank.");
            return 1;
        }

        $columns = Schema::getColumnListing($table);

        if (empty($columns)) {
            $this->warn("Keine Spalten in der Tabelle [{$table}] gefunden.");
            return 0;
        }

        $this->info("Gefunden: " . count($columns) . " Spalten in der Tabelle [{$table}]: " . implode(', ', $columns));

        $model_path = app_path("Models/{$model_name}.php");

        if (!file_exists($model_path)) {
            $this->error("Model-Datei nicht gefunden unter {$model_path}");
            return 1;
        }

        $model_content = file_get_contents($model_path);
        $new_methods = '';

        $primaryKey = $model->getKeyName();
        if (in_array($primaryKey, $columns) && $primaryKey !== 'id') {
            $columns = array_merge(['id' => $primaryKey], $columns);
        }
        if (in_array('created_at', $columns)) {
            $columns = Arr::reject($columns, fn($col) => $col === 'created_at');
            $columns = array_merge($columns, ['created' => 'created_at']);
        }
        if (in_array('updated_at', $columns)) {
            $columns = Arr::reject($columns, fn($col) => $col === 'updated_at');
            $columns = array_merge($columns, ['updated' => 'updated_at']);
        }
        if (in_array('remember_token', $columns)) {
            $columns = Arr::reject($columns, fn($col) => $col === 'remember_token');
        }

        foreach ($columns as $overwrite_name => $column) {
            if (gettype($overwrite_name) != 'integer') {
                $method_suffix = Str::studly($overwrite_name);
            } else {
                $method_suffix = Str::studly($column);
            }


            // Prüfen, ob Getter oder Setter bereits existiert
            if (
                $this->methodExistsInContent($model_content, "get{$method_suffix}") ||
                $this->methodExistsInContent($model_content, "set{$method_suffix}")
            ) {
                $this->warn("⚠️  Überspringe {$column} — Accessor oder Mutator existiert bereits.");
                continue;
            } else {
                $this->info("➕  Generiere Accessor & Mutator für {$column}");
            }

            $return_type = 'mixed';

            try {
                $db_type_raw = Schema::getColumnType($table, $column);
                $db_type = $this->normalizeDatabaseType($db_type_raw);

                $is_nullable = $this->isColumnNullable($table, $column);
                $return_type = $is_nullable ? "?{$db_type}" : $db_type;
            } catch (\Exception $e) {
                $this->warn("⚠️  Fehler beim Abrufen des Typs für {$column}: " . $e->getMessage() . " → Nutze 'mixed'.");
            }
            $return = "\$this->{$column}";
            if (Str::contains($return_type, 'Carbon')) {
                $return = "Carbon::parse({$return})";
            }
            if (Str::containsAll($return_type, ['?', 'Carbon'])) {
                $return = "is_null(\$this->{$column}) ? null : Carbon::parse(\$this->{$column})";
            }

            $getter = <<<EOT

    /**
     * Get the {$column} attribute.
     *
     * @return {$return_type}
     */
    public function get{$method_suffix}(): {$return_type}
    {
        return $return;
    }

EOT;

            $setter = <<<EOT

    /**
     * Set the {$column} attribute.
     *
     * @param $return_type \$value
     * @return void
     */
    public function set{$method_suffix}($return_type \$value)
    {
        \$this->{$column} = \$value;
    }

EOT;

            $new_methods .= $getter . $setter;
        }

        if (empty($new_methods)) {
            $this->info("✅ Keine neuen Accessors oder Mutators zu generieren. Alles aktuell.");
            return 0;
        }

        // Füge Methoden vor der letzten schließenden Klammer ein
        $last_brace_position = strrpos($model_content, '}');
        if ($last_brace_position === false) {
            $this->error("Konnte schließende Klammer der Klasse nicht finden.");
            return 1;
        }

        $updated_content = substr($model_content, 0, $last_brace_position) . "\n" . $new_methods . "\n" . substr($model_content, $last_brace_position);

        file_put_contents($model_path, $updated_content);

        $this->info("✅ Erfolgreich Accessors & Mutators für " . count($columns) . " Spalten im {$model_name} Modell hinzugefügt.");
        return 0;
    }

    /**
     * Normalisiert den rohen Datenbanktyp zu einem PHP-Typ
     *
     * @param string $rawType
     * @return string
     */
    protected function normalizeDatabaseType(string $rawType): string
    {
        // Entferne Längenangaben, z. B. "varchar(255)" → "varchar"
        $baseType = preg_replace('/\(\d+\)/', '', strtolower($rawType));

        $typeMap = [
            // String-Typen
            'varchar' => 'string',
            'char' => 'string',
            'text' => 'string',
            'mediumtext' => 'string',
            'longtext' => 'string',
            'tinytext' => 'string',
            'string' => 'string', // fallback

            // Integer-Typen
            'int' => 'int',
            'integer' => 'int',
            'tinyint' => 'int',
            'smallint' => 'int',
            'mediumint' => 'int',
            'bigint' => 'int',
            'int4' => 'int', // PostgreSQL
            'int8' => 'int',

            // Boolean
            'boolean' => 'bool',
            'bool' => 'bool',
            'tinyint(1)' => 'bool', // MySQL boolean

            // Float/Decimal
            'float' => 'float',
            'double' => 'float',
            'decimal' => 'float',
            'numeric' => 'float',
            'real' => 'float',

            // Datum/Zeit
            'datetime' => 'Carbon',
            'timestamp' => 'Carbon',
            'date' => 'Carbon',
            'time' => 'Carbon',
            'datetimetz' => 'Carbon',

            // JSON/Array
            'json' => 'array',
            'jsonb' => 'array', // PostgreSQL
            'array' => 'array',

            // Sonstige
            'object' => 'object',
        ];

        return $typeMap[$baseType] ?? 'mixed';
    }

    /**
     * Prüft, ob eine Spalte NULL erlaubt (nullable) ist — via information_schema
     * Unterstützt MySQL & PostgreSQL
     */
    protected function isColumnNullable(string $table, string $column): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            $result = DB::select("
                SELECT IS_NULLABLE
                FROM information_schema.columns
                WHERE table_schema = DATABASE()
                  AND table_name = ?
                  AND column_name = ?
            ", [$table, $column]);

            return $result && isset($result[0]->IS_NULLABLE) && $result[0]->IS_NULLABLE === 'YES';
        }

        if ($driver === 'pgsql') {
            $result = DB::select("
                SELECT is_nullable
                FROM information_schema.columns
                WHERE table_catalog = current_database()
                  AND table_name = ?
                  AND column_name = ?
            ", [$table, $column]);

            return $result && isset($result[0]->is_nullable) && $result[0]->is_nullable === 'YES';
        }

        return true;
    }

    /**
     * Prüft, ob eine Methode bereits im Model-Code existiert (anhand des Namens)
     *
     * @param mixed $content
     * @param mixed $method_name
     * @return bool
     */
    protected function methodExistsInContent($content, $method_name): bool
    {
        if ($method_name == 'setCreatedAt' || $method_name == 'setUpdatedAt') {
            return true;
        }

        return strpos($content, "function {$method_name}") !== false;
    }
}
