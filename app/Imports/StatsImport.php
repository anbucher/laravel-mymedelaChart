<?php

namespace App\Imports;

use App\Stat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StatsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // return new Stat([
        //     'date'          => $row['datum'],
        //     'time_start'    => $row['start'], 
        //     'time_end'      => $row['ende'],
        // ]);
    }

    public function headingRow(): int
    {
        return 14;
    }
}
