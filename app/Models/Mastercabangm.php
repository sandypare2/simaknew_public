<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mastercabangm extends Model
{
    use HasFactory;
    protected $table = 'master_cabang';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('kd_region', 'asc')->get();
    }

}
