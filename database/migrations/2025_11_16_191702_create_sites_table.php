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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->unique();
            $table->timestamps();
        });

        // Site tokens for API access could be added here, site_tokens
        Schema::create('site_tokens', function (Blueprint $table) {
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('token')->unique();
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_tokens');
        Schema::dropIfExists('sites');
        
    }
};
