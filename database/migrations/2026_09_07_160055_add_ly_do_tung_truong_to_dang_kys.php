<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dang_kys', function (Blueprint $table) {
            // Lưu lý do bổ sung riêng cho từng trường dạng JSON: {"so_cccd": "Số CCCD không khớp", ...}
            $table->json('ly_do_tung_truong')->nullable()->after('ly_do_bo_sung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dang_kys', function (Blueprint $table) {
            $table->dropColumn('ly_do_tung_truong');
        });
    }
};
