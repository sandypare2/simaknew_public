<?php

namespace App\Imports;

use App\Models\Datacascadingm;
// use Maatwebsite\Excel\Concerns\WithheadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Cascadingcabangimportm implements ToCollection, WithStartRow{   
    protected $tahun;
    protected $jenis_kpi;

    public function __construct($tahun, $jenis_kpi)
    {
        $this->tahun = $tahun;
        $this->jenis_kpi = $jenis_kpi;
    }

    public function startRow(): int
    {
        return 6;
    }

    public function collection(Collection $rows)    
    {
        $i=0;
        $last_kode = "";
        $group_kpi = "";
        foreach ($rows as $row)
        {
            $nilai1 = $row[1];
            $nilai2 = $row[2];
            $nilai3 = $row[3];
            $uraian = trim($nilai1.$nilai2.$nilai3);
            if($uraian!=""){
                $tahun = $this->tahun;
                $jenis_kpi = $this->jenis_kpi;
                if($row[0]!="" && $row[0]!="null"){
                    $group_kpi = $row[0];
                } else {
                    $group_kpi = $group_kpi;
                }            
                if($nilai1!="" && $nilai1!="null"){
                    $level_kpi = "1";                                        
                    $i++;
                    $kode2 = 0;
                    $kode3 = 0;
                }
                $last_kode = $i;             
                $kd_urut = "";
                if($nilai2!=""){
                    $level_kpi = "2";
                    $kode2++;
                    $kode3 = 0;
                }
                if($nilai3!=""){
                    $level_kpi = "3";
                    $kode3++;
                }
               $last_kode .= ".$kode2.$kode3";

                Datacascadingm::create([
                    'tahun' => $tahun,
                    'jenis_kpi' => $jenis_kpi,
                    'group_kpi' => $group_kpi,
                    'level_kpi' => $level_kpi,
                    'kd_urut' => $last_kode,
                    'uraian' => $uraian
                ]);
            }
        }
    }
}