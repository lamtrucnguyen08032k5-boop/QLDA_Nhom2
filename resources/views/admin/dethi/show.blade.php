@extends('layouts.app')
@section('title', $dethi->ten_de)
@section('content')
<div class="card mb-3"><div class="card-body">
    <h5>{{ $dethi->ten_de }} <code>{{ $dethi->ma_de }}</code></h5>
    <p class="text-muted mb-0">Khoa: {{ $dethi->khoa->ten_khoa }} • Loại: {{ $dethi->loai_chung_chi === 'cntt' ? 'CNTT' : 'Tiếng Anh' }} • Tổng điểm: {{ $dethi->tongDiem() }}</p>
</div></div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Thêm câu hỏi thủ công</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.dethi.cauhoi.store', $dethi) }}">
                    @csrf
                    <div class="mb-2"><textarea name="noi_dung" class="form-control form-control-sm" placeholder="Nội dung câu hỏi" required></textarea></div>
                    <div class="mb-2">
                        <select name="loai_cau" class="form-select form-select-sm">
                            <option value="tracnghiem">Trắc nghiệm</option>
                            <option value="tuluan">Tự luận</option>
                        </select>
                    </div>
                    <div class="row g-1 mb-2">
                        <div class="col-6"><input name="dap_an_a" class="form-control form-control-sm" placeholder="Đáp án A"></div>
                        <div class="col-6"><input name="dap_an_b" class="form-control form-control-sm" placeholder="Đáp án B"></div>
                        <div class="col-6"><input name="dap_an_c" class="form-control form-control-sm" placeholder="Đáp án C"></div>
                        <div class="col-6"><input name="dap_an_d" class="form-control form-control-sm" placeholder="Đáp án D"></div>
                    </div>
                    <div class="mb-2">
                        <select name="dap_an_dung" class="form-select form-select-sm">
                            <option value="">-- Đáp án đúng (nếu trắc nghiệm) --</option>
                            <option>A</option><option>B</option><option>C</option><option>D</option>
                        </select>
                    </div>
                    <div class="mb-2"><input type="number" step="0.1" name="diem" class="form-control form-control-sm" placeholder="Điểm" value="1" required></div>
                    <button class="btn btn-sm btn-primary w-100">Thêm câu hỏi</button>
                </form>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">Import câu hỏi từ file (CSV)</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.dethi.import', $dethi) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control form-control-sm mb-2" required>
                    <button class="btn btn-sm btn-outline-primary w-100">Import</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Danh sách câu hỏi ({{ $cauHois->count() }})</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>#</th><th>Nội dung</th><th>Loại</th><th>Đáp án đúng</th><th>Điểm</th><th></th></tr></thead>
                    <tbody>
                    @foreach ($cauHois as $c)
                        <tr>
                            <td>{{ $c->thu_tu }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($c->noi_dung, 60) }}</td>
                            <td>{{ $c->loai_cau === 'tracnghiem' ? 'TN' : 'TL' }}</td>
                            <td>{{ $c->dap_an_dung }}</td>
                            <td>{{ $c->diem }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.dethi.cauhoi.destroy', [$dethi, $c]) }}" data-confirm="Xoá câu hỏi này?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Xoá</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
