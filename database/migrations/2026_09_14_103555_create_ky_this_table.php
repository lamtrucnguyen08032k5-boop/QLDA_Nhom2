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
        Schema::create('ky_this', function (Blueprint $table) {
            $table->id();
            $table->string('ten_ky_thi');
            $table->string('nam_hoc')->nullable(); // VD: 2025-2026
            $table->string('hoc_ky')->nullable(); // VD: Học kỳ 1
            $table->text('mo_ta')->nullable();
            $table->string('trang_thai')->default('dang_mo_dang_ky'); // dang_mo_dang_ky | da_dong_dang_ky | dang_dien_ra | da_ket_thuc
            $table->timestamps();
        });

        Schema::table('lich_this', function (Blueprint $table) {
            $table->foreignId('ky_thi_id')->nullable()->after('id')->constrained('ky_this')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_this', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ky_thi_id');
        });
        Schema::dropIfExists('ky_this');
    }
};
