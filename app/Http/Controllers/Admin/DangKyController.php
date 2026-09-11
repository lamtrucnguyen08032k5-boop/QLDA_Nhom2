<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\KetQuaDuyetMail;
use App\Models\DangKy;
use App\Models\LichSuXuLyHoSo;
use App\Models\LichThi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

// M4 - Đăng ký thi (Duyệt đăng ký dự thi Admin)
class DangKyController extends Controller
{
    // Hiển thị danh sách các lịch thi đã có sinh viên đăng ký
    public function danhSachLichThi(Request $request)
    {
        $q = LichThi::with('khoa')
            ->withCount([
                'dangKys as so_luong_dang_ky',
                'dangKys as so_ho_so_cho_duyet' => function ($query) {
                    $query->whereIn('trang_thai', ['cho_duyet', 'da_bo_sung']);
                },
            ]);

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $q->where(function ($sub) use ($keyword) {
                $sub->where('ten_ky_thi', 'like', $keyword)
                    ->orWhere('ma_ca_thi', 'like', $keyword)
                    ->orWhere('phong_thi', 'like', $keyword);
            });
        }

        if ($request->filled('trang_thai')) {
            $q->where('trang_thai', $request->trang_thai);
        }

        $lichThis = $q->orderByDesc('ngay_thi')->paginate(15)->withQueryString();

