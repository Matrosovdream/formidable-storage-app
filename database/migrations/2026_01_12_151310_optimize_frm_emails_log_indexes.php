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
        Schema::table('frm_emails_log', function (Blueprint $table) {

            /**
             * ================
             * Core indexes
             * ================
             */

            // entry logs per site
            $table->index(['site_id', 'entry_id'], 'idx_eml_site_entry');

            // form logs per site
            $table->index(['site_id', 'form_id'], 'idx_eml_site_form');

            // recent logs per site (sorting / ranges)
            $table->index(['site_id', 'date_sent'], 'idx_eml_site_date_sent');

            // status queues / errors by time
            $table->index(['site_id', 'status', 'date_sent'], 'idx_eml_site_status_date');

            // filtering by mailer + time (optional but often useful)
            $table->index(['site_id', 'mailer', 'date_sent'], 'idx_eml_site_mailer_date');

            // link to original log (retries/threads)
            $table->index(['site_id', 'original_log_id'], 'idx_eml_site_original');


            /**
             * ================
             * message_id indexes
             * ================
             */

            // global lookup by message_id (when site_id not known)
            $table->index(['message_id'], 'idx_eml_message_id');
            $table->index(['site_id', 'message_id'], 'idx_eml_site_message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frm_emails_log', function (Blueprint $table) {
            $table->dropIndex('idx_eml_site_entry');
            $table->dropIndex('idx_eml_site_form');
            $table->dropIndex('idx_eml_site_date_sent');
            $table->dropIndex('idx_eml_site_status_date');
            $table->dropIndex('idx_eml_site_mailer_date');
            $table->dropIndex('idx_eml_site_original');
            $table->dropIndex('idx_eml_message_id');
            $table->dropIndex('idx_eml_site_message_id');
        });
    }
};
