-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2026 at 04:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hvnh`
--

-- --------------------------------------------------------

--
-- Table structure for table `bai_this`
--

CREATE TABLE `bai_this` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_bai_thi` varchar(255) DEFAULT NULL,
  `dang_ky_id` bigint(20) UNSIGNED NOT NULL,
  `de_thi_id` bigint(20) UNSIGNED NOT NULL,
  `gio_bat_dau` datetime DEFAULT NULL,
  `gio_nop` datetime DEFAULT NULL,
  `trang_thai` varchar(255) NOT NULL DEFAULT 'dang_thi',
  `diem_tu_dong` decimal(5,2) NOT NULL DEFAULT 0.00,
  `diem_cham_tay` decimal(5,2) NOT NULL DEFAULT 0.00,
  `diem_tong` decimal(5,2) DEFAULT NULL,
  `cham_xong` tinyint(1) NOT NULL DEFAULT 0,
  `giang_vien_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_cham` timestamp NULL DEFAULT NULL,
  `ngay_cong_bo` timestamp NULL DEFAULT NULL,
  `nguoi_cong_bo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bai_this`
--

INSERT INTO `bai_this` (`id`, `ma_bai_thi`, `dang_ky_id`, `de_thi_id`, `gio_bat_dau`, `gio_nop`, `trang_thai`, `diem_tu_dong`, `diem_cham_tay`, `diem_tong`, `cham_xong`, `giang_vien_id`, `ngay_cham`, `ngay_cong_bo`, `nguoi_cong_bo_id`, `created_at`, `updated_at`) VALUES
(1, 'BT260901001', 13, 1, '2026-09-20 08:05:00', '2026-09-20 08:55:00', 'da_cong_bo', 50.00, 40.00, 90.00, 1, NULL, '2026-09-21 13:39:04', '2026-09-22 13:58:55', 1, '2026-09-22 13:06:47', '2026-09-22 13:58:55'),
(2, 'BT260901002', 14, 1, '2026-09-20 08:05:00', '2026-09-20 08:55:00', 'da_cong_bo', 50.00, 25.00, 75.00, 1, NULL, '2026-09-21 13:39:04', '2026-09-22 13:58:55', 1, '2026-09-22 13:06:47', '2026-09-22 13:58:55'),
(3, 'BT260901003', 15, 1, '2026-09-20 08:05:00', '2026-09-20 08:55:00', 'da_cong_bo', 25.00, 35.00, 60.00, 1, NULL, '2026-09-21 13:39:04', '2026-09-22 13:58:55', 1, '2026-09-22 13:06:47', '2026-09-22 13:58:55'),
(4, 'BT260902001', 16, 1, '2026-09-20 09:35:00', '2026-09-20 10:30:00', 'da_cham', 50.00, 30.00, 80.00, 1, NULL, '2026-09-21 13:39:04', NULL, NULL, '2026-09-22 13:06:49', '2026-09-22 13:39:04'),
(5, 'BT260902002', 17, 1, '2026-09-20 09:35:00', '2026-09-20 10:30:00', 'dang_cham', 50.00, 0.00, NULL, 0, NULL, NULL, NULL, NULL, '2026-09-22 13:06:49', '2026-09-22 13:39:04'),
(6, 'BT260902003', 18, 1, '2026-09-20 09:35:00', '2026-09-20 10:30:00', 'dang_thi', 25.00, 0.00, NULL, 0, NULL, NULL, NULL, NULL, '2026-09-22 13:06:49', '2026-09-22 13:39:04'),
(7, 'BT260903001', 19, 1, '2026-09-19 13:35:00', '2026-09-19 14:30:00', 'da_cong_bo', 50.00, 35.00, 85.00, 1, NULL, '2026-09-20 13:39:04', '2026-09-21 13:39:04', 1, '2026-09-22 13:06:49', '2026-09-22 13:39:04'),
(8, 'BT260903002', 20, 1, '2026-09-19 13:35:00', '2026-09-19 14:30:00', 'da_cong_bo', 25.00, 15.00, 40.00, 1, NULL, '2026-09-20 13:39:04', '2026-09-21 13:39:04', 1, '2026-09-22 13:06:49', '2026-09-22 13:39:04'),
(9, 'BT260903003', 21, 1, '2026-09-19 13:35:00', '2026-09-19 14:30:00', 'da_cong_bo', 50.00, 20.00, 70.00, 1, NULL, '2026-09-20 13:39:04', '2026-09-21 13:39:04', 1, '2026-09-22 13:06:49', '2026-09-22 13:39:04');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cau_hois`
--

CREATE TABLE `cau_hois` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `de_thi_id` bigint(20) UNSIGNED NOT NULL,
  `noi_dung` text NOT NULL,
  `loai_cau` varchar(255) NOT NULL DEFAULT 'tracnghiem',
  `dap_an_a` varchar(255) DEFAULT NULL,
  `dap_an_b` varchar(255) DEFAULT NULL,
  `dap_an_c` varchar(255) DEFAULT NULL,
  `dap_an_d` varchar(255) DEFAULT NULL,
  `dap_an_dung` varchar(255) DEFAULT NULL,
  `diem` decimal(5,2) NOT NULL DEFAULT 1.00,
  `thu_tu` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cau_hois`
--

INSERT INTO `cau_hois` (`id`, `de_thi_id`, `noi_dung`, `loai_cau`, `dap_an_a`, `dap_an_b`, `dap_an_c`, `dap_an_d`, `dap_an_dung`, `diem`, `thu_tu`, `created_at`, `updated_at`) VALUES
(1, 1, 'Trong Microsoft Word, tổ hợp phím nào dùng để căn đều 2 bên đoạn văn bản?', 'tracnghiem', 'Ctrl + L', 'Ctrl + R', 'Ctrl + J', 'Ctrl + E', 'C', 25.00, 1, '2026-09-22 13:06:47', '2026-09-22 13:06:47'),
(2, 1, 'Trong Microsoft Excel, hàm nào dùng để đếm các ô thỏa mãn một điều kiện cho trước?', 'tracnghiem', 'COUNT', 'COUNTA', 'COUNTIF', 'COUNTIFS', 'C', 25.00, 2, '2026-09-22 13:06:47', '2026-09-22 13:06:47'),
(3, 1, 'Trình bày các biện pháp phòng chống mã độc và bảo vệ dữ liệu cá nhân khi sử dụng Internet trong môi trường ngân hàng.', 'tuluan', NULL, NULL, NULL, NULL, NULL, 50.00, 3, '2026-09-22 13:06:47', '2026-09-22 13:06:47');

-- --------------------------------------------------------

