@extends('layouts.app')
@section('title', 'Xử lý phúc khảo (Giảng viên)')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i>Danh sách bài thi phúc khảo được phân công</h5>
        <form method="GET" action="{{ route('giangvien.phuc-khao.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-semibold">Lọc theo trạng thái</label>
                <select name="trang_thai" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="dang_xu_ly" {{ request('trang_thai') === 'dang_xu_ly' ? 'selected' : '' }}>Đang xử lý (Cần chấm lại)</option>
                    <option value="cho_admin_duyet" {{ request('trang_thai') === 'cho_admin_duyet' ? 'selected' : '' }}>Chờ Admin duyệt</option>
                    <option value="hoan_tat" {{ request('trang_thai') === 'hoan_tat' ? 'selected' : '' }}>Hoàn tất</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Sinh viên</th>
                    <th>Đề thi</th>
                    <th>Lý do phúc khảo</th>
                    <th>Điểm hiện tại</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($phucKhaos as $pk)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $pk->baiThi->dangKy->sinhVien->name ?? 'N/A' }}</div>
                        <span class="text-muted small">{{ $pk->baiThi->dangKy->sinhVien->ma_so ?? '' }}</span>
                    </td>
                    <td>{{ $pk->baiThi->deThi->ten_de ?? 'N/A' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($pk->ly_do, 50) }}</td>
                    <td><span class="badge bg-secondary fs-6">{{ $pk->baiThi->diem_tong }}</span></td>
                    <td>
                        @switch($pk->trang_thai)
                            @case('dang_xu_ly')
                                <span class="badge bg-warning text-dark">Cần xử lý chấm lại</span>
                                @break
                            @case('cho_admin_duyet')
                                <span class="badge bg-primary">Chờ Admin duyệt</span>
                                @break
                            @case('hoan_tat')
                                <span class="badge bg-success">Hoàn tất</span>
                                @break
                            @case('tu_choi')
                                <span class="badge bg-danger">Từ chối</span>
                                @break
                            @default
                                <span class="badge bg-light text-dark">{{ $pk->trang_thai }}</span>
                        @endswitch
                    </td>
                    <td class="text-end">
                        <a href="{{ route('giangvien.phuc-khao.show', $pk->id) }}" class="btn btn-sm {{ $pk->trang_thai === 'dang_xu_ly' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-pencil me-1"></i> {{ $pk->trang_thai === 'dang_xu_ly' ? 'Chấm lại' : 'Xem chi tiết' }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Không có bài thi phúc khảo nào được phân công.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $phucKhaos->links() }}
</div>
@endsection
