<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Khoa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

// M1: Quản lý Khoa và tạo tài khoản Khoa/Giảng viên (Use case "Tạo tài khoản")
class KhoaController extends Controller
{
    // Bước 1-2: Admin truy cập chức năng Quản lý Khoa -> hiển thị danh sách Khoa
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 20;
        }

        $khoas = Khoa::withCount('giangViens')
            ->orderBy('ten_khoa')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.khoa.index', compact('khoas'));
    }

    public function downloadKhoaSample()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mau_import_khoa.csv"',
        ];

        // File mẫu trống chỉ gồm dòng tiêu đề rõ ràng
        $content = "\xEF\xBB\xBF" . "Mã khoa,Tên khoa,Email,Mô tả\n";

        return response($content, 200, $headers);
    }

    public function importKhoa(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'password' => 'required|string|min:6',
        ], [
            'file.required' => 'Vui lòng chọn file CSV để import.',
            'file.mimes' => 'File phải có định dạng CSV hoặc TXT.',
            'password.required' => 'Vui lòng nhập mật khẩu áp dụng cho các tài khoản import.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $defaultPassword = $request->input('password');

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        
        // Bỏ qua BOM nếu có
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle);
        $count = 0;
        $errors = [];
        $line = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                if (empty(array_filter($row))) {
                    continue;
                }

                $maKhoa = trim($row[0] ?? '');
                $tenKhoa = trim($row[1] ?? '');
                $email = strtolower(trim($row[2] ?? ''));
                $moTa = trim($row[3] ?? '');

                if (empty($maKhoa) || empty($tenKhoa) || empty($email)) {
                    $errors[] = "Dòng {$line}: Thiếu thông tin bắt buộc (Mã khoa, Tên khoa hoặc Email).";
                    continue;
                }

                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Dòng {$line}: Email '{$email}' không đúng định dạng.";
                    continue;
                }

                $khoa = Khoa::updateOrCreate(
                    ['ma_khoa' => $maKhoa],
                    [
                        'ten_khoa' => $tenKhoa,
                        'email' => $email,
                        'mo_ta' => $moTa ?: null,
                        'active' => true,
                    ]
                );

                // Tạo hoặc cập nhật tài khoản Khoa tương ứng với mật khẩu từ form popup
                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'role' => 'khoa',
                        'ma_so' => $maKhoa,
                        'name' => 'Tài khoản Khoa ' . $tenKhoa,
                        'password' => Hash::make($defaultPassword),
                        'khoa_id' => $khoa->id,
                        'active' => true,
                        'email_verified_at' => now(),
                    ]
                );

                $count++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            return back()->withErrors(['file' => 'Lỗi khi import Khoa: ' . $e->getMessage()]);
        }
        fclose($handle);

        $msg = "Import thành công {$count} Khoa và tạo tài khoản tương ứng.";
        if (! empty($errors)) {
            return back()->with('status', $msg)->withErrors($errors);
        }

        return back()->with('status', $msg);
    }

    // Bước 3-4: Admin chọn Thêm mới Khoa -> hiển thị form thêm Khoa
    public function create()
    {
        return view('admin.khoa.create');
    }

    // Bước 5-9: Nhập thông tin Khoa -> Lưu -> Kiểm tra hợp lệ -> Lưu Khoa & tạo tài khoản Khoa
    public function store(Request $request)
    {
        // Luồng phụ 1: Thông tin Khoa không hợp lệ & Luồng phụ 2: Khoa đã tồn tại
        $data = $request->validate([
            'ma_khoa' => 'required|string|max:50|unique:khoas,ma_khoa',
            'ten_khoa' => 'required|string|max:255',
            'email' => 'required|email|unique:khoas,email|unique:users,email',
            'password' => 'required|string|min:6',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_khoa.required' => 'Vui lòng nhập mã khoa.',
            'ma_khoa.unique' => 'Mã khoa đã tồn tại trên hệ thống.',
            'ten_khoa.required' => 'Vui lòng nhập tên khoa.',
            'email.required' => 'Vui lòng nhập email khoa.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng trên hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        DB::transaction(function () use ($data) {
            $khoa = Khoa::create([
                'ma_khoa' => $data['ma_khoa'],
                'ten_khoa' => $data['ten_khoa'],
                'email' => $data['email'],
                'mo_ta' => $data['mo_ta'] ?? null,
                'active' => true,
            ]);

            // Bước 8: Hệ thống lưu thông tin Khoa, tạo tài khoản Khoa và gán vai trò Khoa
            User::create([
                'role' => 'khoa',
                'ma_so' => $data['ma_khoa'],
                'name' => 'Tài khoản Khoa ' . $data['ten_khoa'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'khoa_id' => $khoa->id,
                'email_verified_at' => now(),
            ]);

            Mail::raw("Tài khoản Khoa {$data['ten_khoa']} đã được tạo thành công.\nEmail: {$data['email']}\nMật khẩu: {$data['password']}", function ($m) use ($data) {
                $m->to($data['email'])->subject('Tài khoản Khoa - Hệ thống thi chứng chỉ HVNH');
            });
        });

        // Bước 9: Hệ thống thông báo “Thêm Khoa thành công” và hiển thị Khoa trong danh sách
        return redirect()->route('admin.khoa.index')->with('status', 'Thêm Khoa thành công.');
    }

    public function show(Khoa $khoa)
    {
        return redirect()->route('admin.khoa.edit', $khoa);
    }

    public function edit(Request $request, Khoa $khoa)
    {
        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 20;
        }

        $giangViens = $khoa->giangViens()
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.khoa.edit', compact('khoa', 'giangViens'));
    }

    // Luồng phụ 1: Thông tin Khoa không hợp lệ
    public function update(Request $request, Khoa $khoa)
    {
        $data = $request->validate([
            'ma_khoa' => 'required|string|max:50|unique:khoas,ma_khoa,' . $khoa->id,
            'ten_khoa' => 'required|string|max:255',
            'email' => 'required|email|unique:khoas,email,' . $khoa->id,
            'mo_ta' => 'nullable|string',
            'active' => 'nullable|boolean',
        ], [
            'ma_khoa.required' => 'Vui lòng nhập mã khoa.',
            'ma_khoa.unique' => 'Mã khoa đã tồn tại trên hệ thống.',
            'ten_khoa.required' => 'Vui lòng nhập tên khoa.',
            'email.required' => 'Vui lòng nhập email khoa.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng trên hệ thống.',
        ]);

        $data['active'] = $request->boolean('active');

        DB::transaction(function () use ($khoa, $data) {
            $khoa->update($data);

            // Đồng bộ tài khoản đại diện Khoa
            $khoa->taiKhoanKhoa()->update([
                'ma_so' => $data['ma_khoa'],
                'name' => 'Tài khoản Khoa ' . $data['ten_khoa'],
                'email' => $data['email'],
                'active' => $data['active'],
            ]);
        });

        return redirect()->route('admin.khoa.edit', $khoa)->with('status', 'Cập nhật thông tin Khoa thành công.');
    }

    public function destroy(Khoa $khoa)
    {
        if ($khoa->lichThis()->exists() || $khoa->deThis()->exists()) {
            return back()->withErrors(['error' => "Không thể xóa khoa '{$khoa->ten_khoa}' vì đã có dữ liệu lịch thi hoặc đề thi liên kết."]);
        }

        $tenKhoa = $khoa->ten_khoa;

        DB::transaction(function () use ($khoa) {
            $khoa->giangViens()->delete();
            $khoa->taiKhoanKhoa()->delete();
            $khoa->delete();
        });

        return redirect()->route('admin.khoa.index')->with('status', "Đã xóa Khoa {$tenKhoa} thành công.");
    }

    // Bước 12-18: Thêm Giảng viên vào Khoa
    // Bước 12-18: Thêm Giảng viên vào Khoa
    public function storeGiangVien(Request $request, Khoa $khoa)
    {
        // Luồng phụ 3: Thông tin Giảng viên không hợp lệ & Luồng phụ 4: Giảng viên đã tồn tại
        $data = $request->validate([
            'ma_giang_vien' => 'required|string|max:50|unique:users,ma_so',
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ], [
            'ma_giang_vien.required' => 'Vui lòng nhập mã giảng viên.',
            'ma_giang_vien.unique' => 'Giảng viên đã tồn tại (trùng mã giảng viên).',
            'ho_ten.required' => 'Vui lòng nhập họ tên giảng viên.',
            'email.required' => 'Vui lòng nhập email giảng viên.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Giảng viên đã tồn tại (trùng email).',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        // Bước 17: Hệ thống lưu Giảng viên vào Khoa tương ứng, tạo tài khoản và gán vai trò Giảng viên
        $gv = User::create([
            'role' => 'giangvien',
            'ma_so' => $data['ma_giang_vien'],
            'name' => $data['ho_ten'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'khoa_id' => $khoa->id,
            'email_verified_at' => now(),
        ]);

        Mail::raw("Tài khoản Giảng viên {$data['ho_ten']} đã được tạo thành công.\nEmail: {$data['email']}\nMật khẩu: {$data['password']}", function ($m) use ($data) {
            $m->to($data['email'])->subject('Tài khoản Giảng viên - Hệ thống thi chứng chỉ HVNH');
        });

        // Bước 18: Hệ thống thông báo “Thêm Giảng viên thành công” và cập nhật danh sách Giảng viên của Khoa
        return back()->with('status', 'Thêm Giảng viên thành công.');
    }

    public function downloadGiangVienSample(Khoa $khoa)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mau_import_giang_vien_' . strtolower($khoa->ma_khoa) . '.csv"',
        ];

        // File mẫu trống chỉ gồm dòng tiêu đề rõ ràng
        $content = "\xEF\xBB\xBF" . "Mã giảng viên,Họ tên,Email\n";

        return response($content, 200, $headers);
    }

    public function importGiangVien(Request $request, Khoa $khoa)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'password' => 'required|string|min:6',
        ], [
            'file.required' => 'Vui lòng chọn file CSV để import.',
            'file.mimes' => 'File phải có định dạng CSV hoặc TXT.',
            'password.required' => 'Vui lòng nhập mật khẩu áp dụng cho các tài khoản import.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $defaultPassword = $request->input('password');

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        
        // Bỏ qua BOM nếu có
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle);
        $count = 0;
        $errors = [];
        $line = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                if (empty(array_filter($row))) {
                    continue;
                }

                $maGv = trim($row[0] ?? '');
                $hoTen = trim($row[1] ?? '');
                $email = strtolower(trim($row[2] ?? ''));

                if (empty($maGv) || empty($hoTen) || empty($email)) {
                    $errors[] = "Dòng {$line}: Thiếu thông tin bắt buộc (Mã GV, Họ tên hoặc Email).";
                    continue;
                }

                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Dòng {$line}: Email '{$email}' không đúng định dạng.";
                    continue;
                }

                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'role' => 'giangvien',
                        'ma_so' => $maGv,
                        'name' => $hoTen,
                        'password' => Hash::make($defaultPassword),
                        'khoa_id' => $khoa->id,
                        'active' => true,
                        'email_verified_at' => now(),
                    ]
                );

                $count++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            return back()->withErrors(['file' => 'Lỗi khi import Giảng viên: ' . $e->getMessage()]);
        }
        fclose($handle);

        $msg = "Import thành công {$count} Giảng viên vào {$khoa->ten_khoa}.";
        if (! empty($errors)) {
            return back()->with('status', $msg)->withErrors($errors);
        }

        return back()->with('status', $msg);
    }

    public function updateGiangVien(Request $request, Khoa $khoa, User $giangvien)
    {
        $data = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $giangvien->id,
            'password' => 'nullable|string|min:6',
            'active' => 'nullable|boolean',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
        ]);

        $updateData = [
            'name' => $data['ho_ten'],
            'email' => $data['email'],
            'active' => $request->boolean('active'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $giangvien->update($updateData);

        return back()->with('status', 'Cập nhật Giảng viên thành công.');
    }

    public function destroyGiangVien(Khoa $khoa, User $giangvien)
    {
        $giangvien->delete();
        return back()->with('status', 'Đã xoá Giảng viên.');
    }
}
