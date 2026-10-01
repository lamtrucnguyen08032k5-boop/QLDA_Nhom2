<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\CauTraLoi;
use App\Models\DangKy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

// M5 - Tổ chức thi: xác thực ca thi -> làm bài -> nộp bài -> tự chấm trắc nghiệm
class ThiController extends Controller
{
    public function index()
    {
        $dangKys = DangKy::with(['lichThi', 'baiThi'])
            ->where('sinh_vien_id', Auth::id())
            ->where('trang_thai', 'da_duyet')
            ->whereHas('lichThi', fn ($q) => $q->whereIn('trang_thai', ['da_dong_dang_ky', 'dang_thi', 'da_ket_thuc']))
            ->orderByDesc('created_at')
            ->get();

        return view('sinhvien.thi.index', compact('dangKys'));
    }

    public function chiTiet(DangKy $dangky)
    {
        abort_unless($dangky->sinh_vien_id === Auth::id(), 403);
        abort_unless($dangky->trang_thai === 'da_duyet', 403);

        $dangky->load(['lichThi.deThi', 'baiThi']);

        return view('sinhvien.thi.chi-tiet', compact('dangky'));
    }

    public function nhapMa(Request $request, DangKy $dangky)
    {
        abort_unless($dangky->sinh_vien_id === Auth::id(), 403);
        abort_unless($dangky->trang_thai === 'da_duyet', 403);

        $data = $request->validate([
            'ma_ca_thi' => 'required|string|max:50',
        ]);

        $lichThi = $dangky->lichThi;
        $maCaThi = strtoupper(trim($data['ma_ca_thi']));

        if ($maCaThi !== strtoupper((string) $lichThi->ma_ca_thi)) {
            throw ValidationException::withMessages([
                'ma_ca_thi' => 'Mã ca thi không đúng.',
            ]);
        }

        if ($lichThi->trang_thai !== 'dang_thi') {
            throw ValidationException::withMessages([
                'ma_ca_thi' => 'Ca thi chưa bắt đầu hoặc đã kết thúc.',
            ]);
        }

        if (! $lichThi->de_thi_id) {
            throw ValidationException::withMessages([
                'ma_ca_thi' => 'Ca thi chưa được gán đề thi.',
            ]);
        }

        $deThi = $lichThi->deThi;
        if (! $deThi || ! $deThi->active || $deThi->cauHois()->count() === 0) {
            throw ValidationException::withMessages([
                'ma_ca_thi' => 'Đề thi không khả dụng.',
            ]);
        }

        $baiThi = $dangky->baiThi;

        if ($baiThi?->gio_nop) {
            return redirect()->route('sinhvien.thi.chi-tiet', $dangky)
                ->withErrors(['ma_ca_thi' => 'Bạn đã nộp bài thi này rồi.']);
        }

        if (! $baiThi) {
            $baiThi = BaiThi::create([
                'dang_ky_id' => $dangky->id,
                'de_thi_id' => $deThi->id,
                'gio_bat_dau' => now(),
                'trang_thai' => 'dang_thi',
            ]);
        }

        return redirect()->route('sinhvien.thi.lam-bai', $baiThi);
    }

    public function lamBai(BaiThi $baithi)
    {
        abort_unless($baithi->dangKy->sinh_vien_id === Auth::id(), 403);
        abort_unless($baithi->dangKy->trang_thai === 'da_duyet', 403);
        abort_if($baithi->gio_nop, 403, 'Bài thi đã được nộp.');

        $baithi->load(['deThi.cauHois', 'dangKy.lichThi']);

        $lichThi = $baithi->dangKy->lichThi;
        abort_unless($lichThi->trang_thai === 'dang_thi', 403, 'Ca thi đã kết thúc.');

        $hanNop = $baithi->gio_bat_dau->copy()->addMinutes((int) $lichThi->thoi_gian_thi_phut);

        if (now()->gte($hanNop)) {
            return $this->autoSubmitExpired($baithi);
        }

        return view('sinhvien.thi.lam-bai', compact('baithi', 'hanNop'));
    }