--
-- Table structure for table `cau_tra_lois`
--

CREATE TABLE `cau_tra_lois` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bai_thi_id` bigint(20) UNSIGNED NOT NULL,
  `cau_hoi_id` bigint(20) UNSIGNED NOT NULL,
  `dap_an_chon` varchar(255) DEFAULT NULL,
  `bai_lam_tu_luan` text DEFAULT NULL,
  `diem_dat` decimal(5,2) NOT NULL DEFAULT 0.00,
  `da_cham` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chung_nhans`
--

CREATE TABLE `chung_nhans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bai_thi_id` bigint(20) UNSIGNED NOT NULL,
  `sinh_vien_id` bigint(20) UNSIGNED NOT NULL,
  `so_chung_nhan` varchar(255) DEFAULT NULL,
  `trang_thai` varchar(255) NOT NULL DEFAULT 'cho_duyet',
  `dia_chi_nhan` varchar(255) DEFAULT NULL,
  `so_dien_thoai` varchar(255) DEFAULT NULL,
  `file_chung_nhan` varchar(255) DEFAULT NULL,
  `ngay_cap` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chung_nhans`
--

INSERT INTO `chung_nhans` (`id`, `bai_thi_id`, `sinh_vien_id`, `so_chung_nhan`, `trang_thai`, `dia_chi_nhan`, `so_dien_thoai`, `file_chung_nhan`, `ngay_cap`, `created_at`, `updated_at`) VALUES
(1, 7, 6, 'CC-HVNH-2026-00088', 'da_cap', '12 Chùa Bộc, Đống Đa, Hà Nội', '0988001001', NULL, '2026-09-22 01:39:04', '2026-09-22 13:06:49', '2026-09-22 13:39:04');

-- --------------------------------------------------------

--
-- Table structure for table `dang_kys`
--

CREATE TABLE `dang_kys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_dang_ky` varchar(255) DEFAULT NULL,
  `sinh_vien_id` bigint(20) UNSIGNED NOT NULL,
  `lich_thi_id` bigint(20) UNSIGNED NOT NULL,
  `trang_thai` varchar(255) NOT NULL DEFAULT 'cho_duyet',
  `ly_do_tu_choi` varchar(255) DEFAULT NULL,
  `ngay_duyet` timestamp NULL DEFAULT NULL,
  `nguoi_duyet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `so_dien_thoai` varchar(255) DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` varchar(255) DEFAULT NULL,
  `dan_toc` varchar(255) DEFAULT NULL,
  `noi_sinh` varchar(255) DEFAULT NULL,
  `tinh_thanh_pho_code` varchar(255) DEFAULT NULL,
  `tinh_thanh_pho_ten` varchar(255) DEFAULT NULL,
  `xa_phuong_code` varchar(255) DEFAULT NULL,
  `xa_phuong_ten` varchar(255) DEFAULT NULL,
  `dia_chi_chi_tiet` varchar(255) DEFAULT NULL,
  `email_lien_he` varchar(255) DEFAULT NULL,
  `so_cccd` varchar(255) DEFAULT NULL,
  `anh_cccd_truoc` varchar(255) DEFAULT NULL,
  `anh_cccd_sau` varchar(255) DEFAULT NULL,
  `anh_ho_so` varchar(255) DEFAULT NULL,
  `anh_the_sv` varchar(255) DEFAULT NULL,
  `truong_can_bo_sung` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`truong_can_bo_sung`)),
  `ly_do_bo_sung` text DEFAULT NULL,
  `ly_do_tung_truong` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ly_do_tung_truong`)),
  `han_bo_sung` timestamp NULL DEFAULT NULL,
  `ngay_bo_sung` timestamp NULL DEFAULT NULL,
  `han_thanh_toan` timestamp NULL DEFAULT NULL,
  `phuong_thuc_thanh_toan` varchar(255) DEFAULT NULL,
  `trang_thai_thanh_toan` varchar(255) NOT NULL DEFAULT 'cho_thanh_toan',
  `ma_giao_dich` varchar(255) DEFAULT NULL,
  `so_tien` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_thanh_toan` timestamp NULL DEFAULT NULL,
  `ngay_nhac_thanh_toan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dang_kys`
--

INSERT INTO `dang_kys` (`id`, `ma_dang_ky`, `sinh_vien_id`, `lich_thi_id`, `trang_thai`, `ly_do_tu_choi`, `ngay_duyet`, `nguoi_duyet_id`, `so_dien_thoai`, `ngay_sinh`, `gioi_tinh`, `dan_toc`, `noi_sinh`, `tinh_thanh_pho_code`, `tinh_thanh_pho_ten`, `xa_phuong_code`, `xa_phuong_ten`, `dia_chi_chi_tiet`, `email_lien_he`, `so_cccd`, `anh_cccd_truoc`, `anh_cccd_sau`, `anh_ho_so`, `anh_the_sv`, `truong_can_bo_sung`, `ly_do_bo_sung`, `ly_do_tung_truong`, `han_bo_sung`, `ngay_bo_sung`, `han_thanh_toan`, `phuong_thuc_thanh_toan`, `trang_thai_thanh_toan`, `ma_giao_dich`, `so_tien`, `ngay_thanh_toan`, `ngay_nhac_thanh_toan`, `created_at`, `updated_at`) VALUES
(11, 'DK260910KUWOM', 5, 1, 'da_duyet', NULL, '2026-09-11 03:35:05', 1, '0354337920', '2005-08-03', 'nu', 'Kinh', 'Thái Bình', '26', 'Tỉnh Lai Châu', '02791', 'Bum Tở', '3', 'lamtrucnguyen08032k5@gmail.com', '324235555555', 'hoso/5/rd7bAKQpA2N4lXZllaBPQiBUi6Ask3JXkDdM48pU.png', 'hoso/5/VjNoJlyNmKjeTwHfN3XutSV5g4FbrIC9SH3rR5As.png', 'hoso/5/4QEjjDa3ADtVRCmVE6Edx2gipK26uoFktZbIyF3C.png', NULL, '[\"xa_phuong\"]', 'Yêu cầu sinh viên bổ sung/chỉnh sửa các thông tin sau:\n- Xã/Phường: sai thông tin', '{\"xa_phuong\":\"sai th\\u00f4ng tin\"}', '2026-09-11 03:34:00', '2026-09-11 03:35:05', '2026-09-12 02:08:00', 'vnpay', 'da_thanh_toan', 'GD260910152036SVJK', 555555, '2026-09-10 08:21:19', NULL, '2026-09-10 08:20:33', '2026-09-11 03:35:05'),
(12, 'DK260921S3VAZ', 5, 2, 'cho_duyet', NULL, NULL, NULL, '0354337920', '2008-09-17', 'nu', 'Kinh', 'Thái Bình', '09', 'Tỉnh Lào Cai', '02687', 'Châu Quế', 'w22', 'lamtrucnguyen08032k5@gmail.com', '324235555555', 'hoso/5/oI0CdnENwxWbflburkpUfe2A2inh72QXpcS1FlRO.jpg', 'hoso/5/ifNhnVuaOSOMVU2QVfMFGsaZ8Kqj2c7KKHYuXQJi.jpg', 'hoso/5/35fzLjm0u1GQl1mfqN1sJscHeegp9kvb6SlaIwIM.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-23 04:59:28', 'vnpay', 'da_thanh_toan', 'GD2609211159319ZN0', 200000, '2026-09-21 04:59:56', NULL, '2026-09-21 04:59:28', '2026-09-21 04:59:56'),
(13, 'DK260922BBHK6', 6, 3, 'da_duyet', NULL, NULL, NULL, '0988001001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001202001111', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-17 13:06:47', NULL, 'da_thanh_toan', NULL, NULL, NULL, NULL, '2026-09-22 13:06:47', '2026-09-22 13:06:47'),
(14, 'DK260922WVMJK', 7, 3, 'da_duyet', NULL, NULL, NULL, '0988001002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001202002222', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-17 13:06:47', NULL, 'da_thanh_toan', NULL, NULL, NULL, NULL, '2026-09-22 13:06:47', '2026-09-22 13:06:47'),
(15, 'DK260922CNUBY', 8, 3, 'da_duyet', NULL, NULL, NULL, '0988001003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001202003333', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-17 13:06:47', NULL, 'da_thanh_toan', NULL, NULL, NULL, NULL, '2026-09-22 13:06:47', '2026-09-22 13:06:47'),
(16, 'DK260922TOFKD', 7, 4, 'da_duyet', NULL, NULL, NULL, '0988002001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001202002222', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-17 13:06:49', NULL, 'da_thanh_toan', NULL, NULL, NULL, NULL, '2026-09-22 13:06:49', '2026-09-22 13:06:49'),
(17, 'DK260922DDTJW', 9, 4, 'da_duyet', NULL, NULL, NULL, '0988002002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001202004444', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-17 13:06:49', NULL, 'da_thanh_toan', NULL, NULL, NULL, NULL, '2026-09-22 13:06:49', '2026-09-22 13:06:49'),
(18, 'DK2609227T70E', 10, 4, 'da_duyet', NULL, NULL, NULL, '0988002003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001202005555', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-17 13:06:49', NULL, 'da_thanh_toan', NULL, NULL, NULL, NULL, '2026-09-22 13:06:49', '2026-09-22 13:06:49'),
(19, 'DK260922ELPYG', 6, 5, 'da_duyet', NULL, NULL, NULL, '0988003001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001202001111', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15 13:06:49', NULL, 'da_thanh_toan', NULL, NULL, NULL, NULL, '2026-09-22 13:06:49', '2026-09-22 13:06:49'),
(20, 'DK2609226FY3C', 8, 5, 'da_duyet', NULL, NULL, NULL, '0988003002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001202003333', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15 13:06:49', NULL, 'da_thanh_toan', NULL, NULL, NULL, NULL, '2026-09-22 13:06:49', '2026-09-22 13:06:49'),
(21, 'DK260922PS20U', 10, 5, 'da_duyet', NULL, NULL, NULL, '0988003003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001202005555', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15 13:06:49', NULL, 'da_thanh_toan', NULL, NULL, NULL, NULL, '2026-09-22 13:06:49', '2026-09-22 13:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `de_this`
--

CREATE TABLE `de_this` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_de` varchar(255) NOT NULL,
  `loai_chung_chi` varchar(255) NOT NULL,
  `khoa_id` bigint(20) UNSIGNED NOT NULL,
  `ten_de` varchar(255) NOT NULL,
  `file_goc` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `de_this`
