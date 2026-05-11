<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finalisasikpim extends Model
{
    use HasFactory;
    protected $table = 'finalisasi_kpi';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function getAllData()
    {
        return $this->orderBy('id', 'desc')->get();
    }
}
