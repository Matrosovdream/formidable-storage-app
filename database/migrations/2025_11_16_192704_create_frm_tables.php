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

        Schema::create('frm_fields', function (Blueprint $table) {
            $table->id();
            $table->integer('field_id')->nullable();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('key')->nullable();
            $table->string('type')->nullable();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // frm_entry_update_types
        Schema::create('frm_entry_update_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        // Formidable entry update operations
        Schema::create('frm_entry_history', function (Blueprint $table) {
            $table->id();
            $table->integer('entry_id')->nullable();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('field_id')->constrained('frm_fields')->onDelete('cascade');
            $table->foreignId('update_type_id')->constrained('frm_entry_update_types')->nullable();
            $table->text('value')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frm_entry_history');
        Schema::dropIfExists('frm_fields');
        Schema::dropIfExists('frm_entry_update_types');
        
    }
};
