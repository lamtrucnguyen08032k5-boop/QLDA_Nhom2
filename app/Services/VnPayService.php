<?php

namespace App\Services;

/**
 * Tích hợp cổng thanh toán VNPAY.
 *
 * Hệ thống hỗ trợ 2 phương thức thanh toán cho sinh viên:
 *  - "vnpay": thanh toán qua VNPAY-QR / ví điện tử, thẻ quốc tế (vnp_BankCode để trống).
 *  - "napas": thanh toán bằng thẻ ATM nội địa qua liên minh thẻ Napas
 *             (vnp_BankCode = VNBANK - VNPAY định tuyến các giao dịch thẻ nội địa qua Napas).
 *
 * Cấu hình lấy từ config('services.vnpay'). Đây là code tích hợp đầy đủ theo tài liệu
 * VNPAY; để chạy thật cần điền vnp_TmnCode/vnp_HashSecret thật trong .env. Nếu chưa có
 * thông tin merchant (môi trường demo), hệ thống sẽ tự chuyển sang cổng mô phỏng nội bộ
 * (xem routes "thanh-toan/mo-phong") để vẫn có thể trình diễn được toàn bộ luồng.
 */
class VnPayService
{
    public function daCauHinh(): bool
    {
        return filled(config('services.vnpay.tmn_code')) && filled(config('services.vnpay.hash_secret'));
    }

    public function taoUrlThanhToan(string $maGiaoDich, int $soTien, string $noiDung, string $phuongThuc, ?string $returnUrl = null): string
    {
        $vnpUrl = config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => config('services.vnpay.tmn_code'),
            'vnp_Amount' => $soTien * 100, // VNPAY yêu cầu nhân 100
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => request()->ip(),
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => $noiDung,
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $returnUrl ?: (config('services.vnpay.return_url') ?: route('sinhvien.dangky.thanhtoan.vnpay.return')),
            'vnp_TxnRef' => $maGiaoDich,
        ];

        // NAPAS: ép luồng thanh toán thẻ ATM nội địa (định tuyến qua liên minh thẻ Napas)
        if ($phuongThuc === 'napas') {
            $inputData['vnp_BankCode'] = 'VNBANK';
        }

        ksort($inputData);

        $hashData = '';
        $query = '';
        foreach ($inputData as $key => $value) {
            $hashData .= (empty($hashData) ? '' : '&') . urlencode($key) . '=' . urlencode((string) $value);
            $query .= urlencode($key) . '=' . urlencode((string) $value) . '&';
        }
        $query = rtrim($query, '&');

        $secureHash = hash_hmac('sha512', $hashData, (string) config('services.vnpay.hash_secret'));

        return $vnpUrl . '?' . $query . '&vnp_SecureHash=' . $secureHash;
    }

    public function xacThucChuKy(array $params): bool
    {
        $secureHash = $params['vnp_SecureHash'] ?? '';
        $vnpParams = [];
        foreach ($params as $key => $value) {
            if (str_starts_with($key, 'vnp_')) {
                $vnpParams[$key] = $value;
            }
        }
        unset($vnpParams['vnp_SecureHash'], $vnpParams['vnp_SecureHashType']);
        ksort($vnpParams);

        $hashData = '';
        foreach ($vnpParams as $key => $value) {
            $hashData .= (empty($hashData) ? '' : '&') . urlencode($key) . '=' . urlencode((string) $value);
        }

        $checkHash = hash_hmac('sha512', $hashData, (string) config('services.vnpay.hash_secret'));

        return hash_equals(strtolower($checkHash), strtolower((string) $secureHash));
    }
}
