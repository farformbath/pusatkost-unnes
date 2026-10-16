# NESA (UNNES Smart Assistant) Agentic RAG Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Membangun NESA (UNNES Smart Assistant), asisten percakapan cerdas berbasis Agentic Tool Calling (RAG) di sekitar UNNES menggunakan InternLM Discovery API (OpenAI-compatible) dengan toleransi kegagalan (fallback) dan antarmuka kartu interaktif.

**Architecture:** Bounded context `app/Domain/Ai/` dalam arsitektur Modular Monolith Laravel 12. Menggunakan HTTP Client bawaan framework untuk komunikasi ke `https://discovery-api.intern-ai.org.cn/v1`, pemanggilan tool ke repository database kos riil, penyimpanan riwayat sesi di PostgreSQL, dan widget floating React 19 di frontend.

**Tech Stack:** Laravel 12, PHP 8.4+, PostgreSQL 16, React 19, Inertia.js v3, Tailwind CSS v4, Pest PHP.

**Spec:** [`docs/superpowers/specs/2026-10-16-nesa-agentic-rag-chatbot-design.md`](file:///c:/projects/unneskost2/docs/superpowers/specs/2026-10-16-nesa-agentic-rag-chatbot-design.md)

## Global Constraints
- **Prinsip Ponytail & YAGNI:** 0 paket Composer baru (gunakan `Http::class` bawaan Laravel), 0 paket npm baru.
- **Strict PHP:** Setiap file PHP baru wajib diawali `declare(strict_types=1);`.
- **Strict Zero Axios:** Tidak boleh menggunakan axios. Gunakan `fetch` native di browser.
- **Strict TypeScript:** `0` tipe `any` pada kode frontend.
- **Definisi Selesai:** Pest test hijau, PHPStan Level Max bersih, Laravel Pint passed.

---

### Task 1: Konfigurasi AI & Migrasi Database Sesi Percakapan

**Files:**
- Create: `config/ai.php`
- Modify: `.env.example`
- Create: `database/migrations/2026_09_02_000003_create_nesa_chat_tables.php`
- Create: `app/Domain/Ai/Models/NesaConversation.php`
- Create: `app/Domain/Ai/Models/NesaMessage.php`
- Test: `tests/Feature/NesaDatabaseModelTest.php`

**Interfaces:**
- Produces: Model `NesaConversation` dan `NesaMessage` dengan relasi hasMany/belongsTo dan UUID primary key.

- [ ] **Step 1: Tulis feature test database dan relasi model NESA**

```php
<?php

declare(strict_types=1);

use App\Domain\Ai\Models\NesaConversation;
use App\Domain\Ai\Models\NesaMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('nesa conversation and messages can be created with uuid and relations', function () {
    $user = User::factory()->create();
    $conversation = NesaConversation::create([
        'user_id' => $user->id,
        'session_token' => 'test-session-12345',
        'title' => 'Tanya Kos Putri Sekaran',
    ]);

    expect($conversation->id)->not->toBeNull()
        ->and($conversation->session_token)->toBe('test-session-12345');

    $message = NesaMessage::create([
        'conversation_id' => $conversation->id,
        'role' => 'user',
        'content' => 'Ada kos putri di Sekaran dekat FBS?',
        'recommended_listing_ids' => ['00000000-0000-0000-0000-000000000001'],
        'tokens_used' => 120,
        'latency_ms' => 450,
    ]);

    expect($message->conversation->id)->toBe($conversation->id)
        ->and($conversation->messages)->toHaveCount(1)
        ->and($message->recommended_listing_ids)->toBe(['00000000-0000-0000-0000-000000000001']);
});
```

- [ ] **Step 2: Jalankan test untuk memverifikasi kegagalan**

Run: `php artisan test --filter=NesaDatabaseModelTest`
Expected: FAIL (Class NesaConversation not found atau tabel belum ada)

- [ ] **Step 3: Buat konfigurasi `config/ai.php` dan update `.env.example`**

Tambahkan baris berikut di `.env.example`:
```env
AI_BASE_URL=https://discovery-api.intern-ai.org.cn/v1
AI_API_KEY=
AI_MODEL=intern-latest
AI_MAX_TOKENS=1024
AI_TEMPERATURE=0.7
AI_TIMEOUT_SECONDS=15
```

Buat `config/ai.php`:
```php
<?php

declare(strict_types=1);

return [
    'base_url' => env('AI_BASE_URL', 'https://discovery-api.intern-ai.org.cn/v1'),
    'api_key' => env('AI_API_KEY', ''),
    'model' => env('AI_MODEL', 'intern-latest'),
    'max_tokens' => (int) env('AI_MAX_TOKENS', 1024),
    'temperature' => (float) env('AI_TEMPERATURE', 0.7),
    'timeout_seconds' => (int) env('AI_TIMEOUT_SECONDS', 15),
];
```

- [ ] **Step 4: Buat migrasi database `database/migrations/2026_09_02_000003_create_nesa_chat_tables.php`**

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nesa_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_token', 100)->index();
            $table->string('title', 255)->default('Percakapan Baru');
            $table->timestamps();
        });

        Schema::create('nesa_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->constrained('nesa_conversations')->cascadeOnDelete();
            $table->string('role', 20); // user, assistant, tool, system
            $table->text('content');
            $table->jsonb('tool_calls')->nullable();
            $table->string('tool_call_id', 100)->nullable();
            $table->jsonb('recommended_listing_ids')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->integer('latency_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nesa_messages');
        Schema::dropIfExists('nesa_conversations');
    }
};
```

- [ ] **Step 5: Buat Model `NesaConversation.php` dan `NesaMessage.php` di `app/Domain/Ai/Models/`**

`app/Domain/Ai/Models/NesaConversation.php`:
```php
<?php

