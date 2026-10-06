@extends('layouts.app')
@section('title', 'Phân công chấm thi')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3"><i class="bi bi-person-gear me-2"></i>Phân công Giảng viên chấm thi</h5>
        <form method="GET" action="{{ route('khoa.phan-cong-cham.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-semibold">Lọc theo trạng thái</label>
                <select name="trang_thai" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="da_nop" {{ request('trang_thai') == 'da_nop' ? 'selected' : '' }}>Đã nộp (Chưa phân công)</option>
                    <option value="cho_cham_1" {{ request('trang_thai') == 'cho_cham_1' ? 'selected' : '' }}>Chờ chấm lần 1</option>
                    <option value="dang_cham_1" {{ request('trang_thai') == 'dang_cham_1' ? 'selected' : '' }}>Đang chấm lần 1</option>
                    <option value="cho_cham_2" {{ request('trang_thai') == 'cho_cham_2' ? 'selected' : '' }}>Chờ chấm lần 2</option>
                    <option value="dang_cham_2" {{ request('trang_thai') == 'dang_cham_2' ? 'selected' : '' }}>Đang chấm lần 2</option>
                    <option value="cho_thong_nhat" {{ request('trang_thai') == 'cho_thong_nhat' ? 'selected' : '' }}>Chờ thống nhất</option>
                    <option value="da_chot" {{ request('trang_thai') == 'da_chot' ? 'selected' : '' }}>Đã chốt điểm</option>
                </select>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="{{ route('khoa.phan-cong-cham.hang-loat') }}" id="formBatchAssign">
    @csrf
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body bg-light rounded">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">GV Chấm lần 1 (cho các bài đã chọn)</label>
                    <select name="giang_vien_1_id" class="form-select form-select-sm" required>
                        <option value="">-- Chọn GV1 --</option>
                        @foreach($giangViens as $gv)
                            <option value="{{ $gv->id }}">{{ $gv->name }} ({{ $gv->ma_so }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">GV Chấm lần 2 (cho các bài đã chọn)</label>
                    <select name="giang_vien_2_id" class="form-select form-select-sm" required>
                        <option value="">-- Chọn GV2 --</option>
                        @foreach($giangViens as $gv)
                            <option value="{{ $gv->id }}">{{ $gv->name }} ({{ $gv->ma_so }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100 mt-3">
                        <i class="bi bi-check2-all me-1"></i> Phân công các bài đã chọn
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px;"><input type="checkbox" id="selectAll"></th>
                        <th>Sinh viên</th>
                        <th>Kỳ thi / Phòng</th>
                        <th>Ngày nộp</th>
                        <th>GV Chấm lần 1</th>
                        <th>GV Chấm lần 2</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($baiThis as $bt)
                    @php
                        $cannotAssign = $bt->cham_xong || $bt->da_khoa || !in_array($bt->trang_thai, ['da_nop', 'cho_cham_1']);
                    @endphp
                    <tr>
                        <td>
                            <input type="checkbox" name="bai_thi_ids[]" value="{{ $bt->id }}" class="item-checkbox" {{ $cannotAssign ? 'disabled' : '' }}>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $bt->dangKy->sinhVien->name ?? 'N/A' }}</div>
                            <span class="text-muted small">{{ $bt->dangKy->sinhVien->ma_so ?? '' }}</span>
                        </td>
                        <td>
                            <div>{{ $bt->dangKy->lichThi->ten_ky_thi ?? 'N/A' }}</div>
                            <span class="badge bg-secondary-subtle text-secondary small">Phòng {{ $bt->dangKy->lichThi->phong_thi ?? '' }}</span>
                        </td>
                        <td>{{ $bt->gio_nop?->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($bt->giangVien1)
                                <span class="fw-semibold text-primary"><i class="bi bi-person-fill me-1"></i>{{ $bt->giangVien1->name }}</span>
                            @else
                                <span class="text-muted fst-italic">Chưa phân công</span>
                            @endif
                        </td>
                        <td>
                            @if($bt->giangVien2)
                                <span class="fw-semibold text-info"><i class="bi bi-person-fill me-1"></i>{{ $bt->giangVien2->name }}</span>
                            @else
                                <span class="text-muted fst-italic">Chưa phân công</span>
                            @endif
                        </td>
                        <td>
                            @switch($bt->trang_thai)
                                @case('da_nop')
                                    <span class="badge bg-secondary">Đã nộp</span>
                                    @break
                                @case('cho_cham_1')
                                    <span class="badge bg-warning text-dark">Chờ chấm 1</span>
                                    @break
                                @case('dang_cham_1')
                                    <span class="badge bg-info text-dark">Đang chấm 1</span>
                                    @break
                                @case('cho_cham_2')
                                    <span class="badge bg-warning text-dark">Chờ chấm 2</span>
                                    @break
                                @case('dang_cham_2')
                                    <span class="badge bg-info text-dark">Đang chấm 2</span>
                                    @break
                                @case('cho_thong_nhat')
                                    <span class="badge bg-danger">Chờ thống nhất</span>
                                    @break
                                @case('da_chot')
                                    <span class="badge bg-success">Đã chốt điểm</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ $bt->trang_thai }}</span>
                            @endswitch
                        </td>
                        <td class="text-end">
                            @if($cannotAssign)
                                <a href="{{ route('khoa.phan-cong-cham.show', $bt->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-eye me-1"></i>Xem chi tiết
                                </a>
                            @else
                                <a href="{{ route('khoa.phan-cong-cham.show', $bt->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil-square me-1"></i>Phân công
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Không có bài thi nào cần phân công.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</form>

<div class="mt-3">
    {{ $baiThis->links() }}
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection
