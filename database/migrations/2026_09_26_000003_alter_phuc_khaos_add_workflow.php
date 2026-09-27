<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('phuc_khaos', function (Blueprint $table) {
            $table->foreignId('giang_vien_id')->nullable()->after('sinh_vien_id')->constrained('users')->nullOnDelete();
            $table->foreignId('admin_tiep_nhan_id')->nullable()->after('giang_vien_id')->constrained('users')->nullOnDelete();
            $table->timestamp('ngay_tiep_nhan')->nullable()->after('admin_tiep_nhan_id');
            $table->foreignId('admin_duyet_id')->nullable()->after('ngay_tiep_nhan')->constrained('users')->nullOnDelete();
            $table->timestamp('ngay_duyet')->nullable()->after('admin_duyet_id');
            $table->text('ly_do_duyet')->nullable()->after('ngay_duyet');
        });
    }

    public function down(): void
    {
        Schema::table('phuc_khaos', function (Blueprint $table) {
            $table->dropForeign(['giang_vien_id']);
            $table->dropForeign(['admin_tiep_nhan_id']);
            $table->dropForeign(['admin_duyet_id']);
            $table->dropColumn([
                'giang_vien_id',
                'admin_tiep_nhan_id',
                'ngay_tiep_nhan',
                'admin_duyet_id',
                'ngay_duyet',
                'ly_do_duyet',
            ]);
        });
    }
};