declare(strict_types=1);

namespace App\Domain\Ai\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NesaConversation extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'session_token',
        'title',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<NesaMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(NesaMessage::class, 'conversation_id')->orderBy('created_at', 'asc');
    }
}
```

`app/Domain/Ai/Models/NesaMessage.php`:
```php
<?php

declare(strict_types=1);

namespace App\Domain\Ai\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NesaMessage extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tool_calls',
        'tool_call_id',
        'recommended_listing_ids',
        'tokens_used',
        'latency_ms',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'tool_calls' => 'array',
        'recommended_listing_ids' => 'array',
        'tokens_used' => 'integer',
        'latency_ms' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<NesaConversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(NesaConversation::class, 'conversation_id');
    }
}
```

- [ ] **Step 6: Jalankan migrasi dan tes verifikasi**

Run:
```powershell
php artisan migrate
php artisan test --filter=NesaDatabaseModelTest
```
Expected: PASS

---

### Task 2: Implementasi LLM Service & Tool Calling Handler

**Files:**
- Create: `app/Domain/Ai/Services/NesaLlmService.php`
- Create: `app/Domain/Ai/Services/NesaToolService.php`
- Test: `tests/Unit/Domain/Ai/NesaLlmServiceTest.php`

**Interfaces:**
- Consumes: `config('ai')`, `App\Domain\Search\Actions\SearchListingsAction`
- Produces: `NesaLlmService::sendChat(array $messages): array`, `NesaToolService::execute(string $toolName, array $arguments): array`

- [ ] **Step 1: Tulis unit test untuk `NesaLlmService` dan mock HTTP call**

```php
<?php

declare(strict_types=1);

use App\Domain\Ai\Services\NesaLlmService;
use App\Domain\Ai\Services\NesaToolService;
use Illuminate\Support\Facades\Http;

