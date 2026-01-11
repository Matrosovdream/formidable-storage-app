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

        Schema::create('frm_easypost_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('easypost_shipment_id')->nullable();
            $table->integer('entry_id')->nullable();
            $table->foreignId('site_id')->on('sites');
            $table->boolean('is_return')->default(false);
            $table->string('status')->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('tracking_url')->nullable();
            $table->string('refund_status')->nullable();
            $table->string('mode')->nullable();
            $table->timestamps();
        });

        /**
         * wp_frm_easypost_shipment_addresses
         */
        Schema::create('frm_easypost_shipment_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('easypost_id');
            $table->string('easypost_shipment_id');
            $table->integer('entry_id')->nullable();
            $table->foreignId('site_id')->on('sites');
            $table->string('address_type');
            $table->string('name')->nullable();
            $table->string('company')->nullable();
            $table->string('street1')->nullable();
            $table->string('street2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            // Helpful indexes (optional but recommended)
            $table->index('easypost_shipment_id');
            $table->index(['entry_id'], 'idx_ep_addr_entry');
            $table->index(['easypost_id'], 'idx_ep_addr_epid');
        });

        /**
         * wp_frm_easypost_shipment_label
         */
        Schema::create('frm_easypost_shipment_labels', function (Blueprint $table) {
            $table->id();
            $table->string('easypost_id');
            $table->string('easypost_shipment_id');
            $table->integer('entry_id')->nullable();
            $table->foreignId('site_id')->on('sites');
            $table->integer('date_advance')->nullable()->default(0);
            $table->string('integrated_form')->nullable();
            $table->dateTime('label_date')->nullable();
            $table->integer('label_resolution')->nullable();
            $table->string('label_size', 20)->nullable();
            $table->string('label_type', 50)->nullable();
            $table->string('label_file_type', 50)->nullable();
            $table->text('label_url')->nullable();
            $table->text('label_pdf_url')->nullable();
            $table->text('label_zpl_url')->nullable();
            $table->text('label_epl2_url')->nullable();
            $table->timestamps();

            $table->index('easypost_shipment_id');
            $table->index(['entry_id'], 'idx_ep_label_entry');
            $table->index(['easypost_id'], 'idx_ep_label_epid');
        });

        /**
         * wp_frm_easypost_shipment_parcel
         */
        Schema::create('frm_easypost_shipment_parcels', function (Blueprint $table) {
            $table->id(); 
            $table->string('easypost_id');
            $table->string('easypost_shipment_id');
            $table->integer('entry_id')->nullable();
            $table->foreignId('site_id')->on('sites');
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->timestamps();

            $table->index('easypost_shipment_id');
            $table->index(['entry_id'], 'idx_ep_parcel_entry');
            $table->index(['easypost_id'], 'idx_ep_parcel_epid');
        });

        /**
         * wp_frm_easypost_shipment_rate
         */
        Schema::create('frm_easypost_shipment_rates', function (Blueprint $table) {
            $table->id(); 
            $table->string('easypost_id');
            $table->string('easypost_shipment_id');
            $table->integer('entry_id')->nullable();
            $table->foreignId('site_id')->on('sites');
            $table->string('mode', 20)->default('test');
            $table->string('service', 100)->nullable();
            $table->string('carrier', 100)->nullable();
            $table->decimal('rate', 12, 2)->nullable();
            $table->char('currency', 10)->nullable();
            $table->decimal('retail_rate', 12, 2)->nullable();
            $table->char('retail_currency', 3)->nullable();
            $table->decimal('list_rate', 12, 2)->nullable();
            $table->char('list_currency', 3)->nullable();
            $table->string('billing_type', 50)->nullable();
            $table->integer('delivery_days')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->boolean('delivery_date_guaranteed')->nullable();
            $table->integer('est_delivery_days')->nullable();
            $table->timestamps();

            $table->index('easypost_shipment_id');
            $table->index(['entry_id'], 'idx_ep_rate_entry');
            $table->index(['easypost_id'], 'idx_ep_rate_epid');
            $table->index(['carrier', 'service'], 'idx_ep_rate_carrier_service');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frm_easypost_shipments');
        Schema::dropIfExists('frm_easypost_shipment_addresses');
        Schema::dropIfExists('frm_easypost_shipment_labels');
        Schema::dropIfExists('frm_easypost_shipment_parcels');
        Schema::dropIfExists('frm_easypost_shipment_rates');
    }
};
