# Hệ thống Đăng ký & Tổ chức thi Chứng chỉ HVNH

Hệ thống được xây dựng bằng **Laravel**, phục vụ việc quản lý toàn bộ quy trình đăng ký, tổ chức thi, chấm thi, công bố kết quả, phúc khảo và cấp chứng nhận tại **Học viện Ngân hàng (HVNH)**.

Hệ thống hỗ trợ **04 nhóm người dùng** và **09 phân hệ nghiệp vụ chính**, đồng thời tích hợp cơ chế phân quyền, đăng ký sinh viên bằng email trường, quản lý hồ sơ dự thi, thanh toán lệ phí và gửi email thông báo tự động.

---

## 1. Tổng quan hệ thống

### Công nghệ sử dụng

* **Backend:** Laravel
* **Ngôn ngữ:** PHP >= 8.2
* **Database:** SQLite / MySQL
* **Frontend:** Blade Template, Bootstrap 5
* **Biểu đồ:** Chart.js
* **Thanh toán:** VNPAY / NAPAS
* **Email:** Laravel Mail
* **Phân quyền:** Middleware theo vai trò người dùng

### Các vai trò trong hệ thống

Hệ thống gồm 04 nhóm người dùng:

1. **Admin / Phòng Khảo thí**
2. **Khoa**
3. **Giảng viên**
4. **Sinh viên**

Mỗi vai trò được phân quyền truy cập các chức năng tương ứng thông qua Middleware của Laravel.

---

## 2. Cấu trúc Project

Project sử dụng cấu trúc chuẩn của Laravel:

```text
hvnh-exam-app/
│
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
│
├── artisan
├── composer.json
├── composer.lock
├── .env
└── README.md
```

Trong đó:

* `app/`: chứa Models, Controllers, Middleware và logic nghiệp vụ.
* `database/`: chứa Migration, Seeder và Factory.
* `resources/`: chứa giao diện Blade.
* `routes/`: khai báo các route của hệ thống.
* `public/`: chứa CSS, JavaScript, hình ảnh và các tài nguyên public.
* `storage/`: lưu log, file upload và dữ liệu tạm.
* `config/`: chứa các file cấu hình của Laravel.

> Thư mục `vendor/` không được đưa lên GitHub vì có dung lượng lớn và đã được Laravel mặc định khai báo trong `.gitignore`. Sau khi clone project, thư mục này sẽ được tạo bằng lệnh `composer install`.

---

## 3. Yêu cầu môi trường

Trước khi chạy project, cần cài đặt:

* PHP >= 8.2
* Composer
* SQLite hoặc MySQL
* Git
* Trình duyệt web

Nếu sử dụng MySQL có thể quản lý cơ sở dữ liệu bằng:

* phpMyAdmin
* MySQL Workbench
* DBeaver

Project sử dụng **Bootstrap 5** và **Chart.js thông qua CDN**, do đó không bắt buộc phải chạy `npm install`.

---

## 4. Cài đặt và chạy Project

### Bước 1: Clone Repository

```bash
git clone https://github.com/lamtrucnguyen08032k5-boop/QLDA_Nhom2.git
```

Di chuyển vào thư mục project:

```bash
cd QLDA_Nhom2
```

Hoặc nếu sử dụng thư mục project có tên khác:

```bash
cd hvnh-exam-app
```

---

### Bước 2: Cài đặt thư viện PHP

```bash
composer install
```

Lệnh trên sẽ tải Laravel Framework và các thư viện cần thiết vào thư mục:

```text
vendor/
```

---

### Bước 3: Cấu hình file `.env`

Nếu project chưa có file `.env`, tạo từ file mẫu:

```bash
cp .env.example .env
```

Trên Windows có thể sử dụng:

```bash
copy .env.example .env
```

Sau đó tạo `APP_KEY`:

```bash
php artisan key:generate
```

---

### Bước 4: Khởi tạo cơ sở dữ liệu

Nếu chạy project lần đầu:

```bash
php artisan migrate --seed
```

Lệnh trên sẽ:

* Tạo toàn bộ bảng dữ liệu.
* Tạo dữ liệu mẫu.
* Tạo tài khoản mẫu cho Admin, Khoa và Giảng viên.
* Tạo sẵn email sinh viên để kiểm thử chức năng đăng ký.

