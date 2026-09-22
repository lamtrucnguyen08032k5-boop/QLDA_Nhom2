@extends('layouts.sinhvien')
@section('title', 'Đăng ký của tôi')
@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary fw-bold">Danh sách các ca thi đã đăng ký</h5>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Mã đăng ký</th>
                    <th>Kỳ thi</th>
                    <th>Ngày thi</th>
                    <th>Trạng thái hồ sơ</th>
                    <th>Thanh toán</th>
                    <th>Ghi chú / Lý do</th>
                    <th class="text-end pe-3">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($dangKys as $dk)
                <tr>
                    <td class="ps-3 font-monospace fw-bold">{{ $dk->ma_dang_ky }}</td>
                    <td class="fw-semibold">{{ $dk->lichThi->ten_ky_thi }}</td>
                    <td>{{ optional($dk->lichThi->ngay_thi)->format('d/m/Y') }}</td>
                    <td>
                        @if ($dk->trang_thai_thanh_toan === 'cho_thanh_toan' && $dk->trang_thai !== 'da_huy')
                            <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                        @elseif ($dk->trang_thai === 'cho_duyet')
                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @elseif ($dk->trang_thai === 'cho_bo_sung')
                            @if ($dk->isHetHanBoSungOnline())
                                <span class="badge bg-danger">Hết hạn trực tuyến</span>
                            @else
                                <span class="badge bg-info text-dark">Yêu cầu bổ sung</span>
                            @endif
                        @elseif ($dk->trang_thai === 'da_bo_sung')
                            <span class="badge bg-primary">Đã bổ sung/Chờ duyệt lại</span>
                        @elseif ($dk->trang_thai === 'da_duyet')
                            <span class="badge bg-success">Đã duyệt</span>
                        @elseif ($dk->trang_thai === 'tu_choi')
                            <span class="badge bg-danger">Đã từ chối</span>
                        @else
                            <span class="badge bg-secondary">Đã huỷ</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $dk->trang_thai_thanh_toan === 'da_thanh_toan' ? 'success' : ($dk->trang_thai_thanh_toan === 'thanh_toan_that_bai' ? 'danger' : 'warning text-dark') }}">
                            {{ $dk->nhanTrangThaiThanhToanLabel() }}
                        </span>
                    </td>
                    <td>
                        @if ($dk->trang_thai_thanh_toan === 'cho_thanh_toan' && $dk->trang_thai !== 'da_huy')
                            @if ($dk->isSapHetHanThanhToan())
                                <small class="text-danger fw-bold d-block">⚠️ Sắp hết hạn (còn &lt;12h)</small>
                                <small class="text-danger">Hạn: {{ $dk->hanThanhToan()->format('H:i d/m/Y') }}</small>
                            @else
                                <small class="text-muted d-block">Hạn nộp lệ phí:</small>
                                <small class="text-primary fw-semibold">{{ $dk->hanThanhToan()->format('H:i d/m/Y') }}</small>
                            @endif
                        @elseif ($dk->trang_thai === 'cho_bo_sung')
                            <small class="text-warning-emphasis d-block"><strong>Cần bổ sung:</strong> {{ Str::limit($dk->ly_do_bo_sung, 50) }}</small>
                            <small class="text-muted">Hạn: {{ optional($dk->han_bo_sung)->format('d/m/Y H:i') }}</small>
                        @elseif ($dk->trang_thai === 'tu_choi')
                            <small class="text-danger"><strong>Lý do:</strong> {{ $dk->ly_do_tu_choi }}</small>
                        @elseif ($dk->trang_thai === 'da_huy')
                            <small class="text-muted">Hồ sơ đã bị huỷ</small>
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-end pe-3 text-nowrap">
                        @if ($dk->trang_thai === 'cho_bo_sung')
                            <a href="{{ route('sinhvien.dangky.bo-sung', $dk) }}" class="btn btn-sm {{ $dk->isHetHanBoSungOnline() ? 'btn-outline-secondary' : 'btn-warning' }}">
                                {{ $dk->isHetHanBoSungOnline() ? 'Xem hướng dẫn' : '✏️ Bổ sung hồ sơ' }}
                            </a>
                        @endif

                        @if ($dk->trang_thai === 'da_huy')
                            @php
                                $lichThi = $dk->lichThi;
                                $coTheDangKyLai = $lichThi && !$lichThi->daHetHanDangKy() && $lichThi->trang_thai === 'dang_mo_dang_ky' && ($lichThi->dangKysDaDuyet()->count() < $lichThi->so_luong_toi_da);
                            @endphp
                            @if ($coTheDangKyLai)
                                <a href="{{ route('sinhvien.dangky.buoc1', $lichThi) }}" class="btn btn-sm btn-outline-primary ms-1">🔄 Đăng ký lại</a>
                            @endif
                            <a href="{{ route('sinhvien.dangky.buoc4', $dk) }}" class="btn btn-sm btn-outline-secondary ms-1">Chi tiết</a>
                        @elseif ($dk->trang_thai_thanh_toan !== 'da_thanh_toan')
                            <a href="{{ route('sinhvien.dangky.buoc3', $dk) }}" class="btn btn-sm {{ $dk->isSapHetHanThanhToan() ? 'btn-danger' : 'btn-primary' }} ms-1">
                                {{ $dk->isSapHetHanThanhToan() ? '💳 Thanh toán gấp' : 'Thanh toán' }}
                            </a>
                            <a href="{{ route('sinhvien.dangky.buoc4', $dk) }}" class="btn btn-sm btn-outline-secondary ms-1">Chi tiết</a>
                        @else
                            <a href="{{ route('sinhvien.dangky.buoc4', $dk) }}" class="btn btn-sm btn-outline-primary ms-1">Xem chi tiết</a>
                        @endif

                        @if ($dk->trang_thai !== 'da_huy' && $dk->trang_thai_thanh_toan !== 'da_thanh_toan')
                            <form method="POST" action="{{ route('sinhvien.dangky.huy', $dk) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn huỷ đăng ký này không?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger ms-1">Huỷ</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Bạn chưa đăng ký kỳ thi nào.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $dangKys->links() }}</div>
@endsection
