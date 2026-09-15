<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ thống thi chứng chỉ HVNH')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
</head>
<body>
    <div class="hvnh-topbar">
        <div class="container d-flex justify-content-between flex-wrap">
            <span>TRUNG TÂM TIN HỌC NGOẠI NGỮ - HỌC VIỆN NGÂN HÀNG</span>
            <span class="d-flex gap-3">
                <a href="mailto:trungtamtinhocngoaingu@hvnh.edu.vn">✉ trungtamtinhocngoaingu@hvnh.edu.vn</a>
                <a href="tel:02435726385">☎ 024 3572 6385</a>
            </span>
        </div>
    </div>
    <nav class="hvnh-navbar py-2">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <a href="{{ route('sinhvien.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Logo HVNH" width="40" height="46">
                <span>
                    <span class="brand-text d-block">HỌC VIỆN NGÂN HÀNG</span>
                    <span class="brand-sub">HỆ THỐNG ĐĂNG KÝ THI CHỨNG CHỈ</span>
                </span>
            </a>
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <a class="nav-link {{ request()->routeIs('sinhvien.dashboard') ? 'active' : '' }}" href="{{ route('sinhvien.dashboard') }}">Trang chủ</a>
                <a class="nav-link {{ request()->routeIs('sinhvien.dangky.cua-toi') ? 'active' : '' }}" href="{{ route('sinhvien.dangky.cua-toi') }}">Đăng ký của tôi</a>
                <a class="nav-link {{ request()->routeIs('sinhvien.thi.*') ? 'active' : '' }}" href="{{ route('sinhvien.thi.index') }}">Thi</a>
                <a class="nav-link {{ request()->routeIs('sinhvien.ketqua.*') ? 'active' : '' }}" href="{{ route('sinhvien.ketqua.index') }}">Kết quả</a>
                <a class="nav-link {{ request()->routeIs('sinhvien.phuc-khao.*') ? 'active' : '' }}" href="{{ route('sinhvien.phuc-khao.index') }}">Phúc khảo</a>
                <a class="nav-link {{ request()->routeIs('sinhvien.chung-nhan.*') ? 'active' : '' }}" href="{{ route('sinhvien.chung-nhan.index') }}">Chứng nhận</a>
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item">Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="hvnh-page-title">
        <div class="container">
            <h1>@yield('title', 'Trang chủ')</h1>
        </div>
    </div>

    <div class="container pb-5">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any() && empty($hideErrorSummary))
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>

    <footer class="hvnh-footer">
        <div class="container">
            <div class="row g-4">
                {{-- Cột 1: Thông tin Học viện Ngân hàng & Trung tâm --}}
                <div class="col-lg-5 col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo HVNH" width="44" height="50">
                        <div>
                            <div class="fw-bold fs-5 text-white" style="line-height: 1.2; letter-spacing: 0.5px;">HỌC VIỆN NGÂN HÀNG</div>
                            <div class="small text-white-50" style="font-size: 0.72rem; letter-spacing: 0.8px;">BANKING ACADEMY OF VIETNAM</div>
                        </div>
                    </div>
                    <div class="fw-bold text-white mb-3" style="font-size: 0.95rem; letter-spacing: 0.5px;">
                        TRUNG TÂM TIN HỌC NGOẠI NGỮ
                    </div>
                    <ul class="list-unstyled small text-white-50 mb-0 d-flex flex-column gap-2">
                        <li>
                            <span class="me-1" style="color: #ef4444;">🎯</span>
                            <span class="text-white">Địa chỉ:</span> 12 Chùa Bộc, Kim Liên, Hà Nội, Việt Nam
                        </li>
                        <li>
                            <span class="me-1" style="color: #22c55e;">📞</span>
                            <span class="text-white">SĐT:</span> <a href="tel:02435726385" class="text-white-50">+84 (0)24 3572 6385</a>
                        </li>
                        <li>
                            <span class="me-1" style="color: #38bdf8;">✉</span>
                            <a href="mailto:trungtamtinhocngoaingu@hvnh.edu.vn" class="text-white-50">trungtamtinhocngoaingu@hvnh.edu.vn</a>
                        </li>
                    </ul>
                </div>

                {{-- Cột 2: HƯỚNG DẪN (Đã bỏ: Hướng dẫn đăng ký thi/ôn thi các kỳ thi) --}}
                <div class="col-lg-4 col-md-3 ps-lg-4">
                    <div class="footer-title">HƯỚNG DẪN</div>
                    <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                        <li><a href="{{ route('sinhvien.dashboard') }}">Tra cứu phòng thi</a></li>
                        <li><a href="{{ route('sinhvien.ketqua.index') }}">Tra cứu kết quả thi</a></li>
                        <li><a href="{{ route('sinhvien.chung-nhan.index') }}">Tra cứu chứng chỉ</a></li>
                        <li><a href="#">Hướng dẫn đăng ký/đăng nhập tài khoản</a></li>
                    </ul>
                </div>

                {{-- Cột 3: LIÊN KẾT WEBSITE --}}
                <div class="col-lg-3 col-md-3">
                    <div class="footer-title">LIÊN KẾT WEBSITE</div>
                    <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                        <li><a href="https://hvnh.edu.vn" target="_blank" rel="noopener noreferrer">Học Viện Ngân Hàng</a></li>
                        <li><a href="https://ttbd.hvnh.edu.vn" target="_blank" rel="noopener noreferrer">Trường Đào tạo Bồi dưỡng Cán bộ</a></li>
                        <li><a href="https://moet.gov.vn" target="_blank" rel="noopener noreferrer">Bộ giáo dục &amp; đào tạo</a></li>
                    </ul>
                </div>
            </div>

            {{-- Dòng bản quyền --}}
            <hr class="hvnh-footer-divider">
            <div class="text-center small text-white-50">
                @2025 - Bản quyền thuộc về Trung Tâm Tin Học Ngoại Ngữ - Học Viện Ngân Hàng.
            </div>
        </div>

        {{-- Nút cuộn lên đầu trang --}}
        <a href="#" class="scroll-to-top-btn" onclick="window.scrollTo({top:0, behavior:'smooth'}); return false;" title="Lên đầu trang">
            ▲
        </a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
