@extends('layouts.app')
@section('title', 'Quản lý Phúc khảo (Khoa)')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3"><i class="bi bi-arrow-counterclockwise me-2"></i>Danh sách yêu cầu phúc khảo</h5>
        <form method="GET" action="{{ route('khoa.phuc-khao.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-semibold">Lọc theo trạng thái</label>
                <select name="trang_thai" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="cho_phan_cong" {{ request('trang_thai') == 'cho_phan_cong' ? 'selected' : '' }}>Chờ Khoa phân công</option>
                    <option value="dang_xu_ly" {{ request('trang_thai') == 'dang_xu_ly' ? 'selected' : '' }}>Đang xử lý (Đã phân công GV)</option>
                    <option value="cho_admin_duyet" {{ request('trang_thai') == 'cho_admin_duyet' ? 'selected' : '' }}>Chờ Admin duyệt</option>
                    <option value="hoan_tat" {{ request('trang_thai') == 'hoan_tat' ? 'selected' : '' }}>Hoàn tất</option>
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
                    <th>Bài thi / Đề thi</th>
                    <th>Ngày nộp</th>
                    <th>GV Chấm phúc khảo</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Phân công</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($phucKhaos as $pk)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $pk->sinhVien->name ?? 'N/A' }}</div>
                        <span class="text-muted small">{{ $pk->sinhVien->ma_so ?? '' }}</span>
                    </td>
                    <td>
                        <div>{{ $pk->baiThi->deThi->ten_de ?? 'N/A' }}</div>
                        <span class="badge bg-secondary-subtle text-secondary small">Điểm trước: {{ $pk->baiThi->diem_tong }}</span>
                    </td>
                    <td>{{ $pk->created_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($pk->giangVien)
                            <span class="fw-semibold text-primary"><i class="bi bi-person-fill me-1"></i>{{ $pk->giangVien->name }}</span>
                        @else
                            <span class="text-muted fst-italic">Chưa phân công</span>
                        @endif
                    </td>
                    <td>
                        @switch($pk->trang_thai)
                            @case('cho_tiep_nhan')
                                <span class="badge bg-secondary">Chờ tiếp nhận</span>
                                @break
                            @case('cho_phan_cong')
                                <span class="badge bg-warning text-dark">Chờ Khoa phân công</span>
                                @break
                            @case('dang_xu_ly')
                                <span class="badge bg-info text-dark">Đang xử lý</span>
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
                        <form method="POST" action="{{ route('khoa.phuc-khao.phan-cong', $pk->id) }}" class="d-flex align-items-center gap-1 justify-content-end">
                            @csrf
                            <select name="giang_vien_id" class="form-select form-select-sm w-auto" required>
                                <option value="">-- Chọn GV --</option>
                                @foreach($giangViens as $gv)
                                    <option value="{{ $gv->id }}" {{ $pk->giang_vien_id == $gv->id ? 'selected' : '' }}>
                                        {{ $gv->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Không tìm thấy yêu cầu phúc khảo nào.</td>
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
