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
        Schema::create('token_connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refresh_token_id');
            $table->foreign('refresh_token_id')->references('id')->on('personal_access_tokens');
            $table->unsignedBigInteger('access_token_id');
            $table->foreign('access_token_id')->references('id')->on('personal_access_tokens');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_connections');
    }
};