test('nesa llm service sends chat completion request and handles tool call response', function () {
    Http::fake([
        '*/chat/completions' => Http::response([
            'id' => 'chatcmpl-123',
            'choices' => [
                [
                    'message' => [
                        'role' => 'assistant',
                        'content' => null,
                        'tool_calls' => [
                            [
                                'id' => 'call_abc',
                                'type' => 'function',
                                'function' => [
                                    'name' => 'search_kos',
                                    'arguments' => json_encode(['gender' => 'putri', 'area' => 'Sekaran']),
                                ],
                            ],
                        ],
                    ],
                    'finish_reason' => 'tool_calls',
                ],
            ],
            'usage' => [
                'total_tokens' => 85,
            ],
        ], 200),
    ]);

    $service = new NesaLlmService(app(NesaToolService::class));
    $response = $service->sendChat([
        ['role' => 'user', 'content' => 'Cari kos putri di Sekaran dong'],
    ]);

    expect($response)->toHaveKeys(['content', 'tool_calls', 'tokens_used'])
        ->and($response['tool_calls'])->toHaveCount(1)
        ->and($response['tool_calls'][0]['function']['name'])->toBe('search_kos');
});
```

- [ ] **Step 2: Jalankan test untuk memverifikasi kegagalan**

Run: `php artisan test --filter=NesaLlmServiceTest`
Expected: FAIL (Class NesaLlmService not found)

- [ ] **Step 3: Implementasikan `NesaToolService.php`**

`app/Domain/Ai/Services/NesaToolService.php`:
Mendefinisikan skema tools (OpenAI format) dan handler eksekusi yang memanggil `SearchListingsAction` atau detail kampus UNNES.

- [ ] **Step 4: Implementasikan `NesaLlmService.php`**

`app/Domain/Ai/Services/NesaLlmService.php`:
Melakukan panggilan HTTP ke `config('ai.base_url')` dengan timeout, header bearer token, dan penanganan fallback jika HTTP call throw error.

- [ ] **Step 5: Jalankan test verifikasi**

Run: `php artisan test --filter=NesaLlmServiceTest`
Expected: PASS

---

### Task 3: Orchestration Action & Fallback Handling

**Files:**
- Create: `app/Domain/Ai/Actions/SendNesaMessageAction.php`
- Test: `tests/Feature/NesaChatActionTest.php`

**Interfaces:**
- Consumes: `NesaLlmService`, `NesaToolService`, `NesaConversation`, `NesaMessage`
- Produces: `SendNesaMessageAction::execute(string $message, string $sessionToken, ?int $userId = null): array`

- [ ] **Step 1: Tulis feature test orkestrasi pesan dan fallback**

```php
<?php

declare(strict_types=1);

use App\Domain\Ai\Actions\SendNesaMessageAction;
use App\Domain\Ai\Models\NesaConversation;
use Database\Seeders\FacilitySeeder;
use Database\Seeders\ListingSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->seed(FacilitySeeder::class);
    $this->seed(ListingSeeder::class);
});

test('send nesa message action executes agentic loop with tool calls and saves conversation', function () {
    Http::fake([
        '*/chat/completions' => Http::sequence()
            ->push([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => null,
                            'tool_calls' => [
                                [
                                    'id' => 'call_1',
                                    'type' => 'function',
                                    'function' => [
                                        'name' => 'search_kos',
                                        'arguments' => json_encode(['gender' => 'putri', 'area' => 'Sekaran']),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'usage' => ['total_tokens' => 50],
            ], 200)
            ->push([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Halo kak! Di Sekaran ada Kost Putri Melati yang sangat dekat dengan FBS UNNES.',
                        ],
                    ],
                ],
                'usage' => ['total_tokens' => 90],
            ], 200),
    ]);

    $action = app(SendNesaMessageAction::class);
    $result = $action->execute('Ada rekomendasi kos putri di Sekaran?', 'session-uuid-test');

    expect($result)->toHaveKeys(['reply', 'session_token', 'recommended_listings'])
        ->and($result['reply'])->toContain('Kost Putri Melati')
        ->and($result['recommended_listings'])->not->toBeEmpty();

    $conversation = NesaConversation::where('session_token', 'session-uuid-test')->first();
    expect($conversation)->not->toBeNull()
        ->and($conversation->messages->count())->toBeGreaterThanOrEqual(2);
});

