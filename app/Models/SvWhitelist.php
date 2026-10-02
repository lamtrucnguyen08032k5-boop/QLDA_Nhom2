<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SvWhitelist extends Model
{
    protected $table = 'sv_whitelists';
    protected $fillable = ['ma_sv', 'ho_ten', 'email', 'lop', 'khoa_hoc', 'khoa_id', 'da_dang_ky'];

    public function khoa()
    {
        return $this->belongsTo(Khoa::class);
    }
}