---

### Bước 5: Tạo Storage Link

Project có chức năng upload ảnh hồ sơ và ảnh CCCD, vì vậy cần chạy:

```bash
php artisan storage:link
```

Lệnh này tạo liên kết:

```text
storage/app/public
        ↓
public/storage
```

Nếu không thực hiện bước này, hình ảnh sinh viên upload có thể không hiển thị trên giao diện.

---

### Bước 6: Chạy Server

```bash
php artisan serve
```

Sau đó truy cập:

```text
http://127.0.0.1:8000
```

---

## 5. Các lệnh cài đặt nhanh

Đối với máy mới clone project:

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Nếu project đã có database và chỉ cập nhật phiên bản mới:

```bash
composer install
php artisan migrate
php artisan storage:link
php artisan serve
```

---

## 6. Tài khoản kiểm thử

Sau khi chạy:

```bash
php artisan migrate --seed
```

có thể sử dụng các tài khoản sau.

| Vai trò                | Email                        | Mật khẩu        |
| ---------------------- | ---------------------------- | --------------- |
| Admin / Phòng Khảo thí | `admin@hvnh.edu.vn`          | `Admin@123`     |
| Khoa CNTT              | `khoa.cntt@hvnh.edu.vn`      | `Khoa@123`      |
| Khoa Ngoại ngữ         | `khoa.nn@hvnh.edu.vn`        | `Khoa@123`      |
| Giảng viên Khoa CNTT   | `giangvien.cntt@hvnh.edu.vn` | `GiangVien@123` |

> Các tài khoản trên chỉ sử dụng cho mục đích phát triển và kiểm thử. Khi triển khai thực tế cần thay đổi mật khẩu mặc định.

---

## 7. Đăng ký tài khoản Sinh viên

Sinh viên **không được Admin tạo tài khoản trực tiếp** mà thực hiện đăng ký bằng email trường.

Trang đăng ký:

```text
/dang-ky
```

Hệ thống chỉ cho phép đăng ký nếu email tồn tại trong **Kho email Sinh viên**.

Email mẫu đã được Seeder tạo sẵn:

```text
sv22a4000001@hvnh.edu.vn
```

Quy trình đăng ký:

```text
Sinh viên nhập thông tin
        ↓
Kiểm tra email
        ↓
Email có trong Kho email Sinh viên?
        ↓
      Có
        ↓
Tạo tài khoản
        ↓
Xác minh email
        ↓
Đăng nhập hệ thống
```

Admin có thể quản lý danh sách email sinh viên tại chức năng:

```text
Kho email Sinh viên
```

---

# 8. Các phân hệ chức năng

Hệ thống gồm **09 phân hệ nghiệp vụ chính**.

## 8.1. Xác thực và quản lý tài khoản

Bao gồm:

* Đăng ký tài khoản sinh viên.
* Kiểm tra email sinh viên trong whitelist.
* Xác minh email.
* Đăng nhập.
* Đăng xuất.
* Quên mật khẩu.
* Đặt lại mật khẩu.
* Admin tạo tài khoản Khoa.
* Admin tạo tài khoản Giảng viên.
* Phân quyền người dùng.

---

## 8.2. Quản lý kỳ thi

Admin có thể:

* Tạo kỳ thi.
* Tạo lịch thi.
* Cập nhật lịch thi.
* Quản lý ca thi.
* Quản lý địa điểm/phòng thi.
* Theo dõi trạng thái kỳ thi.

Mã ca thi được hệ thống tự động sinh khi tạo ca thi.

---

## 8.3. Quản lý kho đề thi

Khoa/Giảng viên có thể:

* Thêm câu hỏi thủ công.
* Cập nhật câu hỏi.
* Xóa câu hỏi.
* Phân loại câu hỏi.
* Tạo đề thi.
* Import danh sách câu hỏi bằng CSV.
* Quản lý câu hỏi trắc nghiệm.
* Quản lý câu hỏi tự luận.

---

## 8.4. Đăng ký dự thi

Sinh viên có thể:

