<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masterdivisim extends Model
{
    use HasFactory;
    protected $table = 'master_divisi';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('kd_divisi', 'asc')->get();
    }

}
