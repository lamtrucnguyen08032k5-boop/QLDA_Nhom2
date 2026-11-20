<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('phuc_khaos', function (Blueprint $table) {
            if (!Schema::hasColumn('phuc_khaos', 'ma_phuc_khao')) {
                $table->string('ma_phuc_khao')->nullable()->after('id');
            }
            if (!Schema::hasColumn('phuc_khaos', 'file_minh_chung')) {
                $table->string('file_minh_chung')->nullable()->after('ly_do');
            }
            if (!Schema::hasColumn('phuc_khaos', 'le_phi')) {
                $table->decimal('le_phi', 10, 2)->default(50000)->after('file_minh_chung');
            }
            if (!Schema::hasColumn('phuc_khaos', 'trang_thai_thanh_toan')) {
                // cho_thanh_toan | da_thanh_toan | thanh_toan_that_bai
                $table->string('trang_thai_thanh_toan')->default('cho_thanh_toan')->after('le_phi');
            }
            if (!Schema::hasColumn('phuc_khaos', 'phuong_thuc_thanh_toan')) {
                $table->string('phuong_thuc_thanh_toan')->nullable()->after('trang_thai_thanh_toan');
            }
            if (!Schema::hasColumn('phuc_khaos', 'ma_giao_dich')) {
                $table->string('ma_giao_dich')->nullable()->after('phuong_thuc_thanh_toan');
            }
            if (!Schema::hasColumn('phuc_khaos', 'ngay_thanh_toan')) {
                $table->timestamp('ngay_thanh_toan')->nullable()->after('ma_giao_dich');
            }
        });
    }

    public function down(): void
    {
        Schema::table('phuc_khaos', function (Blueprint $table) {
            $table->dropColumn([
                'ma_phuc_khao',
                'file_minh_chung',
                'le_phi',
                'trang_thai_thanh_toan',
                'phuong_thuc_thanh_toan',
                'ma_giao_dich',
                'ngay_thanh_toan',
            ]);
        });
    }
};

