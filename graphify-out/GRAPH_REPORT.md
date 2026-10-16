# Graph Report - unneskost2  (2026-10-16)

## Corpus Check
- 359 files · ~358,388 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1963 nodes · 4157 edges · 156 communities (130 shown, 26 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 1 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d347ff39`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- BookingRepositoryInterface.php
- Illuminate\Database\Migrations\Migration
- devDependencies
- CreateReviewActionTest.php
- Booking
- Room
- User
- RoomStatus.php
- Payout
- Illuminate\Database\Eloquent\Relations\BelongsTo
- UserRole.php
- InvalidArgumentException
- Illuminate\Foundation\Testing\RefreshDatabase
- Booking
- PublicLayout.tsx
- compilerOptions
- Illuminate\Foundation\Http\FormRequest
- Pages/Listings/Index.tsx
- ToggleWishlistActionTest.php
- listing.ts
- Listing
- Listing
- NotificationBell.tsx
- GenderType.php
- OwnerListingManagementTest.php
- Illuminate\Database\Eloquent\Relations\HasMany
- NesaConversation
- Illuminate\Contracts\Pagination\LengthAwarePaginator
- UserProfileData
- scripts
- Controller
- PaymentReview
- ListingRepositoryInterface.php
- composer.json
- require-dev
- Payment
- cn
- Illuminate\Http\RedirectResponse
- NesaWidget.tsx
- Illuminate\Support\Str
- web.php
- require
- dependencies
- config
- Facility
- PRD & Technical Design: NESA (UNNES Smart Assistant) — Resilient Agentic RAG Chatbot
- 🏗️ PROMPT FINAL — Rebuild UNNESKost v3 (Produk + Infrastruktur)
- LoginRequest
- psr-4
- tasteskill: Anti-Slop Frontend Skill
- AvatarSecurityTest
- ExpireBookingsAction
- AppServiceProvider.php
- extra
- logging.php
- ListingData
- Illuminate\Http\Request
- SearchListingRequest
- post-create-project-cmd
- PaymentData
- Appendix B - Canonical Sources (read these before reinventing)
- keywords
- console.php
- AdminPaymentController
- Illuminate\Contracts\Console\Kernel
- 2. Inventaris Halaman Berdasarkan Modul Fitur (`resources/js/features/`)
- Payments/Index.tsx
- Review
- 3. Daftar Endpoint Inti
- 🤖 AI Agent Guidelines & Architecture Rules — UNNESKost v3
- 4. DESIGN ENGINEERING DIRECTIVES (Bias Correction)
- 💻 Development Setup & Developer Workflow — UNNESKost v3
- ReviewData
- 2. Skema Database PostgreSQL 16
- 📋 Master Task Tracker — UNNESKost v3
- 10. REFERENCE VOCABULARY (Pattern Names the Agent Should Know)
- RegisterRequest
- 3. Skema & Definisi Tabel
- 🏛️ Architecture Overview — UNNESKost v3
- Task Breakdown
- Task Breakdown
- 📦 1. Apa yang Telah Diimplementasikan
- 2. Database Schema & Invariants
- 🎯 Fokus & Tugas Review
- ProfileController.php
- ⚙️ Backend Architecture Specification — UNNESKost v3
- 1. Rincian Fase Evolusi & Trigger Kriteria
- 🛡️ Security Foundation Guide — UNNESKost v3
- Task Breakdown
- Task Breakdown
- Global Constraints
- 📦 1. Apa yang Telah Diimplementasikan
- 📐 Design Specification: Milestone 2 — Modul Pemesanan, Pembayaran & Anti Double-Booking
- 📦 1. Apa yang Telah Diimplementasikan
- 📦 1. Apa yang Telah Diimplementasikan
- README.md
- 📋 UNNESKost v3 — System Requirements Specification (SRS)
- 3. Kebutuhan Fungsional (Functional Requirements)
- 9. AI TELLS (Forbidden Patterns)
- RoomController.php
- 📊 Observability & System Health Guide — UNNESKost v3
- Task Breakdown
- package.json
- APPENDICES - Real Source-Backed Reference Material
- 11. REDESIGN PROTOCOL
- 3. DEFAULT ARCHITECTURE & CONVENTIONS
- 6. PERFORMANCE & ACCESSIBILITY GUARDRAILS
- EloquentBookingRepository
- 🎨 Frontend Architecture Specification — UNNESKost v3
- 🎨 Panduan Identitas Visual & Brand — Pusat Kost UNNES
- 📐 Design Specification: Milestone 3 — Modul Pencarian & Filter Cerdas
- PayoutNotAllowedException
- 🤝 Panduan Kontribusi & Git Workflow — UNNESKost v3
- ADR-001: Pemilihan Arsitektur Modular Monolith dengan Pola DDD-Lite
- ADR-002: Penerapan Action Pattern dan Single Responsibility di Domain Layer
- ADR-003: Pemilihan PostgreSQL 16 sebagai Basis Data Utama
- ADR-004: Pemilihan Inertia.js v3 + React 19 tanpa Axios
- 12. THE BLOCK LIBRARY (Contract - Implementations Land Here Iteratively)
- 5. CONTEXT-AWARE PROACTIVITY
- 8. DARK MODE PROTOCOL
- 🐎 Ponytail — The Lazily Efficient Senior Developer
- ListingImageController.php
- StoreBookingRequest
- StoreRoomRequest
- 📐 Design Specification: Milestone 5 — Autentikasi, RBAC & Notifikasi
- ErrorBoundary
- 🏛️ Architect Agent — UNNESKost v3
- 7. DIAL DEFINITIONS (Technical Reference)
- Format
- RejectPayoutRequest
- SimulatePaymentRequest
- UploadImageRequest
- ReplyReviewRequest
- 11. Infrastruktur Lokal — Docker & Developer Experience
- 6. Arsitektur Backend — Modular Monolith + DDD-lite + Action Pattern
- 13. Testing & CI
- 4. Konteks Produk
- 8. Arsitektur Frontend — Feature-Based + Design System
- rules/graphify.md
- workflows/graphify.md
- eslint
- laravel-vite-plugin
- react
- @types/react
- typescript

## God Nodes (most connected - your core abstractions)
1. `User` - 212 edges
2. `Listing` - 137 edges
3. `Booking` - 98 edges
4. `Room` - 79 edges
5. `Controller` - 53 edges
6. `TestCase` - 53 edges
7. `Payout` - 40 edges
8. `Review` - 38 edges
9. `PaymentReview` - 37 edges
10. `Money` - 36 edges

## Surprising Connections (you probably didn't know these)
- `NotificationFlowTest` --references--> `Booking`  [EXTRACTED]
  tests/Feature/NotificationFlowTest.php → app/Domain/Booking/Models/Booking.php
- `PolicyCrossIsolationTest` --references--> `Booking`  [EXTRACTED]
  tests/Feature/Security/PolicyCrossIsolationTest.php → app/Domain/Booking/Models/Booking.php
- `NotificationSecurityTest` --references--> `Listing`  [EXTRACTED]
  tests/Feature/Security/NotificationSecurityTest.php → app/Domain/Listing/Models/Listing.php
- `PolicyCrossIsolationTest` --references--> `Listing`  [EXTRACTED]
  tests/Feature/Security/PolicyCrossIsolationTest.php → app/Domain/Listing/Models/Listing.php
- `TenantBookingHistoryTest` --references--> `Listing`  [EXTRACTED]
  tests/Feature/TenantBookingHistoryTest.php → app/Domain/Listing/Models/Listing.php

## Import Cycles
- None detected.

## Communities (156 total, 26 thin omitted)

### Community 0 - "BookingRepositoryInterface.php"
Cohesion: 0.11
Nodes (10): ApproveBookingAction, RejectBookingAction, findByCode(), findById(), getForOwner(), getForUser(), BookingController, PaymentController (+2 more)

### Community 1 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.05
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 2 - "devDependencies"
Cohesion: 0.12
Nodes (17): concurrently, @eslint/js, devDependencies, concurrently, @eslint/js, tailwindcss, @tailwindcss/vite, @types/react-dom (+9 more)

### Community 3 - "CreateReviewActionTest.php"
Cohesion: 0.24
Nodes (4): CreateReviewAction, RecalculateListingRatingAction, ReplyReviewAction, CreateReviewActionTest

### Community 4 - "Booking"
Cohesion: 0.06
Nodes (13): ProcessPaymentAction, Booking, BookingPolicy, AdminReviewStartedNotification, BookingApprovedNotification, BookingCreatedNotification, BookingStatusChangedNotification, PaymentConfirmedNotification (+5 more)

### Community 5 - "Room"
Cohesion: 0.12
Nodes (5): Room, RoomPolicy, ListingSearchFilterTest, PolicyCrossIsolationTest, TenantBookingHistoryTest

### Community 6 - "User"
Cohesion: 0.07
Nodes (11): ListingPolicy, PayoutPolicy, User, Illuminate\Contracts\Auth\MustVerifyEmail, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Spatie\Permission\Traits\HasRoles (+3 more)

### Community 7 - "RoomStatus.php"
Cohesion: 0.08
Nodes (12): CancelBookingAction, CreateBookingAction, BookingData, self, RoomUnavailableException, RoomStatusAudit, Carbon\Carbon, DomainException (+4 more)

### Community 8 - "Payout"
Cohesion: 0.06
Nodes (9): PayoutCompletedNotification, PayoutFailedNotification, PayoutProcessingNotification, ApprovePayoutAction, ProcessPayoutAction, Payout, Money, self (+1 more)

### Community 9 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (9): Payment, Wishlist, Illuminate\Database\Eloquent\Collection, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo, Illuminate\Database\Eloquent\Relations\BelongsToMany, Illuminate\Database\Eloquent\SoftDeletes (+1 more)

### Community 10 - "UserRole.php"
Cohesion: 0.08
Nodes (16): RegisterUserAction, self, RegisterUserData, RoleAndPermissionSeeder, Illuminate\Auth\Events\Registered, Illuminate\Auth\Events\Verified, Illuminate\Database\Eloquent\MassAssignmentException, Illuminate\Support\Facades\Event (+8 more)

### Community 11 - "InvalidArgumentException"
Cohesion: 0.29
Nodes (4): CreateRoomAction, UpdateRoomAction, RoomData, InvalidArgumentException

### Community 12 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.09
Nodes (11): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, AdminFlowTest, ExampleTest, NotificationFlowTest, AuthenticationSecurityTest, WishlistFlowTest, TestCase (+3 more)

### Community 13 - "Booking"
Cohesion: 0.13
Nodes (19): Show(), ShowProps, InvoiceProps, TenantReceiptProps, OwnerPayoutReceiptProps, OwnerPayoutShowProps, PaginationLink, TenantBookingsProps (+11 more)

### Community 14 - "PublicLayout.tsx"
Cohesion: 0.08
Nodes (25): Props, State, FlashHandler(), PublicLayout(), PublicLayoutProps, Button(), ButtonProps, Input (+17 more)

### Community 15 - "compilerOptions"
Cohesion: 0.10
Nodes (19): resources/js/**/*.d.ts, resources/js/**/*.ts, resources/js/**/*.tsx, vite/client, compilerOptions, baseUrl, esModuleInterop, forceConsistentCasingInFileNames (+11 more)

### Community 16 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (5): ApprovePayoutRequest, VerifyContactRequest, UpdateAvatarRequest, ToggleWishlistRequest, Illuminate\Foundation\Http\FormRequest

### Community 17 - "Pages/Listings/Index.tsx"
Cohesion: 0.12
Nodes (18): AirbnbSearchBar(), AirbnbSearchBarProps, AreaOption, UNNES_AREAS, FacilityBadge(), FacilityBadgeProps, FacilityIcon(), FacilityIconProps (+10 more)

### Community 19 - "listing.ts"
Cohesion: 0.11
Nodes (19): Badge(), BadgeProps, GenderBadge(), StatusBadge(), ListingCard(), RoomCardProps, CheckoutProps, ShowProps (+11 more)

### Community 20 - "Listing"
Cohesion: 0.14
Nodes (16): AREA_COORDINATES, getListingCoords(), ListingFeature, ListingPointProperties, MapView(), MapViewProps, UNNES_CAMPUS_COORDINATES, UNNES_CAMPUS_GEOJSON (+8 more)

### Community 21 - "Listing"
Cohesion: 0.11
Nodes (9): Listing, EloquentListingRepository, SearchCriteria, EloquentSearchRepository, Illuminate\Database\Eloquent\Builder, ListingRepositoryInterface, SearchRepositoryInterface, ReviewFlowTest (+1 more)

### Community 22 - "NotificationBell.tsx"
Cohesion: 0.31
Nodes (6): getCsrfToken(), NotificationBell(), AuthUser, SharedProps, InAppNotification, NotificationItem

### Community 23 - "GenderType.php"
Cohesion: 0.14
Nodes (5): PublishListingAction, ListingImage, Illuminate\Http\Client\Request, Illuminate\Support\Facades\Config, Illuminate\Support\Facades\Http

### Community 24 - "OwnerListingManagementTest.php"
Cohesion: 0.36
Nodes (3): UploadListingImageAction, Illuminate\Http\UploadedFile, Illuminate\Support\Facades\Storage

### Community 26 - "NesaConversation"
Cohesion: 0.07
Nodes (11): SendNesaMessageAction, NesaConversation, NesaMessage, NesaLlmService, NesaToolService, self, Illuminate\Database\Eloquent\Relations\Relation, GenderType (+3 more)

### Community 27 - "Illuminate\Contracts\Pagination\LengthAwarePaginator"
Cohesion: 0.24
Nodes (5): getListingReviews(), SearchListingsAction, search(), getUserWishlists(), Illuminate\Contracts\Pagination\LengthAwarePaginator

### Community 28 - "UserProfileData"
Cohesion: 0.15
Nodes (5): UpdateUserProfileAction, self, UserProfileData, UpdateProfileRequest, Illuminate\Validation\Rule

### Community 29 - "scripts"
Cohesion: 0.17
Nodes (12): scripts, dev, phpstan, post-autoload-dump, post-root-package-install, post-update-cmd, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+4 more)

### Community 30 - "Controller"
Cohesion: 0.12
Nodes (13): AdminDashboardController, RegisteredUserController, InvoiceController, ReceiptController, Controller, OwnerPayoutController, ListingController, TenantBookingController (+5 more)

### Community 31 - "PaymentReview"
Cohesion: 0.09
Nodes (6): RejectPayoutAction, StartAdminReviewAction, PayoutData, PaymentAudit, PaymentReview, PaymentReviewPolicy

### Community 32 - "ListingRepositoryInterface.php"
Cohesion: 0.29
Nodes (5): findBySlug(), findForOwner(), getOwnerListings(), getPublishedListings(), Symfony\Component\HttpKernel\Exception\NotFoundHttpException

### Community 33 - "composer.json"
Cohesion: 0.18
Nodes (10): autoload-dev, psr-4, description, license, minimum-stability, name, prefer-stable, Tests\\ (+2 more)

### Community 34 - "require-dev"
Cohesion: 0.18
Nodes (11): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pint, laravel/sail, mockery/mockery, nunomaduro/collision (+3 more)

### Community 35 - "Payment"
Cohesion: 0.10
Nodes (8): CreatePaymentAction, Payment, VerifyPaymentAction, PaymentData, Payment, PaymentPolicy, createTransaction(), processPayout()

### Community 36 - "cn"
Cohesion: 0.09
Nodes (22): Logo(), LogoProps, Card(), CardContent(), CardFooter(), CardHeader(), CardProps, Checkbox (+14 more)

### Community 37 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.11
Nodes (8): VerifyOwnerAction, AdminOwnerController, OwnerVerificationController, AuthenticatedSessionController, EmailVerificationController, ReviewController, Illuminate\Foundation\Auth\EmailVerificationRequest, Illuminate\Http\RedirectResponse

### Community 38 - "NesaWidget.tsx"
Cohesion: 0.12
Nodes (21): getCsrfToken(), NesaListingCard(), NesaListingCardProps, NesaWidget(), normalizeGender(), QUICK_CHIPS, StickyBookingWidget(), StickyBookingWidgetProps (+13 more)

### Community 39 - "Illuminate\Support\Str"
Cohesion: 0.05
Nodes (20): PaymentVerificationException, self, SandboxPaymentGateway, CorrelationIdMiddleware, HandleInertiaRequests, Closure, UserFactory, Illuminate\Database\Eloquent\Factories\Factory (+12 more)

### Community 40 - "web.php"
Cohesion: 0.16
Nodes (7): MarkNotificationAsReadAction, NesaChatController, HealthCheckController, NotificationController, WebhookController, Illuminate\Http\JsonResponse, Illuminate\Support\Facades\Route

### Community 41 - "require"
Cohesion: 0.25
Nodes (8): require, inertiajs/inertia-laravel, laravel/framework, laravel/sanctum, laravel/tinker, php, spatie/laravel-permission, spatie/typescript-transformer

### Community 42 - "dependencies"
Cohesion: 0.11
Nodes (19): clsx, @inertiajs/react, leaflet, dependencies, clsx, @inertiajs/react, leaflet, @phosphor-icons/react (+11 more)

### Community 43 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 44 - "Facility"
Cohesion: 0.11
Nodes (6): Facility, DatabaseSeeder, FacilitySeeder, ListingSeeder, Illuminate\Database\Seeder, NesaLlmServiceTest

### Community 45 - "PRD & Technical Design: NESA (UNNES Smart Assistant) — Resilient Agentic RAG Chatbot"
Cohesion: 0.11
Nodes (17): 1. Executive Summary & Purpose, 2. System Architecture & Domain Boundaries, 3.1. Tabel `nesa_conversations`, 3.2. Tabel `nesa_messages`, 3. Database Schema (PostgreSQL), 4.1. Provider-Agnostic Adapter (`OpenAiCompatibleLlmClient`), 4.2. File `.env` & `config/ai.php`, 4. LLM Client & Provider Configuration (+9 more)

### Community 46 - "🏗️ PROMPT FINAL — Rebuild UNNESKost v3 (Produk + Infrastruktur)"
Cohesion: 0.12
Nodes (16): 10. Security Foundation, 12. Observability & Health, 14. Struktur Direktori Target, 15. Milestone Eksekusi, 16. Definition of Done (setiap milestone), 17. Dokumentasi Wajib, 18. Output yang Diharapkan dari Agent, 19. Evolution Path (dokumentasikan — jangan implementasi sekarang) (+8 more)

### Community 47 - "LoginRequest"
Cohesion: 0.27
Nodes (4): LoginRequest, Illuminate\Auth\Events\Lockout, Illuminate\Support\Facades\RateLimiter, Illuminate\Validation\ValidationException

### Community 48 - "psr-4"
Cohesion: 0.33
Nodes (6): autoload, psr-4, App\\, App\\Domain\\, Database\\Factories\\, Database\\Seeders\\

### Community 49 - "tasteskill: Anti-Slop Frontend Skill"
Cohesion: 0.13
Nodes (15): 0.A Read these signals first, 0.B Output a one-line "Design Read" before generating, 0. BRIEF INFERENCE (Read the Room Before Anything Else), 0.C If the brief is ambiguous, ask one question, do not guess, 0.D Anti-Default Discipline, 13. OUT OF SCOPE, 14. FINAL PRE-FLIGHT CHECK, 1.A Dial Inference (design read → dial values) (+7 more)

### Community 51 - "ExpireBookingsAction"
Cohesion: 0.60
Nodes (3): ExpireBookingsCommand, ExpireBookingsAction, Illuminate\Console\Command

### Community 52 - "AppServiceProvider.php"
Cohesion: 0.20
Nodes (4): EloquentWishlistRepository, AppServiceProvider, Illuminate\Support\ServiceProvider, WishlistRepositoryInterface

### Community 53 - "extra"
Cohesion: 0.40
Nodes (5): dev-master, extra, branch-alias, laravel, dont-discover

### Community 54 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 55 - "ListingData"
Cohesion: 0.18
Nodes (3): UpdateListingAction, ListingData, StoreListingRequest

### Community 56 - "Illuminate\Http\Request"
Cohesion: 0.21
Nodes (5): CreateListingAction, AdminReviewController, ListingController, UpdateListingRequest, Illuminate\Http\Request

### Community 58 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 60 - "Appendix B - Canonical Sources (read these before reinventing)"
Cohesion: 0.13
Nodes (15): Appendix B - Canonical Sources (read these before reinventing), Apple Liquid Glass (Apple platforms only), Atlassian, Bootstrap, Carbon, Fluent UI, GOV.UK, Material Web (+7 more)

### Community 61 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 63 - "AdminPaymentController"
Cohesion: 0.16
Nodes (4): RecordPartyContactAction, ContactVerificationData, AdminPaymentController, Payment

### Community 66 - "2. Inventaris Halaman Berdasarkan Modul Fitur (`resources/js/features/`)"
Cohesion: 0.14
Nodes (13): 1. Peta Layout Aplikasi (`resources/js/layouts/`), 2.1 Modul Listings & Discovery (`features/listings/`), 2.2 Modul Booking & Transaksi (`features/booking/`), 2.3 Modul Wishlist / Favorit (`features/favorites/`), 2.4 Modul Ulasan & Rating (`features/review/`), 2.5 Modul Owner Dashboard (`features/owner/`), 2.6 Modul Admin Dashboard (`features/admin/`), 2. Inventaris Halaman Berdasarkan Modul Fitur (`resources/js/features/`) (+5 more)

### Community 74 - "Payments/Index.tsx"
Cohesion: 0.19
Nodes (10): DEFAULT_SORT_OPTIONS, SortDropdown(), SortDropdownProps, SortOption, Index(), PaginationLink, PaymentsIndexProps, PaginationLink (+2 more)

### Community 77 - "Review"
Cohesion: 0.19
Nodes (4): Review, ReviewPolicy, EloquentReviewRepository, ReviewRepositoryInterface

### Community 78 - "3. Daftar Endpoint Inti"
Cohesion: 0.15
Nodes (12): 1. Prinsip Utama API, 2. Struktur Respon JSON (Envelope Standard), 3.1 Autentikasi (`/api/v1/auth`), 3.2 Listing & Pencarian (`/api/v1/listings`), 3.3 Kamar (`/api/v1/rooms`), 3.4 Booking (`/api/v1/bookings`), 3.5 Favorit / Wishlist (`/api/v1/favorites`), 3. Daftar Endpoint Inti (+4 more)

### Community 79 - "🤖 AI Agent Guidelines & Architecture Rules — UNNESKost v3"
Cohesion: 0.17
Nodes (11): 1. 🧰 Installed Agent Skills & Pembagian Tanggung Jawab, ⚡ 1. Superpowers, 🐎 2. Ponytail, 2. 🏛️ Prinsip Rekayasa & Arsitektur, 3. 🚨 Larangan Keras (Non-Negotiable Rules), 🎨 3. Taste Skill, 4. 🛡️ Sumber Daya Terlindungi (Protected Resources), 5. 🔄 Alur Kerja Milestone (Milestone Workflow) (+3 more)

### Community 80 - "4. DESIGN ENGINEERING DIRECTIVES (Bias Correction)"
Cohesion: 0.17
Nodes (12): 4.10 Quotes & Testimonials, 4.11 Page Theme Lock (Light / Dark Mode Consistency), 4.1 Typography, 4.2 Color Calibration, 4.3 Layout Diversification, 4.4 Materiality, Shadows, Cards, 4.5 Interactive UI States, 4.6 Data & Form Patterns (+4 more)

### Community 81 - "💻 Development Setup & Developer Workflow — UNNESKost v3"
Cohesion: 0.17
Nodes (11): 1. Menambahkan Fitur Bisnis Baru (Contoh: Cancel Booking), 1. Persyaratan Sistem, 2. Aturan Frontend React:, 2. Langkah Setup Lingkungan, 3. Perintah Kerja Harian (*Workflow Commands*), 4. Konvensi Koding & Struktur File, 💻 Development Setup & Developer Workflow — UNNESKost v3, Menggunakan Docker Compose: (+3 more)

### Community 82 - "ReviewData"
Cohesion: 0.20
Nodes (3): self, ReviewData, StoreReviewRequest

### Community 83 - "2. Skema Database PostgreSQL 16"
Cohesion: 0.18
Nodes (10): 1. Arsitektur Domain & Boundary, 2. Skema Database PostgreSQL 16, 3. Alur Bisnis & Invariants, 4. Antarmuka Pengguna (Frontend React 19 + Inertia v3), A. Tabel `facilities`, B. Tabel `listings`, C. Tabel `rooms`, D. Tabel `listing_images` (+2 more)

### Community 84 - "📋 Master Task Tracker — UNNESKost v3"
Cohesion: 0.18
Nodes (11): 📌 Aturan Pembaruan Dokumen, 📋 Master Task Tracker — UNNESKost v3, ✅ Milestone 0: Fondasi & Scaffolding Codebase, ✅ Milestone 1: Modul Listing & Manajemen Kamar, ✅ Milestone 2: Modul Pemesanan & Pembayaran, ✅ Milestone 3: Modul Pencarian & Filter Cerdas, ✅ Milestone 4: Modul Review, Rating & Wishlist, ✅ Milestone 5: Autentikasi, RBAC & Notifikasi (+3 more)

### Community 85 - "10. REFERENCE VOCABULARY (Pattern Names the Agent Should Know)"
Cohesion: 0.20
Nodes (10): 10. REFERENCE VOCABULARY (Pattern Names the Agent Should Know), Animation Library Choice, Cards & Containers, Galleries & Media, Hero Paradigms, Layout & Grids, Micro-Interactions & Effects, Navigation & Menus (+2 more)

### Community 86 - "RegisterRequest"
Cohesion: 0.20
Nodes (3): RegisterRequest, UpdatePasswordRequest, Illuminate\Validation\Rules\Password

### Community 87 - "3. Skema & Definisi Tabel"
Cohesion: 0.20
Nodes (9): 1. Prinsip Utama Desain Basis Data, 2. Diagram Hubungan Entitas (ERD), 3.1 `listings`, 3.2 `rooms`, 3.3 `bookings` & Double-Booking Protection, 3.4 `room_status_audits`, 3.5 `favorites` & `reviews`, 3. Skema & Definisi Tabel (+1 more)

### Community 88 - "🏛️ Architecture Overview — UNNESKost v3"
Cohesion: 0.20
Nodes (9): 1. Konsep Modular Monolith & DDD-Lite, 2. Bounded Context (Konteks Modul), 3. Komunikasi Antar Bounded Context, 4.1 Alur Booking Kamar (Event-Driven):, 4.2 Alur Pencarian Kos:, 4. Alur Kerja (End-to-End Flow), 5. Model Data & PostgreSQL Constraints, 🏛️ Architecture Overview — UNNESKost v3 (+1 more)

### Community 89 - "Task Breakdown"
Cohesion: 0.20
Nodes (9): Global Constraints, Milestone 1: Modul Listing & Manajemen Kamar Implementation Plan, Task 0: Pembersihan Cepat Temuan Ponytail-Review, Task 1: Database Migrations, Enums, Models & Seeders, Task 2: Domain DTOs, Action Pattern & Repository, Task 3: HTTP Layer (Controllers, FormRequests, Policies), Task 4: Frontend Components & Pages (React 19 + Inertia v3 + Taste Skill), Task 5: Full Verification Gate & Milestone 1 Completion (+1 more)

### Community 90 - "Task Breakdown"
Cohesion: 0.20
Nodes (9): Global Invariants, Milestone 5: Autentikasi, RBAC & Notifikasi Implementation Plan, Task 1: Database Migration, Spatie Permissions & User Model Extensions, Task 2: Domain Policies & Access Control, Task 3: Auth & Profile Actions, DTOs & Notification System, Task 4: HTTP Layer, FormRequests, Controllers & Routes, Task 5: Frontend UI (React 19 + Taste Skill), Task 6: Full Verification Gate & Milestone 5 Completion (+1 more)

### Community 91 - "📦 1. Apa yang Telah Diimplementasikan"
Cohesion: 0.20
Nodes (9): 📦 1. Apa yang Telah Diimplementasikan, 🧪 2. Bukti Hasil Verifikasi Mandiri (Verification Gate), 📁 3. Daftar Berkas Baru & Modifikasi, A. Database & Invariant PostgreSQL 16, B. Anti Double-Booking Pessimistic Locking Strategy, C. Booking & Payment State Machine, D. Action Pattern & Repositories, E. Frontend React 19 (Taste Skill & Anti-Slop) (+1 more)

### Community 92 - "2. Database Schema & Invariants"
Cohesion: 0.20
Nodes (9): 1. Arsitektur Domain & Boundary, 2. Database Schema & Invariants, 3. Frontend Experience (React 19 + Taste Skill), A. Bounded Context `App\Domain\Review\`, A. Tabel `reviews`, B. Bounded Context `App\Domain\Wishlist\`, B. Tabel `wishlists`, C. Invariant Agregasi Rating (+1 more)

### Community 93 - "🎯 Fokus & Tugas Review"
Cohesion: 0.22
Nodes (8): 1. Architecture & DDD Compliance, 2. Security & Data Integrity, 3. Strict Type Safety & Frontend, 4. Testing & Code Quality (DoD Checklist), 5. Performance & Query Optimization, 6. Ponytail / Over-Engineering Audit (YAGNI), 🎯 Fokus & Tugas Review, 🔍 Reviewer Agent — UNNESKost v3

### Community 95 - "⚙️ Backend Architecture Specification — UNNESKost v3"
Cohesion: 0.22
Nodes (8): 1. Prinsip Utama Backend, 2. Struktur Direktori Backend, 3.1 DTO (Data Transfer Object), 3.2 Action Class, 3. Implementasi Action Pattern & DTO, 4. Thin Controller Example, 5. Standar Testing Backend (Pest), ⚙️ Backend Architecture Specification — UNNESKost v3

### Community 96 - "1. Rincian Fase Evolusi & Trigger Kriteria"
Cohesion: 0.22
Nodes (8): 1. Rincian Fase Evolusi & Trigger Kriteria, Fase 0: Local Docker Compose & GitHub Actions (Saat Ini), Fase 1: Production VPS / PaaS Single Node, Fase 2: Managed Cloud Infrastructure & Observability, Fase 3: Cloud-Native Kubernetes (K8s) & GitOps, Fase 4: High Availability (HA) & Disaster Recovery (DR), 🚀 Infrastructure Evolution & Deployment Roadmap — UNNESKost v3, 🗺️ Roadmap Evolusi Bertahap

### Community 97 - "🛡️ Security Foundation Guide — UNNESKost v3"
Cohesion: 0.22
Nodes (8): 1. Prinsip Keamanan Utama, 2.1 Validasi Input (Input Validation & Sanitization), 2.2 Autentikasi & Otorisasi Berbasis Peran (RBAC), 2.3 Pencegahan Vulnerability Umum (OWASP Top 10), 2. Lapisan Keamanan Aplikasi, 3. Manajemen Rahasia & Credential, 4. Keamanan Infrastruktur & Container, 🛡️ Security Foundation Guide — UNNESKost v3

### Community 98 - "Task Breakdown"
Cohesion: 0.22
Nodes (8): Global Invariants, Milestone 2: Modul Pemesanan & Pembayaran Implementation Plan, Task 1: Database Migrations, Enums, Models & Invariant Exceptions, Task 2: Domain Actions & Concurrency Locking (Action Pattern), Task 3: HTTP Layer (Controllers, FormRequests, Policies & Routes), Task 4: Frontend Components & Pages (React 19 + Inertia v3 + Taste Skill), Task 5: Full Verification Gate & Milestone 2 Completion, Task Breakdown

### Community 99 - "Task Breakdown"
Cohesion: 0.22
Nodes (8): Global Invariants, Milestone 4: Modul Review, Rating & Wishlist Implementation Plan, Task 1: Database Migrations, Models & Relationships (`Review` & `Wishlist`), Task 2: Domain Actions, DTOs & Repositories, Task 3: HTTP Layer, FormRequests, Policies & Routes, Task 4: Frontend UI Components (React 19 + Taste Skill), Task 5: Full Verification Gate & Milestone 4 Completion, Task Breakdown

### Community 100 - "Global Constraints"
Cohesion: 0.22
Nodes (8): Global Constraints, NESA (UNNES Smart Assistant) Agentic RAG Implementation Plan, Task 1: Konfigurasi AI & Migrasi Database Sesi Percakapan, Task 2: Implementasi LLM Service & Tool Calling Handler, Task 3: Orchestration Action & Fallback Handling, Task 4: API Endpoint & Routing, Task 5: Frontend Floating Widget & Interactive Listing Cards, Task 6: Audit Kualitas & Verifikasi Penuh (DoD)

### Community 101 - "📦 1. Apa yang Telah Diimplementasikan"
Cohesion: 0.22
Nodes (8): 📦 1. Apa yang Telah Diimplementasikan, 🧪 2. Bukti Hasil Verifikasi Mandiri (Verification Gate), 📁 3. Daftar Berkas Baru yang Dibuat, A. Database & Domain Models (`App\Domain\Listing\`), B. Action Pattern & Repositories, C. HTTP Layer & Policy, D. Frontend React 19 (Taste Skill & Anti-Slop), 🚀 Walkthrough: Milestone 1 — Modul Listing & Manajemen Kamar Selesai

### Community 102 - "📐 Design Specification: Milestone 2 — Modul Pemesanan, Pembayaran & Anti Double-Booking"
Cohesion: 0.22
Nodes (8): 1. Arsitektur Domain & Boundary, 2. Skema Database PostgreSQL 16, 3. Anti-Double-Booking Concurrency Algorithm, 4. Antarmuka Pengguna (Frontend React 19 + Taste Skill), A. Tabel `bookings`, B. Tabel `payments`, C. Tabel `room_status_audits`, 📐 Design Specification: Milestone 2 — Modul Pemesanan, Pembayaran & Anti Double-Booking

### Community 103 - "📦 1. Apa yang Telah Diimplementasikan"
Cohesion: 0.22
Nodes (8): 📦 1. Apa yang Telah Diimplementasikan, 🧪 2. Bukti Hasil Verifikasi Mandiri (*Verification Gate*), 📁 3. Daftar Berkas Baru & Modifikasi, A. Domain DTO & Multi-Criteria Criteria, B. High-Performance Repository & Database Fallback, C. HTTP & Controller Layer, D. Frontend React 19 UI (Taste Skill), 🚀 Walkthrough: Milestone 3 — Modul Pencarian & Filter Cerdas (Smart Faceted Search) Selesai

### Community 104 - "📦 1. Apa yang Telah Diimplementasikan"
Cohesion: 0.22
Nodes (8): 📦 1. Apa yang Telah Diimplementasikan, 🧪 2. Bukti Hasil Verifikasi Mandiri (*Verification Gate*), 📁 3. Daftar Berkas Baru & Modifikasi, A. Review & Rating Domain (`App\Domain\Review\`), B. Wishlist & Bookmark Domain (`App\Domain\Wishlist\`), C. HTTP & Controller Layer, D. Frontend React 19 UI (Taste Skill), 🚀 Walkthrough: Milestone 4 — Modul Review, Rating & Wishlist Selesai

### Community 105 - "README.md"
Cohesion: 0.22
Nodes (8): About Laravel, Code of Conduct, Contributing, Laravel Sponsors, Learning Laravel, License, Premium Partners, Security Vulnerabilities

### Community 106 - "📋 UNNESKost v3 — System Requirements Specification (SRS)"
Cohesion: 0.22
Nodes (6): 1. Ringkasan Produk, 2. Peran Pengguna & Hak Akses (User Roles & RBAC), 4. Kebutuhan Non-Fungsional (Non-Functional Requirements), 5. Matriks Aturan Bisnis & Domain Invariants, 🎯 Tujuan Utama:, 📋 UNNESKost v3 — System Requirements Specification (SRS)

### Community 107 - "3. Kebutuhan Fungsional (Functional Requirements)"
Cohesion: 0.22
Nodes (9): 3.1 Modul Listing Properti Kos, 3.2 Modul Kamar (Room Management & Invariants), 3.3 Modul Pemesanan (Booking Engine), 3.4 Modul Pembayaran (Payment & Invoicing), 3.5 Modul Pencarian & Filter (Search Engine), 3.6 Modul Ulasan & Rating (Review & Moderation), 3.7 Modul Favorit (Wishlist), 3.8 Modul Autentikasi & Akun (+1 more)

### Community 108 - "9. AI TELLS (Forbidden Patterns)"
Cohesion: 0.25
Nodes (8): 9.A Visual & CSS, 9. AI TELLS (Forbidden Patterns), 9.B Typography, 9.C Layout & Spacing, 9.D Content & Data ("Jane Doe" Effect), 9.E External Resources & Components, 9.F Production-Test Tells (banned outright), 9.G EM-DASH BAN (the single most-violated Tell)

### Community 110 - "📊 Observability & System Health Guide — UNNESKost v3"
Cohesion: 0.25
Nodes (7): 1. Prinsip Utama Observability, 2. Endpoint Health Check (`GET /health`), 3. Middleware Correlation ID (`CorrelationIdMiddleware`), 4. Structured JSON Logging, 5. Antrean & Metrik Operasional, Contoh Format Respon (`200 OK` / `503 Service Unavailable`):, 📊 Observability & System Health Guide — UNNESKost v3

### Community 111 - "Task Breakdown"
Cohesion: 0.25
Nodes (7): Global Invariants, Milestone 3: Modul Pencarian & Filter Cerdas Implementation Plan, Task 1: Domain DTOs, Repository & Search Action (`App\Domain\Search\`), Task 2: HTTP Layer & FormRequests, Task 3: Frontend UI Components (React 19 + Taste Skill), Task 4: Full Verification Gate & Milestone 3 Completion, Task Breakdown

### Community 112 - "package.json"
Cohesion: 0.25
Nodes (7): private, scripts, build, dev, lint, type-check, type

### Community 113 - "APPENDICES - Real Source-Backed Reference Material"
Cohesion: 0.29
Nodes (6): APPENDICES - Real Source-Backed Reference Material, Appendix A - Install Commands per Design System, Appendix C - Apple Liquid Glass: Honest Web Approximation, Safer web approximation skeleton, What is NOT official, What is official

### Community 114 - "11. REDESIGN PROTOCOL"
Cohesion: 0.29
Nodes (7): 11.A Detect the Mode (first action), 11.B Audit Before Touching, 11.C Preservation Rules, 11.D Modernisation Levers (priority order), 11.E Decision Tree: Targeted Evolution vs Full Redesign, 11.F What Never Changes Silently, 11. REDESIGN PROTOCOL

### Community 115 - "3. DEFAULT ARCHITECTURE & CONVENTIONS"
Cohesion: 0.29
Nodes (7): 3.A Stack, 3.B State, 3.C Icons, 3.D Emoji Policy, 3. DEFAULT ARCHITECTURE & CONVENTIONS, 3.E Responsiveness & Layout Mechanics, 3.F Dependency Verification (mandatory)

### Community 116 - "6. PERFORMANCE & ACCESSIBILITY GUARDRAILS"
Cohesion: 0.29
Nodes (7): 6.A Hardware Acceleration, 6.B Reduced Motion (mandatory), 6.C Dark Mode (mandatory for any consumer-facing page), 6.D Core Web Vitals Targets, 6.E DOM Cost, 6.F Z-Index Restraint, 6. PERFORMANCE & ACCESSIBILITY GUARDRAILS

### Community 118 - "🎨 Frontend Architecture Specification — UNNESKost v3"
Cohesion: 0.29
Nodes (6): 1. Prinsip Utama Frontend, 2. Struktur Direktori Frontend, 3. Komponen Design System (Radix / shadcn Style), 4. Validasi Form dengan React Hook Form + Zod, 5. Optimistic UI Updates, 🎨 Frontend Architecture Specification — UNNESKost v3

### Community 119 - "🎨 Panduan Identitas Visual & Brand — Pusat Kost UNNES"
Cohesion: 0.29
Nodes (6): 1. Varian Logo Resmi, 2. Palet Warna Resmi (Brand Color Palette), 3. Tipografi (Typography), 4. Komponen Logo di Frontend React (`Logo.tsx`), Karakteristik Varian Logo:, 🎨 Panduan Identitas Visual & Brand — Pusat Kost UNNES

### Community 120 - "📐 Design Specification: Milestone 3 — Modul Pencarian & Filter Cerdas"
Cohesion: 0.29
Nodes (6): 1. Arsitektur Domain & Boundary, 2. Kriteria Pencarian & Filter (*Faceted Filtering*), 3. Strategi Query & Database Fallback, 4. Antarmuka Pengguna (Frontend React 19 + Taste Skill), A. Parameter Pencarian (`SearchCriteria`), 📐 Design Specification: Milestone 3 — Modul Pencarian & Filter Cerdas

### Community 122 - "🤝 Panduan Kontribusi & Git Workflow — UNNESKost v3"
Cohesion: 0.33
Nodes (5): 1. Konvensi Pesan Commit (Conventional Commits), 2. Strategi Branching, 3. Checklist Sebelum Merge / PR (Definition of Done), 🤝 Panduan Kontribusi & Git Workflow — UNNESKost v3, Tipe Commit (`type`):

### Community 123 - "ADR-001: Pemilihan Arsitektur Modular Monolith dengan Pola DDD-Lite"
Cohesion: 0.33
Nodes (5): 1. Konteks & Masalah, 2. Analisis Keputusan, 3. Keputusan, 4. Konsekuensi, ADR-001: Pemilihan Arsitektur Modular Monolith dengan Pola DDD-Lite

### Community 124 - "ADR-002: Penerapan Action Pattern dan Single Responsibility di Domain Layer"
Cohesion: 0.33
Nodes (5): 1. Konteks & Masalah, 2. Analisis Keputusan, 3. Keputusan, 4. Konsekuensi, ADR-002: Penerapan Action Pattern dan Single Responsibility di Domain Layer

### Community 125 - "ADR-003: Pemilihan PostgreSQL 16 sebagai Basis Data Utama"
Cohesion: 0.33
Nodes (5): 1. Konteks & Masalah, 2. Analisis Keputusan, 3. Keputusan, 4. Konsekuensi, ADR-003: Pemilihan PostgreSQL 16 sebagai Basis Data Utama

### Community 126 - "ADR-004: Pemilihan Inertia.js v3 + React 19 tanpa Axios"
Cohesion: 0.33
Nodes (5): 1. Konteks & Masalah, 2. Analisis Keputusan, 3. Keputusan, 4. Konsekuensi, ADR-004: Pemilihan Inertia.js v3 + React 19 tanpa Axios

### Community 127 - "12. THE BLOCK LIBRARY (Contract - Implementations Land Here Iteratively)"
Cohesion: 0.40
Nodes (5): 12.A File Location, 12.B Required Frontmatter, 12.C Required Body Sections, 12.D Block-Library Discipline, 12. THE BLOCK LIBRARY (Contract - Implementations Land Here Iteratively)

### Community 128 - "5. CONTEXT-AWARE PROACTIVITY"
Cohesion: 0.40
Nodes (5): 5.A Sticky-Stack - Canonical Skeleton, 5.B Horizontal-Pan - Canonical Skeleton, 5.C Scroll-Reveal Stagger - Canonical Skeleton (lighter alternative), 5. CONTEXT-AWARE PROACTIVITY, 5.D Forbidden Animation Patterns

### Community 129 - "8. DARK MODE PROTOCOL"
Cohesion: 0.40
Nodes (5): 8.A Token Strategy (pick one, stick to it), 8.B Do Not Prescribe Specific Colors Here, 8.C Default Mode, 8.D Test in Both Modes Before Finishing, 8. DARK MODE PROTOCOL

### Community 130 - "🐎 Ponytail — The Lazily Efficient Senior Developer"
Cohesion: 0.40
Nodes (4): Persistence, 🐎 Ponytail — The Lazily Efficient Senior Developer, Rules, The Necessity Ladder

### Community 134 - "📐 Design Specification: Milestone 5 — Autentikasi, RBAC & Notifikasi"
Cohesion: 0.40
Nodes (4): 1. Arsitektur Domain & Boundary, 2. Invariant & Aturan Keamanan, 3. Frontend Experience (React 19 + Taste Skill), 📐 Design Specification: Milestone 5 — Autentikasi, RBAC & Notifikasi

### Community 136 - "🏛️ Architect Agent — UNNESKost v3"
Cohesion: 0.50
Nodes (3): 🏛️ Architect Agent — UNNESKost v3, 🚨 Aturan & Batasan Ketat, 🎯 Tugas & Tanggung Jawab

### Community 137 - "7. DIAL DEFINITIONS (Technical Reference)"
Cohesion: 0.50
Nodes (4): 7. DIAL DEFINITIONS (Technical Reference), DESIGN_VARIANCE (Level 1-10), MOTION_INTENSITY (Level 1-10), VISUAL_DENSITY (Level 1-10)

### Community 138 - "Format"
Cohesion: 0.50
Nodes (3): Format, 🐎 Ponytail Review — Over-Engineering Hunter, Tags:

### Community 143 - "11. Infrastruktur Lokal — Docker & Developer Experience"
Cohesion: 0.50
Nodes (4): 11.1 Services (docker-compose.yml), 11.2 Dockerfile, 11.3 Developer Experience — Makefile, 11. Infrastruktur Lokal — Docker & Developer Experience

### Community 144 - "6. Arsitektur Backend — Modular Monolith + DDD-lite + Action Pattern"
Cohesion: 0.50
Nodes (4): 6.1 Struktur Layer (Bounded Context), 6.2 Aturan Arsitektur (harus ditegakkan), 6.3 Alur Referensi, 6. Arsitektur Backend — Modular Monolith + DDD-lite + Action Pattern

### Community 145 - "13. Testing & CI"
Cohesion: 0.67
Nodes (3): 13.1 Piramida Test, 13.2 CI (GitHub Actions), 13. Testing & CI

### Community 146 - "4. Konteks Produk"
Cohesion: 0.67
Nodes (3): 4.1 Fitur yang harus dibangun, 4.2 Masalah arsitektur versi lama yang WAJIB diperbaiki, 4. Konteks Produk

### Community 147 - "8. Arsitektur Frontend — Feature-Based + Design System"
Cohesion: 0.67
Nodes (3): 8.1 Struktur, 8.2 Aturan Frontend, 8. Arsitektur Frontend — Feature-Based + Design System

## Knowledge Gaps
- **511 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+506 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **26 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `BookingRepositoryInterface.php`, `ListingImageController.php`, `Booking`, `Room`, `CreateReviewActionTest.php`, `RoomStatus.php`, `Payout`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `UserRole.php`, `InvalidArgumentException`, `Illuminate\Foundation\Testing\RefreshDatabase`, `ToggleWishlistActionTest.php`, `Listing`, `GenderType.php`, `OwnerListingManagementTest.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `NesaConversation`, `UserProfileData`, `Controller`, `PaymentReview`, `ListingRepositoryInterface.php`, `Payment`, `Illuminate\Http\RedirectResponse`, `Illuminate\Support\Str`, `web.php`, `Facility`, `AvatarSecurityTest`, `Illuminate\Http\Request`, `AdminPaymentController`, `Review`, `ProfileController.php`, `RoomController.php`?**
  _High betweenness centrality (0.119) - this node is a cross-community bridge._
- **Why does `GenderType` connect `NesaConversation` to `Pages/Listings/Index.tsx`, `listing.ts`, `NesaWidget.tsx`, `PublicLayout.tsx`?**
  _High betweenness centrality (0.098) - this node is a cross-community bridge._
- **Why does `Listing` connect `Listing` to `CreateReviewActionTest.php`, `Booking`, `Room`, `User`, `ListingImageController.php`, `Payout`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `RoomStatus.php`, `UserRole.php`, `Illuminate\Foundation\Testing\RefreshDatabase`, `InvalidArgumentException`, `ToggleWishlistActionTest.php`, `GenderType.php`, `OwnerListingManagementTest.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `NesaConversation`, `Illuminate\Contracts\Pagination\LengthAwarePaginator`, `Controller`, `ListingRepositoryInterface.php`, `web.php`, `Facility`, `AppServiceProvider.php`, `ListingData`, `Illuminate\Http\Request`, `RoomController.php`?**
  _High betweenness centrality (0.071) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _511 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `BookingRepositoryInterface.php` be split into smaller, more focused modules?**
  _Cohesion score 0.11 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Database\Migrations\Migration` be split into smaller, more focused modules?**
  _Cohesion score 0.05017921146953405 - nodes in this community are weakly interconnected._
- **Should `devDependencies` be split into smaller, more focused modules?**
  _Cohesion score 0.11764705882352941 - nodes in this community are weakly interconnected._