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
            $table->foreignId('site_id')->on('sites');
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
            $table->foreignId('site_id')->on('sites');
            $table->foreignId('field_id')->on('frm_fields');
            $table->integer('user_id')->nullable();
            $table->foreignId('update_type_id')->constrained('frm_entry_update_types')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->dateTime('change_date')->nullable();
            $table->timestamps();
        });

        Schema::create('frm_emails_log', function (Blueprint $table) {
            $table->id();
            $table->integer('entry_id')->nullable();
            $table->foreignId('site_id')->on('sites');
            $table->integer('form_id')->nullable();
            $table->string('subject');
            $table->string('message_id')->nullable();
            $table->string('email_from')->nullable();
            $table->string('email_to')->nullable();
            $table->text('people')->nullable();
            $table->text('headers')->nullable();
            $table->text('error_text')->nullable();
            $table->longText('content_plain');
            $table->longText('content_html');
            $table->tinyInteger('status')->default(0);
            $table->timestamp('date_sent')->nullable();
            $table->string('mailer')->nullable();
            $table->tinyInteger('attachments')->default(0);
            $table->string('initiator_name')->nullable();
            $table->text('initiator_file')->nullable();
            $table->integer('original_log_id')->nullable();
            $table->timestamps();

            $table->unique(['site_id', 'message_id'], 'frm_emails_log_unique');
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
        Schema::dropIfExists('frm_emails_log');
        
    }
};
