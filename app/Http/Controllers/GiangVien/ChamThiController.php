<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\CauTraLoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if ($request->filled('filter')) {
            $filter = $request->get('filter');
            if ($filter === 'chua_cham') {
                $q->whereIn('trang_thai', ['cho_cham_1', 'dang_cham_1', 'cho_cham_2', 'dang_cham_2', 'cho_thong_nhat']);
            } elseif ($filter === 'da_cham') {
                $q->where('trang_thai', 'da_chot');
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

        $isGV1 = ($baithi->giang_vien_1_id === $user->id);
        $isGV2 = ($baithi->giang_vien_2_id === $user->id);

        // HĐ15-16: GV2 xem bài trước khi GV1 hoàn thành -> Chế độ chỉ đọc
        $isGV2ReadOnlyBeforeGV1 = ($isGV2 && in_array($baithi->trang_thai, ['cho_cham_1', 'dang_cham_1']));
        $isReadOnly = $baithi->da_khoa || ($baithi->trang_thai === 'da_chot') || $isGV2ReadOnlyBeforeGV1;

        if ($baithi->trang_thai === 'cho_thong_nhat') {
            return redirect()->route('giangvien.cham-thi.thong-nhat', $baithi->id);
        }

        return view('giangvien.cham-thi.show', compact('baithi', 'isGV1', 'isGV2', 'isReadOnly', 'isGV2ReadOnlyBeforeGV1'));
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
        $action = $request->input('action', 'nhap'); // 'nhap' (lưu nháp) hoặc 'gui' (gửi kết quả)

        if ($isGV1) {
            if (!in_array($baithi->trang_thai, ['cho_cham_1', 'dang_cham_1'])) {
                return redirect()->back()->withErrors(['msg' => 'GV1 chỉ được chấm khi bài thi ở trạng thái chờ/đang chấm lần 1.']);
            }

            $data = $request->validate([
                'diem.*' => 'nullable|numeric|min:0',
                'nhan_xet_1' => 'nullable|string',
            ]);

            foreach ($data['diem'] ?? [] as $cauTraLoiId => $diem) {
                $ctl = CauTraLoi::where('bai_thi_id', $baithi->id)->findOrFail($cauTraLoiId);
                $diemToiDa = $ctl->cauHoi->diem;
                $diemCapped = min((float) $diem, (float) $diemToiDa);
                // Ghi vào diem_gv1, KHÔNG ghi đè diem_dat
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

        if ($isGV2) {
            // BR-03: GV2 không được gửi điểm trước khi GV1 hoàn thành
            if (in_array($baithi->trang_thai, ['cho_cham_1', 'dang_cham_1'])) {
                return redirect()->back()->withErrors(['msg' => 'GV2 chưa đến lượt chấm. GV1 phải hoàn thành chấm lần 1 trước.']);
            }

            if (!in_array($baithi->trang_thai, ['cho_cham_2', 'dang_cham_2'])) {
                return redirect()->back()->withErrors(['msg' => 'Bài thi không ở trạng thái chờ/đang chấm lần 2.']);
            }

            $data = $request->validate([
                'diem.*' => 'nullable|numeric|min:0',
                'nhan_xet_2' => 'nullable|string',
            ]);

            foreach ($data['diem'] ?? [] as $cauTraLoiId => $diem) {
                $ctl = CauTraLoi::where('bai_thi_id', $baithi->id)->findOrFail($cauTraLoiId);
                $diemToiDa = $ctl->cauHoi->diem;
                $diemCapped = min((float) $diem, (float) $diemToiDa);
                // Ghi vào diem_gv2, KHÔNG ghi đè diem_gv1 hay diem_dat
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
     * HĐ20: So sánh kết quả tự luận GV1 vs GV2
     */
    private function xuLyPhanNhanhHD20(BaiThi $baithi)
    {
        $cauTuLuans = $baithi->cauTraLois()->whereHas('cauHoi', function ($q) {
            $q->where('loai_cau', 'tu_luan');
        })->get();

        // Nếu không có câu tự luận nào trong bài
        if ($cauTuLuans->isEmpty()) {
            $tongGV1 = 0;
            $tongGV2 = 0;
        } else {
            $tongGV1 = (float) $cauTuLuans->sum('diem_gv1');
            $tongGV2 = (float) $cauTuLuans->sum('diem_gv2');
        }

        $isThongNhat = $this->kiemTraThongNhat($tongGV1, $tongGV2);

        if ($isThongNhat) {
            // NHÁNH A: Thống nhất theo quy định -> HĐ23 -> HĐ24
            $diemChotTuLuan = $this->tinhDiemChotNhanhA($tongGV1, $tongGV2);
            $diemTong = (float) $baithi->diem_tu_dong + $diemChotTuLuan;

            // HĐ24: Ghi chính thức điểm chốt và tổng điểm
            $baithi->update([
                'diem_chot' => $diemChotTuLuan,
                'diem_tong' => $diemTong,
                'cham_xong' => true,
                'da_khoa' => true,
                'trang_thai' => 'da_chot',
                'ngay_cham' => now(),
            ]);

            // Cập nhật diem_dat từng câu tự luận (backward compatibility)
            foreach ($cauTuLuans as $ctl) {
                // Điểm câu tự luận chốt theo tỉ lệ hoặc giá trị GV1
                $ctl->update([
                    'diem_dat' => $ctl->diem_gv2 ?? $ctl->diem_gv1 ?? 0,
                    'da_cham' => true,
                ]);
            }

            return redirect()->route('giangvien.cham-thi.index')
                ->with('status', 'Kết quả 2 GV thống nhất theo quy định. Hệ thống đã tổng hợp và chốt điểm bài thi (HĐ24).');
        } else {
            // NHÁNH B: Có chênh lệch cần thống nhất -> HĐ21
            $baithi->update([
                'trang_thai' => 'cho_thong_nhat',
            ]);

            return redirect()->route('giangvien.cham-thi.thong-nhat', $baithi->id)
                ->with('status', 'Kết quả chấm có chênh lệch cần thống nhất giữa 2 Giảng viên.');
        }
    }

    /**
     * Helper kiểm tra 2 điểm tự luận có thống nhất theo quy định (PC-01)
     */
    private function kiemTraThongNhat(float $tongGV1, float $tongGV2): bool
    {
        // BUSINESS BLOCKER PC-01: Chờ nhóm xác nhận ngưỡng chênh lệch điểm.
        // Tuyệt đối không hard-code ngưỡng chênh lệch hoặc mặc định hai tổng điểm phải bằng nhau tuyệt đối trong production.
        $nguong = config('exam.nguong_chenh_lech', null);
        if ($nguong !== null) {
            return abs($tongGV1 - $tongGV2) <= (float) $nguong;
        }

        // Fallback dev/test (KHÔNG PHẢI business rule production)
        return abs($tongGV1 - $tongGV2) < 0.001;
    }

    /**
     * Helper tính điểm chốt tự luận cho Nhánh A (PC-02)
     */
    private function tinhDiemChotNhanhA(float $tongGV1, float $tongGV2): float
    {
        // BUSINESS BLOCKER PC-02: Chờ nhóm xác nhận công thức tính điểm chốt Nhánh A.
        // Tuyệt đối không tự ý hard-code GV1, GV2, hay trung bình làm logic production.
        $congThuc = config('exam.cong_thuc_nhanh_a', null);
        if ($congThuc === 'trung_binh') {
            return ($tongGV1 + $tongGV2) / 2;
        } elseif ($congThuc === 'gv1') {
            return $tongGV1;
        } elseif ($congThuc === 'gv2') {
            return $tongGV2;
        }

        // Fallback dev/test (KHÔNG PHẢI business rule production)
        return (float) config('exam.dev_fallback_diem_chot_nhanh_a', $tongGV1);
    }

    /**
     * HĐ21-22: Trang trao đổi & nhập điểm thống nhất
     */
    public function thongNhat(BaiThi $baithi)
    {
        $user = Auth::user();
        $this->authorizeGiangVienForBaiThi($baithi, $user);

        $baithi->load(['cauTraLois.cauHoi', 'dangKy.sinhVien', 'giangVien1', 'giangVien2']);

        $isGV1 = ($baithi->giang_vien_1_id === $user->id);
        $isGV2 = ($baithi->giang_vien_2_id === $user->id);

        return view('giangvien.cham-thi.thong-nhat', compact('baithi', 'isGV1', 'isGV2'));
    }

    /**
     * HĐ22: GV2 nhập điểm thống nhất hoặc GV1 xác nhận
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

        if ($isGV2) {
            // HĐ22: GV2 nhập điểm tự luận đã thống nhất + lý do
            $data = $request->validate([
                'diem_thong_nhat.*' => 'required|numeric|min:0',
                'ly_do_thong_nhat' => 'required|string',
            ], [
                'ly_do_thong_nhat.required' => 'Vui lòng nhập lý do/ghi chú thống nhất điểm.',
            ]);

            $tongDiemThongNhat = 0;
            foreach ($data['diem_thong_nhat'] as $ctlId => $diemTN) {
                $ctl = CauTraLoi::where('bai_thi_id', $baithi->id)->findOrFail($ctlId);
                $diemToiDa = $ctl->cauHoi->diem;
                $diemTNVal = min((float) $diemTN, (float) $diemToiDa);
                // TUYỆT ĐỐI KHÔNG ghi đè diem_gv1 hay diem_gv2.
                // Lưu tạm điểm thống nhất vào diem_dat (chưa chốt) hoặc lưu nháp để GV1 xác nhận
                $ctl->update(['diem_dat' => $diemTNVal]);
                $tongDiemThongNhat += $diemTNVal;
            }

            // HĐ22: Ghi dữ liệu tạm vào ly_do_thong_nhat, chưa ghi diem_chot chính thức
            $baithi->update([
                'ly_do_thong_nhat' => $request->input('ly_do_thong_nhat'),
            ]);

            return redirect()->back()->with('status', 'GV2 đã nhập điểm thống nhất. Đang chờ GV1 xác nhận.');
        }

        if ($isGV1) {
            $action = $request->input('action');
            if ($action === 'xac_nhan') {
                // GV1 xác nhận -> HĐ23 & HĐ24: Tổng hợp & Chốt điểm chính thức
                $cauTuLuans = $baithi->cauTraLois()->whereHas('cauHoi', function ($q) {
                    $q->where('loai_cau', 'tu_luan');
                })->get();

                $diemTuLuanThongNhat = (float) $cauTuLuans->sum('diem_dat');
                $diemTong = (float) $baithi->diem_tu_dong + $diemTuLuanThongNhat;

                // HĐ24: THỜI ĐIỂM DUY NHẤT ghi chính thức diem_chot
                $baithi->update([
                    'diem_chot' => $diemTuLuanThongNhat,
                    'diem_tong' => $diemTong,
                    'cham_xong' => true,
                    'da_khoa' => true,
                    'trang_thai' => 'da_chot',
                    'ngay_cham' => now(),
                ]);

                foreach ($cauTuLuans as $ctl) {
                    $ctl->update(['da_cham' => true]);
                }

                return redirect()->route('giangvien.cham-thi.index')
                    ->with('status', 'GV1 đã xác nhận. Kết quả thi đã được tổng hợp và chốt chính thức (HĐ24).');
            } else {
                return redirect()->back()->with('status', 'GV1 chưa xác nhận. Bài thi tiếp tục ở trạng thái Chờ thống nhất.');
            }
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
