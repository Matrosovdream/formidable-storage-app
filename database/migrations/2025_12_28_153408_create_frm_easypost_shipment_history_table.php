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
        Schema::create('frm_easypost_shipment_history', function (Blueprint $table) {
            $table->id();
            $table->string('easypost_shipment_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->foreignId('site_id')->on('sites');
            $table->string('change_type')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frm_easypost_shipment_history');
    }
};