* Xem lịch thi.
* Đăng ký thi.
* Nhập thông tin hồ sơ.
* Upload ảnh hồ sơ.
* Upload ảnh CCCD.
* Xác nhận thông tin.
* Thanh toán lệ phí.
* Theo dõi trạng thái đăng ký.
* Hủy đăng ký nếu đủ điều kiện.
* Tra cứu hồ sơ đã đăng ký.

Admin có thể:

* Xem danh sách đăng ký.
* Duyệt hồ sơ.
* Từ chối hồ sơ.
* Yêu cầu sinh viên bổ sung hồ sơ.

---

## 8.5. Tổ chức thi

Hệ thống hỗ trợ:

* Admin bắt đầu ca thi.
* Sinh viên nhập mã vào thi.
* Kiểm tra điều kiện dự thi.
* Làm bài trực tuyến.
* Hiển thị bộ đếm thời gian.
* Tự động nộp bài khi hết thời gian.
* Tự động chấm câu hỏi trắc nghiệm.
* Lưu bài thi của sinh viên.

---

## 8.6. Chấm thi

Giảng viên có thể:

* Xem danh sách bài thi.
* Chấm câu hỏi tự luận.
* Nhập điểm.
* Cập nhật điểm.

Khoa và Admin có thể:

* Theo dõi tiến độ chấm thi.
* Theo dõi trạng thái bài thi.
* Kiểm tra kết quả chấm.

---

## 8.7. Quản lý kết quả

Bao gồm:

* Tổng hợp điểm.
* Công bố kết quả.
* Sinh viên tra cứu kết quả.
* Xem chi tiết điểm.
* Phân biệt điểm trắc nghiệm và tự luận.
* Xác định trạng thái đạt/không đạt.

---

## 8.8. Phúc khảo

Sinh viên có thể:

* Gửi yêu cầu phúc khảo.
* Nhập lý do phúc khảo.
* Theo dõi trạng thái xử lý.

Khoa/Giảng viên/Admin có thể:

* Tiếp nhận yêu cầu.
* Kiểm tra bài thi.
* Xử lý phúc khảo.
* Điều chỉnh điểm nếu cần.
* Công bố kết quả sau phúc khảo.

---

## 8.9. Quản lý chứng nhận

Sinh viên đạt yêu cầu có thể:

* Đăng ký nhận chứng nhận.
* Theo dõi trạng thái cấp chứng nhận.

Admin có thể:

* Duyệt yêu cầu.
* Cấp số chứng nhận.
* Quản lý danh sách chứng nhận.
* Theo dõi trạng thái cấp chứng nhận.

---

# 9. Quy trình đăng ký dự thi

Quy trình đăng ký dự thi của sinh viên được chia thành **04 bước**.

## Bước 1 — Nhập hồ sơ

URL:

```text
/sinh-vien/dang-ky-thi/{lichthi}/buoc-1
```

Giao diện gồm hai khu vực:

**Thông tin cá nhân**

Sinh viên kiểm tra và bổ sung các thông tin cần thiết.

**Hồ sơ**

Sinh viên upload:

* Ảnh hồ sơ.
* Ảnh CCCD.
* Các thông tin liên quan theo yêu cầu.

---

## Bước 2 — Xác nhận thông tin

Sinh viên được hiển thị lại toàn bộ thông tin đã nhập bao gồm:

* Thông tin cá nhân.
* Lịch thi.
* Ảnh hồ sơ.
* Ảnh CCCD.
* Lệ phí dự thi.

Sinh viên kiểm tra thông tin trước khi tiếp tục thanh toán.

---

## Bước 3 — Thanh toán

Sinh viên lựa chọn:

* **VNPAY**
* **NAPAS**

Sau khi lựa chọn, hệ thống chuyển tới cổng thanh toán tương ứng.

---

## Bước 4 — Hoàn tất đăng ký

Sau khi thanh toán, hệ thống hiển thị:

* Mã đăng ký.
* Thông tin kỳ thi.
* Số tiền thanh toán.
* Phương thức thanh toán.
* Trạng thái thanh toán.
* Trạng thái hồ sơ.

Sau khi thanh toán thành công, hồ sơ chuyển sang trạng thái:

```text
Chờ duyệt
```

---

