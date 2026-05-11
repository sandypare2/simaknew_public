<?php

namespace App\Imports;

use App\Models\Monitoring\Stokgudang;
use Maatwebsite\Excel\Concerns\WithheadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class StokImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    // protected $kd_region;
    // protected $kd_area;
    // protected $kode_gudang;

    // public function __construct($kd_region, $kd_area, $kode_gudang)
    // {
    //     $this->kd_region = $kd_region;
    //     $this->kd_area = $kd_area;
    //     $this->kode_gudang = $kode_gudang;
    // }

    public function model(array $row)
    {
        /*
        $kode = $this->kode_gudang."-".$row[2]."-".$row[4];
        return new Stokgudang([
            'kd_region' => $this->kd_region,
            'kd_area' => $this->kd_area,
            'kd_unit' => '',
            'kode_gudang' => $this->kode_gudang,
            'item_no' => $row[2],
            'kd_project' => $row[4], 
            'tgl_terima' => '2024-10-31', 
            'baik' => intval($row[5]),
            'rusak' => 0,
            'jumlah' => intval($row[5]),
            'harga_satuan' => 0,
            'keterangan' => '',
            'kode_akun' => '1105101000',
            'kode' => $kode,
        ]);
        */
        return new Stokgudang([
            'item_no' => $row[2],
            'kd_project' => $row[4],
            'baik' => intval($row[5]),
            'rusak' => 0,
            'jumlah' => intval($row[5]),
        ]);
    }
}
