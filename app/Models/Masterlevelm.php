<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masterlevelm extends Model
{
    use HasFactory;
    protected $table = 'master_level_kpi';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('level_kpi', 'asc')->get();
    }

}