--

INSERT INTO `de_this` (`id`, `ma_de`, `loai_chung_chi`, `khoa_id`, `ten_de`, `file_goc`, `active`, `created_at`, `updated_at`) VALUES
(1, 'DT-CNTT-K22-01', 'cntt', 1, 'Đề thi Ứng dụng CNTT cơ bản (Word, Excel, An toàn thông tin)', NULL, 1, '2026-09-22 13:06:47', '2026-09-22 13:06:47');

-- --------------------------------------------------------

--
-- Table structure for table `email_verification_tokens`
--

CREATE TABLE `email_verification_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_verification_tokens`
--

INSERT INTO `email_verification_tokens` (`id`, `email`, `token`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'sv22a4000001@hvnh.edu.vn', 'pmYNU2asNVO2HD0qbowBdl5U0q2eW9fGDiYUHWZRMw3Pu5aa', '2026-09-05 09:28:35', '2026-09-04 09:28:35', '2026-09-04 09:28:35');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `khoas`
--

CREATE TABLE `khoas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_khoa` varchar(255) NOT NULL,
  `ten_khoa` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mo_ta` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khoas`
--

INSERT INTO `khoas` (`id`, `ma_khoa`, `ten_khoa`, `email`, `mo_ta`, `active`, `created_at`, `updated_at`) VALUES
(1, 'CNTT', 'Khoa Công nghệ thông tin', 'khoa.cntt@hvnh.edu.vn', NULL, 1, '2026-09-04 09:25:05', '2026-09-07 02:46:07'),
(2, 'NN', 'Khoa Ngoại ngữ', 'khoa.nn@hvnh.edu.vn', NULL, 1, '2026-09-04 09:25:05', '2026-09-07 02:46:07');

-- --------------------------------------------------------

--
-- Table structure for table `ky_this`
--

CREATE TABLE `ky_this` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_ky_thi` varchar(255) NOT NULL,
  `nam_hoc` varchar(255) DEFAULT NULL,
  `hoc_ky` varchar(255) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai` varchar(255) NOT NULL DEFAULT 'dang_mo_dang_ky',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ky_this`
--

INSERT INTO `ky_this` (`id`, `ten_ky_thi`, `nam_hoc`, `hoc_ky`, `mo_ta`, `trang_thai`, `created_at`, `updated_at`) VALUES
(1, 'Chứng chỉ tin học', '2026-2027', 'Học kỳ 1', NULL, 'dang_mo_dang_ky', '2026-09-21 04:58:27', '2026-09-21 04:58:27'),
(2, 'Kỳ thi Chuẩn đầu ra CNTT Khóa 22 - Học kỳ 1', '2025-2026', 'HK1', 'Kỳ thi đánh giá kỹ năng công nghệ thông tin cơ bản cho sinh viên K22', 'da_ket_thuc', '2026-09-22 13:06:47', '2026-09-22 13:06:47');

-- --------------------------------------------------------

--
-- Table structure for table `lich_su_xu_ly_ho_sos`
--

CREATE TABLE `lich_su_xu_ly_ho_sos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dang_ky_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vai_tro` varchar(255) NOT NULL DEFAULT 'admin',
  `hanh_dong` varchar(255) NOT NULL,
  `trang_thai_truoc` varchar(255) DEFAULT NULL,
  `trang_thai_sau` varchar(255) NOT NULL,
  `noi_dung` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lich_su_xu_ly_ho_sos`
