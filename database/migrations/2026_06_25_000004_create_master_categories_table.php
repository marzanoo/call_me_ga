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
        Schema::create('master_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('master_categories')->insert([
            ['name' => 'Lampu', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meja', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kursi', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'AC', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Toilet', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Listrik', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_categories');
    }
};
