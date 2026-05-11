<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mappingpegawaim extends Model
{
    use HasFactory;
    protected $table = 'data_pegawai';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->whereRaw("aktif='1' and payroll='1' and jabatan not like '%KOMITE%' and jabatan not like '%KOMISARIS%'")->orderBy('id', 'asc')->get();
    }

}
