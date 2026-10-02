@extends('layouts.app')
@section('title', 'Tổng quan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-primary mb-1">Bảng điều khiển Quản trị viên</h4>
        <p class="text-muted mb-0 small">Tổng quan tình hình khảo thí và hoạt động hệ thống</p>
    </div>
    <a href="{{ route('admin.thongke.index') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
        Xem Thống kê chi tiết
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary"><div class="card-body">
            <div class="small">Tổng số lịch thi</div>
            <div class="fs-3 fw-bold">{{ $tongLichThi }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success"><div class="card-body">
            <div class="small">Đăng ký đã duyệt</div>
            <div class="fs-3 fw-bold">{{ $tongDangKyDaDuyet }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info"><div class="card-body">
            <div class="small">Số thí sinh đã thi</div>
            <div class="fs-3 fw-bold">{{ $tongDaThi }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning"><div class="card-body">
            <div class="small">Tổng doanh thu lệ phí</div>
            <div class="fs-4 fw-bold">{{ number_format($tongDoanhThu) }} đ</div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-bar-chart-line text-primary me-2"></i>Doanh thu theo tháng
            </div>
            <div class="card-body">
                <div style="min-height: 260px; max-height: 280px; position: relative;">
                    <canvas id="chartDoanhThu"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-pie-chart text-success me-2"></i>Số sinh viên theo khoá học
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="max-height: 260px; width: 100%; position: relative;">
                    <canvas id="chartKhoaHoc"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white fw-bold py-3">
        <i class="bi bi-calendar3 text-info me-2"></i>Số thí sinh theo ca thi gần đây
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tên kỳ thi</th>
                        <th>Ngày thi</th>
                        <th>Khoa</th>
                        <th class="text-center">Số thí sinh</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($thiSinhTheoCa as $ca)
                    <tr>
                        <td class="fw-semibold">{{ $ca->ten_ky_thi }}</td>
                        <td>{{ \Carbon\Carbon::parse($ca->ngay_thi)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $ca->khoa->ten_khoa ?? 'N/A' }}</span></td>
                        <td class="text-center"><span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2">{{ $ca->so_thi_sinh }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">Chưa có ca thi nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctxDoanhThu = document.getElementById('chartDoanhThu');
    if (ctxDoanhThu) {
        new Chart(ctxDoanhThu, {
            type: 'bar',
            data: {
                labels: {!! json_encode($doanhThuTheoThang->pluck('thang')) !!},
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: {!! json_encode($doanhThuTheoThang->pluck('tong')) !!},
                    backgroundColor: '#0d6efd',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) {
                                return val.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });
    }

    const ctxKhoaHoc = document.getElementById('chartKhoaHoc');
    if (ctxKhoaHoc) {
        new Chart(ctxKhoaHoc, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($sinhVienTheoKhoaHoc->pluck('khoa_hoc')->map(fn($k) => $k ?: 'Khác')) !!},
                datasets: [{
                    data: {!! json_encode($sinhVienTheoKhoaHoc->pluck('so_luong')) !!},
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997', '#fd7e14']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endsection