# 10. Tích hợp thanh toán VNPAY / NAPAS

Hệ thống hỗ trợ hai lựa chọn hiển thị cho sinh viên:

### VNPAY

Có thể hỗ trợ các phương thức thanh toán được VNPAY cung cấp như:

* VNPAY-QR.
* Ứng dụng ngân hàng.
* Thẻ ngân hàng.
* Các phương thức được merchant VNPAY hỗ trợ.

### NAPAS

Trong hệ thống hiện tại, lựa chọn NAPAS được thực hiện thông qua luồng thanh toán VNPAY dành cho thẻ ngân hàng nội địa.

Khi sinh viên chọn NAPAS, hệ thống sử dụng:

```text
vnp_BankCode=VNBANK
```

để hướng người dùng vào luồng thanh toán qua ngân hàng nội địa.

---

## 11. Cấu hình VNPAY

Thêm vào `.env`:

```env
VNPAY_TMN_CODE=
VNPAY_HASH_SECRET=
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL="${APP_URL}/thanh-toan/vnpay/return"
```

Trong đó:

| Biến                | Ý nghĩa                                            |
| ------------------- | -------------------------------------------------- |
| `VNPAY_TMN_CODE`    | Mã website/merchant do VNPAY cấp                   |
| `VNPAY_HASH_SECRET` | Chuỗi bí mật sử dụng để tạo và kiểm tra checksum   |
| `VNPAY_URL`         | Địa chỉ cổng thanh toán VNPAY                      |
| `VNPAY_RETURN_URL`  | URL VNPAY chuyển người dùng trở lại sau thanh toán |

Để sử dụng môi trường Sandbox, cần đăng ký thông tin merchant theo hướng dẫn của VNPAY.

---

# 12. Chế độ thanh toán mô phỏng

Để thuận tiện cho việc kiểm thử và trình bày đồ án, hệ thống hỗ trợ **thanh toán mô phỏng**.

Nếu hai biến sau để trống:

```env
VNPAY_TMN_CODE=
VNPAY_HASH_SECRET=
```

hệ thống sẽ không gọi cổng VNPAY mà tự động chuyển sang chức năng mô phỏng trong:

```text
app/Http/Controllers/SinhVien/ThanhToanController.php
```

Luồng demo:

```text
Chọn phương thức thanh toán
        ↓
Thanh toán mô phỏng
        ↓
Ghi nhận giao dịch thành công
        ↓
Cập nhật hồ sơ
        ↓
Trạng thái: Chờ duyệt
        ↓
Gửi email thông báo
```

Nhờ đó, nhóm có thể trình bày đầy đủ quy trình:

```text
Đăng ký thi
→ Upload hồ sơ
→ Xác nhận thông tin
→ Thanh toán
→ Chờ duyệt
→ Admin duyệt
```

mà không bắt buộc phải có tài khoản merchant VNPAY thật.

---

# 13. Cấu hình Database

## 13.1. Sử dụng SQLite

SQLite phù hợp với:

* Chạy thử project.
* Demo.
* Phát triển nhanh.
* Không muốn cài MySQL.

Sau khi cấu hình `.env`, chạy:

```bash
php artisan migrate --seed
```

---

## 13.2. Sử dụng MySQL

Tạo database:

```text
hvnh_exam
```

Sau đó cấu hình `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hvnh_exam
DB_USERNAME=root
DB_PASSWORD=
```

Tiếp theo chạy:

```bash
php artisan migrate --seed
```

Nếu muốn xóa toàn bộ database hiện tại, tạo lại bảng và dữ liệu mẫu:

```bash
php artisan migrate:fresh --seed
```

> `migrate:fresh` sẽ xóa toàn bộ dữ liệu hiện có. Chỉ nên sử dụng trong môi trường phát triển.

---

# 14. Cấu hình Email

Mặc định project có thể sử dụng:

```env
MAIL_MAILER=log
```

Khi đó hệ thống **không gửi email thật**.

Nội dung email được ghi tại:

```text
storage/logs/laravel.log
```

Chế độ này phù hợp khi:

* Phát triển project.
* Test luồng đăng ký.
* Test xác minh email.
* Test quên mật khẩu.
* Test thông báo trạng thái hồ sơ.

