<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class DewCalculationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('dew_calculations')->truncate();
        $dataToImport = [];
        Excel::load('database/seeds/support/dew.xlsx', function($reader) use (&$dataToImport) {
            $results = $reader->all()->toArray();
            foreach ($results as $row) {
                $temperature = $row['temp_rh'];
                unset($row['temp_rh']);
                foreach ($row as $key => $cell) {
                    if (!is_null($cell)) {
                        $dataToImport[] = [
                            'temperature' => $temperature,
                            'rh' => $key,
                            'result' => (int)$cell
                        ];
                    }
                }
            }
        });

        \DB::table('dew_calculations')->insert($dataToImport);
    }
}
