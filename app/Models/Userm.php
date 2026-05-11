<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userm extends Model
{
    use HasFactory;
    protected $table = 'usersimkp';
    protected $guarded = ['id'];
    // public $timestamps = false;
    
    public function getAllData()
    {
        return $this->orderBy('id', 'desc')->get();
    }
    public function getAllData2()
    {
        return $this->orderBy('id', 'asc')->get();
    }
}
