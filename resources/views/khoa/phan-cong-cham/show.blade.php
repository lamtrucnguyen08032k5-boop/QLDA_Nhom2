@extends('layouts.app')
@section('title', 'Chi tiết phân công chấm thi')
@section('content')
<div class="mb-3">
    <a href="{{ route('khoa.phan-cong-cham.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">Thông tin bài thi</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width:150px;" class="text-muted">Sinh viên:</th>
                        <td class="fw-bold">{{ $baithi->dangKy->sinhVien->name ?? 'N/A' }} ({{ $baithi->dangKy->sinhVien->ma_so ?? '' }})</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Kỳ thi:</th>
                        <td>{{ $baithi->dangKy->lichThi->ten_ky_thi ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Phòng thi:</th>
                        <td>{{ $baithi->dangKy->lichThi->phong_thi ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Đề thi:</th>
                        <td>{{ $baithi->deThi->ten_de ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Nộp lúc:</th>
                        <td>{{ $baithi->gio_nop?->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Trạng thái:</th>
                        <td>
                            @switch($baithi->trang_thai)
                                @case('da_nop')
                                    <span class="badge bg-secondary">Đã nộp (Chưa phân công)</span>
                                    @break
                                @case('cho_cham_1')
                                    <span class="badge bg-warning text-dark">Chờ chấm lần 1</span>
                                    @break
                                @case('dang_cham_1')
                                    <span class="badge bg-info text-dark">Đang chấm lần 1</span>
                                    @break
                                @case('cho_cham_2')
                                    <span class="badge bg-warning text-dark">Chờ chấm lần 2</span>
                                    @break
                                @case('dang_cham_2')
                                    <span class="badge bg-info text-dark">Đang chấm lần 2</span>
                                    @break
                                @case('cho_thong_nhat')
                                    <span class="badge bg-danger">Chờ thống nhất</span>
                                    @break
                                @case('da_chot')
                                    <span class="badge bg-success">Đã chốt điểm</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ $baithi->trang_thai }}</span>
                            @endswitch
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">Phân công Giảng viên</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('khoa.phan-cong-cham.update', $baithi->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giảng viên chấm lần 1 (GV1) <span class="text-danger">*</span></label>
                        <select name="giang_vien_1_id" class="form-select" required>
                            <option value="">-- Chọn GV1 --</option>
                            @foreach($giangViens as $gv)
                                <option value="{{ $gv->id }}" {{ (old('giang_vien_1_id', $baithi->giang_vien_1_id) == $gv->id) ? 'selected' : '' }}>
                                    {{ $gv->name }} ({{ $gv->ma_so }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Giảng viên chấm lần 2 (GV2) <span class="text-danger">*</span></label>
                        <select name="giang_vien_2_id" class="form-select" required>
                            <option value="">-- Chọn GV2 --</option>
                            @foreach($giangViens as $gv)
                                <option value="{{ $gv->id }}" {{ (old('giang_vien_2_id', $baithi->giang_vien_2_id) == $gv->id) ? 'selected' : '' }}>
                                    {{ $gv->name }} ({{ $gv->ma_so }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i> Lưu phân công
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
