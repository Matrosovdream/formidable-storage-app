<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frm_emails_log', function (Blueprint $table) {
            $table->string('subject')->nullable()->change();
            $table->longText('content_plain')->nullable()->change();
            $table->longText('content_html')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('frm_emails_log', function (Blueprint $table) {
            $table->string('subject')->nullable(false)->change();
            $table->longText('content_plain')->nullable(false)->change();
            $table->longText('content_html')->nullable(false)->change();
        });
    }
};
