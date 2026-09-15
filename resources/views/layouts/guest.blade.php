<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo HVNH" width="60" height="70" class="mb-2">
                    <h4 class="fw-bold text-primary mb-0">HỌC VIỆN NGÂN HÀNG</h4>
                    <p class="text-muted">Hệ thống đăng ký thi chứng chỉ CNTT &amp; Tiếng Anh đầu ra</p>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body p-4">
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
                </div>
            </div>
        </div>
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
</body>
</html>
