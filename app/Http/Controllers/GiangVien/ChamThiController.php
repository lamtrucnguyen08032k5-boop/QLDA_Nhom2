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

        $tongDiemToiDaTuLuan = $baithi->tong_diem_toi_da_tu_luan;

        return view('giangvien.cham-thi.show', compact(
            'baithi', 'isGV1', 'isGV2', 'isTurnGV1', 'isTurnGV2', 'isReadOnly', 'isNotMyTurn', 'tongDiemToiDaTuLuan'
        ));
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

        $tongDiemToiDaTuLuan = $baithi->tong_diem_toi_da_tu_luan;

        $data = $request->validate([
            'diem_tu_luan' => 'required|numeric|min:0|max:' . $tongDiemToiDaTuLuan,
            'nhan_xet_1' => 'nullable|string',
            'nhan_xet_2' => 'nullable|string',
        ], [
            'diem_tu_luan.required' => 'Vui lòng nhập tổng điểm phần tự luận.',
            'diem_tu_luan.numeric' => 'Điểm tự luận phải là chữ số hợp lệ.',
            'diem_tu_luan.min' => 'Điểm tự luận không được nhỏ hơn 0.',
            'diem_tu_luan.max' => "Điểm tự luận không được vượt quá {$tongDiemToiDaTuLuan} điểm.",
        ]);

        $diemTuLuan = (float) $data['diem_tu_luan'];

        if ($isTurnGV1) {
            $newTrangThai = ($action === 'gui') ? 'cho_cham_2' : 'dang_cham_1';
            $baithi->update([
                'diem_tu_luan_gv1' => $diemTuLuan,
                'nhan_xet_1' => $request->input('nhan_xet_1'),
                'ngay_cham_1' => ($action === 'gui') ? now() : $baithi->ngay_cham_1,
                'trang_thai' => $newTrangThai,
            ]);

            $msg = ($action === 'gui') ? 'Đã gửi kết quả chấm lần 1 thành công.' : 'Đã lưu nháp kết quả chấm lần 1.';
            return redirect()->route('giangvien.cham-thi.index')->with('status', $msg);
        }

        if ($isTurnGV2) {
            if ($action === 'nhap') {
                $baithi->update([
                    'diem_tu_luan_gv2' => $diemTuLuan,
                    'nhan_xet_2' => $request->input('nhan_xet_2'),
                    'trang_thai' => 'dang_cham_2',
                ]);
                return redirect()->route('giangvien.cham-thi.index')->with('status', 'Đã lưu nháp kết quả chấm lần 2.');
            }

            // GV2 Gửi kết quả -> HĐ20: Đối chiếu & Phân nhánh
            $baithi->update([
                'diem_tu_luan_gv2' => $diemTuLuan,
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
        $diemGV1 = (float) ($baithi->diem_tu_luan_gv1 ?? 0);
        $diemGV2 = (float) ($baithi->diem_tu_luan_gv2 ?? 0);

        // Kiểm tra xem 2 giảng viên có điểm trùng khớp hoàn toàn hay không
        $isThongNhat = (abs($diemGV1 - $diemGV2) < 0.001);

        if ($isThongNhat) {
            // Đồng nhất hoàn toàn -> Tự động chốt điểm bài thi
            $diemChotTuLuan = $diemGV2;
            $diemTong = (float) $baithi->diem_tu_dong + $diemChotTuLuan;

            DB::transaction(function () use ($baithi, $diemChotTuLuan, $diemTong) {
                $baithi->update([
                    'diem_cham_tay' => $diemChotTuLuan,
                    'diem_chot' => $diemChotTuLuan,
                    'diem_tong' => $diemTong,
                    'cham_xong' => true,
                    'da_khoa' => true,
                    'trang_thai' => 'da_chot',
                    'ngay_cham' => now(),
                ]);

                // Đánh dấu các câu trả lời tự luận
                $cauTuLuans = $baithi->cauTraLois()->whereHas('cauHoi', function ($q) {
                    $q->whereIn('loai_cau', ['tu_luan', 'tuluan']);
                })->get();

                $this->phanBoDiemTuLuan($cauTuLuans, $diemChotTuLuan);
            });

            return redirect()->route('giangvien.cham-thi.index')
                ->with('status', 'Kết quả chấm của 2 Giảng viên hoàn toàn đồng nhất. Hệ thống đã tự động tổng hợp và chốt điểm bài thi.');
        } else {
            // Có chênh lệch điểm giữa GV1 và GV2 -> Chuyển sang trạng thái Chờ thống nhất
            $baithi->update([
                'trang_thai' => 'cho_thong_nhat',
            ]);

            return redirect()->route('giangvien.cham-thi.thong-nhat', $baithi->id)
                ->with('status', 'Phát hiện chênh lệch điểm chấm giữa 2 Giảng viên. Bài thi được chuyển sang bước Chờ thống nhất.');
        }
    }

    /**
     * Phân bổ điểm tự luận cho các câu trả lời để hoàn thiện dữ liệu chi tiết
     */
    private function phanBoDiemTuLuan($cauTuLuans, float $tongDiemDat): void
    {
        if ($cauTuLuans->isEmpty()) {
            return;
        }

        $tongDiemToiDa = (float) $cauTuLuans->sum(fn($ctl) => $ctl->cauHoi?->diem ?? 0);

        if ($tongDiemToiDa <= 0) {
            foreach ($cauTuLuans as $ctl) {
                $ctl->update(['da_cham' => true, 'diem_dat' => 0]);
            }
            return;
        }

        if ($cauTuLuans->count() === 1) {
            $ctl = $cauTuLuans->first();
            $ctl->update([
                'diem_dat' => min($tongDiemDat, (float)($ctl->cauHoi?->diem ?? $tongDiemDat)),
                'da_cham' => true,
            ]);
            return;
        }

        $tyLe = $tongDiemDat / $tongDiemToiDa;
        foreach ($cauTuLuans as $ctl) {
            $diemCau = round((float)($ctl->cauHoi?->diem ?? 0) * $tyLe, 2);
            $ctl->update([
                'diem_dat' => $diemCau,
                'da_cham' => true,
            ]);
        }
    }

    public function thongNhat(BaiThi $baithi)
    {
        $user = Auth::user();
        $this->authorizeGiangVienForBaiThi($baithi, $user);

        if ($baithi->trang_thai !== 'cho_thong_nhat' && !$baithi->da_khoa) {
            return redirect()->route('giangvien.cham-thi.show', $baithi->id);
        }

        $baithi->load(['cauTraLois.cauHoi', 'dangKy.sinhVien', 'giangVien1', 'giangVien2']);

        $cauTuLuans = $baithi->cauTraLois()->whereHas('cauHoi', function ($q) {
            $q->whereIn('loai_cau', ['tu_luan', 'tuluan']);
        })->get();

        $isGV1 = ($baithi->giang_vien_1_id === $user->id);
        $isGV2 = ($baithi->giang_vien_2_id === $user->id);
        $isReadOnly = $baithi->da_khoa || ($baithi->trang_thai === 'da_chot');

        $tongDiemToiDaTuLuan = $baithi->tong_diem_toi_da_tu_luan;

        // GV1 chỉ xác nhận nếu GV2 đã lưu điểm thống nhất
        $canConfirmGV1 = $isGV1 && !$isReadOnly && (
            $baithi->diem_chot !== null || !empty($baithi->ly_do_thong_nhat)
        );

        return view('giangvien.cham-thi.thong-nhat', compact(
            'baithi', 'cauTuLuans', 'isGV1', 'isGV2', 'isReadOnly', 'canConfirmGV1', 'tongDiemToiDaTuLuan'
        ));
    }

    public function luuThongNhat(Request $request, BaiThi $baithi)
    {
        $user = Auth::user();
        $this->authorizeGiangVienForBaiThi($baithi, $user);

        if ($baithi->da_khoa || $baithi->trang_thai === 'da_chot') {
            return redirect()->back()->withErrors(['msg' => 'Bài thi đã chốt điểm, không thể chỉnh sửa.']);
        }

        $isGV1 = ($baithi->giang_vien_1_id === $user->id);
        $isGV2 = ($baithi->giang_vien_2_id === $user->id);
        $action = $request->input('action');
        $tongDiemToiDaTuLuan = $baithi->tong_diem_toi_da_tu_luan;

        // 1. Nếu GV1 gửi yêu cầu xác nhận kết quả chốt điểm
        if ($action === 'xac_nhan' && $isGV1) {
            if ($baithi->diem_chot === null && empty($baithi->ly_do_thong_nhat)) {
                return redirect()->back()->withErrors(['msg' => 'Chưa có kết quả thống nhất điểm từ GV2. Không thể xác nhận.']);
            }

            $diemTuLuanThongNhat = (float) ($baithi->diem_chot ?? $baithi->diem_tu_luan_gv2 ?? 0);
            $diemTong = (float) $baithi->diem_tu_dong + $diemTuLuanThongNhat;

            DB::transaction(function () use ($baithi, $diemTuLuanThongNhat, $diemTong) {
                $baithi->update([
                    'diem_cham_tay' => $diemTuLuanThongNhat,
                    'diem_chot' => $diemTuLuanThongNhat,
                    'diem_tong' => $diemTong,
                    'cham_xong' => true,
                    'da_khoa' => true,
                    'trang_thai' => 'da_chot',
                    'ngay_cham' => now(),
                    'ly_do_thong_nhat' => $baithi->ly_do_thong_nhat ?: 'Đã thống nhất điểm',
                ]);

                $cauTuLuans = $baithi->cauTraLois()->whereHas('cauHoi', function ($q) {
                    $q->whereIn('loai_cau', ['tu_luan', 'tuluan']);
                })->get();
                $this->phanBoDiemTuLuan($cauTuLuans, $diemTuLuanThongNhat);
            });

            return redirect()->route('giangvien.cham-thi.index')
                ->with('status', 'GV1 đã xác nhận. Kết quả thi đã được tổng hợp và chốt điểm chính thức.');
        }

        // 2. Nếu GV2 lưu điểm thống nhất & ghi chú
        if ($isGV2) {
            $data = $request->validate([
                'diem_tu_luan_thong_nhat' => 'required|numeric|min:0|max:' . $tongDiemToiDaTuLuan,
                'ly_do_thong_nhat' => 'nullable|string',
            ], [
                'diem_tu_luan_thong_nhat.required' => 'Vui lòng nhập tổng điểm tự luận thống nhất.',
                'diem_tu_luan_thong_nhat.numeric' => 'Điểm thống nhất phải là chữ số hợp lệ.',
                'diem_tu_luan_thong_nhat.min' => 'Điểm thống nhất không được nhỏ hơn 0.',
                'diem_tu_luan_thong_nhat.max' => "Điểm thống nhất không được vượt quá {$tongDiemToiDaTuLuan} điểm.",
            ]);

            $diemTN = (float) $data['diem_tu_luan_thong_nhat'];

            $baithi->update([
                'diem_chot' => $diemTN,
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
