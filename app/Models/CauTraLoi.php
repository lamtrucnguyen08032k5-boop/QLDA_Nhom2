<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CauTraLoi extends Model
{
    protected $table = 'cau_tra_lois';
    protected $fillable = [
        'bai_thi_id', 'cau_hoi_id', 'dap_an_chon', 'bai_lam_tu_luan', 'diem_dat', 'da_cham',
        'diem_gv1', 'diem_gv2',
    ];

    protected function casts(): array
    {
        return ['da_cham' => 'boolean'];
    }

    public function baiThi()
    {
        return $this->belongsTo(BaiThi::class);
    }

    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class);
    }
}
