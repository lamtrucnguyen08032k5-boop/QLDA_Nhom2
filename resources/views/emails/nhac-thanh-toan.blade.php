<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f2f5fa; margin:0; padding:24px;">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:10px;overflow:hidden;border:1px solid #dbe4f0;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
    <div style="background:#0d2b57;padding:20px 28px;">
        <span style="color:#fff;font-size:18px;font-weight:700;letter-spacing:0.5px;">HỌC VIỆN NGÂN HÀNG</span><br>
        <span style="color:#bcd2f0;font-size:12px;">Trung tâm Tin học Ngoại ngữ — Hệ thống thi chứng chỉ</span>
    </div>
    <div style="padding:28px;color:#1f2937;">
        <div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:14px 16px;border-radius:4px;margin-bottom:20px;">
            <h3 style="color:#b45309;margin:0 0 6px 0;font-size:16px;">⚠️ CẢNH BÁO: SẮP HẾT HẠN NỘP LỆ PHÍ THI</h3>
            <p style="margin:0;font-size:14px;color:#92400e;">
                Hồ sơ đăng ký của bạn còn dưới <strong>12 giờ</strong> trước khi hết hạn nộp lệ phí. Sau thời hạn này, hồ sơ sẽ tự động bị huỷ!
            </p>
        </div>

        <p>Xin chào <strong>{{ $dangKy->sinhVien->name }}</strong>,</p>
        <p>Hệ thống ghi nhận bạn đã đăng ký dự thi bài thi <strong>{{ $dangKy->lichThi->ten_ky_thi }}</strong> nhưng hiện tại chưa hoàn tất thanh toán lệ phí thi.</p>

        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:16px;margin:20px 0;">
            <table style="width:100%;font-size:14px;border-collapse:collapse;">
                <tr>
                    <td style="padding:6px 0;color:#64748b;width:150px;">Mã đăng ký:</td>
                    <td style="padding:6px 0;font-weight:bold;color:#0d2b57;">{{ $dangKy->ma_dang_ky }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#64748b;">Kỳ thi:</td>
                    <td style="padding:6px 0;font-weight:bold;">{{ $dangKy->lichThi->ten_ky_thi }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#64748b;">Thời gian thi:</td>
                    <td style="padding:6px 0;">{{ optional($dangKy->lichThi->ngay_thi)->format('d/m/Y') }} ({{ $dangKy->lichThi->gio_bat_dau }})</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#64748b;">Phòng thi:</td>
                    <td style="padding:6px 0;">{{ $dangKy->lichThi->phong_thi }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#64748b;">Lệ phí thi:</td>
                    <td style="padding:6px 0;font-weight:bold;color:#16a34a;font-size:16px;">{{ number_format($dangKy->so_tien) }} VNĐ</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#b91c1c;font-weight:bold;">Hạn chót thanh toán:</td>
                    <td style="padding:6px 0;font-weight:bold;color:#b91c1c;font-size:15px;">{{ $dangKy->hanThanhToan()->format('H:i \n\g\à\y d/m/Y') }}</td>
                </tr>
            </table>
        </div>

        <div style="text-align:center;margin:30px 0;">
            <a href="{{ route('sinhvien.dangky.buoc3', $dangKy) }}"
               style="background:#0d2b57;color:#ffffff;padding:12px 28px;font-size:15px;font-weight:bold;text-decoration:none;border-radius:6px;display:inline-block;">
                Tiến hành thanh toán ngay →
            </a>
        </div>

        <p style="font-size:13px;color:#dc2626;background:#fef2f2;padding:10px 14px;border-radius:4px;">
            * Lưu ý: Nếu không hoàn tất thanh toán trước <strong>{{ $dangKy->hanThanhToan()->format('H:i d/m/Y') }}</strong>, hệ thống sẽ tự động huỷ đăng ký của bạn. Bạn vẫn có thể đăng ký lại nếu kỳ thi còn chỉ tiêu và còn thời hạn đăng ký.
        </p>

        <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">
        <p style="font-size:12px;color:#94a3b8;margin:0;">
            Đây là email tự động từ Hệ thống đăng ký thi chứng chỉ Học viện Ngân hàng. Vui lòng không trả lời thư này.<br>
            Hotline hỗ trợ: 024 3572 6385 | Email: trungtamtinhocngoaingu@hvnh.edu.vn
        </p>
    </div>
</div>
</body>
</html>
