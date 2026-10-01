<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CauHoi;
use App\Models\DeThi;
use App\Models\Khoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

// M3 - Kho đề thi (UC3.1 Nhập & xử lý đề thi, UC3.2 Quản lý kho đề)
class DeThiController extends Controller
{
    public function index(Request $request)
    {
        $q = DeThi::with('khoa')->withCount('cauHois');

        if ($request->filled('tu_khoa')) {
            $kw = trim($request->tu_khoa);
            $q->where(function ($query) use ($kw) {
                $query->where('ma_de', 'like', "%{$kw}%")
                    ->orWhere('ten_de', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('khoa_id')) {
            $q->where('khoa_id', $request->integer('khoa_id'));
        }

        if ($request->filled('loai_chung_chi')) {
            $q->where('loai_chung_chi', $request->loai_chung_chi);
        }

        if ($request->filled('active')) {
            $q->where('active', $request->boolean('active'));
        }

        $deThis = $q->orderByDesc('id')->paginate(15)->withQueryString();
        $khoas = Khoa::where('active', true)->orderBy('ten_khoa')->get();

        return view('admin.dethi.index', compact('deThis', 'khoas'));
    }

    public function create()
    {
        $khoas = Khoa::where('active', true)->orderBy('ten_khoa')->get();

        return view('admin.dethi.create', compact('khoas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ma_de' => 'required|string|max:50|unique:de_this,ma_de',
            'ten_de' => 'required|string|max:255',
            'loai_chung_chi' => 'required|in:cntt,tienganh',
            'khoa_id' => 'required|exists:khoas,id',
            'file' => 'nullable|file|mimes:csv,txt|max:10240',
        ]);

        $filePath = null;

        DB::transaction(function () use ($request, $data, &$filePath) {
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('de-thi', 'local');
            }

            $deThi = DeThi::create([
                'ma_de' => trim($data['ma_de']),
                'ten_de' => trim($data['ten_de']),
                'loai_chung_chi' => $data['loai_chung_chi'],
                'khoa_id' => $data['khoa_id'],
                'file_goc' => $filePath,
                'active' => true,
            ]);

            if ($filePath) {
                $this->importCauHoiFromCsv(Storage::path($filePath), $deThi);
            }
        });

        return redirect()->route('admin.dethi.index')->with('status', 'Tạo đề thi thành công.');
    }

    public function show(DeThi $dethi)
    {
        $dethi->load('khoa');
        $cauHois = $dethi->cauHois()->get();

        return view('admin.dethi.show', compact('dethi', 'cauHois'));
    }

    public function edit(DeThi $dethi)
    {
        $khoas = Khoa::where('active', true)->orderBy('ten_khoa')->get();

        return view('admin.dethi.edit', compact('dethi', 'khoas'));
    }

    public function update(Request $request, DeThi $dethi)
    {
        $data = $request->validate([
            'ma_de' => 'required|string|max:50|unique:de_this,ma_de,' . $dethi->id,
            'ten_de' => 'required|string|max:255',
            'loai_chung_chi' => 'required|in:cntt,tienganh',
            'khoa_id' => 'required|exists:khoas,id',
            'active' => 'nullable|boolean',
        ]);

        // Không đổi loại/khoa khi đề đã được sử dụng trong ca thi hoặc bài thi.
        if (($data['loai_chung_chi'] !== $dethi->loai_chung_chi || (int) $data['khoa_id'] !== (int) $dethi->khoa_id)
            && ($dethi->lichThis()->exists() || $dethi->baiThis()->exists())) {
            throw ValidationException::withMessages([
                'loai_chung_chi' => 'Không thể đổi loại chứng chỉ hoặc Khoa vì đề đã được sử dụng.',
            ]);
        }

        $dethi->update([
            'ma_de' => trim($data['ma_de']),
            'ten_de' => trim($data['ten_de']),
            'loai_chung_chi' => $data['loai_chung_chi'],
            'khoa_id' => $data['khoa_id'],
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('admin.dethi.show', $dethi)->with('status', 'Cập nhật đề thi thành công.');
    }

    public function importQuestions(Request $request, DeThi $dethi)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $count = $this->importCauHoiFromCsv($request->file('file')->getRealPath(), $dethi);

        return back()->with('status', "Đã nhập {$count} câu hỏi vào đề thi.");
    }

    private function importCauHoiFromCsv(string $path, DeThi $dethi): int
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw ValidationException::withMessages(['file' => 'Không thể đọc file câu hỏi.']);
        }

        $header = fgetcsv($handle);
        if ($header === false || count($header) < 1) {
            fclose($handle);
            throw ValidationException::withMessages(['file' => 'File CSV không có dữ liệu.']);
        }

        $header = array_map(fn ($v) => strtolower(trim((string) $v)), $header);
        $required = ['noi_dung'];
        foreach ($required as $column) {
            if (! in_array($column, $header, true)) {
                fclose($handle);
                throw ValidationException::withMessages(['file' => "File CSV thiếu cột bắt buộc: {$column}."]);
            }
        }

        $index = array_flip($header);
        $rows = [];
        $thuTu = (int) ($dethi->cauHois()->max('thu_tu') ?? 0);
        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if (count($row) === 1 && trim((string) $row[0]) === '') {
                continue;
            }

            $get = fn (string $key, $default = null) =>
                isset($index[$key]) ? ($row[$index[$key]] ?? $default) : $default;

            $noiDung = trim((string) $get('noi_dung', ''));
            if ($noiDung === '') {
                fclose($handle);
                throw ValidationException::withMessages(['file' => "Dòng {$line}: nội dung câu hỏi không được trống."]);
            }

            $loai = trim((string) $get('loai_cau', 'tracnghiem'));
            if (! in_array($loai, ['tracnghiem', 'tuluan'], true)) {
                fclose($handle);
                throw ValidationException::withMessages(['file' => "Dòng {$line}: loai_cau phải là tracnghiem hoặc tuluan."]);
            }

            $dapAnDung = strtoupper(trim((string) $get('dap_an_dung', '')));
            if ($loai === 'tracnghiem' && $dapAnDung !== '' && ! in_array($dapAnDung, ['A', 'B', 'C', 'D'], true)) {
                fclose($handle);
                throw ValidationException::withMessages(['file' => "Dòng {$line}: đáp án đúng phải là A/B/C/D."]);
            }

            $diem = $get('diem', 1);
            if (! is_numeric($diem) || (float) $diem <= 0) {
                fclose($handle);
                throw ValidationException::withMessages(['file' => "Dòng {$line}: điểm phải lớn hơn 0."]);
            }

            $thuTu++;
            $rows[] = [
                'de_thi_id' => $dethi->id,
                'noi_dung' => $noiDung,
                'loai_cau' => $loai,
                'dap_an_a' => trim((string) $get('dap_an_a', '')) ?: null,
                'dap_an_b' => trim((string) $get('dap_an_b', '')) ?: null,
                'dap_an_c' => trim((string) $get('dap_an_c', '')) ?: null,
                'dap_an_d' => trim((string) $get('dap_an_d', '')) ?: null,
                'dap_an_dung' => $dapAnDung ?: null,
                'diem' => (float) $diem,
                'thu_tu' => $thuTu,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($handle);

        if ($rows === []) {
            throw ValidationException::withMessages(['file' => 'File CSV không có câu hỏi hợp lệ.']);
        }

        DB::transaction(fn () => CauHoi::insert($rows));

        return count($rows);
    }

    public function storeQuestion(Request $request, DeThi $dethi)
    {
        $data = $request->validate([
            'noi_dung' => 'required|string',
            'loai_cau' => 'required|in:tracnghiem,tuluan',
            'dap_an_a' => 'nullable|string',
            'dap_an_b' => 'nullable|string',
            'dap_an_c' => 'nullable|string',
            'dap_an_d' => 'nullable|string',
            'dap_an_dung' => 'nullable|in:A,B,C,D',
            'diem' => 'required|numeric|min:0.1',
        ]);

        if ($data['loai_cau'] === 'tracnghiem' && empty($data['dap_an_dung'])) {
            throw ValidationException::withMessages([
                'dap_an_dung' => 'Câu trắc nghiệm phải có đáp án đúng.',
            ]);
        }

        if ($data['loai_cau'] === 'tuluan') {
            $data['dap_an_dung'] = null;
        }

        $data['de_thi_id'] = $dethi->id;
        $data['thu_tu'] = ((int) ($dethi->cauHois()->max('thu_tu') ?? 0)) + 1;

        CauHoi::create($data);

        return back()->with('status', 'Thêm câu hỏi thành công.');
    }

    public function destroyQuestion(DeThi $dethi, CauHoi $cauhoi)
    {
        abort_unless($cauhoi->de_thi_id === $dethi->id, 404);

        if ($cauhoi->cauTraLois()->exists()) {
            return back()->withErrors(['cauhoi' => 'Không thể xóa câu hỏi đã phát sinh bài làm.']);
        }

        $cauhoi->delete();

        // Đánh lại thứ tự để danh sách đề luôn liên tục.
        $dethi->cauHois()->get()->each(function (CauHoi $question, $index) {
            $question->updateQuietly(['thu_tu' => $index + 1]);
        });

        return back()->with('status', 'Đã xóa câu hỏi.');
    }

    public function destroy(DeThi $dethi)
    {
        if ($dethi->lichThis()->exists() || $dethi->baiThis()->exists()) {
            return back()->withErrors(['dethi' => 'Không thể xóa đề thi đã được gán cho ca thi hoặc đã phát sinh bài làm.']);
        }

        $file = $dethi->file_goc;
        $dethi->delete();

        if ($file) {
            Storage::disk('local')->delete($file);
        }

        return redirect()->route('admin.dethi.index')->with('status', 'Đã xóa đề thi.');
    }
}