---

## 14.1. Gửi Email thật

Để gửi email thực tế, chuyển sang SMTP:

```env
MAIL_MAILER=smtp
```

Sau đó cấu hình các thông tin SMTP tương ứng, ví dụ:

```env
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"
```

---

# 15. Email thông báo tự động

Hệ thống có thể gửi thông báo khi:

* Sinh viên đăng ký/xác minh tài khoản.
* Sinh viên yêu cầu đặt lại mật khẩu.
* Thanh toán lệ phí thành công.
* Admin duyệt hồ sơ.
* Admin từ chối hồ sơ.
* Admin yêu cầu bổ sung hồ sơ.

Luồng xử lý:

```text
Phát sinh sự kiện
        ↓
Cập nhật trạng thái
        ↓
Tạo nội dung email
        ↓
Laravel Mail
        ↓
SMTP hoặc Log
```

---

# 16. Giao diện hệ thống

## Admin / Khoa / Giảng viên

Sử dụng layout:

```text
resources/views/layouts/app.blade.php
```

Thiết kế gồm:

* Sidebar bên trái.
* Header.
* Khu vực nội dung chính.
* Logo HVNH.
* Tông màu xanh – trắng.

---

## Sinh viên

Sử dụng layout:

```text
resources/views/layouts/sinhvien.blade.php
```

Giao diện được thiết kế theo dạng cổng thông tin sinh viên gồm:

* Top bar.
* Thanh điều hướng ngang.
* Thông tin tài khoản.
* Danh sách lịch thi.
* Đăng ký dự thi.
* Kết quả.
* Phúc khảo.
* Chứng nhận.

---

## Tài nguyên giao diện

Logo:

```text
public/images/logo.svg
```

CSS giao diện:

```text
public/css/theme.css
```

---

# 17. Kho Email Sinh viên

Do hệ thống hiện tại không được kết nối trực tiếp với:

* Hệ thống SIS của Học viện Ngân hàng.
* Cơ sở dữ liệu sinh viên thật.
* Máy chủ quản lý tài khoản email HVNH.

vì vậy chức năng xác thực email sinh viên được mô phỏng bằng bảng:

```text
sv_whitelists
```

Admin có trách nhiệm import trước danh sách email sinh viên hợp lệ.

Quy trình:

```text
Admin import danh sách email
        ↓
Email được lưu vào sv_whitelists
        ↓
Sinh viên đăng ký
        ↓
Hệ thống kiểm tra email
        ↓
Email hợp lệ → Cho phép đăng ký
Email không hợp lệ → Từ chối đăng ký
```

Giải pháp này cho phép mô phỏng tương đối sát nghiệp vụ thực tế mà không yêu cầu quyền truy cập vào hệ thống nội bộ của HVNH.

---

# 18. Import câu hỏi

Hệ thống hỗ trợ import câu hỏi từ file **CSV**.

File cần tuân theo cấu trúc cột được quy định trong màn hình quản lý kho đề/tạo đề thi.

CSV được lựa chọn vì:

* Laravel có thể xử lý trực tiếp.
* Không cần cài thêm package.
* Dễ kiểm thử.
* Dễ tạo dữ liệu mẫu.

Nếu cần import trực tiếp `.xlsx`, có thể mở rộng project bằng thư viện Laravel Excel.

Ví dụ:

```bash
composer require maatwebsite/excel
```

Đây là chức năng mở rộng và không bắt buộc đối với phiên bản hiện tại.

---

# 19. Một số lệnh Laravel hữu ích

Xem danh sách route:

```bash
php artisan route:list
```

Xóa cache:

```bash
php artisan optimize:clear
```

Xóa và tạo lại database:

```bash
php artisan migrate:fresh --seed
```

Chạy Migration mới:

```bash
php artisan migrate
```

Chạy Seeder:

```bash
php artisan db:seed
```

Tạo Storage Link:

```bash
php artisan storage:link
```

Chạy Server:

```bash
php artisan serve
```

---

# 20. Giả định thiết kế

Do một số nghiệp vụ chưa được quy định chi tiết trong tài liệu yêu cầu ban đầu, project sử dụng một số giả định phục vụ xây dựng và demo hệ thống.

