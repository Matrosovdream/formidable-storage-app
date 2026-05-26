<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop duplicates before applying the unique constraint, otherwise the index would fail.
        $duplicates = DB::table('frm_fields')
            ->select('site_id', 'field_id', DB::raw('MIN(id) as keep_id'))
            ->whereNotNull('field_id')
            ->groupBy('site_id', 'field_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('frm_fields')
                ->where('site_id', $dup->site_id)
                ->where('field_id', $dup->field_id)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        Schema::table('frm_fields', function (Blueprint $table) {
            $table->unique(['site_id', 'field_id'], 'frm_fields_site_field_unique');
        });
    }

    public function down(): void
    {
        Schema::table('frm_fields', function (Blueprint $table) {
            $table->dropUnique('frm_fields_site_field_unique');
        });
    }
};
