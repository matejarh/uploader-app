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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_address')->nullable();
            $table->string('logo_photo_path', 2048)->nullable();
            $table->timestamps();
        });


        DB::table('users')->whereNotNull('company_name')->get()->each(function ($user) {
            DB::table('companies')->insert([
                'user_id' => $user->id,
                'company_name' => $user->company_name,
                'company_address' => '', // Provide a default or fetch if available
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
