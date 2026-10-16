<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm;');

            DB::statement('CREATE INDEX IF NOT EXISTS listings_name_trgm_idx ON listings USING gin (name gin_trgm_ops);');
            DB::statement('CREATE INDEX IF NOT EXISTS listings_address_trgm_idx ON listings USING gin (address gin_trgm_ops);');
            DB::statement('CREATE INDEX IF NOT EXISTS listings_description_trgm_idx ON listings USING gin (description gin_trgm_ops);');
            DB::statement('CREATE INDEX IF NOT EXISTS listings_area_trgm_idx ON listings USING gin (area gin_trgm_ops);');
            DB::statement('CREATE INDEX IF NOT EXISTS listings_status_gender_idx ON listings (status, gender_type);');
            DB::statement('CREATE INDEX IF NOT EXISTS rooms_price_status_idx ON rooms (listing_id, status, available_rooms, price_monthly);');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS listings_name_trgm_idx;');
            DB::statement('DROP INDEX IF EXISTS listings_address_trgm_idx;');
            DB::statement('DROP INDEX IF EXISTS listings_description_trgm_idx;');
            DB::statement('DROP INDEX IF EXISTS listings_area_trgm_idx;');
            DB::statement('DROP INDEX IF EXISTS listings_status_gender_idx;');
            DB::statement('DROP INDEX IF EXISTS rooms_price_status_idx;');
        }
    }
};
