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
        Schema::table('images', function (Blueprint $table) {
            Schema::table('images', function (Blueprint $table) {
                $table->unsignedBigInteger('destination_id')->nullable()->after('id'); // or remove nullable() if it's required
                $table->foreign('destination_id')->references('id')->on('destinations')->onDelete('cascade');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            Schema::table('images', function (Blueprint $table) {
                $table->dropForeign(['destination_id']);
                $table->dropColumn('destination_id');
            });
        });
    }
};
