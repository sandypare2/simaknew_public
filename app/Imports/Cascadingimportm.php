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

class Cascadingimportm implements ToCollection, WithStartRow{   
    protected $tahun;
    protected $kd_area;

    public function __construct($tahun, $kd_area)
    {
        $this->tahun = $tahun;
        $this->kd_area = $kd_area;
    }

    public function startRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows)    
    {
        $tahun = $this->tahun;
        $kd_area = $this->kd_area;

        $row3 = DB::table('master_area')
        ->selectRaw("*")
        ->whereRaw("kd_area='$kd_area'")
        ->first();
        $jenis_kpi = $row3->jenis_kpi;

        $i=0;
        $last_kode = "";        
        foreach ($rows as $row)
        {
            $nilai1 = trim($row[1]);
            $nilai2 = trim($row[2]);
            $nilai3 = trim($row[3]);
            $nilai4 = trim($row[4]);
            $nilai5 = trim($row[5]);
            $nilai6 = trim($row[6]);
            $nilai7 = trim($row[7]);
            $nilai8 = trim($row[8]);
            $nilai9 = trim($row[9]);
            $nilai10 = trim($row[10]);
            $uraian = trim($nilai1.$nilai2.$nilai3.$nilai4.$nilai5.$nilai6.$nilai7.$nilai8.$nilai9.$nilai10);
            $satuan_kuantitas = $row[11];
            $satuan_kualitas = $row[12];
            $satuan_waktu = $row[13];
            $prioritas = $row[14];
            $type_target = $row[15];
            $polarisasi = $row[16];
            $target01kn = $row[17];
            $target01kl = $row[18];
            $target01wk = $row[19];
            $target02kn = $row[20];
            $target02kl = $row[21];
            $target02wk = $row[22];
            $target03kn = $row[23];
            $target03kl = $row[24];
            $target03wk = $row[25];
            $target04kn = $row[26];
            $target04kl = $row[27];
            $target04wk = $row[28];
            $target05kn = $row[29];
            $target05kl = $row[30];
            $target05wk = $row[31];
            $target06kn = $row[32];
            $target06kl = $row[33];
            $target06wk = $row[34];
            $target07kn = $row[35];
            $target07kl = $row[36];
            $target07wk = $row[37];
            $target08kn = $row[38];
            $target08kl = $row[39];
            $target08wk = $row[40];
            $target09kn = $row[41];
            $target09kl = $row[42];
            $target09wk = $row[43];
            $target10kn = $row[44];
            $target10kl = $row[45];
            $target10wk = $row[46];
            $target11kn = $row[47];
            $target11kl = $row[48];
            $target11wk = $row[49];
            $target12kn = $row[50];
            $target12kl = $row[51];
            $target12wk = $row[52];
            if(($target01kn!="" && $target01kn!="null") || ($target01kl!="" && $target01kl!="null") || ($target01wk!="" && $target01wk!="null")){
                $target01 = "100%";
            } else {
                $target01 = "";
            }
            if(($target02kn!="" && $target02kn!="null") || ($target02kl!="" && $target02kl!="null") || ($target02wk!="" && $target02wk!="null")){
                $target02 = "100%";
            } else {
                $target02 = "";
            }
            if(($target03kn!="" && $target03kn!="null") || ($target03kl!="" && $target03kl!="null") || ($target03wk!="" && $target03wk!="null")){
                $target03 = "100%";
            } else {
                $target03 = "";
            }
            if(($target04kn!="" && $target04kn!="null") || ($target04kl!="" && $target04kl!="null") || ($target04wk!="" && $target04wk!="null")){
                $target04 = "100%";
            } else {
                $target04 = "";
            }
            if(($target05kn!="" && $target05kn!="null") || ($target05kl!="" && $target05kl!="null") || ($target05wk!="" && $target05wk!="null")){
                $target05 = "100%";
            } else {
                $target05 = "";
            }
            if(($target06kn!="" && $target06kn!="null") || ($target06kl!="" && $target06kl!="null") || ($target06wk!="" && $target06wk!="null")){
                $target06 = "100%";
            } else {
                $target06 = "";
            }
            if(($target07kn!="" && $target07kn!="null") || ($target07kl!="" && $target07kl!="null") || ($target07wk!="" && $target07wk!="null")){
                $target07 = "100%";
            } else {
                $target07 = "";
            }
            if(($target08kn!="" && $target08kn!="null") || ($target08kl!="" && $target08kl!="null") || ($target08wk!="" && $target08wk!="null")){
                $target08 = "100%";
            } else {
                $target08 = "";
            }
            if(($target09kn!="" && $target09kn!="null") || ($target09kl!="" && $target09kl!="null") || ($target09wk!="" && $target09wk!="null")){
                $target09 = "100%";
            } else {
                $target09 = "";
            }
            if(($target10kn!="" && $target10kn!="null") || ($target10kl!="" && $target10kl!="null") || ($target10wk!="" && $target10wk!="null")){
                $target10 = "100%";
            } else {
                $target10 = "";
            }
            if(($target11kn!="" && $target11kn!="null") || ($target11kl!="" && $target11kl!="null") || ($target11wk!="" && $target11wk!="null")){
                $target11 = "100%";
            } else {
                $target11 = "";
            }
            if(($target12kn!="" && $target12kn!="null") || ($target12kl!="" && $target12kl!="null") || ($target12wk!="" && $target12wk!="null")){
                $target12 = "100%";
            } else {
                $target12 = "";
            }
            if($uraian!="" && $uraian!="null"){
                $kd_divisi = $row[0];
                if($nilai1!="" && $nilai1!="null"){
                    $level_kpi = "1";                                        
                    $i++;
                    $kode2 = 0;
                    $kode3 = 0;
                    $kode4 = 0;
                    $kode5 = 0;
                    $kode6 = 0;
                    $kode7 = 0;
                    $kode8 = 0;
                    $kode9 = 0;
                    $kode10 = 0;
                }
                $last_kode = $i;             
                $kd_urut = "";
                if($nilai2!="" && $nilai2!="null"){
                    $level_kpi = "2";
                    $kode2++;
                    $kode3 = 0;
                    $kode4 = 0;
                    $kode5 = 0;
                    $kode6 = 0;
                    $kode7 = 0;
                    $kode8 = 0;
                    $kode9 = 0;
                    $kode10 = 0;
                }
                if($nilai3!="" && $nilai3!="null"){
                    $level_kpi = "3";
                    $kode3++;
                    $kode4 = 0;
                    $kode5 = 0;
                    $kode6 = 0;
                    $kode7 = 0;
                    $kode8 = 0;
                    $kode9 = 0;
                    $kode10 = 0;                    
                }
                if($nilai4!="" && $nilai4!="null"){
                    $level_kpi = "4";
                    $kode4++;
                    $kode5 = 0;
                    $kode6 = 0;
                    $kode7 = 0;
                    $kode8 = 0;
                    $kode9 = 0;
                    $kode10 = 0;                    
                }
                if($nilai5!="" && $nilai5!="null"){
                    $level_kpi = "5";
                    $kode5++;
                    $kode6 = 0;
                    $kode7 = 0;
                    $kode8 = 0;
                    $kode9 = 0;
                    $kode10 = 0;                    
                }
                if($nilai6!="" && $nilai6!="null"){
                    $level_kpi = "6";
                    $kode6++;
                    $kode7 = 0;
                    $kode8 = 0;
                    $kode9 = 0;
                    $kode10 = 0;                    
                }
                if($nilai7!="" && $nilai7!="null"){
                    $level_kpi = "7";
                    $kode7++;
                    $kode8 = 0;
                    $kode9 = 0;
                    $kode10 = 0;                    
                }
                if($nilai8!="" && $nilai8!="null"){
                    $level_kpi = "8";
                    $kode8++;
                    $kode9 = 0;
                    $kode10 = 0;                    
                }
                if($nilai9!="" && $nilai9!="null"){
                    $level_kpi = "9";
                    $kode9++;
                    $kode10 = 0;                    
                }
                if($nilai10!="" && $nilai10!="null"){
                    $level_kpi = "10";
                    $kode10++;
                }
                $last_kode .= ".$kode2.$kode3.$kode4.$kode5.$kode6.$kode7.$kode8.$kode9.$kode10";
                $kode_cascading = $tahun."-".$jenis_kpi."-".$last_kode;
                $kode_cascading2 = $last_kode;
                $kode_cascading2 = str_replace(".","",$kode_cascading2);
                $kode_cascading2 = rtrim($kode_cascading2, "0");
                // dd($last_kode);
                Datacascadingm::updateOrCreate([
                    'kode_cascading' => $kode_cascading, 
                ],
                [
                    'tahun' => $tahun,
                    'kd_area' => $kd_area,
                    'jenis_kpi' => $jenis_kpi,
                    'kd_divisi' => $kd_divisi,
                    'level_kpi' => $level_kpi,
                    'kd_urut' => $last_kode,
                    'uraian' => $uraian,
                    'satuan_kuantitas' => $satuan_kuantitas,
                    'satuan_kualitas' => $satuan_kualitas,
                    'satuan_waktu' => $satuan_waktu,
                    'prioritas' => $prioritas,
                    'type_target' => $type_target,
                    'polarisasi' => $polarisasi,
                    'target01kn' => $target01kn,
                    'target01kl' => $target01kl,
                    'target01wk' => $target01wk,
                    'target01' => $target01,
                    'target02kn' => $target02kn,
                    'target02kl' => $target02kl,
                    'target02wk' => $target02wk,
                    'target02' => $target02,
                    'target03kn' => $target03kn,
                    'target03kl' => $target03kl,
                    'target03wk' => $target03wk,
                    'target03' => $target03,
                    'target04kn' => $target04kn,
                    'target04kl' => $target04kl,
                    'target04wk' => $target04wk,
                    'target04' => $target04,
                    'target05kn' => $target05kn,
                    'target05kl' => $target05kl,
                    'target05wk' => $target05wk,
                    'target05' => $target05,
                    'target06kn' => $target06kn,
                    'target06kl' => $target06kl,
                    'target06wk' => $target06wk,
                    'target06' => $target06,
                    'target07kn' => $target07kn,
                    'target07kl' => $target07kl,
                    'target07wk' => $target07wk,
                    'target07' => $target07,
                    'target08kn' => $target08kn,
                    'target08kl' => $target08kl,
                    'target08wk' => $target08wk,
                    'target08' => $target08,
                    'target09kn' => $target09kn,
                    'target09kl' => $target09kl,
                    'target09wk' => $target09wk,
                    'target09' => $target09,
                    'target10kn' => $target10kn,
                    'target10kl' => $target10kl,
                    'target10wk' => $target10wk,
                    'target10' => $target10,
                    'target11kn' => $target11kn,
                    'target11kl' => $target11kl,
                    'target11wk' => $target11wk,
                    'target11' => $target11,
                    'target12kn' => $target12kn,
                    'target12kl' => $target12kl,
                    'target12wk' => $target12wk,
                    'target12' => $target12,
                    'kode_cascading' => $kode_cascading,
                    'kode_cascading2' => $kode_cascading2
                ]);
            }
        }
    }
}