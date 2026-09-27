@extends('layouts.sinhvien')
@section('title', 'Phúc khảo của tôi')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3"><i class="bi bi-arrow-counterclockwise me-2"></i>Danh sách yêu cầu phúc khảo</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kỳ thi / Đề thi</th>
                        <th>Lý do phúc khảo</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                        <th>Kết quả phúc khảo</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($phucKhaos as $pk)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $pk->baiThi->dangKy->lichThi->ten_ky_thi ?? 'N/A' }}</div>
                            <span class="text-muted small">Đề: {{ $pk->baiThi->deThi->ten_de ?? '' }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($pk->ly_do, 60) }}</td>
                        <td>{{ $pk->created_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            @switch($pk->trang_thai)
                                @case('cho_tiep_nhan')
                                @case('cho_xu_ly')
                                    <span class="badge bg-secondary">Chờ tiếp nhận</span>
                                    @break
                                @case('cho_phan_cong')
                                    <span class="badge bg-warning text-dark">Đã tiếp nhận (Chờ phân công)</span>
                                    @break
                                @case('dang_xu_ly')
                                    <span class="badge bg-info text-dark">Đang xử lý chấm</span>
                                    @break
                                @case('cho_admin_duyet')
                                    <span class="badge bg-primary">Chờ duyệt kết quả</span>
                                    @break
                                @case('hoan_tat')
                                @case('da_xu_ly')
                                    <span class="badge bg-success">Hoàn tất</span>
                                    @break
                                @case('tu_choi')
                                    <span class="badge bg-danger">Từ chối</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ $pk->trang_thai }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if($pk->trang_thai === 'hoan_tat' || $pk->trang_thai === 'da_xu_ly')
                                @if($pk->diem_sau !== null && $pk->diem_sau != $pk->diem_truoc)
                                    <span class="fw-bold text-success">Điểm thay đổi: {{ $pk->diem_truoc }} → {{ $pk->diem_sau }}</span>
                                @else
                                    <span class="text-muted">Giữ nguyên điểm ({{ $pk->diem_truoc }})</span>
                                @endif
                                @if($pk->phan_hoi)
                                    <div class="small text-muted mt-1">Phản hồi: {{ $pk->phan_hoi }}</div>
                                @endif
                            @elseif($pk->trang_thai === 'tu_choi')
                                <span class="text-danger small">Lý do từ chối: {{ $pk->ly_do_duyet ?? $pk->phan_hoi ?? 'Không hợp lệ' }}</span>
                            @else
                                <span class="text-muted fst-italic">Đang trong tiến trình xử lý</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Bạn chưa gửi yêu cầu phúc khảo nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
