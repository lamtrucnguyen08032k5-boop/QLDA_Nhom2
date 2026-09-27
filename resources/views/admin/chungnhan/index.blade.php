@extends('layouts.app')
@section('title', 'Quản lý Chứng nhận (Admin)')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3"><i class="bi bi-award me-2"></i>Danh sách đăng ký cấp chứng nhận</h5>
        <form method="GET" action="{{ route('admin.chungnhan.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-semibold">Lọc theo trạng thái</label>
                <select name="trang_thai" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="cho_xu_ly" {{ request('trang_thai') == 'cho_xu_ly' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="dang_xu_ly" {{ request('trang_thai') == 'dang_xu_ly' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="da_cap" {{ request('trang_thai') == 'da_cap' ? 'selected' : '' }}>Đã cấp</option>
                    <option value="tu_choi" {{ request('trang_thai') == 'tu_choi' ? 'selected' : '' }}>Từ chối</option>
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
                    <th>Điểm thi</th>
                    <th>Số chứng nhận</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($chungNhans as $cn)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $cn->sinhVien->name ?? 'N/A' }}</div>
                        <span class="text-muted small">{{ $cn->sinhVien->ma_so ?? '' }}</span>
                    </td>
                    <td>{{ $cn->baiThi->deThi->ten_de ?? 'N/A' }}</td>
                    <td><span class="badge bg-success fs-6">{{ $cn->baiThi->diem_tong }}</span></td>
                    <td>{{ $cn->so_chung_nhan ?? '—' }}</td>
                    <td>
                        @switch($cn->trang_thai)
                            @case('cho_xu_ly')
                            @case('cho_duyet')
                                <span class="badge bg-secondary">Chờ xử lý</span>
                                @break
                            @case('dang_xu_ly')
                                <span class="badge bg-warning text-dark">Đang xử lý</span>
                                @break
                            @case('da_cap')
                                <span class="badge bg-success">Đã cấp</span>
                                @break
                            @case('tu_choi')
                                <span class="badge bg-danger">Từ chối</span>
                                @break
                            @default
                                <span class="badge bg-light text-dark">{{ $cn->trang_thai }}</span>
                        @endswitch
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.chungnhan.show', $cn->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil-square me-1"></i> Xử lý
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Không có yêu cầu cấp chứng nhận nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $chungNhans->links() }}
</div>
@endsection
