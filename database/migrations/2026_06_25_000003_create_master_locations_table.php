<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_locations', function (Blueprint $table) {
            $table->id();
            $table->string('area');
            $table->string('detail');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['area', 'detail']);
        });

        DB::table('master_locations')->insert([
            ['area' => 'Jatake', 'detail' => 'Gedung MDTC', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Jatake', 'detail' => 'Gedung WARI', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Jatake', 'detail' => 'Gudang Unit', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Jatake', 'detail' => 'Bangunan Penunjang', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Gunung Sahari', 'detail' => 'Lobby Gedung', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Gunung Sahari', 'detail' => 'Lantai 2', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Gunung Sahari', 'detail' => 'Lantai 3', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Gunung Sahari', 'detail' => 'Lantai 4', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Gunung Sahari', 'detail' => 'Lantai 5', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Gunung Sahari', 'detail' => 'Lantai 6', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Gunung Sahari', 'detail' => 'Lantai 7', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['area' => 'Gunung Sahari', 'detail' => 'Lantai 8', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_locations');
    }
};
