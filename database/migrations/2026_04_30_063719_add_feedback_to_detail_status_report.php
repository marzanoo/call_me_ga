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
        Schema::table('detail_status_reports', function (Blueprint $table) {
            $table->text('feedback')->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_status_reports', function (Blueprint $table) {
            $table->dropColumn('feedback');
        });
    }
};
