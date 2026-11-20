@extends('layouts.sinhvien')
@section('title', 'Phúc khảo của tôi')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-primary mb-1">
            <i class="bi bi-arrow-counterclockwise me-2"></i>Danh sách yêu cầu phúc khảo
        </h4>
        <span class="text-muted small">Theo dõi tiến độ thẩm định, chấm lại và kết quả phúc khảo các bài thi</span>
    </div>
    <a href="{{ route('sinhvien.ketqua.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-award me-1"></i> Tra cứu kết quả thi
    </a>
</div>

{{-- Thông báo session nếu có --}}
@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>{{ session('status') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
        <div>{{ session('info') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-3" style="width: 15%;">Mã yêu cầu</th>
                        <th style="width: 20%;">Kỳ thi / Bài thi</th>
                        <th style="width: 15%;">Lệ phí &amp; Thanh toán</th>
                        <th style="width: 20%;">Lý do &amp; Minh chứng</th>
                        <th style="width: 15%;">Trạng thái xử lý</th>
                        <th class="text-end pe-3" style="width: 15%;">Kết quả</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($phucKhaos as $pk)
                    @php
                        $lt = $pk->baiThi?->dangKy?->lichThi;
                        $deThi = $pk->baiThi?->deThi;
                    @endphp
                    <tr>
                        {{-- Mã yêu cầu & Ngày tạo --}}
                        <td class="ps-3">
                            <div class="font-monospace fw-bold text-primary">{{ $pk->ma_phuc_khao_hien_thi }}</div>
                            <small class="text-muted">{{ $pk->created_at?->format('d/m/Y H:i') }}</small>
                        </td>

                        {{-- Bài thi / Kỳ thi --}}
                        <td>
                            <div class="fw-bold text-dark">{{ $lt->ten_ky_thi ?? ($deThi->ten_de ?? 'Bài thi') }}</div>
                            <div class="small text-muted mt-1">
                                Điểm gốc: <strong class="text-dark">{{ (float)$pk->diem_truoc }}</strong> / 100 điểm
                            </div>
                        </td>

                        {{-- Lệ phí & Thanh toán --}}
                        <td>
                            <div class="fw-bold text-dark">{{ number_format($pk->le_phi ?: 50000) }}đ</div>
                            <div class="mt-1">
                                @if ($pk->trang_thai_thanh_toan === 'da_thanh_toan' || $pk->trang_thai !== 'cho_thanh_toan')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle me-1"></i>Đã thanh toán
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mb-1 d-inline-block">
                                        <i class="bi bi-hourglass-split me-1"></i>Chưa thanh toán
                                    </span>
                                    <a href="{{ route('sinhvien.phuc-khao.thanh-toan', $pk->id) }}" class="btn btn-sm btn-primary py-0 px-2 text-nowrap d-block mt-1" style="font-size: 0.75rem;">
                                        Thanh toán ngay →
                                    </a>
                                @endif
                            </div>
                        </td>

                        {{-- Lý do & Minh chứng --}}
                        <td>
                            <div class="text-dark small" title="{{ $pk->ly_do }}">
                                {{ \Illuminate\Support\Str::limit($pk->ly_do, 60) }}
                            </div>
                            @if ($pk->file_minh_chung)
                                <div class="mt-1">
                                    <a href="{{ asset('storage/' . $pk->file_minh_chung) }}" target="_blank" class="small text-primary text-decoration-none">
                                        <i class="bi bi-paperclip me-1"></i>Tệp minh chứng
                                    </a>
                                </div>
                            @endif
                        </td>

                        {{-- Trạng thái xử lý --}}
                        <td>
                            @switch($pk->trang_thai)
                                @case('cho_thanh_toan')
                                    <span class="badge bg-secondary-subtle text-secondary border">Chờ thanh toán</span>
                                    @break
                                @case('cho_tiep_nhan')
                                @case('cho_xu_ly')
                                    <span class="badge bg-info-subtle text-info border">Chờ Admin tiếp nhận</span>
                                    @break
                                @case('cho_phan_cong')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border">Khoa đang phân công GV</span>
                                    @break
                                @case('dang_xu_ly')
                                    <span class="badge bg-primary-subtle text-primary border">GV đang chấm lại</span>
                                    @break
                                @case('cho_admin_duyet')
                                    <span class="badge bg-primary">Chờ Admin phê duyệt</span>
                                    @break
                                @case('hoan_tat')
                                    <span class="badge bg-success">Đã hoàn tất</span>
                                    @break
                                @case('tu_choi')
                                    <span class="badge bg-danger">Từ chối</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ $pk->trang_thai }}</span>
                            @endswitch
                        </td>

                        {{-- Kết quả phúc khảo --}}
                        <td class="text-end pe-3">
                            @if ($pk->trang_thai === 'hoan_tat')
                                @if ($pk->diem_sau !== null && $pk->diem_sau != $pk->diem_truoc)
                                    <div class="fw-bold text-success fs-6">
                                        {{ (float)$pk->diem_truoc }} → {{ (float)$pk->diem_sau }}
                                    </div>
                                    <small class="text-success fw-semibold">Đã cập nhật điểm</small>
                                @else
                                    <span class="text-muted fw-semibold">Giữ nguyên ({{ (float)$pk->diem_truoc }})</span>
                                @endif
                                @if ($pk->phan_hoi)
                                    <button type="button" class="btn btn-link p-0 text-decoration-none small d-block ms-auto text-primary mt-1" 
                                            data-bs-toggle="modal" data-bs-target="#modalPhanHoi{{ $pk->id }}">
                                        Xem phản hồi
                                    </button>

                                    {{-- Modal phản hồi --}}
                                    <div class="modal fade" id="modalPhanHoi{{ $pk->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-success text-white py-3">
                                                    <h6 class="modal-title fw-bold">Kết quả phúc khảo: {{ $pk->ma_phuc_khao_hien_thi }}</h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="mb-2"><strong>Bài thi:</strong> {{ $lt->ten_ky_thi ?? ($deThi->ten_de ?? 'Bài thi') }}</p>
                                                    <p class="mb-2"><strong>Điểm trước:</strong> {{ (float)$pk->diem_truoc }} | <strong>Điểm sau phúc khảo:</strong> <span class="text-success fw-bold">{{ (float)$pk->diem_sau }}</span></p>
                                                    <div class="p-3 bg-light rounded border mt-3">
                                                        <strong class="d-block mb-1 text-primary">Phản hồi của Hội đồng chấm phúc khảo:</strong>
                                                        <p class="mb-0 text-dark">{!! nl2br(e($pk->phan_hoi)) !!}</p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light py-2">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @elseif ($pk->trang_thai === 'tu_choi')
                                <span class="badge bg-danger mb-1">Không chấp thuận</span>
                                <div class="small text-danger">{{ $pk->ly_do_duyet ?? 'Hồ sơ không hợp lệ' }}</div>
                            @else
                                <span class="text-muted small fst-italic">Đang xử lý</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold">Bạn chưa gửi yêu cầu phúc khảo nào.</div>
                            <small class="text-muted">Sau khi có kết quả thi, nếu có thắc mắc về điểm, bạn có thể gửi yêu cầu phúc khảo trong vòng 7 ngày.</small>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($phucKhaos->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-end">
                {{ $phucKhaos->links() }}
            </div>
        </div>
    @endif
</div>

@endsection
