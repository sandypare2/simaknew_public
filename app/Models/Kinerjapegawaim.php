<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kinerjapegawaim extends Model
{
    use HasFactory;
    // protected $connection = 'mysql3';
    protected $table = 'penilaian_pegawai';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function getAllData()
    {
        return $this->orderBy('id', 'desc')->get();
    }
}
