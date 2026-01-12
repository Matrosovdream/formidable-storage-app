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
        Schema::table('frm_easypost_shipment_history', function (Blueprint $table) {

            $table->index(
                ['site_id', 'easypost_shipment_id', 'created_at'],
                'idx_ep_hist_site_shipment_created'
            );

            $table->index(
                ['site_id', 'created_at'],
                'idx_ep_hist_site_created'
            );

            $table->index(
                ['site_id', 'change_type'],
                'idx_ep_hist_site_change_type'
            );

            $table->index(
                ['site_id', 'user_id'],
                'idx_ep_hist_site_user'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frm_easypost_shipment_history', function (Blueprint $table) {
            $table->dropIndex('idx_ep_hist_site_shipment_created');
            $table->dropIndex('idx_ep_hist_site_created');
            $table->dropIndex('idx_ep_hist_site_change_type');
            $table->dropIndex('idx_ep_hist_site_user');
        });
    }
};
