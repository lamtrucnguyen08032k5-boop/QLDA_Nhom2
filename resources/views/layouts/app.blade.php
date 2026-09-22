<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ thống thi chứng chỉ HVNH')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    <style> body { min-height: 100vh; } </style>
</head>
<body>
<div class="d-flex">
    <nav class="sidebar" style="width:250px;">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" width="30" height="35">
            <span>HVNH Khảo thí</span>
        </div>
        <div class="px-2">
            @php $u = auth()->user(); @endphp
            @if ($u->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Tổng quan</a>
                <a href="{{ route('admin.khoa.index') }}" class="{{ request()->routeIs('admin.khoa.*') ? 'active' : '' }}">Quản lý Khoa</a>
                <a href="{{ route('admin.svwhitelist.index') }}" class="{{ request()->routeIs('admin.svwhitelist.*') ? 'active' : '' }}">Kho email Sinh viên</a>
                <a href="{{ route('admin.lichthi.index') }}" class="{{ request()->routeIs('admin.lichthi.*') ? 'active' : '' }}">Quản lý kỳ thi</a>
                <a href="{{ route('admin.dangky.danhsach') }}" class="{{ request()->routeIs('admin.dangky.*') ? 'active' : '' }}">Danh sách đăng ký thi</a>
                <a href="{{ route('admin.dethi.index') }}" class="{{ request()->routeIs('admin.dethi.*') ? 'active' : '' }}">Kho đề thi</a>
                <a href="{{ route('admin.tochuc.index') }}" class="{{ request()->routeIs('admin.tochuc.*') ? 'active' : '' }}">Tổ chức thi</a>
                <a href="{{ route('admin.chamthi.tiendo') }}" class="{{ request()->routeIs('admin.chamthi.*') ? 'active' : '' }}">Tiến độ chấm</a>
                <a href="{{ route('admin.ketqua.index') }}" class="{{ request()->routeIs('admin.ketqua.*') ? 'active' : '' }}">Kết quả thi</a>
                <a href="{{ route('admin.phuckhao.index') }}" class="{{ request()->routeIs('admin.phuckhao.*') ? 'active' : '' }}">Phúc khảo</a>
                <a href="{{ route('admin.chungnhan.index') }}" class="{{ request()->routeIs('admin.chungnhan.*') ? 'active' : '' }}">Chứng nhận</a>
            @elseif ($u->role === 'khoa')
                <a href="{{ route('khoa.dashboard') }}" class="{{ request()->routeIs('khoa.dashboard') ? 'active' : '' }}">Tổng quan</a>
                <a href="{{ route('khoa.giangvien.index') }}" class="{{ request()->routeIs('khoa.giangvien.*') ? 'active' : '' }}">Giảng viên</a>
                <a href="{{ route('khoa.tiendocham') }}" class="{{ request()->routeIs('khoa.tiendocham') ? 'active' : '' }}">Tiến độ chấm</a>
            @elseif ($u->role === 'giangvien')
                <a href="{{ route('giangvien.dashboard') }}" class="{{ request()->routeIs('giangvien.dashboard') ? 'active' : '' }}">Tổng quan</a>
                <a href="{{ route('giangvien.cham-thi.index') }}" class="{{ request()->routeIs('giangvien.cham-thi.*') ? 'active' : '' }}">Chấm bài thi</a>
                <a href="{{ route('giangvien.phuc-khao.index') }}" class="{{ request()->routeIs('giangvien.phuc-khao.*') ? 'active' : '' }}">Xử lý phúc khảo</a>
            @else
                <a href="{{ route('sinhvien.dashboard') }}" class="{{ request()->routeIs('sinhvien.dashboard') ? 'active' : '' }}">Tổng quan</a>
                <a href="{{ route('sinhvien.dangky.index') }}" class="{{ request()->routeIs('sinhvien.dangky.*') ? 'active' : '' }}">Đăng ký thi</a>
                <a href="{{ route('sinhvien.thi.index') }}" class="{{ request()->routeIs('sinhvien.thi.*') ? 'active' : '' }}">Thi</a>
                <a href="{{ route('sinhvien.ketqua.index') }}" class="{{ request()->routeIs('sinhvien.ketqua.*') ? 'active' : '' }}">Kết quả</a>
                <a href="{{ route('sinhvien.phuc-khao.index') }}" class="{{ request()->routeIs('sinhvien.phuc-khao.*') ? 'active' : '' }}">Phúc khảo</a>
                <a href="{{ route('sinhvien.chung-nhan.index') }}" class="{{ request()->routeIs('sinhvien.chung-nhan.*') ? 'active' : '' }}">Chứng nhận</a>
            @endif
        </div>
    </nav>
    <main class="flex-grow-1">
        <nav class="navbar navbar-light bg-white border-bottom px-4">
            <span class="fw-semibold text-primary">@yield('title', 'Trang chủ')</span>
            <div class="d-flex align-items-center gap-3">
                @php
                    $unreadCount = auth()->user()->unreadNotifications->count();
                    $notifications = auth()->user()->notifications()->take(6)->get();
                @endphp
                <div class="dropdown">
                    <button class="btn btn-light position-relative p-1 px-2 border" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo">
                        <i class="bi bi-bell fs-5 text-secondary"></i>
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm p-0" style="width: 330px; max-height: 420px; overflow-y: auto;">
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center bg-light">
                            <span class="fw-bold small text-dark"><i class="bi bi-bell me-1 text-primary"></i>Thông báo</span>
                            @if($unreadCount > 0)
                                <form method="POST" action="{{ route('notifications.read-all') }}" class="m-0">
                                    @csrf
                                    <button class="btn btn-link p-0 small text-decoration-none" style="font-size: 0.75rem;">Đánh dấu đã đọc</button>
                                </form>
                            @endif
                        </li>
                        @forelse($notifications as $notif)
                            @php
                                $d = $notif->data;
                                $isUnread = is_null($notif->read_at);
                            @endphp
                            <li class="border-bottom {{ $isUnread ? 'bg-light bg-opacity-50' : '' }}">
                                <form method="POST" action="{{ route('notifications.read', $notif->id) }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-wrap py-2 px-3 text-start">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="bi {{ $d['icon'] ?? 'bi-bell' }} text-primary mt-1"></i>
                                            <div class="flex-grow-1">
                                                <div class="small fw-semibold {{ $isUnread ? 'text-primary' : 'text-dark' }}">{{ $d['tieu_de'] ?? 'Thông báo' }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $d['noi_dung'] ?? '' }}</div>
                                                <div class="text-secondary opacity-75 mt-1" style="font-size: 0.7rem;">{{ $notif->created_at->locale('vi')->diffForHumans() }}</div>
                                            </div>
                                            @if($isUnread)
                                                <span class="p-1 bg-primary rounded-circle mt-1" title="Chưa đọc"></span>
                                            @endif
                                        </div>
                                    </button>
                                </form>
                            </li>
                        @empty
                            <li class="p-4 text-center text-muted small">
                                <i class="bi bi-bell-slash fs-4 d-block mb-1 text-secondary opacity-50"></i>
                                Không có thông báo mới
                            </li>
                        @endforelse
                    </ul>
                </div>

                <span class="text-muted small">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm">Đăng xuất</button>
                </form>
            </div>
        </nav>
        <div class="p-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
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
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.toggle-password');
            if (!btn) return;
            
            const group = btn.closest('.input-group') || btn.parentElement;
            const input = group ? group.querySelector('input') : null;
            if (!input) return;

            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });
</script>
@yield('scripts')
</body>
</html>
