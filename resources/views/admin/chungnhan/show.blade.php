@extends('layouts.app')
@section('title', 'Chi tiết Đăng ký Chứng nhận (Admin)')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.chungnhan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">Thông tin đăng ký cấp chứng nhận bản cứng</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width:170px;" class="text-muted">Sinh viên:</th>
                        <td class="fw-bold">{{ $chungnhan->sinhVien->name ?? 'N/A' }} ({{ $chungnhan->sinhVien->ma_so ?? '' }})</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Bài thi / Đề thi:</th>
                        <td>{{ $chungnhan->baiThi->deThi->ten_de ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Điểm bài thi:</th>
                        <td><span class="badge bg-success fs-6">{{ $chungnhan->baiThi->diem_tong }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Địa chỉ nhận bản cứng:</th>
                        <td>{{ $chungnhan->dia_chi_nhan ?? 'Chưa cung cấp' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Số điện thoại:</th>
                        <td>{{ $chungnhan->so_dien_thoai ?? 'Chưa cung cấp' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Trạng thái:</th>
                        <td>
                            @switch($chungnhan->trang_thai)
                                @case('cho_xu_ly')
                                @case('cho_duyet')
                                    <span class="badge bg-secondary">Chờ xử lý</span>
                                    @break
                                @case('dang_xu_ly')
                                    <span class="badge bg-warning text-dark">Đang xử lý</span>
                                    @break
                                @case('da_cap')
                                    <span class="badge bg-success">Đã cấp ({{ $chungnhan->so_chung_nhan }})</span>
                                    @break
                                @case('tu_choi')
                                    <span class="badge bg-danger">Từ chối</span>
                                    @break
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
                <h5 class="card-title fw-bold mb-0">Xử lý yêu cầu cấp chứng nhận</h5>
            </div>
            <div class="card-body">
                @if(in_array($chungnhan->trang_thai, ['cho_xu_ly', 'cho_duyet']))
                    <form method="POST" action="{{ route('admin.chungnhan.tiep-nhan', $chungnhan->id) }}" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100 text-dark fw-semibold">
                            <i class="bi bi-hourglass-split me-1"></i> Chuyển sang Đang xử lý
                        </button>
                    </form>
                @endif

                @if($chungnhan->trang_thai !== 'da_cap')
                    <form method="POST" action="{{ route('admin.chungnhan.cap', $chungnhan->id) }}" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ghi chú khi cấp:</label>
                            <textarea name="ghi_chu" class="form-control" rows="2" placeholder="Nhập ghi chú cấp..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Xác nhận duyệt cấp chứng nhận cho sinh viên?')">
                            <i class="bi bi-award me-1"></i> Cấp chứng nhận
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.chungnhan.tu-choi', $chungnhan->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do từ chối:</label>
                            <textarea name="ghi_chu" class="form-control" rows="2" placeholder="Nhập lý do từ chối..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-1"></i> Từ chối cấp
                        </button>
                    </form>
                @else
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle-fill me-1"></i> Chứng nhận đã được cấp vào ngày {{ $chungnhan->ngay_cap?->format('d/m/Y H:i') }}.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
