<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masterkinerjam extends Model
{
    use HasFactory;
    protected $table = 'master_nilai_kinerja';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('id', 'asc')->get();
    }

}
