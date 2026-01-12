<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * -------------------------------------------------
         * Shipments
         * -------------------------------------------------
         */
        Schema::create('frm_easypost_shipments', function (Blueprint $table) {
            $table->id();

            $table->string('easypost_shipment_id')->nullable();
            $table->unsignedBigInteger('entry_id')->nullable();

            $table->foreignId('site_id')
                ->constrained('sites');

            $table->boolean('is_return')->default(false);
            $table->string('status')->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('tracking_url')->nullable();
            $table->string('refund_status')->nullable();
            $table->string('mode', 20)->nullable();

            $table->timestamps();

            // ==== INDEXES ====
            $table->unique(
                ['site_id', 'easypost_shipment_id'],
                'uq_ep_shipments_site_shipment'
            );

            $table->index(
                ['site_id', 'entry_id'],
                'idx_ep_shipments_site_entry'
            );

            $table->index(
                ['site_id', 'status'],
                'idx_ep_shipments_site_status'
            );
        });

        /**
         * -------------------------------------------------
         * Shipment Addresses
         * -------------------------------------------------
         */
        Schema::create('frm_easypost_shipment_addresses', function (Blueprint $table) {
            $table->id();

            $table->string('easypost_id');
            $table->string('easypost_shipment_id');
            $table->unsignedBigInteger('entry_id')->nullable();

            $table->foreignId('site_id')
                ->constrained('sites');

            $table->string('address_type', 20);
            $table->string('name')->nullable();
            $table->string('company')->nullable();
            $table->string('street1')->nullable();
            $table->string('street2')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 50)->nullable();
            $table->string('zip', 20)->nullable();
            $table->char('country', 2)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->timestamps();

            // ==== INDEXES ====
            $table->unique(
                ['site_id', 'easypost_id'],
                'uq_ep_addr_site_epid'
            );

            $table->index(
                ['site_id', 'easypost_shipment_id'],
                'idx_ep_addr_site_shipment'
            );

            $table->index(
                ['site_id', 'entry_id'],
                'idx_ep_addr_site_entry'
            );

            $table->index(
                ['site_id', 'easypost_shipment_id', 'address_type'],
                'idx_ep_addr_site_ship_type'
            );
        });

        /**
         * -------------------------------------------------
         * Shipment Labels
         * -------------------------------------------------
         */
        Schema::create('frm_easypost_shipment_labels', function (Blueprint $table) {
            $table->id();

            $table->string('easypost_id');
            $table->string('easypost_shipment_id');
            $table->unsignedBigInteger('entry_id')->nullable();

            $table->foreignId('site_id')
                ->constrained('sites');

            $table->integer('date_advance')->default(0);
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

            // ==== INDEXES ====
            $table->unique(
                ['site_id', 'easypost_id'],
                'uq_ep_label_site_epid'
            );

            $table->index(
                ['site_id', 'easypost_shipment_id'],
                'idx_ep_label_site_shipment'
            );

            $table->index(
                ['site_id', 'entry_id'],
                'idx_ep_label_site_entry'
            );
        });

        /**
         * -------------------------------------------------
         * Shipment Parcels
         * -------------------------------------------------
         */
        Schema::create('frm_easypost_shipment_parcels', function (Blueprint $table) {
            $table->id();

            $table->string('easypost_id');
            $table->string('easypost_shipment_id');
            $table->unsignedBigInteger('entry_id')->nullable();

            $table->foreignId('site_id')
                ->constrained('sites');

            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('weight', 10, 2)->nullable();

            $table->timestamps();

            // ==== INDEXES ====
            $table->unique(
                ['site_id', 'easypost_id'],
                'uq_ep_parcel_site_epid'
            );

            $table->index(
                ['site_id', 'easypost_shipment_id'],
                'idx_ep_parcel_site_shipment'
            );

            $table->index(
                ['site_id', 'entry_id'],
                'idx_ep_parcel_site_entry'
            );
        });

        /**
         * -------------------------------------------------
         * Shipment Rates
         * -------------------------------------------------
         */
        Schema::create('frm_easypost_shipment_rates', function (Blueprint $table) {
            $table->id();

            $table->string('easypost_id');
            $table->string('easypost_shipment_id');
            $table->unsignedBigInteger('entry_id')->nullable();

            $table->foreignId('site_id')
                ->constrained('sites');

            $table->string('mode', 20)->default('test');
            $table->string('service', 100)->nullable();
            $table->string('carrier', 100)->nullable();

            $table->decimal('rate', 12, 2)->nullable();
            $table->char('currency', 3)->nullable();

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

            // ==== INDEXES ====
            $table->unique(
                ['site_id', 'easypost_id'],
                'uq_ep_rate_site_epid'
            );

            $table->index(
                ['site_id', 'easypost_shipment_id'],
                'idx_ep_rate_site_shipment'
            );

            $table->index(
                ['site_id', 'entry_id'],
                'idx_ep_rate_site_entry'
            );

            $table->index(
                ['site_id', 'carrier', 'service'],
                'idx_ep_rate_site_carrier_service'
            );

            $table->index(
                ['site_id', 'easypost_shipment_id', 'rate'],
                'idx_ep_rate_site_ship_rate'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frm_easypost_shipment_rates');
        Schema::dropIfExists('frm_easypost_shipment_parcels');
        Schema::dropIfExists('frm_easypost_shipment_labels');
        Schema::dropIfExists('frm_easypost_shipment_addresses');
        Schema::dropIfExists('frm_easypost_shipments');
    }
};
