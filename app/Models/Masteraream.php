<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masteraream extends Model
{
    use HasFactory;
    protected $table = 'master_area';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('kd_region', 'asc')->orderBy('kd_area', 'asc')->get();
    }

}
