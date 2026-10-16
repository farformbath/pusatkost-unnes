<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Booking\Actions\ExpireBookingsAction;
use Illuminate\Console\Command;

class ExpireBookingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan secara otomatis pesanan pending yang telah melewati batas waktu pembayaran 24 jam dan kembalikan stok kamar.';

    public function handle(ExpireBookingsAction $action): int
    {
        $count = $action->execute();
        $this->info("Berhasil memproses {$count} pesanan kadaluarsa.");

        return self::SUCCESS;
    }
}
