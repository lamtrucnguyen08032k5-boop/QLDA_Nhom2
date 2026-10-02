<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role', 'ma_so', 'name', 'email', 'password', 'khoa_id',
        'lop', 'khoa_hoc', 'active', 'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function khoa()
    {
        return $this->belongsTo(Khoa::class);
    }

    public function dangKys()
    {
        return $this->hasMany(DangKy::class, 'sinh_vien_id');
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isKhoa(): bool { return $this->role === 'khoa'; }
    public function isGiangVien(): bool { return $this->role === 'giangvien'; }
    public function isSinhVien(): bool { return $this->role === 'sinhvien'; }
}
