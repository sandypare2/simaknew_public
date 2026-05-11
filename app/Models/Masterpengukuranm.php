<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masterpengukuranm extends Model
{
    use HasFactory;
    protected $table = 'master_pengukuran';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('id', 'asc')->get();
    }
    
    public function getAllData2()
    {
        return $this->groupBy('nama_pengukuran')->orderBy('id', 'asc')->get();
    }

}
