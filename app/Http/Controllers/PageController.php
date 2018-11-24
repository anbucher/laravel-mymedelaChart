<?php

namespace App\Http\Controllers;

use App\Stat;
use Carbon\Carbon;
use App\Imports\StatsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    private function check_for_data()
    {
        $this->import_data();
    }

    private function import_data()
    {
        try {
            // Import Data
            $path = 'files/import.xlsx';

            if (Storage::disk('local')->exists($path)) {
                try {
                    $import = Excel::toArray(new StatsImport, $path);
                } catch (\Exception $e) {
                    // Delete file
                    Storage::delete($path);

                    session(['error' => true, 'message' => $e->getMessage()]);
                    return false;
                }
             
                $stats = collect([]);
                $chart_data = [];
                $min_date = "";
                $max_date ="";
    
                $x = 0;
                foreach ($import[0] as $key => $row) {
                    $stat = new Stat([
                        'datetime_start'  => Carbon::createFromFormat('d-m-Y H:i A', $row['datum']." ".$row['start']),
                        'datetime_end'    => Carbon::createFromFormat('d-m-Y H:i A', $row['datum']." ".$row['ende']),
                        'duration' => $row['dauer_min']
                    ]);
        
                    $stats->push($stat);
                    // Create Chart
                    $datetime = clone($stat->datetime_start);
                    $date = clone($datetime);
                    $dec_time = $this->time_to_decimal($datetime->format('H:i:s'));
        
                    $chart_data[] = [$dec_time, $date->setTime(12,0,0)->timestamp*1000, $stat->duration];
        
        
        
                    // Dauer eintragen
                    for ($i=1; $i<= ($stat->duration/5); $i++) {
                        $datetime->addMinutes(5);
                        $date = clone($datetime);
                        $dec_time = $this->time_to_decimal($datetime->format('H:i:s'));
        
                        $chart_data[] = [$dec_time, $date->setTime(12,0,0)->timestamp*1000, $stat->duration];
                    }
        
                    // Set min / max date
                    if ($max_date == "" || $stat->datetime_start > $max_date) {
                        $max_date = $stat->datetime_start;
                    }
        
                    if ($min_date == "" || $stat->datetime_start < $min_date) {
                        $min_date = $stat->datetime_start;
                    }
                }
    
                session(['stat.min_date' => $min_date->timestamp*1000]);
                session(['stat.max_date' => $max_date->addDays(1)->setTime(0,0,0)->timestamp*1000]);
                session(['stat.data' => json_encode($chart_data)]);
                session(['error' => false]);
    
                // Delete file
                Storage::delete($path);

                return true;
            }
        } catch (Exception $ex) {
            // Clear session
            session(['error' => true, 'message' => $ex->getMessage()]);
            session(['stat.data' => null]);

            // Delete file
            Storage::delete($path);

            return false;
        }

        return false;
    }

    public function index()
    {
        $this->check_for_data();

        if (session('error')) {
            session(['alerttype' => 'warning']);
        } else {
            session(['message' => 'Upload successful']);
            session(['alerttype' => 'success']);
        }

        return view('index');
    }

    public function time_to_decimal($time)
    {
        $timeArr = explode(':', $time);
        $decTime = ($timeArr[0]) + ($timeArr[1]/60) + ($timeArr[2]/60);
     
        return $decTime;
    }
}
