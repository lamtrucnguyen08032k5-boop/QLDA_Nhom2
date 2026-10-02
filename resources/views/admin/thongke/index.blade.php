@extends('layouts.app')
@section('title', 'Thống kê')

@section('content')
<style>
    .stat-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .nav-tabs .nav-link {
        font-weight: 500;
        color: #495057;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1.25rem;
    }
    .nav-tabs .nav-link.active {
        color: #00529b;
        border-bottom: 3px solid #00529b;
        background: transparent;
        font-weight: 600;
    }
    @media print {
        .sidebar, .navbar, .btn-print, .filter-card, .swal2-container {
            display: none !important;
        }
        main {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold text-primary mb-1">
            Thống Kê Khảo Thí
        </h3>
        <p class="text-muted mb-0 small">Tổng hợp toàn diện dữ liệu đăng ký, kết quả thi, tài chính, phúc khảo và chứng chỉ</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm px-3 btn-print" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> In báo cáo
        </button>
        <a href="{{ route('admin.thongke.index') }}" class="btn btn-light btn-sm border px-3">
            <i class="bi bi-arrow-clockwise me-1"></i> Làm mới
        </a>
    </div>
</div>

{{-- Bộ lọc thống kê đa chiều --}}
<div class="card shadow-sm border-0 mb-4 filter-card">
    <div class="card-body p-3 bg-light rounded-3">
        <form method="GET" action="{{ route('admin.thongke.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary mb-1">Khoa / Đơn vị:</label>
                <select name="khoa_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả các Khoa --</option>
                    @foreach($danhSachKhoa as $k)
                        <option value="{{ $k->id }}" {{ request('khoa_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->ten_khoa }} ({{ $k->ma_khoa }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-secondary mb-1">Môn thi:</label>
                <select name="loai_chung_chi" class="form-select form-select-sm">
                    <option value="">-- Tất cả môn --</option>
                    <option value="cntt" {{ request('loai_chung_chi') == 'cntt' ? 'selected' : '' }}>Tin học CNTT</option>
                    <option value="tieng_anh" {{ request('loai_chung_chi') == 'tieng_anh' ? 'selected' : '' }}>Tiếng Anh</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary mb-1">Tên kỳ thi:</label>
                <select name="ten_ky_thi" class="form-select form-select-sm">
                    <option value="">-- Tất cả kỳ thi --</option>
                    @foreach($danhSachKyThiTen as $tenKt)
                        <option value="{{ $tenKt }}" {{ request('ten_ky_thi') == $tenKt ? 'selected' : '' }}>
                            {{ $tenKt }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-secondary mb-1">Từ ngày:</label>
                <input type="date" name="tu_ngay" class="form-select form-select-sm" value="{{ request('tu_ngay') }}">
            </div>

            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="bi bi-funnel me-1"></i> Lọc dữ liệu
                    </button>
                    @if(request()->hasAny(['khoa_id', 'loai_chung_chi', 'ten_ky_thi', 'tu_ngay', 'den_ngay']))
                        <a href="{{ route('admin.thongke.index') }}" class="btn btn-outline-secondary btn-sm" title="Bỏ lọc">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Hàng 1: 4 Thẻ KPI cốt lõi --}}
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-sm-6">
        <div class="card stat-card shadow-sm h-100 bg-white border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-medium">Tổng hồ sơ Đăng ký</div>
                        <div class="fs-3 fw-bold text-dark my-1">{{ number_format($tongDangKy) }}</div>
                        <div class="small text-success">
                            <i class="bi bi-check-circle me-1"></i>Đã duyệt: <strong>{{ number_format($dangKyDaDuyet) }}</strong>
                            <span class="text-muted ms-1">({{ $tongDangKy > 0 ? round(($dangKyDaDuyet / $tongDangKy)*100) : 0 }}%)</span>
                        </div>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card stat-card shadow-sm h-100 bg-white border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-medium">Thí sinh Dự thi</div>
                        <div class="fs-3 fw-bold text-dark my-1">{{ number_format($tongThiSinhDuThi) }}</div>
                        <div class="small text-info">
                            <i class="bi bi-person-check me-1"></i>Tỷ lệ đi thi: 
                            <strong>{{ $dangKyDaDuyet > 0 ? round(($tongThiSinhDuThi / $dangKyDaDuyet)*100, 1) : 0 }}%</strong>
                        </div>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-journal-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card stat-card shadow-sm h-100 bg-white border-start border-4 border-success">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-medium">Tỷ lệ Đạt chuẩn (≥50đ)</div>
                        <div class="fs-3 fw-bold text-success my-1">{{ $tyLeDat }}%</div>
                        <div class="small text-muted">
                            Đạt: <strong class="text-success">{{ number_format($tongDat) }}</strong> / Trượt: <strong class="text-danger">{{ number_format($tongKhongDat) }}</strong>
                        </div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card stat-card shadow-sm h-100 bg-white border-start border-4 border-warning">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-medium">Tổng Doanh thu Lệ phí</div>
                        <div class="fs-3 fw-bold text-warning-emphasis my-1">{{ number_format($doanhThuDaThu) }} <small class="fs-6">đ</small></div>
                        <div class="small text-muted">
                            Chờ thu: <strong>{{ number_format($doanhThuChoThu) }} đ</strong>
                        </div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning-emphasis">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hàng 2: Thẻ chỉ số phụ & Vận hành --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary fs-4">
                    <i class="bi bi-calculator"></i>
                </div>
                <div>
                    <div class="text-muted small">Điểm TB toàn hệ thống</div>
                    <div class="fs-4 fw-bold text-primary">{{ $diemTrungBinh }} <small class="text-muted fs-6">/100</small></div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Cao nhất: <strong>{{ $diemCaoNhat }}</strong> | Thấp nhất: <strong>{{ $diemThapNhat }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary fs-4">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div>
                    <div class="text-muted small">Tổng số Ca thi</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($tongCaThi) }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Quy mô: <strong>{{ number_format($tongChoNgoi) }}</strong> chỗ ngồi</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger fs-4">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div>
                    <div class="text-muted small">Đơn Phúc khảo</div>
                    <div class="fs-4 fw-bold text-danger">{{ number_format($tongPhucKhao) }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Đã tăng điểm: <strong>{{ $phucKhaoTangDiem }}</strong> đơn</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success fs-4">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Chứng nhận đã cấp</div>
                    <div class="fs-4 fw-bold text-success">{{ number_format($chungNhanDaCap) }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Tổng yêu cầu: <strong>{{ $tongChungNhan }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hàng 3: Biểu đồ Trực quan --}}
<div class="row g-3 mb-4">
    {{-- Biểu đồ Phổ điểm --}}
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Phân bố Phổ điểm thi</h6>
                <span class="badge bg-light text-dark border">Tổng {{ $tongBaiDaCham }} bài chấm</span>
            </div>
            <div class="card-body">
                <canvas id="chartPhoDiem" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- Biểu đồ Trạng thái Hồ sơ & Tỷ lệ Đạt --}}
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Tỷ lệ Kết quả thi</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="max-height: 200px; width: 100%;">
                    <canvas id="chartKetQua"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Biểu đồ Trạng thái Đăng ký --}}
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-ui-checks-grid me-2 text-info"></i>Trạng thái Hồ sơ</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="max-height: 200px; width: 100%;">
                    <canvas id="chartTrangThaiDangKy"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hàng 4: Biểu đồ Doanh thu theo tháng --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up me-2 text-warning"></i>Doanh thu Lệ phí thi theo các kỳ gần đây</h6>
    </div>
    <div class="card-body">
        <canvas id="chartDoanhThu" height="90"></canvas>
    </div>
</div>

{{-- Hàng 5: Tabs Chi tiết Chuyên sâu --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom p-0">
        <ul class="nav nav-tabs px-3" id="thongKeTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabMonThi" type="button">
                    <i class="bi bi-book me-1"></i> Thống kê theo Môn thi
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabKhoa" type="button">
                    <i class="bi bi-building me-1"></i> Thống kê theo Khoa / Viện
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabCaThi" type="button">
                    <i class="bi bi-table me-1"></i> Chi tiết từng Ca thi
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-4">
        <div class="tab-content" id="thongKeTabContent">
            {{-- Tab 1: Môn thi --}}
            <div class="tab-pane fade show active" id="tabMonThi">
                <div class="row g-4">
                    @foreach($thongKeMonThi as $key => $mon)
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold text-primary mb-0">
                                        <i class="bi {{ $key === 'cntt' ? 'bi-laptop' : 'bi-translate' }} me-2"></i>{{ $mon['ten'] }}
                                    </h5>
                                    <span class="badge bg-primary px-3 py-2 fs-6">Đạt: {{ $mon['ty_le_dat'] }}%</span>
                                </div>
                                <div class="row g-2 text-center">
                                    <div class="col-3">
                                        <div class="bg-white p-2 rounded border">
                                            <div class="small text-muted">Số ĐK</div>
                                            <div class="fw-bold fs-5 text-dark">{{ number_format($mon['so_dang_ky']) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-white p-2 rounded border">
                                            <div class="small text-muted">Dự thi</div>
                                            <div class="fw-bold fs-5 text-info">{{ number_format($mon['so_du_thi']) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-white p-2 rounded border">
                                            <div class="small text-muted">Đạt</div>
                                            <div class="fw-bold fs-5 text-success">{{ number_format($mon['so_dat']) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-white p-2 rounded border">
                                            <div class="small text-muted">Điểm TB</div>
                                            <div class="fw-bold fs-5 text-warning-emphasis">{{ $mon['diem_tb'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tab 2: Theo Khoa --}}
            <div class="tab-pane fade" id="tabKhoa">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã Khoa</th>
                                <th>Tên Khoa / Đơn vị</th>
                                <th class="text-center">Số lượt ĐK</th>
                                <th class="text-center">Số SV dự thi</th>
                                <th class="text-center">Số SV Đạt</th>
                                <th class="text-center">Tỷ lệ Đạt</th>
                                <th class="text-center">Điểm TB</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($thongKeTheoKhoa as $khoa)
                                <tr>
                                    <td><span class="badge bg-light text-dark border">{{ $khoa['ma_khoa'] }}</span></td>
                                    <td class="fw-semibold">{{ $khoa['ten_khoa'] }}</td>
                                    <td class="text-center fw-bold">{{ number_format($khoa['tong_dang_ky']) }}</td>
                                    <td class="text-center">{{ number_format($khoa['so_du_thi']) }}</td>
                                    <td class="text-center text-success fw-bold">{{ number_format($khoa['so_dat']) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                                <div class="progress-bar bg-success" style="width: {{ $khoa['ty_le_dat'] }}%"></div>
                                            </div>
                                            <span class="small fw-semibold">{{ $khoa['ty_le_dat'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center fw-semibold text-primary">{{ $khoa['diem_tb'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Chưa có dữ liệu thống kê theo Khoa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 3: Bảng Ca thi chi tiết --}}
            <div class="tab-pane fade" id="tabCaThi">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tên kỳ thi / Ca thi</th>
                                <th>Môn</th>
                                <th>Ngày thi</th>
                                <th>Phòng thi</th>
                                <th class="text-center">ĐK Hợp lệ</th>
                                <th class="text-center">Dự thi</th>
                                <th class="text-center">Đạt</th>
                                <th class="text-center">Tỷ lệ Đạt</th>
                                <th class="text-end">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($chiTietCaThi as $ca)
                                <tr>
                                    <td class="fw-semibold">{{ $ca->ten_ky_thi }}</td>
                                    <td>
                                        <span class="badge {{ $ca->loai_chung_chi === 'cntt' ? 'text-bg-primary' : 'text-bg-success' }}">
                                            {{ $ca->loai_chung_chi === 'cntt' ? 'Tin học CNTT' : 'Tiếng Anh' }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($ca->ngay_thi)->format('d/m/Y') }}</td>
                                    <td>{{ $ca->phong_thi }}</td>
                                    <td class="text-center">{{ number_format($ca->dang_ky_hop_le) }}</td>
                                    <td class="text-center">{{ number_format($ca->da_nop_bai) }}</td>
                                    <td class="text-center text-success fw-bold">{{ number_format($ca->so_dat) }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $ca->ty_le_dat >= 70 ? 'bg-success' : ($ca->ty_le_dat >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                            {{ $ca->ty_le_dat }}%
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-primary">{{ number_format($ca->doanh_thu) }} đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Không có ca thi nào phù hợp với bộ lọc.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Biểu đồ Phổ điểm
    const ctxPhoDiem = document.getElementById('chartPhoDiem');
    if (ctxPhoDiem) {
        new Chart(ctxPhoDiem, {
            type: 'bar',
            data: {
                labels: ['Yếu/Kém (<50đ)', 'Trung bình (50-64đ)', 'Khá (65-79đ)', 'Giỏi/Xuất sắc (80-100đ)'],
                datasets: [{
                    label: 'Số lượng thí sinh',
                    data: [
                        {{ $phoDiem['kem'] }},
                        {{ $phoDiem['trung_binh'] }},
                        {{ $phoDiem['kha'] }},
                        {{ $phoDiem['gioi'] }}
                    ],
                    backgroundColor: ['#dc3545', '#ffc107', '#0d6efd', '#198754'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    // 2. Biểu đồ Tỷ lệ Kết quả
    const ctxKetQua = document.getElementById('chartKetQua');
    if (ctxKetQua) {
        new Chart(ctxKetQua, {
            type: 'doughnut',
            data: {
                labels: ['Đạt chuẩn (≥50đ)', 'Không đạt (<50đ)'],
                datasets: [{
                    data: [{{ $tongDat }}, {{ $tongKhongDat }}],
                    backgroundColor: ['#198754', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // 3. Biểu đồ Trạng thái Đăng ký
    const ctxTrangThai = document.getElementById('chartTrangThaiDangKy');
    if (ctxTrangThai) {
        new Chart(ctxTrangThai, {
            type: 'doughnut',
            data: {
                labels: ['Đã duyệt', 'Chờ duyệt', 'Bổ sung', 'Từ chối', 'Đã hủy'],
                datasets: [{
                    data: [
                        {{ $dangKyDaDuyet }},
                        {{ $dangKyChoDuyet }},
                        {{ $dangKyBoSung }},
                        {{ $dangKyTuChoi }},
                        {{ $dangKyDaHuy }}
                    ],
                    backgroundColor: ['#198754', '#ffc107', '#0dcaf0', '#dc3545', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // 4. Biểu đồ Doanh thu theo tháng
    const ctxDoanhThu = document.getElementById('chartDoanhThu');
    if (ctxDoanhThu) {
        new Chart(ctxDoanhThu, {
            type: 'line',
            data: {
                labels: {!! json_encode($doanhThuTheoThang->pluck('thang')) !!},
                datasets: [{
                    label: 'Doanh thu lệ phí (VNĐ)',
                    data: {!! json_encode($doanhThuTheoThang->pluck('tong')) !!},
                    borderColor: '#00529b',
                    backgroundColor: 'rgba(0, 82, 155, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
