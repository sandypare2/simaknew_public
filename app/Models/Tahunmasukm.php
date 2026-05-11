<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tahunmasukm extends Model
{
    use HasFactory;
    protected $table = 'v_tahun_masuk';
    protected $guarded = ['tahun_masuk'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('tahun_masuk', 'desc')->get();
    }

}