--

INSERT INTO `lich_su_xu_ly_ho_sos` (`id`, `dang_ky_id`, `user_id`, `vai_tro`, `hanh_dong`, `trang_thai_truoc`, `trang_thai_sau`, `noi_dung`, `created_at`, `updated_at`) VALUES
(7, 11, 1, 'admin', 'yeu_cau_bo_sung', 'cho_duyet', 'cho_bo_sung', 'Yêu cầu bổ sung các trường: Xã/Phường. Hạn bổ sung trực tuyến: 10/09/2026 15:51', '2026-09-10 08:48:46', '2026-09-10 08:48:46'),
(8, 11, 1, 'admin', 'bo_sung_ho_so_qua_han', 'cho_bo_sung', 'da_bo_sung', 'Cán bộ Phòng Khảo thí đã hỗ trợ sinh viên bổ sung hồ sơ sau thời hạn.\n📌 Lý do bổ sung sau thời hạn: ok\n📋 Danh sách các mục đã cập nhật:\n- Địa chỉ hành chính: Mường Kim, Tỉnh Lai Châu ➔ Hua Bum, Tỉnh Lai Châu', '2026-09-11 03:25:39', '2026-09-11 03:25:39'),
(9, 11, 1, 'admin', 'yeu_cau_bo_sung', 'da_bo_sung', 'cho_bo_sung', 'Yêu cầu bổ sung các trường: Xã/Phường. Hạn bổ sung trực tuyến: 11/09/2026 22:33', '2026-09-11 03:32:08', '2026-09-11 03:32:08'),
(10, 11, 1, 'admin', 'yeu_cau_bo_sung', 'cho_bo_sung', 'cho_bo_sung', 'Yêu cầu bổ sung các trường: Xã/Phường. Hạn bổ sung trực tuyến: 11/09/2026 10:34', '2026-09-11 03:33:32', '2026-09-11 03:33:32'),
(11, 11, 1, 'admin', 'bo_sung_ho_so_qua_han', 'cho_bo_sung', 'da_duyet', 'Cán bộ Phòng Khảo thí đã hỗ trợ sinh viên bổ sung hồ sơ sau thời hạn và duyệt hồ sơ.\n📌 Lý do bổ sung sau thời hạn: ok\n📋 Danh sách các mục đã cập nhật:\n- Địa chỉ hành chính: Hua Bum, Tỉnh Lai Châu ➔ Bum Tở, Tỉnh Lai Châu', '2026-09-11 03:35:05', '2026-09-11 03:35:05');

-- --------------------------------------------------------

--
-- Table structure for table `lich_this`
--

CREATE TABLE `lich_this` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ky_thi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ten_ky_thi` varchar(255) NOT NULL,
  `loai_chung_chi` varchar(255) NOT NULL,
  `khoa_id` bigint(20) UNSIGNED NOT NULL,
  `ngay_thi` date NOT NULL,
  `gio_bat_dau` time NOT NULL,
  `thoi_gian_thi_phut` int(11) NOT NULL DEFAULT 60,
  `phong_thi` varchar(255) NOT NULL,
  `so_luong_toi_da` int(11) NOT NULL DEFAULT 50,
  `han_dang_ky` datetime NOT NULL,
  `le_phi` decimal(12,0) NOT NULL DEFAULT 0,
  `ma_ca_thi` varchar(255) NOT NULL,
  `trang_thai` varchar(255) NOT NULL DEFAULT 'dang_mo_dang_ky',
  `trang_thai_cong_bo` varchar(255) NOT NULL DEFAULT 'chua_cong_bo',
  `ngay_cong_bo` timestamp NULL DEFAULT NULL,
  `nguoi_cong_bo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `de_thi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lich_this`
--

