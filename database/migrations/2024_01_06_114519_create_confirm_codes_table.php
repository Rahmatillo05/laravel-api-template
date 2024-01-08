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
        Schema::create('confirm_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('key');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_used')->default(false);
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('confirm_codes');
    }
};
