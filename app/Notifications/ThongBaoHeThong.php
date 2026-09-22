<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ThongBaoHeThong extends Notification
{
    use Queueable;

    public string $tieuDe;
    public string $noiDung;
    public ?string $url;
    public string $loai; // 'thanh_cong', 'canh_bao', 'thong_tin', 'quan_trong'
    public string $icon;

    public function __construct(string $tieuDe, string $noiDung, ?string $url = null, string $loai = 'thong_tin', string $icon = 'bi-bell')
    {
        $this->tieuDe = $tieuDe;
        $this->noiDung = $noiDung;
        $this->url = $url;
        $this->loai = $loai;
        $this->icon = $icon;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tieu_de' => $this->tieuDe,
            'noi_dung' => $this->noiDung,
            'url' => $this->url,
            'loai' => $this->loai,
            'icon' => $this->icon,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
