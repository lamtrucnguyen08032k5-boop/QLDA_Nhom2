<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeThi extends Model
{
    protected $table = 'de_this';
    protected $fillable = ['ma_de', 'loai_chung_chi', 'khoa_id', 'ten_de', 'file_goc', 'active'];

    public function khoa()
    {
        return $this->belongsTo(Khoa::class);
    }

    public function cauHois()
    {
        return $this->hasMany(CauHoi::class)->orderBy('thu_tu');
    }

    public function lichThis()
    {
        return $this->hasMany(LichThi::class, 'de_thi_id');
    }

    public function baiThis()
    {
        return $this->hasMany(BaiThi::class, 'de_thi_id');
    }

    public function tongDiem()
    {
        return $this->cauHois()->sum('diem');
    }
}
