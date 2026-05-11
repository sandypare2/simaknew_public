<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mappingpengukuranm extends Model
{
    use HasFactory;
    protected $table = 'matriks_pengukuran';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('level_kpi', 'asc')->get();
    }

}
