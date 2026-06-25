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
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->string('lokasi_area')->nullable()->after('lokasi');
            $table->string('lokasi_detail')->nullable()->after('lokasi_area');
            $table->string('lokasi_catatan')->nullable()->after('lokasi_detail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn([
                'assigned_to',
                'lokasi_area',
                'lokasi_detail',
                'lokasi_catatan',
            ]);
        });
    }
};
