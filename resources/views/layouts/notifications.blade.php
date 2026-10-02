<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Tuỳ biến giao diện Toast & Popup SweetAlert2 đồng bộ hệ thống */
    .swal2-toast {
        font-family: inherit !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        border-radius: 10px !important;
        padding: 12px 16px !important;
    }
    .swal2-popup {
        font-family: inherit !important;
        border-radius: 12px !important;
    }
    .swal2-title {
        font-size: 1.25rem !important;
        font-weight: 600 !important;
    }
    .swal2-html-container {
        font-size: 0.95rem !important;
    }
    .swal2-confirm {
        border-radius: 6px !important;
        font-weight: 500 !important;
        padding: 8px 20px !important;
    }
    .swal2-cancel {
        border-radius: 6px !important;
        font-weight: 500 !important;
        padding: 8px 20px !important;
    }
</style>

<script>
    // Khởi tạo Toast chuẩn của hệ thống
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Helper functions toàn cục
    window.AppNotify = {
        toast: function(icon, message) {
            return Toast.fire({
                icon: icon || 'info',
                title: message
            });
        },
        success: function(message, title = 'Thành công') {
            return Toast.fire({
                icon: 'success',
                title: message
            });
        },
        error: function(message, title = 'Có lỗi xảy ra') {
            return Toast.fire({
                icon: 'error',
                title: message
            });
        },
        warning: function(message, title = 'Cảnh báo') {
            return Toast.fire({
                icon: 'warning',
                title: message
            });
        },
        info: function(message, title = 'Thông báo') {
            return Toast.fire({
                icon: 'info',
                title: message
            });
        },
        alert: function(message, title = 'Thông báo', icon = 'info') {
            return Swal.fire({
                title: title,
                text: message,
                icon: icon,
                confirmButtonColor: '#00529b',
                confirmButtonText: 'Đã hiểu'
            });
        },
        confirm: function(message, onConfirm, onCancel, options = {}) {
            return Swal.fire({
                title: options.title || 'Xác nhận thao tác',
                text: message,
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonColor: options.confirmColor || '#00529b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: options.confirmText || 'Đồng ý',
                cancelButtonText: options.cancelText || 'Hủy bỏ',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof onConfirm === 'function') onConfirm();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    if (typeof onCancel === 'function') onCancel();
                }
            });
        }
    };

    // Override mặc định window.alert để ngăn hoàn toàn popup xấu của browser
    window.alert = function(message) {
        return AppNotify.alert(message, 'Thông báo hệ thống');
    };
    window.showToast = AppNotify.toast;
    window.showAlert = AppNotify.alert;
    window.showConfirm = AppNotify.confirm;

    // Tự động bắt tất cả các form hoặc button có thuộc tính data-confirm
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('submit', function(e) {
            const form = e.target;
            const confirmMsg = form.getAttribute('data-confirm');
            if (confirmMsg && !form.dataset.confirmed) {
                e.preventDefault();
                AppNotify.confirm(confirmMsg, function() {
                    form.dataset.confirmed = "true";
                    form.submit();
                });
            }
        });

        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-confirm]');
            if (!btn) return;
            
            // Nếu là nút submit trong form mà form chưa có data-confirm, áp dụng cho form đó
            const form = btn.closest('form');
            if (form && (btn.type === 'submit' || btn.tagName === 'BUTTON')) {
                const confirmMsg = btn.getAttribute('data-confirm');
                if (!form.dataset.confirmed) {
                    e.preventDefault();
                    AppNotify.confirm(confirmMsg, function() {
                        form.dataset.confirmed = "true";
                        if (btn.name && btn.value) {
                            const hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = btn.name;
                            hidden.value = btn.value;
                            form.appendChild(hidden);
                        }
                        form.submit();
                    });
                }
            } else if (btn.tagName === 'A' && btn.href && !btn.dataset.confirmed) {
                // Nếu là thẻ link <a>
                e.preventDefault();
                const confirmMsg = btn.getAttribute('data-confirm');
                AppNotify.confirm(confirmMsg, function() {
                    btn.dataset.confirmed = "true";
                    window.location.href = btn.href;
                });
            }
        });
    });

    // Xử lý flash messages từ Laravel session
    @if (session('status') || session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            AppNotify.success("{{ session('status') ?? session('success') }}");
        });
    @endif

    @if (session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            AppNotify.error("{{ session('error') }}");
        });
    @endif

    @if (session('warning'))
        document.addEventListener('DOMContentLoaded', function() {
            AppNotify.warning("{{ session('warning') }}");
        });
    @endif

    @if (session('info'))
        document.addEventListener('DOMContentLoaded', function() {
            AppNotify.info("{{ session('info') }}");
        });
    @endif

    @if (isset($errors) && $errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            let errorHtml = '<ul class="text-start mb-0 ps-3">';
            @foreach ($errors->all() as $error)
                errorHtml += '<li>{{ addslashes($error) }}</li>';
            @endforeach
            errorHtml += '</ul>';

            Swal.fire({
                title: 'Có lỗi trong dữ liệu nhập!',
                html: errorHtml,
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Đã hiểu'
            });
        });
    @endif
</script>
