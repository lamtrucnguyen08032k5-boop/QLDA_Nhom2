@extends('layouts.sinhvien')
@section('title', 'Tra cứu kết quả thi')
@section('content')

{{-- Luồng phụ 7: Thông báo lỗi nếu có --}}
@if (!empty($errorMessage))
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
        <div>{{ $errorMessage }}</div>
    </div>
@endif

{{-- Luồng phụ 1: Chưa có kết quả thi nào được công bố --}}
@if ($tongBaiDaCongBo === 0 && ! $hasFilter)
    <div class="card border-0 shadow-sm py-5 text-center">
        <div class="card-body">
            <div class="mb-3 text-secondary" style="font-size: 3.5rem;">
                📋
            </div>
            <h4 class="fw-bold text-dark mb-2">Chưa có kết quả thi.</h4>
            <p class="text-muted mb-4 max-w-md mx-auto">
                Hiện tại bạn chưa có bài thi nào được công bố kết quả. Kết quả sẽ tự động hiển thị tại đây ngay sau khi Phòng Khảo thí hoàn tất công tác chấm và công bố kết quả thi.
            </p>
            <a href="{{ route('sinhvien.dashboard') }}" class="btn btn-outline-primary btn-sm px-4">
                <i class="bi bi-house me-1"></i>Về trang chủ
            </a>
        </div>
    </div>
@else
    {{-- Thanh tìm kiếm & bộ lọc kết quả thi --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('sinhvien.ketqua.index') }}" class="row g-2 align-items-center">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Tên bài thi / Kỳ thi</label>
                    <input type="text" name="q" class="form-control form-control-sm"
                           placeholder="Nhập tên bài thi, kỳ thi..." value="{{ request('q') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Mã bài thi</label>
                    <input type="text" name="ma_bai_thi" class="form-control form-control-sm"
                           placeholder="VD: BT2609..." value="{{ request('ma_bai_thi') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Thi từ ngày</label>
                    <input type="date" name="tu_ngay" class="form-control form-control-sm" value="{{ request('tu_ngay') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Đến ngày</label>
                    <input type="date" name="den_ngay" class="form-control form-control-sm" value="{{ request('den_ngay') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Kết quả</label>
                    <select name="ket_qua" class="form-select form-select-sm">
                        <option value="">-- Tất cả kết quả --</option>
                        <option value="dat" {{ request('ket_qua') === 'dat' ? 'selected' : '' }}>Đạt (>= 50 điểm)</option>
                        <option value="khong_dat" {{ request('ket_qua') === 'khong_dat' ? 'selected' : '' }}>Không đạt (&lt; 50 điểm)</option>
                    </select>
                </div>
                <div class="col-md-auto d-flex flex-column justify-content-end">
                    <label class="form-label small text-transparent mb-1 d-none d-md-block">&nbsp;</label>
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary" title="Tìm kiếm">
                            <i class="bi bi-filter me-1"></i>Lọc
                        </button>
                        <a href="{{ route('sinhvien.ketqua.index') }}" class="btn btn-sm btn-outline-secondary" title="Xóa bộ lọc">
                            <i class="bi bi-x-circle me-1"></i>Xóa lọc
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Bảng danh sách kết quả thi --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 25%;">Tên bài thi / Kỳ thi</th>
                            <th style="width: 14%;">Mã bài thi</th>
                            <th style="width: 11%;">Ngày thi</th>
                            <th style="width: 10%;">Ca thi</th>
                            <th style="width: 10%;">Phòng thi</th>
                            <th class="text-center" style="width: 8%;">Điểm thi</th>
                            <th class="text-center" style="width: 10%;">Kết quả</th>
                            <th style="width: 12%;">Ngày công bố</th>
                            <th class="text-end pe-3" style="width: 10%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($baiThis as $bt)
                        @php
                            $lt = $bt->dangKy?->lichThi;
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $lt->ten_ky_thi ?? ($bt->deThi?->ten_de ?? 'Bài thi') }}</div>
                                @if($bt->deThi)
                                    <div class="small text-muted">Đề: {{ $bt->deThi->ten_de }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="font-monospace fw-semibold text-primary">{{ $bt->ma_bai_thi_hien_thi }}</span>
                            </td>
                            <td>
                                <div>{{ optional($lt?->ngay_thi)->format('d/m/Y') }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $lt?->ma_ca_thi }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    {{ $lt?->phong_thi }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fs-6 fw-bold {{ $bt->is_dat ? 'text-success' : 'text-danger' }}">
                                    {{ $bt->diem_tong !== null ? (float)$bt->diem_tong : '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($bt->is_dat)
                                    <span class="badge bg-success px-2 py-1">
                                        <i class="bi bi-check-circle me-1"></i>Đạt
                                    </span>
                                @else
                                    <span class="badge bg-danger px-2 py-1">
                                        <i class="bi bi-x-circle me-1"></i>Không đạt
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-muted">
                                    {{ optional($bt->ngay_cong_bo)->format('d/m/Y H:i') ?? optional($bt->created_at)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('sinhvien.ketqua.show', $bt) }}" class="btn btn-sm btn-outline-primary text-nowrap">
                                    Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        {{-- Luồng phụ 2: Không tìm thấy kết quả phù hợp với điều kiện tra cứu --}}
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div class="fs-2 mb-2">🔍</div>
                                <div class="fw-semibold text-dark fs-6 mb-1">Không tìm thấy kết quả thi phù hợp.</div>
                                <div class="small text-muted">Không có bài thi nào khớp với điều kiện tra cứu của bạn. Bạn vui lòng thay đổi điều kiện lọc để thử lại.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($baiThis->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-end">
                    {{ $baiThis->links() }}
                </div>
            </div>
        @endif
    </div>
@endif

@endsection