## 20.1. Ngưỡng điểm đạt

Ngưỡng điểm đủ điều kiện đăng ký nhận chứng nhận hiện được đặt:

```text
50/100
```

Giá trị được khai báo trong:

```text
app/Http/Controllers/SinhVien/ChungNhanController.php
```

với hằng số:

```text
DIEM_DAT
```

Có thể thay đổi theo quy chế thi thực tế.

---

## 20.2. Kho email sinh viên

Kho email HVNH được mô phỏng bằng:

```text
sv_whitelists
```

thay vì kết nối trực tiếp với hệ thống sinh viên/email của Học viện.

---

## 20.3. Import câu hỏi

Phiên bản hiện tại sử dụng:

```text
CSV
```

thay vì `.xlsx` để giảm phụ thuộc vào thư viện bên ngoài.

---

## 20.4. Thanh toán

VNPAY Sandbox được hỗ trợ nếu có thông tin merchant.

Nếu chưa cấu hình merchant, hệ thống sử dụng:

```text
Thanh toán mô phỏng
```

để đảm bảo nhóm vẫn có thể trình bày đầy đủ nghiệp vụ trong quá trình kiểm thử và bảo vệ đồ án.

---

# 21. Luồng nghiệp vụ tổng quát

```text
                         ┌─────────────────┐
                         │    Sinh viên    │
                         └────────┬────────┘
                                  │
                                  ▼
                         Đăng ký tài khoản
                                  │
                                  ▼
                         Xác minh email HVNH
                                  │
                                  ▼
                              Đăng nhập
                                  │
                                  ▼
                           Xem lịch thi
                                  │
                                  ▼
                           Đăng ký dự thi
                                  │
                                  ▼
                         Upload hồ sơ/CCCD
                                  │
                                  ▼
                         Xác nhận thông tin
                                  │
                                  ▼
                            Thanh toán
                                  │
                                  ▼
                              Chờ duyệt
                                  │
                         ┌────────┴────────┐
                         ▼                 ▼
                     Được duyệt         Từ chối
                         │
                         ▼
                       Dự thi
                         │
                         ▼
                       Chấm thi
                         │
                         ▼
                     Công bố điểm
                         │
                  ┌──────┴──────┐
                  ▼             ▼
               Phúc khảo     Đạt yêu cầu
                                  │
                                  ▼
                          Đăng ký chứng nhận
                                  │
                                  ▼
                            Admin xét duyệt
                                  │
                                  ▼
                           Cấp chứng nhận
```

---

# 22. Lưu ý khi chạy Project

Nếu gặp lỗi sau khi clone hoặc cập nhật source code, nên chạy:

```bash
composer install
php artisan optimize:clear
php artisan migrate
php artisan storage:link
```

Nếu `.env` vừa được tạo mới:

```bash
php artisan key:generate
```

Nếu muốn khởi tạo lại toàn bộ dữ liệu demo:

```bash
php artisan migrate:fresh --seed
```

Sau đó:

```bash
php artisan serve
```

---

# 23. Mục tiêu của Project

Project được xây dựng nhằm mô phỏng một hệ thống quản lý thi chứng chỉ tương đối hoàn chỉnh tại Học viện Ngân hàng, bao phủ toàn bộ quy trình:

```text
Quản lý người dùng
        ↓
Quản lý kỳ thi
        ↓
Quản lý đề thi
        ↓
Đăng ký dự thi
        ↓
Thanh toán
        ↓
Duyệt hồ sơ
        ↓
Tổ chức thi
        ↓
Chấm thi
        ↓
Công bố kết quả
        ↓
Phúc khảo
        ↓
Cấp chứng nhận
```

Hệ thống được xây dựng phục vụ mục đích **học tập, quản lý dự án, phát triển phần mềm và trình bày đồ án**.

---

# 24. Thành viên phát triển

**Nhóm 2 — Quản lý Dự án**

Repository:

`https://github.com/lamtrucnguyen08032k5-boop/QLDA_Nhom2`

---

## License

Project được xây dựng phục vụ mục đích học tập và nghiên cứu.

Vui lòng tham khảo quy định của nhóm trước khi sử dụng hoặc phân phối lại mã nguồn.
