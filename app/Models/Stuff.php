<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stuff extends Model
{
    use SoftDeletes; //optional, digunakan hanya untuk table yang menggunakan fitur softdeletes
    protected $fillable = ["name", "category"];

    // Variable $fillable berguna untuk mendaftarkan atribut (nama kolom) yang bisa kita isi ketika melakukan insert atau update ke database.

    //mendefinsikan relasi
    //table yang berperan sebagai primary key : hasOne / hasMany / ...
    //table yang berperan sebagai foreign key : belongsTo
    //nama function disarankan menggunakan aturan berikut :
    // 1. one to one : nama model yang terhubung versi tunggal
    // 2. one to many : nama model yang terhubung versi jamak(untuk foreign keynya)
    public function stuffStocks()
    {
        return $this->hasOne(StuffStock::class);
    }

    public function inboundStuffs()
    {
        return $this->hasMany(InboundStuff::class);
    }

    public function lendings()
    {
        return $this->hasMany(Lending::class);
    }
}
