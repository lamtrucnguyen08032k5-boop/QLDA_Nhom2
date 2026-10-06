<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\CauTraLoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// M6 - UC2.3.6 Giảng viên chấm bài thi (Quy trình 2 lần chấm, thống nhất, chốt điểm)
class ChamThiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $q = BaiThi::with(['dangKy.sinhVien', 'dangKy.lichThi', 'deThi', 'giangVien1', 'giangVien2'])
            ->where(function ($qq) use ($user) {
                $qq->where('giang_vien_1_id', $user->id)
                   ->orWhere('giang_vien_2_id', $user->id)
                   // Backward compatibility cho dữ liệu cũ
                   ->orWhere('giang_vien_id', $user->id);
            })
            ->whereNotNull('gio_nop');

        if ($request->filled('tab')) {
            $tab = $request->get('tab');
            if ($tab === 'lan1') {
                $q->where('giang_vien_1_id', $user->id);
            } elseif ($tab === 'lan2') {
                $q->where('giang_vien_2_id', $user->id);
            } elseif ($tab === 'thong_nhat') {
                $q->where('trang_thai', 'cho_thong_nhat');
            }
        }

        if ($request->filled('trang_thai')) {
            $q->where('trang_thai', $request->trang_thai);
        }

        if ($request->filled('filter')) {
            $filter = $request->get('filter');
            if ($filter === 'chua_cham') {
                $q->whereIn('trang_thai', ['cho_cham_1', 'dang_cham_1', 'cho_cham_2', 'dang_cham_2', 'cho_thong_nhat']);
            } elseif ($filter === 'da_cham') {
                $q->whereIn('trang_thai', ['da_chot', 'da_cong_bo']);
            }
        }

        $baiThis = $q->orderByDesc('gio_nop')->paginate(20)->withQueryString();

        return view('giangvien.cham-thi.index', compact('baiThis'));
    }

    public function show(BaiThi $baithi)
    {
        $user = Auth::user();
        $this->authorizeGiangVienForBaiThi($baithi, $user);

        $baithi->load(['cauTraLois.cauHoi', 'dangKy.sinhVien', 'dangKy.lichThi', 'giangVien1', 'giangVien2']);

        if ($baithi->trang_thai === 'cho_thong_nhat') {
            return redirect()->route('giangvien.cham-thi.thong-nhat', $baithi->id);
        }

        $isGV1 = ($baithi->giang_vien_1_id === $user->id);
        $isGV2 = ($baithi->giang_vien_2_id === $user->id);

        // Xác định lượt chấm tích cực (Active Turn)
        $isTurnGV1 = $isGV1 && in_array($baithi->trang_thai, ['cho_cham_1', 'dang_cham_1']);
        $isTurnGV2 = $isGV2 && in_array($baithi->trang_thai, ['cho_cham_2', 'dang_cham_2']);

        // Chưa tới lượt chấm hoặc bài thi đã chốt/khóa -> Chế độ chỉ đọc
        $isNotMyTurn = !$isTurnGV1 && !$isTurnGV2;
        $isReadOnly = $baithi->da_khoa || ($baithi->trang_thai === 'da_chot') || $isNotMyTurn;

        return view('giangvien.cham-thi.show', compact('baithi', 'isGV1', 'isGV2', 'isTurnGV1', 'isTurnGV2', 'isReadOnly', 'isNotMyTurn'));
    }

    public function luuDiem(Request $request, BaiThi $baithi)
    {
        $user = Auth::user();
        $this->authorizeGiangVienForBaiThi($baithi, $user);

        if ($baithi->da_khoa || $baithi->trang_thai === 'da_chot') {
            return redirect()->back()->withErrors(['msg' => 'Bài thi đã chốt điểm, không thể chỉnh sửa.']);
        }

        $isGV1 = ($baithi->giang_vien_1_id === $user->id);
        $isGV2 = ($baithi->giang_vien_2_id === $user->id);
        $action = $request->input('action', 'nhap');

        $isTurnGV1 = $isGV1 && in_array($baithi->trang_thai, ['cho_cham_1', 'dang_cham_1']);
        $isTurnGV2 = $isGV2 && in_array($baithi->trang_thai, ['cho_cham_2', 'dang_cham_2']);

        if (!$isTurnGV1 && !$isTurnGV2) {
            return redirect()->back()->withErrors(['msg' => 'Chưa tới lượt chấm của bạn hoặc bài thi đang ở trạng thái không cho phép chỉnh sửa.']);
        }

        if ($isTurnGV1) {
            $data = $request->validate([
                'diem.*' => 'nullable|numeric|min:0',
                'nhan_xet_1' => 'nullable|string',
            ]);

            foreach ($data['diem'] ?? [] as $cauTraLoiId => $diem) {
                $ctl = CauTraLoi::where('bai_thi_id', $baithi->id)->findOrFail($cauTraLoiId);
                $diemToiDa = $ctl->cauHoi->diem;
                $diemCapped = min((float) $diem, (float) $diemToiDa);
                $ctl->update(['diem_gv1' => $diemCapped]);
            }

            $newTrangThai = ($action === 'gui') ? 'cho_cham_2' : 'dang_cham_1';
            $baithi->update([
                'nhan_xet_1' => $request->input('nhan_xet_1'),
                'ngay_cham_1' => ($action === 'gui') ? now() : $baithi->ngay_cham_1,
                'trang_thai' => $newTrangThai,
            ]);

            $msg = ($action === 'gui') ? 'Đã gửi kết quả chấm lần 1 thành công.' : 'Đã lưu nháp kết quả chấm lần 1.';
            return redirect()->route('giangvien.cham-thi.index')->with('status', $msg);
        }

        if ($isTurnGV2) {
            $data = $request->validate([
                'diem.*' => 'nullable|numeric|min:0',
                'nhan_xet_2' => 'nullable|string',
            ]);

            foreach ($data['diem'] ?? [] as $cauTraLoiId => $diem) {
                $ctl = CauTraLoi::where('bai_thi_id', $baithi->id)->findOrFail($cauTraLoiId);
                $diemToiDa = $ctl->cauHoi->diem;
                $diemCapped = min((float) $diem, (float) $diemToiDa);
                $ctl->update(['diem_gv2' => $diemCapped]);
            }

            if ($action === 'nhap') {
                $baithi->update([
                    'nhan_xet_2' => $request->input('nhan_xet_2'),
                    'trang_thai' => 'dang_cham_2',
                ]);
                return redirect()->route('giangvien.cham-thi.index')->with('status', 'Đã lưu nháp kết quả chấm lần 2.');
            }

            // GV2 Gửi kết quả -> HĐ20: Đối chiếu & Phân nhánh
            $baithi->update([
                'nhan_xet_2' => $request->input('nhan_xet_2'),
                'ngay_cham_2' => now(),
            ]);

            return $this->xuLyPhanNhanhHD20($baithi);
        }

        abort(403);
    }

    /**
     * So sánh kết quả tự luận GV1 vs GV2
     */
    private function xuLyPhanNhanhHD20(BaiThi $baithi)
    {
        $cauTuLuans = $baithi->cauTraLois()->whereHas('cauHoi', function ($q) {
            $q->whereIn('loai_cau', ['tu_luan', 'tuluan']);
        })->get();

        // Kiểm tra xem 2 giảng viên có điểm trùng khớp hoàn toàn trên từng câu hay không
        $isThongNhat = $this->kiemTraThongNhat($cauTuLuans);

        if ($isThongNhat) {
            // Đồng nhất hoàn toàn (không có chênh lệch ở bất kỳ câu nào) -> Tự động chốt điểm bài thi
            $tongGV1 = (float) $cauTuLuans->sum('diem_gv1');

            DB::transaction(function () use ($baithi, $tongGV1, $cauTuLuans) {
                $diemChotTuLuan = $tongGV1;
                $diemTong = (float) $baithi->diem_tu_dong + $diemChotTuLuan;

                $baithi->update([
                    'diem_chot' => $diemChotTuLuan,
                    'diem_tong' => $diemTong,
                    'cham_xong' => true,
                    'da_khoa' => true,
                    'trang_thai' => 'da_chot',
                    'ngay_cham' => now(),
                ]);

                foreach ($cauTuLuans as $ctl) {
                    $ctl->update([
                        'diem_dat' => $ctl->diem_gv2 ?? $ctl->diem_gv1 ?? 0,
                        'da_cham' => true,
                    ]);
                }
            });

            return redirect()->route('giangvien.cham-thi.index')
                ->with('status', 'Kết quả chấm của 2 Giảng viên hoàn toàn đồng nhất. Hệ thống đã tự động tổng hợp và chốt điểm bài thi.');
        } else {
            // Có chênh lệch điểm ở ít nhất 1 câu giữa GV1 và GV2 -> Chuyển sang trạng thái Chờ thống nhất
            $baithi->update([
                'trang_thai' => 'cho_thong_nhat',
            ]);

            return redirect()->route('giangvien.cham-thi.thong-nhat', $baithi->id)
                ->with('status', 'Phát hiện chênh lệch điểm chấm giữa 2 Giảng viên. Bài thi được chuyển sang bước Chờ thống nhất.');
        }
    }

    /**
     * Kiểm tra thống nhất: Chỉ khi điểm GV1 và GV2 trên tất cả các câu tự luận trùng khớp hoàn toàn mới coi là đồng nhất
     */
    private function kiemTraThongNhat($cauTuLuans): bool
    {
        if ($cauTuLuans->isEmpty()) {
            return true;
        }

        foreach ($cauTuLuans as $ctl) {
            $diem1 = (float) ($ctl->diem_gv1 ?? 0);
            $diem2 = (float) ($ctl->diem_gv2 ?? 0);
            if (abs($diem1 - $diem2) >= 0.001) {
                return false; // Có chênh lệch ở ít nhất một câu tự luận
            }
        }

        return true; // Đồng nhất hoàn toàn
    }

    /**
     * Helper tính điểm chốt tự luận cho Nhánh A (PC-02)
     */
    private function tinhDiemChotNhanhA(float $tongGV1, float $tongGV2): float
    {
        $congThuc = config('exam.cong_thuc_nhanh_a', null);
        if ($congThuc === 'trung_binh') {
            return ($tongGV1 + $tongGV2) / 2;
        } elseif ($congThuc === 'gv1') {
            return $tongGV1;
        } elseif ($congThuc === 'gv2') {
            return $tongGV2;
        }

        return (float) config('exam.dev_fallback_diem_chot_nhanh_a', $tongGV1);
    }

    /**
     * Trang trao đổi & nhập điểm thống nhất
     */
    public function thongNhat(BaiThi $baithi)
    {
        $user = Auth::user();
        $this->authorizeGiangVienForBaiThi($baithi, $user);

        $baithi->load(['cauTraLois.cauHoi', 'dangKy.sinhVien', 'giangVien1', 'giangVien2']);

        $isGV1 = ($baithi->giang_vien_1_id === $user->id);
        $isGV2 = ($baithi->giang_vien_2_id === $user->id);

        $hasDiemThongNhat = $baithi->cauTraLois->filter(function ($ctl) {
            return in_array($ctl->cauHoi->loai_cau ?? '', ['tu_luan', 'tuluan']);
        })->every(function ($ctl) {
            return $ctl->diem_dat !== null;
        });

        return view('giangvien.cham-thi.thong-nhat', compact('baithi', 'isGV1', 'isGV2', 'hasDiemThongNhat'));
    }

    /**
     * GV2 nhập điểm thống nhất hoặc GV1 xác nhận
     */
    public function luuThongNhat(Request $request, BaiThi $baithi)
    {
        $user = Auth::user();
        $this->authorizeGiangVienForBaiThi($baithi, $user);

        if ($baithi->trang_thai !== 'cho_thong_nhat') {
            return redirect()->back()->withErrors(['msg' => 'Bài thi không ở trạng thái chờ thống nhất.']);
        }

        $isGV1 = ($baithi->giang_vien_1_id === $user->id);
        $isGV2 = ($baithi->giang_vien_2_id === $user->id);
        $action = $request->input('action');

        $cauTuLuans = $baithi->cauTraLois()->whereHas('cauHoi', function ($q) {
            $q->whereIn('loai_cau', ['tu_luan', 'tuluan']);
        })->get();

        $hasDiemTN = $cauTuLuans->isNotEmpty() && $cauTuLuans->every(fn($ctl) => $ctl->diem_dat !== null);

        // 1. Nếu GV1 gửi yêu cầu xác nhận kết quả chốt điểm
        if ($action === 'xac_nhan' && $isGV1) {
            if (!$hasDiemTN && empty($baithi->ly_do_thong_nhat)) {
                return redirect()->back()->withErrors(['msg' => 'Chưa có kết quả thống nhất điểm từ GV2. Không thể xác nhận.']);
            }

            $diemTuLuanThongNhat = (float) $cauTuLuans->sum('diem_dat');
            $diemTong = (float) $baithi->diem_tu_dong + $diemTuLuanThongNhat;

            DB::transaction(function () use ($baithi, $diemTuLuanThongNhat, $diemTong, $cauTuLuans) {
                $baithi->update([
                    'diem_chot' => $diemTuLuanThongNhat,
                    'diem_tong' => $diemTong,
                    'cham_xong' => true,
                    'da_khoa' => true,
                    'trang_thai' => 'da_chot',
                    'ngay_cham' => now(),
                    'ly_do_thong_nhat' => $baithi->ly_do_thong_nhat ?: 'Đã thống nhất điểm',
                ]);

                foreach ($cauTuLuans as $ctl) {
                    $ctl->update(['da_cham' => true]);
                }
            });

            return redirect()->route('giangvien.cham-thi.index')
                ->with('status', 'GV1 đã xác nhận. Kết quả thi đã được tổng hợp và chốt điểm chính thức.');
        }

        // 2. Nếu GV2 lưu điểm thống nhất & ghi chú
        if ($isGV2) {
            $data = $request->validate([
                'diem_thong_nhat.*' => 'required|numeric|min:0',
                'ly_do_thong_nhat' => 'nullable|string',
            ]);

            foreach ($data['diem_thong_nhat'] as $ctlId => $diemTN) {
                $ctl = CauTraLoi::where('bai_thi_id', $baithi->id)->findOrFail($ctlId);
                $diemToiDa = $ctl->cauHoi->diem;
                $diemTNVal = min((float) $diemTN, (float) $diemToiDa);
                $ctl->update(['diem_dat' => $diemTNVal]);
            }

            $baithi->update([
                'ly_do_thong_nhat' => $request->input('ly_do_thong_nhat') ?: 'Đã thống nhất điểm',
            ]);

            return redirect()->back()->with('status', 'GV2 đã lưu điểm thống nhất. Đang chờ GV1 xác nhận.');
        }

        abort(403);
    }

    private function authorizeGiangVienForBaiThi(BaiThi $baithi, $user): void
    {
        if ($baithi->giang_vien_1_id !== $user->id &&
            $baithi->giang_vien_2_id !== $user->id &&
            $baithi->giang_vien_id !== $user->id) {
            abort(403, 'Bạn không được phân công chấm bài thi này.');
        }
    }
}
