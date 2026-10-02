<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Khoa;
use App\Models\SvWhitelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// "Kho mail HVNH" - Admin quản lý danh sách sinh viên hợp lệ được phép đăng ký tài khoản.
class SvWhitelistController extends Controller
{
    public function index(Request $request)
    {
        $khoas = Khoa::orderBy('ten_khoa')->get();

        $q = SvWhitelist::with('khoa');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $q->where(function ($qq) use ($s) {
                $qq->where('ma_sv', 'like', "%{$s}%")
                    ->orWhere('ho_ten', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('lop', 'like', "%{$s}%")
                    ->orWhere('khoa_hoc', 'like', "%{$s}%");
            });
        }

        if ($request->filled('khoa_id')) {
            $q->where('khoa_id', $request->khoa_id);
        }

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 20;
        }

        $sinhViens = $q->orderBy('ma_sv')->paginate($perPage)->withQueryString();

        return view('admin.giangvien.sv-whitelist', compact('sinhViens', 'khoas'));
    }

    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mau_import_sinh_vien.csv"',
        ];

        // File mẫu trống gồm dòng tiêu đề rõ ràng
        $content = "\xEF\xBB\xBF" . "Mã SV,Họ tên,Email,Lớp niên chế,Khóa học,Mã khoa\n";

        return response($content, 200, $headers);
    }

    public function storeSingle(Request $request)
    {
        $data = $request->validate([
            'ma_sv' => 'required|string|max:50|unique:sv_whitelists,ma_sv',
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:sv_whitelists,email',
            'khoa_id' => 'required|exists:khoas,id',
            'lop' => 'nullable|string|max:100',
            'khoa_hoc' => 'nullable|string|max:50',
        ], [
            'ma_sv.required' => 'Vui lòng nhập mã sinh viên.',
            'ma_sv.unique' => 'Mã sinh viên này đã tồn tại trong danh sách.',
            'ho_ten.required' => 'Vui lòng nhập họ tên sinh viên.',
            'email.required' => 'Vui lòng nhập email trường.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại trong danh sách.',
            'khoa_id.required' => 'Vui lòng chọn Khoa quản lý sinh viên.',
            'khoa_id.exists' => 'Khoa được chọn không hợp lệ.',
        ]);

        SvWhitelist::create($data);

        return back()->with('status', 'Đã thêm sinh viên vào danh sách thành công.');
    }

    public function update(Request $request, SvWhitelist $sv)
    {
        $data = $request->validate([
            'ma_sv' => 'required|string|max:50|unique:sv_whitelists,ma_sv,' . $sv->id,
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:sv_whitelists,email,' . $sv->id,
            'khoa_id' => 'required|exists:khoas,id',
            'lop' => 'nullable|string|max:100',
            'khoa_hoc' => 'nullable|string|max:50',
        ], [
            'ma_sv.required' => 'Vui lòng nhập mã sinh viên.',
            'ma_sv.unique' => 'Mã sinh viên này đã tồn tại trong danh sách.',
            'ho_ten.required' => 'Vui lòng nhập họ tên sinh viên.',
            'email.required' => 'Vui lòng nhập email trường.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại trong danh sách.',
            'khoa_id.required' => 'Vui lòng chọn Khoa quản lý sinh viên.',
            'khoa_id.exists' => 'Khoa được chọn không hợp lệ.',
        ]);

        $sv->update($data);

        // Đồng bộ cập nhật thông tin User tương ứng nếu sinh viên đã tạo tài khoản
        $user = \App\Models\User::where('email', $sv->email)->orWhere('ma_so', $sv->ma_sv)->first();
        if ($user) {
            $user->update([
                'ma_so' => $data['ma_sv'],
                'name' => $data['ho_ten'],
                'email' => $data['email'],
                'khoa_id' => $data['khoa_id'],
                'lop' => $data['lop'] ?? null,
                'khoa_hoc' => $data['khoa_hoc'] ?? null,
            ]);
        }

        return back()->with('status', 'Cập nhật thông tin sinh viên thành công.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ], [
            'file.required' => 'Vui lòng chọn file CSV để import.',
            'file.mimes' => 'File phải có định dạng CSV hoặc TXT.',
        ]);

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

        // Cache danh sách Khoa theo ma_khoa và ten_khoa để tra cứu nhanh
        $khoaMap = Khoa::all()->keyBy(fn($k) => strtolower(trim($k->ma_khoa)));
        $khoaTenMap = Khoa::all()->keyBy(fn($k) => strtolower(trim($k->ten_khoa)));

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                if (empty(array_filter($row))) {
                    continue;
                }

                $maSv = trim($row[0] ?? '');
                $hoTen = trim($row[1] ?? '');
                $email = strtolower(trim($row[2] ?? ''));
                $lop = trim($row[3] ?? '') ?: null;
                $khoaHoc = trim($row[4] ?? '') ?: null;
                $maOrTenKhoa = strtolower(trim($row[5] ?? ''));

                if (empty($maSv) || empty($hoTen) || empty($email)) {
                    $errors[] = "Dòng {$line}: Thiếu thông tin bắt buộc (Mã SV, Họ tên hoặc Email).";
                    continue;
                }

                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Dòng {$line}: Email '{$email}' không đúng định dạng.";
                    continue;
                }

                // Tìm khoa_id nếu có nhập mã khoa hoặc tên khoa
                $khoaId = null;
                if (!empty($maOrTenKhoa)) {
                    if (isset($khoaMap[$maOrTenKhoa])) {
                        $khoaId = $khoaMap[$maOrTenKhoa]->id;
                    } elseif (isset($khoaTenMap[$maOrTenKhoa])) {
                        $khoaId = $khoaTenMap[$maOrTenKhoa]->id;
                    }
                }

                SvWhitelist::updateOrCreate(
                    ['ma_sv' => $maSv],
                    [
                        'ho_ten' => $hoTen,
                        'email' => $email,
                        'lop' => $lop,
                        'khoa_hoc' => $khoaHoc,
                        'khoa_id' => $khoaId,
                    ]
                );

                $count++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            return back()->withErrors(['file' => 'Lỗi khi import danh sách sinh viên: ' . $e->getMessage()]);
        }
        fclose($handle);

        $msg = "Import thành công {$count} sinh viên vào kho email hợp lệ.";
        if (! empty($errors)) {
            return back()->with('status', $msg)->withErrors($errors);
        }

        return back()->with('status', $msg);
    }

    public function destroy(SvWhitelist $sv)
    {
        $sv->delete();
        return back()->with('status', 'Đã xoá sinh viên khỏi danh sách.');
    }
}
