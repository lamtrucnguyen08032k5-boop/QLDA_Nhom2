<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKy;
use App\Models\Khoa;
use App\Models\KyThi;
use App\Models\LichThi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// M2 - Quản lý kỳ thi (UC: Thêm kỳ thi, Sửa kỳ thi, Xóa kỳ thi)
class LichThiController extends Controller
{
    // UC: Xem danh sách kỳ thi
    public function index(Request $request)
    {
        $q = KyThi::with(['lichThis.khoa', 'lichThis.dangKys']);

        if ($request->filled('tu_khoa')) {
            $kw = $request->tu_khoa;
            $q->where(function ($query) use ($kw) {
                $query->where('ten_ky_thi', 'like', "%{$kw}%")
                    ->orWhere('nam_hoc', 'like', "%{$kw}%")
                    ->orWhere('hoc_ky', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('nam_hoc')) {
            $q->where('nam_hoc', $request->nam_hoc);
        }

        if ($request->filled('trang_thai')) {
            $q->where('trang_thai', $request->trang_thai);
        }

        $kyThis = $q->orderByDesc('id')->paginate(10)->withQueryString();

        // Danh sách năm học để lọc
        $namHocs = KyThi::whereNotNull('nam_hoc')->distinct()->pluck('nam_hoc');

        // Thống kê tổng quan & số lượng theo trạng thái
        $totalKyThi = KyThi::count();
        $countDangMo = KyThi::where('trang_thai', 'dang_mo_dang_ky')->count();
        $countDaDong = KyThi::where('trang_thai', 'da_dong_dang_ky')->count();
        $countDangDienRa = KyThi::where('trang_thai', 'dang_dien_ra')->count();
        $countDaKetThuc = KyThi::where('trang_thai', 'da_ket_thuc')->count();
        $totalCaThi = LichThi::count();
        $totalThiSinh = DangKy::whereNotIn('trang_thai', ['da_huy', 'tu_choi'])->count();

        $stats = [
            'total_ky_thi' => $totalKyThi,
            'dang_mo' => $countDangMo,
            'da_dong' => $countDaDong,
            'dang_dien_ra' => $countDangDienRa,
            'da_ket_thuc' => $countDaKetThuc,
            'total_ca_thi' => $totalCaThi,
            'total_thi_sinh' => $totalThiSinh,
        ];

        return view('admin.lichthi.index', compact('kyThis', 'namHocs', 'stats'));
    }

    // UC Thêm kỳ thi: Bước 5-6 (hiển thị giao diện thêm mới kỳ thi)
    public function create()
    {
        $khoas = Khoa::where('active', true)->orderBy('ten_khoa')->get();
        $existingSlots = LichThi::select('id', 'ten_ky_thi', 'ma_ca_thi', 'ngay_thi', 'gio_bat_dau', 'thoi_gian_thi_phut', 'phong_thi')
            ->get()
            ->map(fn($lt) => [
                'id' => $lt->id,
                'ten_ky_thi' => $lt->ten_ky_thi,
                'ma_ca_thi' => $lt->ma_ca_thi,
                'ngay_thi' => $lt->ngay_thi ? $lt->ngay_thi->format('Y-m-d') : '',
                'gio_bat_dau' => substr($lt->gio_bat_dau, 0, 5),
                'thoi_gian_thi_phut' => (int) $lt->thoi_gian_thi_phut,
                'phong_thi' => trim($lt->phong_thi),
            ]);

        return view('admin.lichthi.create', compact('khoas', 'existingSlots'));
    }

    // UC Thêm kỳ thi: Bước 15-20 (kiểm tra hợp lệ -> kiểm tra xung đột -> lưu kỳ thi & lịch thi & ca thi)
    public function store(Request $request)
    {
        // Hỗ trợ cấu trúc đa Lịch thi & Ca thi (mới)
        if ($request->has('lich_this') && is_array($request->input('lich_this'))) {
            return $this->storeHierarchicalExam($request);
        }

        // Hỗ trợ fallback dạng phẳng (backward compatibility)
        return $this->storeLegacyExam($request);
    }

    private function storeHierarchicalExam(Request $request)
    {
        // Luồng phụ 3: Thông tin kỳ thi không hợp lệ
        $request->validate([
            'ten_ky_thi' => 'required|string|max:255',
            'nam_hoc' => 'nullable|string|max:50',
            'hoc_ky' => 'nullable|string|max:50',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'required|in:dang_mo_dang_ky,da_dong_dang_ky,dang_dien_ra,da_ket_thuc',
            'lich_this' => 'required|array|min:1',
            'lich_this.*.loai_chung_chi' => 'required|in:cntt,tienganh',
            'lich_this.*.khoa_id' => 'required|exists:khoas,id',
            'lich_this.*.ngay_thi' => 'required|date',
            'lich_this.*.han_dang_ky' => 'required|date',
            'lich_this.*.le_phi' => 'required|numeric|min:0',
            'lich_this.*.ca_this' => 'required|array|min:1',
            'lich_this.*.ca_this.*.gio_bat_dau' => 'required',
            'lich_this.*.ca_this.*.thoi_gian_thi_phut' => 'required|integer|min:10|max:300',
            'lich_this.*.ca_this.*.phong_thi' => 'required|string|max:100',
            'lich_this.*.ca_this.*.so_luong_toi_da' => 'required|integer|min:1',
        ], [
            'ten_ky_thi.required' => 'Vui lòng nhập tên kỳ thi.',
            'lich_this.required' => 'Kỳ thi phải có ít nhất 1 lịch thi.',
            'lich_this.min' => 'Kỳ thi phải có ít nhất 1 lịch thi.',
            'lich_this.*.ca_this.required' => 'Mỗi lịch thi phải có ít nhất 1 ca thi & phòng thi.',
            'lich_this.*.ca_this.min' => 'Mỗi lịch thi phải có ít nhất 1 ca thi & phòng thi.',
            'lich_this.*.ca_this.*.phong_thi.required' => 'Vui lòng nhập phòng thi.',
            'lich_this.*.ca_this.*.gio_bat_dau.required' => 'Vui lòng nhập giờ bắt đầu ca thi.',
            'lich_this.*.ca_this.*.thoi_gian_thi_phut.required' => 'Vui lòng nhập thời gian thi.',
            'lich_this.*.ca_this.*.so_luong_toi_da.required' => 'Vui lòng nhập số lượng tối đa.',
        ]);

        $lichThisInput = $request->input('lich_this');

        // Kiểm tra logic hạn đăng ký & xung đột phòng thi / ca thi
        $errors = $this->validateExamConflicts($lichThisInput);
        if (! empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        DB::beginTransaction();
        try {
            // Bước 18: Tạo kỳ thi
            $kyThi = KyThi::create([
                'ten_ky_thi' => $request->input('ten_ky_thi'),
                'nam_hoc' => $request->input('nam_hoc'),
                'hoc_ky' => $request->input('hoc_ky'),
                'mo_ta' => $request->input('mo_ta'),
                'trang_thai' => $request->input('trang_thai', 'dang_mo_dang_ky'),
            ]);

            // Lưu các lịch thi và ca thi
            foreach ($lichThisInput as $lt) {
                foreach ($lt['ca_this'] as $ct) {
                    $maCaThi = ! empty($ct['ma_ca_thi']) ? trim($ct['ma_ca_thi']) : strtoupper(Str::random(8));

                    // Đảm bảo mã ca thi là duy nhất
                    while (LichThi::where('ma_ca_thi', $maCaThi)->exists()) {
                        $maCaThi = strtoupper(Str::random(8));
                    }

                    LichThi::create([
                        'ky_thi_id' => $kyThi->id,
                        'ten_ky_thi' => $kyThi->ten_ky_thi,
                        'loai_chung_chi' => $lt['loai_chung_chi'],
                        'khoa_id' => $lt['khoa_id'],
                        'ngay_thi' => $lt['ngay_thi'],
                        'gio_bat_dau' => $ct['gio_bat_dau'],
                        'thoi_gian_thi_phut' => (int) $ct['thoi_gian_thi_phut'],
                        'phong_thi' => trim($ct['phong_thi']),
                        'so_luong_toi_da' => (int) $ct['so_luong_toi_da'],
                        'han_dang_ky' => $lt['han_dang_ky'],
                        'le_phi' => (float) $lt['le_phi'],
                        'ma_ca_thi' => $maCaThi,
                        'trang_thai' => $kyThi->trang_thai,
                    ]);
                }
            }

            DB::commit();

            // Bước 20: Hệ thống hiển thị thông báo “Thêm kỳ thi thành công”
            return redirect()->route('admin.lichthi.index')->with('status', 'Thêm kỳ thi thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['general' => 'Đã có lỗi xảy ra khi lưu kỳ thi: ' . $e->getMessage()])->withInput();
        }
    }

    private function storeLegacyExam(Request $request)
    {
        $data = $request->validate([
            'ten_ky_thi' => 'required|string|max:255',
            'loai_chung_chi' => 'required|in:cntt,tienganh',
            'khoa_id' => 'required|exists:khoas,id',
            'ngay_thi' => 'required|date',
            'gio_bat_dau' => 'required',
            'thoi_gian_thi_phut' => 'required|integer|min:10|max:300',
            'phong_thi' => 'required|string|max:100',
            'so_luong_toi_da' => 'required|integer|min:1',
            'han_dang_ky' => 'required|date',
            'le_phi' => 'required|numeric|min:0',
        ]);

        if (Carbon::parse($data['han_dang_ky'])->greaterThanOrEqualTo(Carbon::parse($data['ngay_thi']))) {
            return back()->withErrors(['han_dang_ky' => 'Hạn đăng ký phải trước ngày thi.'])->withInput();
        }

        // Tạo parent KyThi
        $kyThi = KyThi::firstOrCreate(
            ['ten_ky_thi' => $data['ten_ky_thi']],
            ['trang_thai' => 'dang_mo_dang_ky']
        );

        $data['ky_thi_id'] = $kyThi->id;
        $data['ma_ca_thi'] = strtoupper(Str::random(8));
        $data['trang_thai'] = 'dang_mo_dang_ky';

        LichThi::create($data);

        return redirect()->route('admin.lichthi.index')->with('status', 'Thêm kỳ thi thành công.');
    }

    // UC Sửa kỳ thi: Bước 5-7 (hiển thị thông tin kỳ thi, lịch thi, ca thi)
    public function edit($id)
    {
        $kyThi = KyThi::with(['lichThis.khoa'])->find($id);

        if (! $kyThi) {
            // Trường hợp truyền id của 1 LichThi
            $lt = LichThi::findOrFail($id);
            $kyThi = $lt->kyThi ?? KyThi::create([
                'ten_ky_thi' => $lt->ten_ky_thi,
                'trang_thai' => $lt->trang_thai,
            ]);
            $lt->update(['ky_thi_id' => $kyThi->id]);
            $kyThi->load(['lichThis.khoa']);
        }

        $khoas = Khoa::where('active', true)->orderBy('ten_khoa')->get();

        $existingSlots = LichThi::select('id', 'ten_ky_thi', 'ma_ca_thi', 'ngay_thi', 'gio_bat_dau', 'thoi_gian_thi_phut', 'phong_thi')
            ->where(function ($query) use ($kyThi) {
                $query->where('ky_thi_id', '!=', $kyThi->id)
                    ->orWhereNull('ky_thi_id');
            })
            ->get()
            ->map(fn($lt) => [
                'id' => $lt->id,
                'ten_ky_thi' => $lt->ten_ky_thi,
                'ma_ca_thi' => $lt->ma_ca_thi,
                'ngay_thi' => $lt->ngay_thi ? $lt->ngay_thi->format('Y-m-d') : '',
                'gio_bat_dau' => substr($lt->gio_bat_dau, 0, 5),
                'thoi_gian_thi_phut' => (int) $lt->thoi_gian_thi_phut,
                'phong_thi' => trim($lt->phong_thi),
            ]);

        return view('admin.lichthi.edit', compact('kyThi', 'khoas', 'existingSlots'));
    }

    // UC Sửa kỳ thi: Bước 12-17 (kiểm tra hợp lệ -> kiểm tra xung đột -> cập nhật)
    public function update(Request $request, $id)
    {
        $kyThi = KyThi::with('lichThis')->find($id);
        if (! $kyThi) {
            $lt = LichThi::findOrFail($id);
            $kyThi = $lt->kyThi;
        }

        // Hỗ trợ cấu trúc đa Lịch thi & Ca thi
        if ($request->has('lich_this') && is_array($request->input('lich_this'))) {
            return $this->updateHierarchicalExam($request, $kyThi);
        }

        // Fallback update
        return $this->updateLegacyExam($request, $kyThi);
    }

    private function updateHierarchicalExam(Request $request, KyThi $kyThi)
    {
        // Luồng phụ 2: Thông tin kỳ thi không hợp lệ
        $request->validate([
            'ten_ky_thi' => 'required|string|max:255',
            'nam_hoc' => 'nullable|string|max:50',
            'hoc_ky' => 'nullable|string|max:50',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'required|in:dang_mo_dang_ky,da_dong_dang_ky,dang_dien_ra,da_ket_thuc',
            'lich_this' => 'required|array|min:1',
            'lich_this.*.loai_chung_chi' => 'required|in:cntt,tienganh',
            'lich_this.*.khoa_id' => 'required|exists:khoas,id',
            'lich_this.*.ngay_thi' => 'required|date',
            'lich_this.*.han_dang_ky' => 'required|date',
            'lich_this.*.le_phi' => 'required|numeric|min:0',
            'lich_this.*.ca_this' => 'required|array|min:1',
            'lich_this.*.ca_this.*.gio_bat_dau' => 'required',
            'lich_this.*.ca_this.*.thoi_gian_thi_phut' => 'required|integer|min:10|max:300',
            'lich_this.*.ca_this.*.phong_thi' => 'required|string|max:100',
            'lich_this.*.ca_this.*.so_luong_toi_da' => 'required|integer|min:1',
        ]);

        $lichThisInput = $request->input('lich_this');

        // Lấy danh sách ID các ca thi hiện có của kỳ thi này để loại trừ khi check xung đột
        $existingCaThiIds = $kyThi->lichThis->pluck('id')->toArray();

        // Kiểm tra xung đột thời gian & phòng thi (Luồng phụ 4 & 5)
        $errors = $this->validateExamConflicts($lichThisInput, $existingCaThiIds);
        if (! empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Kiểm tra các ca thi bị xóa xem đã có sinh viên đăng ký chưa
        $submittedCaThiIds = [];
        foreach ($lichThisInput as $lt) {
            foreach ($lt['ca_this'] as $ct) {
                if (! empty($ct['id'])) {
                    $submittedCaThiIds[] = (int) $ct['id'];
                }
            }
        }

        $removedCaThis = $kyThi->lichThis()->whereNotIn('id', $submittedCaThiIds)->get();
        foreach ($removedCaThis as $removed) {
            if ($removed->dangKys()->exists()) {
                return back()->withErrors([
                    'general' => "Không thể xóa ca thi '{$removed->ma_ca_thi}' (Phòng {$removed->phong_thi}) do đã có thí sinh đăng ký dự thi.",
                ])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Bước 15: Cập nhật thông tin Kỳ thi
            $kyThi->update([
                'ten_ky_thi' => $request->input('ten_ky_thi'),
                'nam_hoc' => $request->input('nam_hoc'),
                'hoc_ky' => $request->input('hoc_ky'),
                'mo_ta' => $request->input('mo_ta'),
                'trang_thai' => $request->input('trang_thai'),
            ]);

            // Xóa các ca thi không còn trong danh sách đã gửi (đã kiểm tra an toàn)
            foreach ($removedCaThis as $removed) {
                $removed->delete();
            }

            // Cập nhật hoặc tạo mới các ca thi
            foreach ($lichThisInput as $lt) {
                foreach ($lt['ca_this'] as $ct) {
                    $caThiData = [
                        'ky_thi_id' => $kyThi->id,
                        'ten_ky_thi' => $kyThi->ten_ky_thi,
                        'loai_chung_chi' => $lt['loai_chung_chi'],
                        'khoa_id' => $lt['khoa_id'],
                        'ngay_thi' => $lt['ngay_thi'],
                        'gio_bat_dau' => $ct['gio_bat_dau'],
                        'thoi_gian_thi_phut' => (int) $ct['thoi_gian_thi_phut'],
                        'phong_thi' => trim($ct['phong_thi']),
                        'so_luong_toi_da' => (int) $ct['so_luong_toi_da'],
                        'han_dang_ky' => $lt['han_dang_ky'],
                        'le_phi' => (float) $lt['le_phi'],
                        'trang_thai' => $kyThi->trang_thai,
                    ];

                    if (! empty($ct['id']) && ($existingLt = LichThi::find($ct['id']))) {
                        if (! empty($ct['ma_ca_thi'])) {
                            $caThiData['ma_ca_thi'] = trim($ct['ma_ca_thi']);
                        }
                        $existingLt->update($caThiData);
                    } else {
                        $maCaThi = ! empty($ct['ma_ca_thi']) ? trim($ct['ma_ca_thi']) : strtoupper(Str::random(8));
                        while (LichThi::where('ma_ca_thi', $maCaThi)->exists()) {
                            $maCaThi = strtoupper(Str::random(8));
                        }
                        $caThiData['ma_ca_thi'] = $maCaThi;
                        LichThi::create($caThiData);
                    }
                }
            }

            DB::commit();

            // Bước 17: Hệ thống hiển thị thông báo “Cập nhật kỳ thi thành công”
            return redirect()->route('admin.lichthi.index')->with('status', 'Cập nhật kỳ thi thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['general' => 'Đã có lỗi xảy ra khi cập nhật kỳ thi: ' . $e->getMessage()])->withInput();
        }
    }

    private function updateLegacyExam(Request $request, KyThi $kyThi)
    {
        $data = $request->validate([
            'ten_ky_thi' => 'required|string|max:255',
            'loai_chung_chi' => 'required|in:cntt,tienganh',
            'khoa_id' => 'required|exists:khoas,id',
            'ngay_thi' => 'required|date',
            'gio_bat_dau' => 'required',
            'thoi_gian_thi_phut' => 'required|integer|min:10|max:300',
            'phong_thi' => 'required|string|max:100',
            'so_luong_toi_da' => 'required|integer|min:1',
            'han_dang_ky' => 'required|date',
            'le_phi' => 'required|numeric|min:0',
            'trang_thai' => 'required|in:dang_mo_dang_ky,da_dong_dang_ky,dang_thi,da_ket_thuc',
        ]);

        $kyThi->update([
            'ten_ky_thi' => $data['ten_ky_thi'],
            'trang_thai' => $data['trang_thai'],
        ]);

        if ($kyThi->lichThis->isNotEmpty()) {
            $kyThi->lichThis->first()->update($data);
        }

        return redirect()->route('admin.lichthi.index')->with('status', 'Cập nhật kỳ thi thành công.');
    }

    // UC Xóa kỳ thi: Bước 7-15
    public function destroy($id)
    {
        $kyThi = KyThi::with('lichThis.dangKys')->find($id);

        if (! $kyThi) {
            $lt = LichThi::findOrFail($id);
            if ($lt->dangKys()->exists()) {
                return back()->withErrors(['kythi' => 'Không thể xóa lịch thi do có dữ liệu liên quan (đã có sinh viên đăng ký).']);
            }
            $lt->delete();
            return back()->with('status', 'Xóa thành công.');
        }

        // Luồng phụ 2: Không thể xóa kỳ thi do có dữ liệu liên quan (đã có sinh viên đăng ký)
        if ($kyThi->dangKys()->exists()) {
            return back()->withErrors(['kythi' => 'Không thể xóa kỳ thi do có dữ liệu liên quan (đã có sinh viên đăng ký dự thi).']);
        }

        DB::beginTransaction();
        try {
            $kyThi->lichThis()->delete();
            $kyThi->delete();
            DB::commit();

            // Bước 14: Hệ thống hiển thị thông báo “Xóa thành công”
            return redirect()->route('admin.lichthi.index')->with('status', 'Xóa thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['kythi' => 'Lỗi khi xóa kỳ thi: ' . $e->getMessage()]);
        }
    }

    // Xóa riêng một ca thi / lịch thi
    public function destroyLichThi(LichThi $lichthi)
    {
        // Luồng phụ 4: Không thể xóa ca thi do có sinh viên đăng ký
        if ($lichthi->dangKys()->exists()) {
            return back()->withErrors(['lichthi' => 'Không thể xóa ca thi do có dữ liệu liên quan (đã có sinh viên đăng ký).']);
        }

        $lichthi->delete();
        return back()->with('status', 'Xóa ca thi thành công.');
    }

    /**
     * Kiểm tra tính hợp lệ & xung đột ca thi / phòng thi (Luồng phụ 4, 5, 6)
     */
    private function validateExamConflicts(array $lichThisInput, array $ignoreLichThiIds = []): array
    {
        $errors = [];
        $slotsToCreate = [];

        foreach ($lichThisInput as $ltIdx => $lt) {
            $ngayThiStr = $lt['ngay_thi'] ?? '';
            $hanDangKyStr = $lt['han_dang_ky'] ?? '';
            $tenMontoan = ($lt['loai_chung_chi'] ?? '') === 'cntt' ? 'CNTT' : 'Tiếng Anh';

            // Luồng phụ 4: Lịch thi không hợp lệ (hạn đăng ký >= ngày thi)
            if ($ngayThiStr && $hanDangKyStr) {
                try {
                    $ngayThi = Carbon::parse($ngayThiStr)->startOfDay();
                    $hanDangKy = Carbon::parse($hanDangKyStr);

                    if ($hanDangKy->greaterThanOrEqualTo($ngayThi)) {
                        $errors["lich_this.{$ltIdx}.han_dang_ky"] = "Hạn đăng ký của môn {$tenMontoan} phải trước ngày thi ({$ngayThi->format('d/m/Y')}).";
                    }
                } catch (\Throwable) {
                    $errors["lich_this.{$ltIdx}.ngay_thi"] = 'Định dạng ngày thi hoặc hạn đăng ký không hợp lệ.';
                }
            }

            if (empty($lt['ca_this']) || ! is_array($lt['ca_this'])) {
                continue;
            }

            foreach ($lt['ca_this'] as $ctIdx => $ct) {
                $gioBatDau = $ct['gio_bat_dau'] ?? '';
                $thoiGianPhut = (int) ($ct['thoi_gian_thi_phut'] ?? 60);
                $phongThi = trim($ct['phong_thi'] ?? '');

                if (! $ngayThiStr || ! $gioBatDau || ! $phongThi) {
                    continue;
                }

                try {
                    $start = Carbon::parse($ngayThiStr . ' ' . $gioBatDau);
                    $end = $start->copy()->addMinutes($thoiGianPhut);

                    $slotsToCreate[] = [
                        'lt_idx' => $ltIdx,
                        'ct_idx' => $ctIdx,
                        'montoan' => $tenMontoan,
                        'ngay_thi' => $ngayThiStr,
                        'phong_thi' => $phongThi,
                        'start' => $start,
                        'end' => $end,
                        'gio_bat_dau' => $start->format('H:i'),
                        'gio_ket_thuc' => $end->format('H:i'),
                    ];
                } catch (\Throwable) {
                    $errors["lich_this.{$ltIdx}.ca_this.{$ctIdx}.gio_bat_dau"] = 'Thời gian ca thi không hợp lệ.';
                }
            }
        }

        // 1. Kiểm tra xung đột nội bộ giữa các ca thi trong cùng form gửi lên
        $count = count($slotsToCreate);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $s1 = $slotsToCreate[$i];
                $s2 = $slotsToCreate[$j];

                // Nếu cùng ngày thi và cùng phòng thi
                if ($s1['ngay_thi'] === $s2['ngay_thi'] && mb_strtolower($s1['phong_thi']) === mb_strtolower($s2['phong_thi'])) {
                    // Kiểm tra trùng khoảng thời gian (Start1 < End2 && End1 > Start2)
                    if ($s1['start']->lt($s2['end']) && $s1['end']->gt($s2['start'])) {
                        $errors["conflict_{$i}_{$j}"] = "Xung đột phòng thi: Phòng '{$s1['phong_thi']}' bị trùng thời gian giữa ca thi ({$s1['gio_bat_dau']} - {$s1['gio_ket_thuc']}) và ca thi ({$s2['gio_bat_dau']} - {$s2['gio_ket_thuc']}) vào ngày " . Carbon::parse($s1['ngay_thi'])->format('d/m/Y') . '. Vui lòng điều chỉnh lại phòng thi hoặc thời gian.';
                    }
                }
            }
        }

        // 2. Kiểm tra xung đột với các ca thi đã có trong cơ sở dữ liệu
        foreach ($slotsToCreate as $idx => $slot) {
            $dbConflictsQuery = LichThi::where('ngay_thi', $slot['ngay_thi'])
                ->whereRaw('LOWER(phong_thi) = ?', [mb_strtolower($slot['phong_thi'])]);

            if (! empty($ignoreLichThiIds)) {
                $dbConflictsQuery->whereNotIn('id', $ignoreLichThiIds);
            }

            $dbCaThis = $dbConflictsQuery->get();
            foreach ($dbCaThis as $dbCa) {
                try {
                    $dbStart = Carbon::parse($dbCa->ngay_thi->format('Y-m-d') . ' ' . $dbCa->gio_bat_dau);
                    $dbEnd = $dbStart->copy()->addMinutes($dbCa->thoi_gian_thi_phut);

                    if ($slot['start']->lt($dbEnd) && $slot['end']->gt($dbStart)) {
                        $errors["db_conflict_{$idx}"] = "Phòng thi không khả dụng: Phòng '{$slot['phong_thi']}' đã được gán cho ca thi '{$dbCa->ma_ca_thi}' ({$dbStart->format('H:i')} - {$dbEnd->format('H:i')}) thuộc kỳ thi '{$dbCa->ten_ky_thi}' vào ngày " . Carbon::parse($slot['ngay_thi'])->format('d/m/Y') . '. Vui lòng chọn phòng thi khác hoặc điều chỉnh thời gian ca thi.';
                    }
                } catch (\Throwable) {
                    // ignore format errors in existing data
                }
            }
        }

        return $errors;
    }
}

