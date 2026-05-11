<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mappingskorm extends Model
{
    use HasFactory;
    protected $table = 'matriks_skor';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('level_kpi', 'asc')->get();
    }

}
