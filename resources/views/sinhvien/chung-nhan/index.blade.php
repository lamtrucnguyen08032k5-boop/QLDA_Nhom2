@extends('layouts.sinhvien')
@section('title', 'Chứng nhận của tôi')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3"><i class="bi bi-award me-2"></i>Danh sách chứng nhận</h5>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Đề thi / Bài thi</th>
                        <th>Điểm đạt</th>
                        <th>Số chứng nhận</th>
                        <th>Trạng thái bản cứng</th>
                        <th class="text-end">Chứng nhận điện tử</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($chungNhans as $cn)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $cn->baiThi->deThi->ten_de ?? 'N/A' }}</div>
                            <span class="text-muted small">Nộp ngày {{ $cn->created_at?->format('d/m/Y') }}</span>
                        </td>
                        <td><span class="badge bg-success fs-6">{{ $cn->baiThi->diem_tong }} / 100</span></td>
                        <td>{{ $cn->so_chung_nhan ?? 'Điện tử' }}</td>
                        <td>
                            @switch($cn->trang_thai)
                                @case('cho_xu_ly')
                                @case('cho_duyet')
                                    <span class="badge bg-secondary">Đã đăng ký (Chờ xử lý)</span>
                                    @break
                                @case('dang_xu_ly')
                                    <span class="badge bg-warning text-dark">Đang xử lý cấp</span>
                                    @break
                                @case('da_cap')
                                    <span class="badge bg-success">Đã cấp bản cứng</span>
                                    @break
                                @case('tu_choi')
                                    <span class="badge bg-danger">Từ chối bản cứng</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="text-end">
                            <a href="{{ route('sinhvien.chung-nhan.show', $cn->bai_thi_id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i> Xem trực tuyến
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Bạn chưa đăng ký nhận chứng nhận nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
