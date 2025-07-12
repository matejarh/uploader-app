<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Add the company_id column without the foreign key constraint
            $table->foreignId('company_id')
                ->nullable()
                ->after('user_id');
        });


        // Populate the company_id column with the user's first company_id
        DB::table('documents')->join('users', 'documents.user_id', '=', 'users.id')
            ->leftJoin('companies', 'users.id', '=', 'companies.user_id')
            ->whereNotNull('companies.id')
            ->update(['documents.company_id' => DB::raw('companies.id')]);

        // Add the foreign key constraint
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
