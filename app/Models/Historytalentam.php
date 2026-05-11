<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historytalentam extends Model
{
    use HasFactory;
    protected $table = 'riwayat_talenta';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('id', 'asc')->get();
    }

}