INSERT INTO `lich_this` (`id`, `ky_thi_id`, `ten_ky_thi`, `loai_chung_chi`, `khoa_id`, `ngay_thi`, `gio_bat_dau`, `thoi_gian_thi_phut`, `phong_thi`, `so_luong_toi_da`, `han_dang_ky`, `le_phi`, `ma_ca_thi`, `trang_thai`, `trang_thai_cong_bo`, `ngay_cong_bo`, `nguoi_cong_bo_id`, `de_thi_id`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Chứng chỉ tin học', 'cntt', 1, '2026-09-12', '09:09:00', 60, 'P304', 50, '2026-09-12 09:08:00', 555555, 'F1JSJBLV', 'dang_mo_dang_ky', 'chua_cong_bo', NULL, NULL, NULL, '2026-09-05 02:09:12', '2026-09-10 08:19:10'),
(2, 1, 'Chứng chỉ tin học', 'cntt', 1, '2026-09-28', '08:00:00', 60, 'Phòng Máy 101 - Tòa A', 40, '2026-09-26 23:59:00', 200000, 'RPT3W8YL', 'dang_mo_dang_ky', 'chua_cong_bo', NULL, NULL, NULL, '2026-09-21 04:58:27', '2026-09-21 04:58:27'),
(3, 2, 'Kỳ thi Chuẩn đầu ra CNTT Khóa 22 - Học kỳ 1', 'cntt', 1, '2026-09-20', '08:00:00', 60, 'P.201 - Nhà D1', 40, '2026-09-17 20:39:04', 200000, 'CA01-P201', 'da_ket_thuc', 'da_cong_bo', '2026-09-22 13:58:55', 1, 1, '2026-09-22 13:06:47', '2026-09-22 13:58:55'),
(4, 2, 'Kỳ thi Chuẩn đầu ra CNTT Khóa 22 - Học kỳ 1', 'cntt', 1, '2026-09-20', '09:30:00', 60, 'P.202 - Nhà D1', 40, '2026-09-17 20:39:04', 200000, 'CA02-P202', 'da_ket_thuc', 'chua_cong_bo', NULL, NULL, 1, '2026-09-22 13:06:49', '2026-09-22 13:39:04'),
(5, 2, 'Kỳ thi Chuẩn đầu ra CNTT Đợt tháng 9', 'cntt', 1, '2026-09-19', '13:30:00', 60, 'P.305 - Giảng đường B', 30, '2026-09-15 20:39:04', 200000, 'CA03-P305', 'da_ket_thuc', 'da_cong_bo', '2026-09-21 13:39:04', 1, 1, '2026-09-22 13:06:49', '2026-09-22 13:39:04');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2024_01_01_000001_create_khoas_table', 1),
(4, '2024_01_01_000002_create_users_table', 1),
(5, '2024_01_01_000003_create_sv_whitelist_table', 1),
(6, '2024_01_01_000004_create_de_this_table', 1),
(7, '2024_01_01_000005_create_lich_this_table', 1),
(8, '2024_01_01_000006_create_dang_kys_table', 1),
(9, '2024_01_01_000007_create_bai_this_table', 1),
(10, '2024_01_01_000008_create_phuc_khaos_table', 1),
(11, '2024_01_01_000009_create_chung_nhans_table', 1),
(12, '2024_01_02_000001_add_ho_so_fields_to_dang_kys_table', 2),
(13, '2024_01_02_000002_add_lien_he_fields_to_dang_kys_table', 3),
(14, '2024_01_02_000003_add_nguoi_duyet_to_dang_kys_table', 4),
(15, '2024_01_02_000004_create_lich_su_xu_ly_ho_sos_table', 4),
(16, '2024_01_02_000005_add_ngay_nhac_thanh_toan_to_dang_kys_table', 5),
(17, '2024_01_02_000006_drop_unique_sinh_vien_lich_thi_on_dang_kys_table', 6),
(18, '2024_01_02_000007_add_han_thanh_toan_to_dang_kys_table', 7),
(19, '2026_09_07_160055_add_ly_do_tung_truong_to_dang_kys', 8),
(20, '2026_09_14_103555_create_ky_this_table', 9),
(21, '2026_09_22_195500_add_cong_bo_fields_to_bai_this_and_lich_this_table', 10),
(22, '2026_09_22_200222_create_notifications_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('2eeab302-343c-4075-bea6-4052c581577b', 'App\\Notifications\\ThongBaoHeThong', 'App\\Models\\User', 6, '{\"tieu_de\":\"\\u0110\\u00e3 c\\u00f3 k\\u1ebft qu\\u1ea3 thi: K\\u1ef3 thi Chu\\u1ea9n \\u0111\\u1ea7u ra CNTT Kh\\u00f3a 22 - H\\u1ecdc k\\u1ef3 1\",\"noi_dung\":\"Ph\\u00f2ng P.201 - Nh\\u00e0 D1 (CA01-P201) \\u0111\\u00e3 c\\u00f4ng b\\u1ed1 k\\u1ebft qu\\u1ea3. \\u0110i\\u1ec3m c\\u1ee7a b\\u1ea1n: 90.00 (\\u0110\\u1ea1t).\",\"url\":\"http:\\/\\/localhost\\/hvnh-exam-app\\/hvnh-exam-app\\/public\\/sinh-vien\\/ket-qua\\/1\",\"loai\":\"thanh_cong\",\"icon\":\"bi-award\",\"created_at\":\"2026-09-22T20:59:00+07:00\"}', NULL, '2026-09-22 13:59:00', '2026-09-22 13:59:00'),
('36bef377-70bf-4613-88c4-0f812810c889', 'App\\Notifications\\ThongBaoHeThong', 'App\\Models\\User', 10, '{\"tieu_de\":\"\\u0110\\u00e3 c\\u00f3 k\\u1ebft qu\\u1ea3 thi: K\\u1ef3 thi Chu\\u1ea9n \\u0111\\u1ea7u ra CNTT \\u0110\\u1ee3t th\\u00e1ng 9\",\"noi_dung\":\"K\\u1ebft qu\\u1ea3 thi ph\\u00f2ng P.305 - Gi\\u1ea3ng \\u0111\\u01b0\\u1eddng B (Ca CA03-P305) \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u00f4ng b\\u1ed1. \\u0110i\\u1ec3m c\\u1ee7a b\\u1ea1n: 70 (\\u0110\\u1ea1t).\",\"url\":\"http:\\/\\/localhost\\/hvnh-exam-app\\/hvnh-exam-app\\/public\\/sinh-vien\\/ket-qua\\/9\",\"loai\":\"thanh_cong\",\"icon\":\"bi-award\",\"created_at\":\"2026-09-22T20:39:04+07:00\"}', NULL, '2026-09-22 13:39:04', '2026-09-22 13:39:04'),
('3d284c8d-eb6e-4894-a56e-79182e81d0ca', 'App\\Notifications\\ThongBaoHeThong', 'App\\Models\\User', 6, '{\"tieu_de\":\"\\u0110\\u00e3 c\\u00f3 k\\u1ebft qu\\u1ea3 thi: K\\u1ef3 thi Chu\\u1ea9n \\u0111\\u1ea7u ra CNTT \\u0110\\u1ee3t th\\u00e1ng 9\",\"noi_dung\":\"K\\u1ebft qu\\u1ea3 thi ph\\u00f2ng P.305 - Gi\\u1ea3ng \\u0111\\u01b0\\u1eddng B (Ca CA03-P305) \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u00f4ng b\\u1ed1. \\u0110i\\u1ec3m c\\u1ee7a b\\u1ea1n: 85 (\\u0110\\u1ea1t).\",\"url\":\"http:\\/\\/localhost\\/hvnh-exam-app\\/hvnh-exam-app\\/public\\/sinh-vien\\/ket-qua\\/7\",\"loai\":\"thanh_cong\",\"icon\":\"bi-award\",\"created_at\":\"2026-09-22T20:39:04+07:00\"}', NULL, '2026-09-22 13:39:04', '2026-09-22 13:39:04'),
('949419be-9e6e-4710-a5a3-9a5562d7f8d2', 'App\\Notifications\\ThongBaoHeThong', 'App\\Models\\User', 1, '{\"tieu_de\":\"Ph\\u00f2ng thi P.201 - Nh\\u00e0 D1 \\u0111\\u00e3 ch\\u1ea5m xong 100%!\",\"noi_dung\":\"To\\u00e0n b\\u1ed9 3 b\\u00e0i l\\u00e0m trong ph\\u00f2ng thi P.201 - Nh\\u00e0 D1 (Ca CA01-P201) \\u0111\\u00e3 ch\\u1ea5m xong. B\\u1ea1n c\\u00f3 th\\u1ec3 th\\u1ef1c hi\\u1ec7n Tr\\u1ea3 k\\u1ebft qu\\u1ea3 thi cho sinh vi\\u00ean.\",\"url\":\"http:\\/\\/localhost\\/hvnh-exam-app\\/hvnh-exam-app\\/public\\/admin\\/lich-thi\\/3\\/ket-qua\",\"loai\":\"thanh_cong\",\"icon\":\"bi-check-circle-fill\",\"created_at\":\"2026-09-22T20:39:04+07:00\"}', '2026-09-22 13:46:57', '2026-09-22 13:39:04', '2026-09-22 13:46:57'),
('98b698f3-29d0-4a13-b72d-e287ac3f5976', 'App\\Notifications\\ThongBaoHeThong', 'App\\Models\\User', 8, '{\"tieu_de\":\"\\u0110\\u00e3 c\\u00f3 k\\u1ebft qu\\u1ea3 thi: K\\u1ef3 thi Chu\\u1ea9n \\u0111\\u1ea7u ra CNTT \\u0110\\u1ee3t th\\u00e1ng 9\",\"noi_dung\":\"K\\u1ebft qu\\u1ea3 thi ph\\u00f2ng P.305 - Gi\\u1ea3ng \\u0111\\u01b0\\u1eddng B (Ca CA03-P305) \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u00f4ng b\\u1ed1. \\u0110i\\u1ec3m c\\u1ee7a b\\u1ea1n: 40 (Kh\\u00f4ng \\u0111\\u1ea1t).\",\"url\":\"http:\\/\\/localhost\\/hvnh-exam-app\\/hvnh-exam-app\\/public\\/sinh-vien\\/ket-qua\\/8\",\"loai\":\"canh_bao\",\"icon\":\"bi-award\",\"created_at\":\"2026-09-22T20:39:04+07:00\"}', NULL, '2026-09-22 13:39:04', '2026-09-22 13:39:04'),
('d6fae273-a704-49b9-ab0c-c6a92d548ade', 'App\\Notifications\\ThongBaoHeThong', 'App\\Models\\User', 8, '{\"tieu_de\":\"\\u0110\\u00e3 c\\u00f3 k\\u1ebft qu\\u1ea3 thi: K\\u1ef3 thi Chu\\u1ea9n \\u0111\\u1ea7u ra CNTT Kh\\u00f3a 22 - H\\u1ecdc k\\u1ef3 1\",\"noi_dung\":\"Ph\\u00f2ng P.201 - Nh\\u00e0 D1 (CA01-P201) \\u0111\\u00e3 c\\u00f4ng b\\u1ed1 k\\u1ebft qu\\u1ea3. \\u0110i\\u1ec3m c\\u1ee7a b\\u1ea1n: 60.00 (\\u0110\\u1ea1t).\",\"url\":\"http:\\/\\/localhost\\/hvnh-exam-app\\/hvnh-exam-app\\/public\\/sinh-vien\\/ket-qua\\/3\",\"loai\":\"thanh_cong\",\"icon\":\"bi-award\",\"created_at\":\"2026-09-22T20:59:03+07:00\"}', NULL, '2026-09-22 13:59:03', '2026-09-22 13:59:03'),
('dc3b2f21-9963-41b4-b15f-97a7b6e9830b', 'App\\Notifications\\ThongBaoHeThong', 'App\\Models\\User', 7, '{\"tieu_de\":\"\\u0110\\u00e3 c\\u00f3 k\\u1ebft qu\\u1ea3 thi: K\\u1ef3 thi Chu\\u1ea9n \\u0111\\u1ea7u ra CNTT Kh\\u00f3a 22 - H\\u1ecdc k\\u1ef3 1\",\"noi_dung\":\"Ph\\u00f2ng P.201 - Nh\\u00e0 D1 (CA01-P201) \\u0111\\u00e3 c\\u00f4ng b\\u1ed1 k\\u1ebft qu\\u1ea3. \\u0110i\\u1ec3m c\\u1ee7a b\\u1ea1n: 75.00 (\\u0110\\u1ea1t).\",\"url\":\"http:\\/\\/localhost\\/hvnh-exam-app\\/hvnh-exam-app\\/public\\/sinh-vien\\/ket-qua\\/2\",\"loai\":\"thanh_cong\",\"icon\":\"bi-award\",\"created_at\":\"2026-09-22T20:59:02+07:00\"}', '2026-09-22 13:59:33', '2026-09-22 13:59:02', '2026-09-22 13:59:33');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phuc_khaos`
--

CREATE TABLE `phuc_khaos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bai_thi_id` bigint(20) UNSIGNED NOT NULL,
  `sinh_vien_id` bigint(20) UNSIGNED NOT NULL,
  `ly_do` text NOT NULL,
  `trang_thai` varchar(255) NOT NULL DEFAULT 'cho_xu_ly',
  `phan_hoi` text DEFAULT NULL,
  `diem_truoc` decimal(5,2) DEFAULT NULL,
  `diem_sau` decimal(5,2) DEFAULT NULL,
  `xu_ly_boi` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_xu_ly` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('lzlD1iHc74WtuSolEbfHTMjqhaY92K32NstJkYMw', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYktuMEZpQ2lNcWxQNlhyZkJrTUh5TG5pdFJ4S0JDWm1MN252ZHo5MCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Njk6Imh0dHA6Ly9sb2NhbGhvc3QvaHZuaC1leGFtLWFwcC9odm5oLWV4YW0tYXBwL3B1YmxpYy9hZG1pbi9rZXQtcXVhLXRoaSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1790085704),
('NB3PnxX4DcZlhkHbVCfR2CEUtRWL4ccA5SiNi7aG', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVU1zMGpjMjVSMUdsMUtaR0dCVUZaYjN3VVZ4bzlKOFdhZTFidkpFbyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9rZXQtcXVhLXRoaSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1790084817);

-- --------------------------------------------------------

--
-- Table structure for table `sv_whitelists`
--

CREATE TABLE `sv_whitelists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_sv` varchar(255) NOT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `lop` varchar(255) DEFAULT NULL,
  `khoa_hoc` varchar(255) DEFAULT NULL,
  `da_dang_ky` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sv_whitelists`
--

INSERT INTO `sv_whitelists` (`id`, `ma_sv`, `ho_ten`, `email`, `lop`, `khoa_hoc`, `da_dang_ky`, `created_at`, `updated_at`) VALUES
(1, '22A4000001', 'Trần Thị B', 'sv22a4000001@hvnh.edu.vn', 'K22CLC1', 'K22', 1, '2026-09-04 09:25:06', '2026-09-04 09:33:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(255) NOT NULL,
  `ma_so` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `khoa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lop` varchar(255) DEFAULT NULL,
  `khoa_hoc` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `ma_so`, `name`, `email`, `email_verified_at`, `password`, `khoa_id`, `lop`, `khoa_hoc`, `active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'ADMIN001', 'Quản trị hệ thống - Phòng khảo thí', 'admin@hvnh.edu.vn', '2026-09-07 02:46:07', '$2y$12$edl5XMa0KbHxzal8LADjvODV.YFwuThThHmaxPBMsBOzsylKoJ2gO', NULL, NULL, NULL, 1, NULL, '2026-09-04 09:25:05', '2026-09-07 02:46:07'),
(2, 'khoa', 'CNTT', 'Tài khoản Khoa Công nghệ thông tin', 'khoa.cntt@hvnh.edu.vn', '2026-09-07 02:46:07', '$2y$12$3LLPHEuDf8tfPGcIsDB12epXs5e9p797NNMiVTwE5N6c7QzuloX4q', 1, NULL, NULL, 1, NULL, '2026-09-04 09:25:05', '2026-09-07 02:46:07'),
(3, 'khoa', 'NN', 'Tài khoản Khoa Ngoại ngữ', 'khoa.nn@hvnh.edu.vn', '2026-09-07 02:46:07', '$2y$12$F7JnIQeTDwC/Zkfh8yKoPOndQ2ThX2Q2oR/LZ7//aD.RsFEBwomyS', 2, NULL, NULL, 1, NULL, '2026-09-04 09:25:06', '2026-09-07 02:46:07'),
(4, 'giangvien', 'GV001', 'Nguyễn Văn A', 'giangvien.cntt@hvnh.edu.vn', '2026-09-07 02:46:07', '$2y$12$QV7uwN/xB1jhXRU/mPxlXuisozn/OjYFKq0p5oRaE.v3YGnLzOuBm', 1, NULL, NULL, 1, NULL, '2026-09-04 09:25:06', '2026-09-07 02:46:07'),
(5, 'sinhvien', '22A4000001', 'Trần Thị B', 'sv22a4000001@hvnh.edu.vn', '2026-09-04 09:33:24', '$2y$12$JSLrPoTEug6Ap0MlaHDQoO9KsNqVaWgPgfPilvpNKvJkFZ0CaIoeW', NULL, 'K22CLC1', 'K22', 1, NULL, '2026-09-04 09:33:24', '2026-09-04 09:33:24'),
(6, 'sinhvien', '22A4010001', 'Nguyễn Văn An', 'sv.nguyenvanan@hvnh.edu.vn', '2026-09-22 13:39:03', '$2y$12$XxTT8h0Zm6VoB.lBVAnGX.GbMHJHAkeSVUKzrLPP4cfaA3f49oKwu', NULL, 'K22CLC1', 'K22', 1, NULL, '2026-09-22 13:06:46', '2026-09-22 13:39:03'),
(7, 'sinhvien', '22A4010002', 'Trần Thị Bình', 'sv.tranthibinh@hvnh.edu.vn', '2026-09-22 13:39:03', '$2y$12$pyEKn53nPbuCBK3AdUmZpufIuoQY8XwwB4ynHIK6syhGfeI88u7L2', NULL, 'K22CLC1', 'K22', 1, NULL, '2026-09-22 13:06:46', '2026-09-22 13:39:03'),
(8, 'sinhvien', '22A4010003', 'Lê Quang Cường', 'sv.lequangcuong@hvnh.edu.vn', '2026-09-22 13:39:03', '$2y$12$VwKaevMDYQgTfZ/gqCUzcunlkFDFA/Y910NwnTyU4ZtKq8POLYgKq', NULL, 'K22CLC2', 'K22', 1, NULL, '2026-09-22 13:06:46', '2026-09-22 13:39:03'),
(9, 'sinhvien', '22A4010004', 'Phạm Thị Dung', 'sv.phamthidung@hvnh.edu.vn', '2026-09-22 13:39:04', '$2y$12$6hNph4K1X4x6CccnhcU/q.57IP5Tx53j4E5P33dA2YC/5T2jHBMmC', NULL, 'K22CLC2', 'K22', 1, NULL, '2026-09-22 13:06:47', '2026-09-22 13:39:04'),
(10, 'sinhvien', '22A4010005', 'Hoàng Minh Em', 'sv.hoangminhe@hvnh.edu.vn', '2026-09-22 13:39:04', '$2y$12$PKb4rCxU7fKJrb/ng/i70O54os/Qa95u4Po5njkX8ytUQ2lvNjj.e', NULL, 'K22CLC3', 'K22', 1, NULL, '2026-09-22 13:06:47', '2026-09-22 13:39:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bai_this`
--
ALTER TABLE `bai_this`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bai_this_ma_bai_thi_unique` (`ma_bai_thi`),
  ADD KEY `bai_this_dang_ky_id_foreign` (`dang_ky_id`),
  ADD KEY `bai_this_de_thi_id_foreign` (`de_thi_id`),
  ADD KEY `bai_this_giang_vien_id_foreign` (`giang_vien_id`),
  ADD KEY `bai_this_nguoi_cong_bo_id_foreign` (`nguoi_cong_bo_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cau_hois`
--
ALTER TABLE `cau_hois`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cau_hois_de_thi_id_foreign` (`de_thi_id`);

--
-- Indexes for table `cau_tra_lois`
--
ALTER TABLE `cau_tra_lois`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cau_tra_lois_bai_thi_id_foreign` (`bai_thi_id`),
  ADD KEY `cau_tra_lois_cau_hoi_id_foreign` (`cau_hoi_id`);

--
-- Indexes for table `chung_nhans`
--
ALTER TABLE `chung_nhans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chung_nhans_so_chung_nhan_unique` (`so_chung_nhan`),
  ADD KEY `chung_nhans_bai_thi_id_foreign` (`bai_thi_id`),
  ADD KEY `chung_nhans_sinh_vien_id_foreign` (`sinh_vien_id`);

--
-- Indexes for table `dang_kys`
--
ALTER TABLE `dang_kys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dang_kys_ma_dang_ky_unique` (`ma_dang_ky`),
  ADD KEY `dang_kys_lich_thi_id_foreign` (`lich_thi_id`),
  ADD KEY `dang_kys_nguoi_duyet_id_foreign` (`nguoi_duyet_id`),
  ADD KEY `dang_kys_sinh_vien_id_index` (`sinh_vien_id`);

--
-- Indexes for table `de_this`
--
ALTER TABLE `de_this`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `de_this_ma_de_unique` (`ma_de`),
  ADD KEY `de_this_khoa_id_foreign` (`khoa_id`);

--
-- Indexes for table `email_verification_tokens`
--
ALTER TABLE `email_verification_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_verification_tokens_token_unique` (`token`),
  ADD KEY `email_verification_tokens_email_index` (`email`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `khoas`
--
ALTER TABLE `khoas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `khoas_ma_khoa_unique` (`ma_khoa`),
  ADD UNIQUE KEY `khoas_email_unique` (`email`);

--
-- Indexes for table `ky_this`
--
ALTER TABLE `ky_this`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lich_su_xu_ly_ho_sos`
--
ALTER TABLE `lich_su_xu_ly_ho_sos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lich_su_xu_ly_ho_sos_dang_ky_id_foreign` (`dang_ky_id`),
  ADD KEY `lich_su_xu_ly_ho_sos_user_id_foreign` (`user_id`);

--
-- Indexes for table `lich_this`
--
ALTER TABLE `lich_this`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lich_this_ma_ca_thi_unique` (`ma_ca_thi`),
  ADD KEY `lich_this_khoa_id_foreign` (`khoa_id`),
  ADD KEY `lich_this_de_thi_id_foreign` (`de_thi_id`),
  ADD KEY `lich_this_ky_thi_id_foreign` (`ky_thi_id`),
  ADD KEY `lich_this_nguoi_cong_bo_id_foreign` (`nguoi_cong_bo_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `phuc_khaos`
--
ALTER TABLE `phuc_khaos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phuc_khaos_bai_thi_id_foreign` (`bai_thi_id`),
  ADD KEY `phuc_khaos_sinh_vien_id_foreign` (`sinh_vien_id`),
  ADD KEY `phuc_khaos_xu_ly_boi_foreign` (`xu_ly_boi`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sv_whitelists`
--
ALTER TABLE `sv_whitelists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sv_whitelists_ma_sv_unique` (`ma_sv`),
  ADD UNIQUE KEY `sv_whitelists_email_unique` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_ma_so_unique` (`ma_so`),
  ADD KEY `users_khoa_id_foreign` (`khoa_id`),
  ADD KEY `users_role_index` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bai_this`
--
ALTER TABLE `bai_this`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cau_hois`
--
ALTER TABLE `cau_hois`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cau_tra_lois`
--
ALTER TABLE `cau_tra_lois`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chung_nhans`
--
ALTER TABLE `chung_nhans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dang_kys`
--
ALTER TABLE `dang_kys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `de_this`
--
ALTER TABLE `de_this`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `email_verification_tokens`
--
ALTER TABLE `email_verification_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `khoas`
--
ALTER TABLE `khoas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ky_this`
--
ALTER TABLE `ky_this`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lich_su_xu_ly_ho_sos`
--
ALTER TABLE `lich_su_xu_ly_ho_sos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lich_this`
--
ALTER TABLE `lich_this`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `phuc_khaos`
--
ALTER TABLE `phuc_khaos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sv_whitelists`
--
ALTER TABLE `sv_whitelists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bai_this`
--
ALTER TABLE `bai_this`
  ADD CONSTRAINT `bai_this_dang_ky_id_foreign` FOREIGN KEY (`dang_ky_id`) REFERENCES `dang_kys` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bai_this_de_thi_id_foreign` FOREIGN KEY (`de_thi_id`) REFERENCES `de_this` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bai_this_giang_vien_id_foreign` FOREIGN KEY (`giang_vien_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bai_this_nguoi_cong_bo_id_foreign` FOREIGN KEY (`nguoi_cong_bo_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cau_hois`
--
ALTER TABLE `cau_hois`
  ADD CONSTRAINT `cau_hois_de_thi_id_foreign` FOREIGN KEY (`de_thi_id`) REFERENCES `de_this` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cau_tra_lois`
--
ALTER TABLE `cau_tra_lois`
  ADD CONSTRAINT `cau_tra_lois_bai_thi_id_foreign` FOREIGN KEY (`bai_thi_id`) REFERENCES `bai_this` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cau_tra_lois_cau_hoi_id_foreign` FOREIGN KEY (`cau_hoi_id`) REFERENCES `cau_hois` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chung_nhans`
--
ALTER TABLE `chung_nhans`
  ADD CONSTRAINT `chung_nhans_bai_thi_id_foreign` FOREIGN KEY (`bai_thi_id`) REFERENCES `bai_this` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chung_nhans_sinh_vien_id_foreign` FOREIGN KEY (`sinh_vien_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dang_kys`
--
ALTER TABLE `dang_kys`
  ADD CONSTRAINT `dang_kys_lich_thi_id_foreign` FOREIGN KEY (`lich_thi_id`) REFERENCES `lich_this` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dang_kys_nguoi_duyet_id_foreign` FOREIGN KEY (`nguoi_duyet_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dang_kys_sinh_vien_id_foreign` FOREIGN KEY (`sinh_vien_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `de_this`
--
ALTER TABLE `de_this`
  ADD CONSTRAINT `de_this_khoa_id_foreign` FOREIGN KEY (`khoa_id`) REFERENCES `khoas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lich_su_xu_ly_ho_sos`
--
ALTER TABLE `lich_su_xu_ly_ho_sos`
  ADD CONSTRAINT `lich_su_xu_ly_ho_sos_dang_ky_id_foreign` FOREIGN KEY (`dang_ky_id`) REFERENCES `dang_kys` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lich_su_xu_ly_ho_sos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lich_this`
--
ALTER TABLE `lich_this`
  ADD CONSTRAINT `lich_this_de_thi_id_foreign` FOREIGN KEY (`de_thi_id`) REFERENCES `de_this` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lich_this_khoa_id_foreign` FOREIGN KEY (`khoa_id`) REFERENCES `khoas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lich_this_ky_thi_id_foreign` FOREIGN KEY (`ky_thi_id`) REFERENCES `ky_this` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lich_this_nguoi_cong_bo_id_foreign` FOREIGN KEY (`nguoi_cong_bo_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `phuc_khaos`
--
ALTER TABLE `phuc_khaos`
  ADD CONSTRAINT `phuc_khaos_bai_thi_id_foreign` FOREIGN KEY (`bai_thi_id`) REFERENCES `bai_this` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phuc_khaos_sinh_vien_id_foreign` FOREIGN KEY (`sinh_vien_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phuc_khaos_xu_ly_boi_foreign` FOREIGN KEY (`xu_ly_boi`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_khoa_id_foreign` FOREIGN KEY (`khoa_id`) REFERENCES `khoas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