test('send nesa message action gracefully falls back to local search when llm api is down', function () {
    Http::fake([
        '*/chat/completions' => Http::response(['error' => 'Service Unavailable'], 503),
    ]);

    $action = app(SendNesaMessageAction::class);
    $result = $action->execute('Cari kos putri di Sekaran', 'session-fallback-test');

    expect($result['reply'])->not->toBeEmpty()
        ->and($result['recommended_listings'])->toBeArray();
});
```

- [ ] **Step 2: Jalankan test untuk memverifikasi kegagalan**

Run: `php artisan test --filter=NesaChatActionTest`
Expected: FAIL (Class SendNesaMessageAction not found)

- [ ] **Step 3: Implementasikan `SendNesaMessageAction.php`**

Kelola:
1. Retrieval atau inisialisasi `NesaConversation` berdasarkan `session_token`.
2. Simpan pesan `user`.
3. Panggil `NesaLlmService->sendChat()`.
4. Jika terdapat `tool_calls`, panggil `NesaToolService->execute()`, kumpulkan ID listing yang direkomendasikan, kirim kembali konteks ke LLM untuk ringkasan akhir.
5. Simpan pesan `assistant` dan metrik token/latency.
6. Kembalikan array terstruktur berisi `{ reply, session_token, recommended_listings }`.

- [ ] **Step 4: Jalankan test verifikasi**

Run: `php artisan test --filter=NesaChatActionTest`
Expected: PASS

---

### Task 4: API Endpoint & Routing

**Files:**
- Create: `app/Http/Controllers/Ai/NesaChatController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/NesaEndpointTest.php`

**Interfaces:**
- Produces: `POST /api/nesa/chat`, `GET /api/nesa/history`

- [ ] **Step 1: Tulis test endpoint API HTTP**

```php
<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('guest can send chat message to nesa api endpoint and receive response', function () {
    Http::fake([
        '*/chat/completions' => Http::response([
            'choices' => [
                ['message' => ['role' => 'assistant', 'content' => 'Halo! Saya NESA, asisten kos UNNES. Ada yang bisa dibantu?']],
            ],
            'usage' => ['total_tokens' => 30],
        ], 200),
    ]);

    $response = $this->postJson('/api/nesa/chat', [
        'message' => 'Halo NESA',
        'session_token' => 'guest-test-123',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'reply',
            'session_token',
            'recommended_listings',
        ]);
});

test('it validates chat message required', function () {
    $response = $this->postJson('/api/nesa/chat', [
        'message' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['message']);
});
```

- [ ] **Step 2: Jalankan test untuk memverifikasi kegagalan**

Run: `php artisan test --filter=NesaEndpointTest`
Expected: FAIL (404 Not Found)

- [ ] **Step 3: Buat `NesaChatController.php` dan daftarkan route di `routes/web.php`**

Tambahkan method:
- `chat(Request $request, SendNesaMessageAction $action)`
- `history(Request $request)`

- [ ] **Step 4: Jalankan test verifikasi**

Run: `php artisan test --filter=NesaEndpointTest`
Expected: PASS

---

### Task 5: Frontend Floating Widget & Interactive Listing Cards

**Files:**
- Create: `resources/js/components/nesa/NesaWidget.tsx`
- Modify: `resources/js/components/shared/PublicLayout.tsx`
- Build: `npm run build`

**Interfaces:**
- Consumes: API `/api/nesa/chat`, `/api/nesa/history`
- Produces: Floating interactive drawer dengan chat bubbles dan kartu mini kos yang bisa diklik.

- [ ] **Step 1: Buat komponen `NesaWidget.tsx`**
  - Menggunakan React 19 `useState`, `useEffect`, `useRef`.
  - Mengelola `session_token` di `localStorage`.
  - Quick action chips (*"Kos putri dekat FBS"*, *"Kos bebas jam malam"*, *"Kos campur ada parkir mobil"*).
  - Menggunakan native `window.fetch` (Zero Axios).
  - Render kartu listing interaktif jika NESA mengembalikan `recommended_listings`.

- [ ] **Step 2: Sisipkan `NesaWidget` ke dalam `PublicLayout.tsx`**
  - Pastikan widget otomatis tampil di seluruh halaman publik tanpa mengganggu elemen UI lain.

- [ ] **Step 3: Verifikasi TypeScript dan Vite build**

Run:
```powershell
npx tsc --noEmit
npm run build
```
Expected: 0 error, build sukses.

---

### Task 6: Audit Kualitas & Verifikasi Penuh (DoD)

- [ ] **Step 1: Jalankan seluruh test suite**
Run: `php artisan test`
Expected: 100% test lulus.

- [ ] **Step 2: Jalankan static analysis PHPStan Level Max**
Run: `vendor\bin\phpstan analyse --memory-limit=1G`
Expected: `[OK] No errors`.

- [ ] **Step 3: Jalankan Laravel Pint**
Run: `vendor\bin\pint --test`
Expected: `passed`.

- [ ] **Step 4: Periksa git diff & status**
Run: `git status`
Expected: Hanya file yang relevan dengan NESA yang berubah/terbuat.
