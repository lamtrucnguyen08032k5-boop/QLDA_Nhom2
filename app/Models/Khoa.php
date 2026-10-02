<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Khoa extends Model
{
    protected $fillable = ['ma_khoa', 'ten_khoa', 'email', 'mo_ta', 'active'];

    public function giangViens()
    {
        return $this->hasMany(User::class)->where('role', 'giangvien');
    }

    public function sinhViens()
    {
        return $this->hasMany(User::class)->where('role', 'sinhvien');
    }

    public function taiKhoanKhoa()
    {
        return $this->hasOne(User::class)->where('role', 'khoa');
    }

    public function lichThis()
    {
        return $this->hasMany(LichThi::class);
    }

    public function deThis()
    {
        return $this->hasMany(DeThi::class);
    }
}
