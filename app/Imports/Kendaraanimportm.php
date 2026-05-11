<?php

namespace App\Imports;

use App\Models\Datakendaraanm;
use App\Models\Kendaraanprojectm;
use App\Models\Datapemilikm;
// use Maatwebsite\Excel\Concerns\WithheadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class kendaraanimportm implements ToCollection{   
    protected $kd_kota;
    protected $kd_jenis;
    protected $kd_project;

    public function __construct($kd_kota, $kd_jenis, $kd_project)
    {
        $this->kd_kota = $kd_kota;
        $this->kd_jenis = $kd_jenis;
        $this->kd_project = $kd_project;
    }

    public function collection(Collection $rows)    
    {
        foreach ($rows as $row)
        {
            if($row[0]!="Nopol" && $row[0]!=""){
                $kd_kota = $this->kd_kota;
                $kd_jenis = $this->kd_jenis;
                $kd_project = $this->kd_project;
                $nama_pemilik = strtoupper($row[1]);
                $kode_pemilik = str_replace(" ","",$nama_pemilik);
                $row1 = DB::table('master_pemilik')->selectRaw("kd_pemilik")->whereRaw("kode_pemilik='$kode_pemilik'")->first();
                if($row1){
                    $kd_pemilik = $row1->kd_pemilik;
                } else {
                    $kd_pemilik = "";
                }
                if($kd_pemilik==""){
                    $row2 = DB::table('master_pemilik')->selectRaw("max(kd_pemilik) as kd_pemilik3")->first();
                    if($row2){
                        $kd_pemilik3 = intval($row2->kd_pemilik3);
                    } else {
                        $kd_pemilik3 = 0;
                    }
                    $kd_pemilik = str_pad(intval($kd_pemilik3)+1,4,"0",STR_PAD_LEFT);  
                    Datapemilikm::create([
                        'kd_pemilik' => $kd_pemilik, 
                        'nama_pemilik' => $nama_pemilik,
                        'kode_pemilik' => $kode_pemilik
                    ]);                    
                }

                $row4 = DB::table('master_kendaraan')->selectRaw("max(kd_kendaraan) as kd_kendaraan2")->first();
                if($row4){
                    $kd_kendaraan2 = intval($row4->kd_kendaraan2);
                } else {
                    $kd_kendaraan2 = 0;
                }
                $kd_kendaraan = str_pad(intval($kd_kendaraan2)+1,6,"0",STR_PAD_LEFT); 

                if($row[13]!=""){
                    $masa_berlaku_pajak = Carbon::parse(strtotime($row[13]));
                } else {
                    $masa_berlaku_pajak = "";
                }
                if($row[12]!=""){
                    $masa_berlaku_plat_nomor = Carbon::parse(strtotime($row[12]));
                } else {
                    $masa_berlaku_plat_nomor = "";
                }
                if($row[14]!=""){
                    $masa_berlaku_asuransi = Carbon::parse(strtotime($row[14]));
                } else {
                    $masa_berlaku_asuransi = "";
                }

                Datakendaraanm::create([
                    'kd_jenis' => $kd_jenis,
                    'kd_kendaraan' => $kd_kendaraan,
                    'kd_pemilik' => $kd_pemilik,
                    'nopol' => $row[0],
                    'type' => $row[6],
                    'tahun_pembuatan' => $row[5],
                    'warna' => $row[10],
                    'no_rangka' => $row[3],
                    'no_mesin' => $row[4],
                    'bahan_bakar' => $row[9],
                    'masa_berlaku_pajak' => $masa_berlaku_pajak,
                    'masa_berlaku_plat_nomor' => $masa_berlaku_plat_nomor,
                    'masa_berlaku_asuransi' => $masa_berlaku_asuransi,
                    'nama_asuransi' => $row[15],
                    'kd_kota' => $kd_kota,
                    'no_bpkb' => $row[2]
                ]);

                $kode2 = $kd_project."|".$kd_kendaraan;
                Kendaraanprojectm::create([
                    'kd_project' => $kd_project, 
                    'kd_kendaraan' => $kd_kendaraan,
                    'lokasi' => $row[7],
                    'pengguna' => $row[11],
                    'kode' => $kode2
                ]);
            }
            // $myString = $row[8];
            // $myArray = explode(',', $myString);
            // foreach ($myArray as $value) {
            //     Courses::create([
            //         'user_id' => $user->id,
            //         'course_name' => $value,
            //     ]);
            // }
        }
    }
}