        return view('admin.dangky.danhsach', compact('lichThis'));
    }

    // Hiển thị danh sách sinh viên đăng ký của 1 lịch thi cụ thể
    public function index(Request $request, LichThi $lichthi)
    {
        $q = $lichthi->dangKys()->with(['sinhVien.khoa', 'nguoiDuyet', 'lichSuXuLy.user']);

        // Tìm kiếm theo Mã SV, Họ tên, Mã đăng ký, Lớp
        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $q->where(function ($query) use ($keyword) {
                $query->where('ma_dang_ky', 'like', $keyword)
                    ->orWhereHas('sinhVien', function ($sv) use ($keyword) {
                        $sv->where('name', 'like', $keyword)
                            ->orWhere('ma_so', 'like', $keyword)
                            ->orWhere('lop', 'like', $keyword);
                    });
            });
        }

        // Lọc theo trạng thái hồ sơ
        if ($request->filled('trang_thai')) {
            if ($request->trang_thai === 'cho_thanh_toan') {
                $q->where('trang_thai_thanh_toan', 'cho_thanh_toan')->where('trang_thai', '!=', 'da_huy');
            } elseif ($request->trang_thai === 'het_han_bo_sung') {
                $q->where('trang_thai', 'cho_bo_sung')->where('han_bo_sung', '<', now());
            } else {
                $q->where('trang_thai', $request->trang_thai);
            }
        }

        $dangKys = $q->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.dangky.index', compact('lichthi', 'dangKys'));
    }

    // Xem chi tiết một hồ sơ đăng ký (trả về JSON cho AJAX hoặc hỗ trợ mở chi tiết)
    public function show(LichThi $lichthi, DangKy $dangky)
    {
        $dangky->load(['sinhVien.khoa', 'lichThi', 'nguoiDuyet', 'lichSuXuLy.user']);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $dangky,
                'lich_su' => $dangky->lichSuXuLy->map(function ($ls) {
                    return [
                        'created_at' => $ls->created_at->format('d/m/Y H:i:s'),
                        'user_name' => $ls->user ? $ls->user->name : 'Hệ thống',
                        'vai_tro' => $ls->nhanVaiTroLabel(),
                        'hanh_dong' => $ls->nhanHanhDongLabel(),
                        'trang_thai' => $ls->trang_thai_sau,
                        'noi_dung' => $ls->noi_dung,
                    ];
                }),
            ]);
        }

        return view('admin.dangky.show', compact('lichthi', 'dangky'));
    }

    // Trường hợp 1: Duyệt hồ sơ
    public function approve(Request $request, LichThi $lichthi, DangKy $dangky)
    {
        if ($dangky->trang_thai_thanh_toan !== 'da_thanh_toan') {
            return back()->withErrors(['dangky' => 'Thí sinh chưa thanh toán lệ phí thi. Không thể duyệt hồ sơ khi chưa hoàn tất thanh toán.']);
        }

        if ($lichthi->dangKysDaDuyet()->where('id', '!=', $dangky->id)->count() >= $lichthi->so_luong_toi_da) {
            return back()->withErrors(['dangky' => 'Ca thi đã đủ số lượng tối đa (' . $lichthi->so_luong_toi_da . ' thí sinh), không thể duyệt thêm.']);
        }

        $trangThaiTruoc = $dangky->trang_thai;
        $dangky->update([
            'trang_thai' => 'da_duyet',
            'ngay_duyet' => now(),
            'nguoi_duyet_id' => Auth::id(),
        ]);

        // Ghi nhật ký xử lý hồ sơ
        LichSuXuLyHoSo::create([
            'dang_ky_id' => $dangky->id,
            'user_id' => Auth::id(),
            'vai_tro' => 'admin',
            'hanh_dong' => 'duyet',
            'trang_thai_truoc' => $trangThaiTruoc,
            'trang_thai_sau' => 'da_duyet',
            'noi_dung' => 'Duyệt hồ sơ đăng ký dự thi thành công. Sinh viên được đưa vào danh sách dự thi chính thức.',
        ]);

        // Gửi email thông báo cho sinh viên
        try {
            Mail::to($dangky->sinhVien->email)
                ->cc($dangky->email_lien_he && $dangky->email_lien_he !== $dangky->sinhVien->email ? [$dangky->email_lien_he] : [])
                ->send(new KetQuaDuyetMail($dangky));
        } catch (\Throwable $e) {
            // Không làm gián đoạn nếu mail chưa cấu hình
        }

        return back()->with('status', 'Đã duyệt hồ sơ đăng ký của sinh viên ' . $dangky->sinhVien->name . ' (' . $dangky->ma_dang_ky . ').');
    }

    // Trường hợp 2: Yêu cầu bổ sung hồ sơ
    public function yeuCauBoSung(Request $request, LichThi $lichthi, DangKy $dangky)
    {
        if ($dangky->trang_thai_thanh_toan !== 'da_thanh_toan') {
            return back()->withErrors(['dangky' => 'Thí sinh chưa thanh toán lệ phí thi. Không thể yêu cầu bổ sung hồ sơ khi chưa hoàn tất thanh toán.']);
        }

        $messages = [
            'truong_can_bo_sung.required' => 'Vui lòng chọn ít nhất một trường cần bổ sung.',
            'truong_can_bo_sung.min' => 'Vui lòng chọn ít nhất một trường cần bổ sung.',
            'han_bo_sung.required' => 'Vui lòng chọn thời hạn bổ sung.',
            'han_bo_sung.after' => 'Thời hạn bổ sung không hợp lệ (thời hạn phải sau thời điểm hiện tại).',
            'han_bo_sung.before_or_equal' => 'Thời hạn bổ sung không hợp lệ (không được vượt quá ngày thi ' . $lichthi->ngay_thi->format('d/m/Y') . ').',
        ];

        $data = $request->validate([
            'truong_can_bo_sung' => ['required', 'array', 'min:1'],
            'truong_can_bo_sung.*' => ['string'],
            'ly_do_tung_truong' => ['nullable', 'array'],
            'ly_do_tung_truong.*' => ['nullable', 'string', 'max:500'],
            'han_bo_sung' => [
                'required',
                'date',
                'after:now',
                'before_or_equal:' . $lichthi->ngay_thi->copy()->endOfDay()->toDateTimeString(),
            ],
        ], $messages);

        $tenTruongMap = [
            'so_dien_thoai' => 'Số điện thoại',
            'ngay_sinh' => 'Ngày sinh',
            'gioi_tinh' => 'Giới tính',
            'dan_toc' => 'Dân tộc',
            'noi_sinh' => 'Nơi sinh',
            'so_cccd' => 'Số CCCD',
            'anh_cccd_truoc' => 'Ảnh CCCD mặt trước',
            'anh_cccd_sau' => 'Ảnh CCCD mặt sau',
            'anh_ho_so' => 'Ảnh hồ sơ dự thi 3x4',
            'anh_the_sv' => 'Ảnh thẻ sinh viên',
            'tinh_thanh_pho' => 'Tỉnh/Thành phố',
            'xa_phuong' => 'Xã/Phường',
            'dia_chi_chi_tiet' => 'Địa chỉ chi tiết',
            'email_lien_he' => 'Email liên hệ',
        ];

        // Kiểm tra lý do từng trường đã chọn là bắt buộc
        $lyDoTungTruong = [];
        $doanhSachLyDo = [];
        foreach ($data['truong_can_bo_sung'] as $key) {
            $lyDo = trim($data['ly_do_tung_truong'][$key] ?? '');
            $tenTruong = $tenTruongMap[$key] ?? $key;
            if (empty($lyDo)) {
                return back()->withErrors(['truong_can_bo_sung' => "Vui lòng nhập lý do yêu cầu bổ sung cho trường '{$tenTruong}'."])->withInput();
            }
            $lyDoTungTruong[$key] = $lyDo;
            $doanhSachLyDo[] = "- {$tenTruong}: {$lyDo}";
        }

        // Tổng hợp ly_do_bo_sung gửi email và lưu lịch sử
        $lyDoBoSungTongHop = "Yêu cầu sinh viên bổ sung/chỉnh sửa các thông tin sau:\n" . implode("\n", $doanhSachLyDo);

        $trangThaiTruoc = $dangky->trang_thai;
        $dangky->update([
            'trang_thai' => 'cho_bo_sung',
            'truong_can_bo_sung' => $data['truong_can_bo_sung'],
            'ly_do_tung_truong' => $lyDoTungTruong,
            'ly_do_bo_sung' => $lyDoBoSungTongHop,
            'han_bo_sung' => $data['han_bo_sung'],
            'nguoi_duyet_id' => Auth::id(),
        ]);

        // Ghi nhật ký xử lý hồ sơ
        $dsTenTruong = array_map(fn($k) => $tenTruongMap[$k] ?? $k, $data['truong_can_bo_sung']);
        LichSuXuLyHoSo::create([
            'dang_ky_id' => $dangky->id,
            'user_id' => Auth::id(),
            'vai_tro' => 'admin',
            'hanh_dong' => 'yeu_cau_bo_sung',
            'trang_thai_truoc' => $trangThaiTruoc,
            'trang_thai_sau' => 'cho_bo_sung',
            'noi_dung' => 'Yêu cầu bổ sung các trường: ' . implode(', ', $dsTenTruong) . '. Hạn bổ sung trực tuyến: ' . date('d/m/Y H:i', strtotime($data['han_bo_sung'])),
        ]);

        // Gửi email thông báo cho sinh viên
        try {
            Mail::to($dangky->sinhVien->email)
                ->cc($dangky->email_lien_he && $dangky->email_lien_he !== $dangky->sinhVien->email ? [$dangky->email_lien_he] : [])
                ->send(new KetQuaDuyetMail($dangky));
        } catch (\Throwable $e) {
            // Ignored if mail server not running
        }

        return back()->with('status', 'Đã gửi yêu cầu bổ sung hồ sơ cho sinh viên ' . $dangky->sinhVien->name . '.');
    }

    // Trường hợp 3: Từ chối đăng ký
    public function reject(Request $request, LichThi $lichthi, DangKy $dangky)
    {
        if ($dangky->trang_thai_thanh_toan !== 'da_thanh_toan') {
            return back()->withErrors(['dangky' => 'Thí sinh chưa thanh toán lệ phí thi. Không thể từ chối hồ sơ khi chưa hoàn tất thanh toán.']);
        }

        $messages = [
            'ly_do_tu_choi.required' => 'Vui lòng nhập lý do xử lý hồ sơ.',
        ];

        $data = $request->validate([
            'ly_do_tu_choi' => ['required', 'string'],
        ], $messages);

        $trangThaiTruoc = $dangky->trang_thai;
        $dangky->update([
            'trang_thai' => 'tu_choi',
            'ly_do_tu_choi' => $data['ly_do_tu_choi'],
            'ngay_duyet' => now(),
            'nguoi_duyet_id' => Auth::id(),
        ]);

        // Ghi nhật ký xử lý hồ sơ
        LichSuXuLyHoSo::create([
            'dang_ky_id' => $dangky->id,
            'user_id' => Auth::id(),
            'vai_tro' => 'admin',
            'hanh_dong' => 'tu_choi',
            'trang_thai_truoc' => $trangThaiTruoc,
            'trang_thai_sau' => 'tu_choi',
            'noi_dung' => 'Từ chối hồ sơ đăng ký dự thi. Lý do từ chối: ' . $data['ly_do_tu_choi'],
        ]);

        // Gửi email thông báo cho sinh viên
        try {
            Mail::to($dangky->sinhVien->email)
                ->cc($dangky->email_lien_he && $dangky->email_lien_he !== $dangky->sinhVien->email ? [$dangky->email_lien_he] : [])
                ->send(new KetQuaDuyetMail($dangky));
        } catch (\Throwable $e) {
            // Ignored
        }

        return back()->with('status', 'Đã từ chối hồ sơ đăng ký của sinh viên ' . $dangky->sinhVien->name . '.');
    }

    // Trường hợp 4: Admin hỗ trợ bổ sung hồ sơ cho sinh viên khi hồ sơ đã hết thời hạn bổ sung
    public function hoTroBoSungQuaHan(Request $request, LichThi $lichthi, DangKy $dangky)
    {
        // 1. Kiểm tra điều kiện hồ sơ phải đang ở trạng thái 'cho_bo_sung' và đã hết hạn trực tuyến
        if ($dangky->trang_thai !== 'cho_bo_sung' || ! $dangky->isHetHanBoSungOnline()) {
            return back()->withErrors(['dangky' => 'Chức năng này chỉ áp dụng cho hồ sơ có trạng thái Yêu cầu bổ sung và đã hết thời hạn bổ sung trực tuyến.']);
        }

        // 2. Validate dữ liệu
        $messages = [
            'ly_do_bo_sung_qua_han.required' => 'Vui lòng nhập lý do bổ sung sau thời hạn.',
            'so_dien_thoai.digits' => 'Số điện thoại phải bao gồm đúng 10 chữ số.',
            'ngay_sinh.before_or_equal' => 'Thí sinh phải từ đủ 18 tuổi trở lên mới được đăng ký dự thi.',
            'so_cccd.digits' => 'Số CCCD phải bao gồm đúng 12 chữ số.',
            'email_lien_he.email' => 'Email liên hệ không đúng định dạng.',
            'anh_cccd_truoc.image' => 'Ảnh CCCD mặt trước phải là file ảnh (jpg, png...).',
            'anh_cccd_sau.image' => 'Ảnh CCCD mặt sau phải là file ảnh (jpg, png...).',
            'anh_ho_so.image' => 'Ảnh hồ sơ 3x4 phải là file ảnh (jpg, png...).',
            'anh_the_sv.image' => 'Ảnh thẻ sinh viên phải là file ảnh (jpg, png...).',
            'anh_cccd_truoc.max' => 'Dung lượng ảnh CCCD mặt trước tối đa 2MB.',
            'anh_cccd_sau.max' => 'Dung lượng ảnh CCCD mặt sau tối đa 2MB.',
            'anh_ho_so.max' => 'Dung lượng ảnh hồ sơ tối đa 2MB.',
            'anh_the_sv.max' => 'Dung lượng ảnh thẻ SV tối đa 2MB.',
        ];

        $data = $request->validate([
            'ly_do_bo_sung_qua_han' => ['required', 'string', 'max:1000'],
            'ghi_chu_can_bo' => ['nullable', 'string', 'max:1000'],

            'so_dien_thoai' => ['nullable', 'digits:10'],
            'ngay_sinh' => ['nullable', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
            'gioi_tinh' => ['nullable', 'in:nam,nu,khac'],
            'dan_toc' => ['nullable', 'string', 'max:100'],
            'noi_sinh' => ['nullable', 'string', 'max:255'],
            'so_cccd' => ['nullable', 'digits:12'],
            'tinh_thanh_pho' => ['nullable', 'string'],
            'xa_phuong' => ['nullable', 'string'],
            'dia_chi_chi_tiet' => ['nullable', 'string', 'max:255'],
            'email_lien_he' => ['nullable', 'email', 'max:255'],

            'anh_cccd_truoc' => ['nullable', 'image', 'max:2048'],
            'anh_cccd_sau' => ['nullable', 'image', 'max:2048'],
            'anh_ho_so' => ['nullable', 'image', 'max:2048'],
            'anh_the_sv' => ['nullable', 'image', 'max:2048'],
        ], $messages);

        $updateData = [];
        $thayDoiList = [];

        // Tra tên tỉnh/thành & xã/phường nếu được chọn
        if (! empty($data['tinh_thanh_pho']) && ! empty($data['xa_phuong'])) {
            [$tenTinh, $tenXa] = $this->layTenTinhXa($data['tinh_thanh_pho'], $data['xa_phuong']);
            if ($tenTinh && $tenXa) {
                if ($dangky->tinh_thanh_pho_code !== $data['tinh_thanh_pho'] || $dangky->xa_phuong_code !== $data['xa_phuong']) {
                    $thayDoiList[] = "- Địa chỉ hành chính: {$dangky->xa_phuong_ten}, {$dangky->tinh_thanh_pho_ten} ➔ {$tenXa}, {$tenTinh}";
                }
                $updateData['tinh_thanh_pho_code'] = $data['tinh_thanh_pho'];
                $updateData['tinh_thanh_pho_ten'] = $tenTinh;
                $updateData['xa_phuong_code'] = $data['xa_phuong'];
                $updateData['xa_phuong_ten'] = $tenXa;
            }
        }

        $fieldsMap = [
            'so_dien_thoai' => 'Số điện thoại',
            'ngay_sinh' => 'Ngày sinh',
            'gioi_tinh' => 'Giới tính',
            'dan_toc' => 'Dân tộc',
            'noi_sinh' => 'Nơi sinh',
            'so_cccd' => 'Số CCCD',
            'dia_chi_chi_tiet' => 'Địa chỉ chi tiết',
            'email_lien_he' => 'Email liên hệ',
        ];

        foreach ($fieldsMap as $field => $label) {
            if (isset($data[$field]) && $data[$field] !== null) {
                $oldVal = $field === 'ngay_sinh' ? optional($dangky->ngay_sinh)->format('d/m/Y') : $dangky->$field;
                $newVal = $field === 'ngay_sinh' ? date('d/m/Y', strtotime($data[$field])) : $data[$field];
                if ((string)$oldVal !== (string)$newVal) {
                    $thayDoiList[] = "- {$label}: " . ($oldVal ?: '(Trống)') . " ➔ {$newVal}";
                    $updateData[$field] = $data[$field];
                }
            }
        }

        // Xử lý tệp/hình ảnh minh chứng
        $imgMap = [
            'anh_cccd_truoc' => 'Ảnh CCCD mặt trước',
            'anh_cccd_sau' => 'Ảnh CCCD mặt sau',
            'anh_ho_so' => 'Ảnh hồ sơ 3x4',
            'anh_the_sv' => 'Ảnh thẻ sinh viên',
        ];

        foreach ($imgMap as $imgField => $imgLabel) {
            if ($request->hasFile($imgField) && $request->file($imgField)->isValid()) {
                $path = $request->file($imgField)->store('hoso/' . $dangky->sinh_vien_id, 'public');
                $updateData[$imgField] = $path;
                $thayDoiList[] = "- {$imgLabel}: Đã cập nhật/thay thế tệp minh chứng mới";
            }
        }

        $trangThaiTruoc = $dangky->trang_thai;
        // Cán bộ đã trực tiếp kiểm tra & hoàn thiện hồ sơ thay sinh viên nên hồ sơ được duyệt luôn,
        // đồng nhất trạng thái 'da_duyet' (Đã duyệt) giống như khi Admin bấm Duyệt hồ sơ bình thường.
        $updateData['trang_thai'] = 'da_duyet';
        $updateData['ngay_bo_sung'] = now();
        $updateData['ngay_duyet'] = now();
        $updateData['nguoi_duyet_id'] = Auth::id();

        $dangky->update($updateData);

        // Tổng hợp nội dung ghi nhật ký xử lý hồ sơ
        $noiDungLog = "Cán bộ Phòng Khảo thí đã hỗ trợ sinh viên bổ sung hồ sơ sau thời hạn và duyệt hồ sơ.\n";
        $noiDungLog .= "📌 Lý do bổ sung sau thời hạn: " . $data['ly_do_bo_sung_qua_han'] . "\n";
        if (! empty($data['ghi_chu_can_bo'])) {
            $noiDungLog .= "📝 Ghi chú cán bộ: " . $data['ghi_chu_can_bo'] . "\n";
        }
        if (! empty($thayDoiList)) {
            $noiDungLog .= "📋 Danh sách các mục đã cập nhật:\n" . implode("\n", $thayDoiList);
        } else {
            $noiDungLog .= "📋 Cán bộ đã kiểm tra và giữ nguyên các thông tin hồ sơ hiện tại.";
        }

        // Ghi nhật ký xử lý hồ sơ (Audit Log)
        LichSuXuLyHoSo::create([
            'dang_ky_id' => $dangky->id,
            'user_id' => Auth::id(),
            'vai_tro' => 'admin',
            'hanh_dong' => 'bo_sung_ho_so_qua_han',
            'trang_thai_truoc' => $trangThaiTruoc,
            'trang_thai_sau' => 'da_duyet',
            'noi_dung' => $noiDungLog,
        ]);

        // Gửi email thông báo kết quả duyệt cho sinh viên, đồng nhất với luồng Duyệt hồ sơ thông thường
        try {
            Mail::to($dangky->sinhVien->email)
                ->cc($dangky->email_lien_he && $dangky->email_lien_he !== $dangky->sinhVien->email ? [$dangky->email_lien_he] : [])
                ->send(new KetQuaDuyetMail($dangky));
        } catch (\Throwable $e) {
            // Không làm gián đoạn nếu mail chưa cấu hình
        }

        return back()->with('status', 'Bổ sung hồ sơ thành công. Hồ sơ đã được duyệt.');
    }

    private function layTenTinhXa(?string $maTinh, ?string $maXa): array
    {
        static $ds = null;
        if ($ds === null) {
            $path = public_path('data/vn-address.json');
            $ds = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
        }

        foreach ($ds as $tinh) {
            if ($tinh['c'] === $maTinh) {
                foreach ($tinh['w'] as $xa) {
                    if ($xa['c'] === $maXa) {
                        return [$tinh['n'], $xa['n']];
                    }
                }
                return [$tinh['n'], null];
            }
        }
        return [null, null];
    }
}
