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


        // Populate the company_id column with the user's first company_id.
        // SQLite does not allow updating a table using a joined table reference in the SET clause,
        // so we use a correlated subquery instead.
        DB::table('documents')->whereNotNull('user_id')->update([
            'company_id' => DB::raw('(SELECT companies.id FROM companies WHERE companies.user_id = documents.user_id LIMIT 1)'),
        ]);

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