    public function nopBai(Request $request, BaiThi $baithi)
    {
        abort_unless($baithi->dangKy->sinh_vien_id === Auth::id(), 403);
        abort_unless($baithi->dangKy->trang_thai === 'da_duyet', 403);
        abort_if($baithi->gio_nop, 403, 'Bài thi đã được nộp.');

        $baithi->load(['deThi.cauHois', 'dangKy.lichThi']);
        $lichThi = $baithi->dangKy->lichThi;

        abort_unless($lichThi->trang_thai === 'dang_thi', 403, 'Ca thi đã kết thúc.');

        $traLoi = $request->input('tra_loi', []);
        if (! is_array($traLoi)) {
            $traLoi = [];
        }

        $hanNop = $baithi->gio_bat_dau->copy()->addMinutes((int) $lichThi->thoi_gian_thi_phut);
        $tuDongNop = now()->gte($hanNop);

        DB::transaction(function () use ($baithi, $traLoi) {
            $diemTuDong = 0;
            $coTuLuan = false;

            foreach ($baithi->deThi->cauHois as $cauHoi) {
                $giaTri = $traLoi[$cauHoi->id] ?? null;

                if ($cauHoi->loai_cau === 'tracnghiem') {
                    $giaTri = $giaTri !== null ? strtoupper(trim((string) $giaTri)) : null;
                    $dung = $giaTri !== null && $giaTri === strtoupper((string) $cauHoi->dap_an_dung);
                    $diem = $dung ? (float) $cauHoi->diem : 0;
                    $diemTuDong += $diem;

                    CauTraLoi::create([
                        'bai_thi_id' => $baithi->id,
                        'cau_hoi_id' => $cauHoi->id,
                        'dap_an_chon' => $giaTri,
                        'diem_dat' => $diem,
                        'da_cham' => true,
                    ]);
                } else {
                    $coTuLuan = true;

                    CauTraLoi::create([
                        'bai_thi_id' => $baithi->id,
                        'cau_hoi_id' => $cauHoi->id,
                        'bai_lam_tu_luan' => is_string($giaTri) ? trim($giaTri) : null,
                        'diem_dat' => 0,
                        'da_cham' => false,
                    ]);
                }
            }

            $baithi->update([
                'gio_nop' => now(),
                'diem_tu_dong' => $diemTuDong,
                'diem_tong' => $coTuLuan ? null : $diemTuDong,
                'cham_xong' => ! $coTuLuan,
                'trang_thai' => $coTuLuan ? 'dang_cham' : 'da_cham',
            ]);
        });

        return redirect()->route('sinhvien.thi.chi-tiet', $baithi->dangKy)
            ->with('status', $tuDongNop ? 'Hết giờ, hệ thống đã tự động nộp bài.' : 'Nộp bài thành công.');
    }

    private function autoSubmitExpired(BaiThi $baithi)
    {
        // Gọi lại cùng nghiệp vụ nộp bài với bộ câu trả lời rỗng.
        // Không dùng HTTP redirect nội bộ để tránh tạo request giả.
        DB::transaction(function () use ($baithi) {
            $coTuLuan = false;
            $diemTuDong = 0;

            foreach ($baithi->deThi->cauHois as $cauHoi) {
                if ($cauHoi->loai_cau === 'tracnghiem') {
                    CauTraLoi::create([
                        'bai_thi_id' => $baithi->id,
                        'cau_hoi_id' => $cauHoi->id,
                        'dap_an_chon' => null,
                        'diem_dat' => 0,
                        'da_cham' => true,
                    ]);
                } else {
                    $coTuLuan = true;
                    CauTraLoi::create([
                        'bai_thi_id' => $baithi->id,
                        'cau_hoi_id' => $cauHoi->id,
                        'bai_lam_tu_luan' => null,
                        'diem_dat' => 0,
                        'da_cham' => false,
                    ]);
                }
            }

            $baithi->update([
                'gio_nop' => now(),
                'diem_tu_dong' => $diemTuDong,
                'diem_tong' => $coTuLuan ? null : 0,
                'cham_xong' => ! $coTuLuan,
                'trang_thai' => $coTuLuan ? 'dang_cham' : 'da_cham',
            ]);
        });

        return redirect()->route('sinhvien.thi.chi-tiet', $baithi->dangKy)
            ->with('status', 'Hết giờ, hệ thống đã tự động nộp bài.');
    }
}
