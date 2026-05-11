<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datapegawaim extends Model
{
    use HasFactory;
    protected $table = 'data_pegawai';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('id', 'asc')->get();
    }

}
