# ⚙️ Backend Architecture Specification — UNNESKost v3

Dokumen ini mendefinisikan standar arsitektur backend, pola desain (*Design Patterns*), konvensi kode, dan struktur Bounded Context pada **UNNESKost v3**.

---

## 1. Prinsip Utama Backend

1. **Modular Monolith & DDD-Lite:** Logika bisnis diisolasi di dalam Bounded Context (`app/Domain/`).
2. **Action Pattern:** 1 class = 1 operasi bisnis tunggal dengan method `handle()` atau `__invoke()`.
3. **HTTP-Agnostic Domain:** Domain layer tidak boleh bergantung pada HTTP (`request()`, `session()`, `auth()`, `redirect()` dilarang di dalam `app/Domain/`).
4. **Thin Controllers:** Controller hanya bertindak sebagai orkestrator (Validasi FormRequest $\rightarrow$ Panggil Action/Query $\rightarrow$ Return Inertia/API Resource).
5. **Event-Driven Side Effects:** Efek samping (notifikasi, audit, sinkronisasi index search) diproses secara asynchronous via Domain Events & Redis Queue.

---

## 2. Struktur Direktori Backend

```
app/
├── Domain/                      # 🧠 INTI BISNIS (HTTP-Agnostic)
│   ├── Listing/                 # Bounded Context: Listing, Kamar, Fasilitas, Galeri
│   │   ├── Models/              # Rich Eloquent Models (scopes, casts, invariants)
│   │   ├── Actions/             # Operasi bisnis (CreateListingAction, UpdateRoomStatusAction)
│   │   ├── Repositories/        # Repository Interfaces & Eloquent Implementations
│   │   ├── Services/            # Logika lintas model (RoomAvailabilityService)
│   │   ├── DTOs/                # Data Transfer Objects (immutable/readonly)
│   │   ├── Enums/               # Backed Enums (RoomStatus, ListingType, dll)
│   │   ├── Events/              # Domain Events (ListingCreated, RoomStatusChanged)
│   │   ├── Listeners/           # Event Listeners (queued via Redis)
│   │   └── Policies/            # Domain Authorization (ListingPolicy, RoomPolicy)
│   ├── Booking/                 # Bounded Context: Pemesanan, Pembayaran, Status Audit
│   │   ├── Models/  Actions/  Repositories/  Services/  DTOs/  Enums/  Events/  Listeners/  Policies/
│   │   └── Exceptions/          # RoomNotAvailableException, BookingConflictException
│   ├── Review/                  # Bounded Context: Ulasan & Moderasi
│   ├── Favorite/                # Bounded Context: Wishlist
│   ├── Search/                  # Bounded Context: Scout & Meilisearch Query Objects
│   └── User/                    # Bounded Context: Akun, Profil, & Spatie RBAC
├── Http/                        # 🚪 ADAPTER LAYER (Tipis)
│   ├── Controllers/
│   │   ├── Admin/               # Controller khusus dashboard Admin
│   │   ├── Owner/               # Controller khusus dashboard Owner
│   │   ├── Public/              # Controller publik (Katalog, Detail Kos, Search)
│   │   ├── Booking/             # Controller alur booking & pembayaran
│   │   └── Api/V1/              # REST API untuk mobile/integrasi eksternal
│   ├── Requests/                # FormRequest (1 operasi = 1 Request class)
│   ├── Middleware/              # Role check, Inertia share, Correlation ID (X-Request-Id)
│   └── Resources/               # API Resources untuk /api/v1
├── Support/                     # Helper murni (ApiResponse, Money, DTO Base)
└── Providers/                   # Service Providers & Repository Bindings
```

---

## 3. Implementasi Action Pattern & DTO

### 3.1 DTO (Data Transfer Object)
Semua data yang masuk ke Domain layer wajib dibungkus dalam DTO yang immutable:

```php
namespace App\Domain\Booking\DTOs;

readonly class BookRoomDTO
{
    public function __construct(
        public string $userId,
        public string $roomId,
        public string $startDate,
        public int $durationMonths,
        public ?string $notes = null,
    ) {}

    public static function fromRequest(StoreBookingRequest $request): self
    {
        return new self(
            userId: (string) $request->user()->id,
            roomId: (string) $request->validated('room_id'),
            startDate: (string) $request->validated('start_date'),
            durationMonths: (int) $request->validated('duration_months'),
            notes: $request->validated('notes'),
        );
    }
}
```

### 3.2 Action Class
Setiap Action hanya menangani 1 transaksi/operasi bisnis:

```php
namespace App\Domain\Booking\Actions;

use App\Domain\Booking\DTOs\BookRoomDTO;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Services\RoomAvailabilityService;
use App\Domain\Booking\Events\BookingCreated;
use Illuminate\Support\Facades\DB;

class BookRoomAction
{
    public function __construct(
        private RoomAvailabilityService $availabilityService
    ) {}

    public function handle(BookRoomDTO $dto): Booking
    {
        return DB::transaction(function () use ($dto) {
            // 1. Validasi ketersediaan kamar dengan lock
            $room = $this->availabilityService->lockAndValidate($dto->roomId, $dto->startDate, $dto->durationMonths);

            // 2. Hitung total harga (integer)
            $totalPrice = $room->harga_bulanan * $dto->durationMonths;

            // 3. Simpan record Booking
            $booking = Booking::create([
                'user_id' => $dto->userId,
                'room_id' => $dto->roomId,
                'tanggal_mulai' => $dto->startDate,
                'durasi_bulan' => $dto->durationMonths,
                'total_harga' => $totalPrice,
                'status' => BookingStatus::PENDING,
            ]);

            // 4. Ubah status kamar & catat audit
            $room->updateStatus(RoomStatus::BOOKED, changedBy: $dto->userId, reason: 'New booking created');

            // 5. Dispatch domain event
            event(new BookingCreated($booking));

            return $booking;
        });
    }
}
```

---

## 4. Thin Controller Example

```php
namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Domain\Booking\Actions\BookRoomAction;
use App\Domain\Booking\DTOs\BookRoomDTO;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request, BookRoomAction $action)
    {
        $dto = BookRoomDTO::fromRequest($request);
        $booking = $action->handle($dto);

        return redirect()
            ->route('bookings.show', $booking->id)
            ->with('success', 'Kamar berhasil dipesan! Silakan lanjutkan pembayaran.');
    }
}
```

---

## 5. Standar Testing Backend (Pest)

- **Unit Testing:** Menguji DTO, Action, Service, dan Invariants tanpa dependensi HTTP.
- **Feature Testing:** Menguji FormRequest validation, Controller flow, Authorization Policies, Database Constraints, dan Queued Listeners.
- **PHPStan Level Max:** Semua class dan method harus memiliki type hint lengkap dan lolos analisis statis level tertinggi.
