<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowTableColumns extends Command
{
    protected $signature = 'schema:columns {table}';
    protected $description = 'Muestra las columnas de una tabla';

    public function handle()
    {
        $table = $this->argument('table');

        try {
            $cols = DB::select("SHOW COLUMNS FROM `{$table}`");
        } catch (\Throwable $e) {
            $this->error('Error al consultar la tabla: ' . $e->getMessage());
            return 1;
        }

        $rows = [];
        foreach ($cols as $c) {
            if (is_array($c)) {
                $rows[] = [$c['Field'] ?? '', $c['Type'] ?? '', $c['Null'] ?? ''];
            } elseif (is_object($c)) {
                $rows[] = [$c->Field ?? '', $c->Type ?? '', $c->Null ?? ''];
            }
        }

        $this->table(['Field','Type','Null'], $rows);
        return 0;
    }
}
