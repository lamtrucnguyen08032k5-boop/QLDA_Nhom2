@extends('layouts.app')
@section('title', 'Danh sách bài thi được phân công chấm')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3"><i class="bi bi-file-earmark-check me-2"></i>Danh sách bài thi phân công chấm</h5>

        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link {{ !request('tab') ? 'active' : '' }}" href="{{ route('giangvien.cham-thi.index', array_merge(request()->query(), ['tab' => null])) }}">Tất cả bài thi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') === 'lan1' ? 'active' : '' }}" href="{{ route('giangvien.cham-thi.index', array_merge(request()->query(), ['tab' => 'lan1'])) }}">Lượt 1 (GV1)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') === 'lan2' ? 'active' : '' }}" href="{{ route('giangvien.cham-thi.index', array_merge(request()->query(), ['tab' => 'lan2'])) }}">Lượt 2 (GV2)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') === 'thong_nhat' ? 'active' : '' }}" href="{{ route('giangvien.cham-thi.index', array_merge(request()->query(), ['tab' => 'thong_nhat'])) }}">Chờ thống nhất</a>
                </li>
            </ul>

            <form method="GET" action="{{ route('giangvien.cham-thi.index') }}" class="d-flex align-items-center gap-2">
                @if(request('tab'))
                    <input type="hidden" name="tab" value="{{ request('tab') }}">
                @endif
                <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Tất cả tiến độ --</option>
                    <option value="chua_cham" {{ request('filter') === 'chua_cham' ? 'selected' : '' }}>Chưa hoàn tất</option>
                    <option value="da_cham" {{ request('filter') === 'da_cham' ? 'selected' : '' }}>Đã chốt điểm</option>
                </select>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sinh viên</th>
                        <th>Kỳ thi</th>
                        <th>Vai trò của bạn</th>
                        <th>Ngày nộp</th>
                        <th>Điểm TN</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($baiThis as $bt)
                    @php
                        $isGV1 = ($bt->giang_vien_1_id === auth()->id());
                        $isGV2 = ($bt->giang_vien_2_id === auth()->id());
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $bt->dangKy->sinhVien->name ?? 'N/A' }}</div>
                            <span class="text-muted small">{{ $bt->dangKy->sinhVien->ma_so ?? '' }}</span>
                        </td>
                        <td>
                            <div>{{ $bt->dangKy->lichThi->ten_ky_thi ?? 'N/A' }}</div>
                            <span class="badge bg-secondary-subtle text-secondary small">Phòng {{ $bt->dangKy->lichThi->phong_thi ?? '' }}</span>
                        </td>
                        <td>
                            @if($isGV1 && $isGV2)
                                <span class="badge bg-purple text-white" style="background-color: #6f42c1;">GV1 & GV2</span>
                            @elseif($isGV1)
                                <span class="badge bg-primary">Chấm Lần 1 (GV1)</span>
                            @elseif($isGV2)
                                <span class="badge bg-info text-dark">Chấm Lần 2 (GV2)</span>
                            @else
                                <span class="badge bg-secondary">Giảng viên</span>
                            @endif
                        </td>
                        <td>{{ $bt->gio_nop?->format('d/m/Y H:i') }}</td>
                        <td><span class="fw-bold text-success">{{ $bt->diem_tu_dong }}</span></td>
                        <td>
                            @switch($bt->trang_thai)
                                @case('da_nop')
                                    <span class="badge bg-secondary">Chưa phân công</span>
                                    @break
                                @case('cho_cham_1')
                                    <span class="badge bg-warning text-dark">Chờ chấm 1</span>
                                    @break
                                @case('dang_cham_1')
                                    <span class="badge bg-warning text-dark">Đang chấm 1 (Nháp)</span>
                                    @break
                                @case('cho_cham_2')
                                    <span class="badge bg-info text-dark">Chờ chấm 2</span>
                                    @break
                                @case('dang_cham_2')
                                    <span class="badge bg-info text-dark">Đang chấm 2 (Nháp)</span>
                                    @break
                                @case('cho_thong_nhat')
                                    <span class="badge bg-danger animate-pulse">Chờ thống nhất</span>
                                    @break
                                @case('da_chot')
                                    <span class="badge bg-success">Đã chốt điểm ({{ $bt->diem_tong }})</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ $bt->trang_thai }}</span>
                            @endswitch
                        </td>
                        <td class="text-end">
                            @if($bt->trang_thai === 'cho_thong_nhat')
                                <a href="{{ route('giangvien.cham-thi.thong-nhat', $bt->id) }}" class="btn btn-danger btn-sm">
                                    <i class="bi bi-chat-square-dots me-1"></i>Thống nhất điểm
                                </a>
                            @elseif($isGV2 && in_array($bt->trang_thai, ['cho_cham_1', 'dang_cham_1']))
                                <a href="{{ route('giangvien.cham-thi.show', $bt->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-eye me-1"></i>Xem bài (GV2 chỉ đọc)
                                </a>
                            @elseif($isGV1 && in_array($bt->trang_thai, ['cho_cham_1', 'dang_cham_1']))
                                <a href="{{ route('giangvien.cham-thi.show', $bt->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil me-1"></i>Chấm lần 1
                                </a>
                            @elseif($isGV2 && in_array($bt->trang_thai, ['cho_cham_2', 'dang_cham_2']))
                                <a href="{{ route('giangvien.cham-thi.show', $bt->id) }}" class="btn btn-info btn-sm text-dark fw-semibold">
                                    <i class="bi bi-pencil me-1"></i>Chấm lần 2
                                </a>
                            @else
                                <a href="{{ route('giangvien.cham-thi.show', $bt->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>Xem chi tiết
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy bài thi nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">
    {{ $baiThis->links() }}
</div>
@endsection
