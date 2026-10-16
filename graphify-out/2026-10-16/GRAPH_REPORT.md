# Graph Report - unneskost2  (2026-09-02)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 1026 nodes · 2402 edges · 77 communities (62 shown, 15 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 38 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d347ff39`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\RedirectResponse
- Illuminate\Database\Migrations\Migration
- devDependencies
- Review
- Booking.php
- Illuminate\Foundation\Testing\RefreshDatabase
- User
- Booking
- Money
- Listing.php
- UserRole.php
- InvalidArgumentException
- TestCase.php
- Bookings/Index.tsx
- Button.tsx
- compilerOptions
- Illuminate\Foundation\Http\FormRequest
- Rooms/Index.tsx
- Wishlist
- listing.ts
- Listings/Show.tsx
- Listing
- PublicLayout.tsx
- GenderType.php
- Illuminate\Http\UploadedFile
- Illuminate\Database\Eloquent\Relations\HasMany
- ListingPolicy
- SearchCriteria
- UserProfileData
- scripts
- Pages/Listings/Index.tsx
- Illuminate\Support\Str
- Illuminate\Contracts\Pagination\LengthAwarePaginator
- composer.json
- require-dev
- RoleAndPermissionSeeder.php
- cn
- NotificationSecurityTest.php
- PolicyCrossIsolationTest
- HandleInertiaRequests.php
- EmailVerificationTest.php
- require
- PrivilegeEscalationTest.php
- config
- Illuminate\Database\Seeder
- EloquentSearchRepository
- LoginRequest
- psr-4
- UserFactory.php
- AvatarSecurityTest
- ExpireBookingsAction
- AppServiceProvider
- extra
- logging.php
- StoreListingRequest
- UpdateListingRequest
- SearchListingRequest
- post-create-project-cmd
- PaymentData
- Illuminate\Database\Eloquent\Relations\HasOne
- keywords
- console.php
- .setUp
- Illuminate\Contracts\Console\Kernel
- notification.ts
- GenderType

## God Nodes (most connected - your core abstractions)
1. `User` - 141 edges
2. `Listing` - 78 edges
3. `Room` - 39 edges
4. `Booking` - 29 edges
5. `Review` - 24 edges
6. `Button()` - 22 edges
7. `Controller` - 20 edges
8. `cn()` - 20 edges
9. `Listing` - 18 edges
10. `SearchCriteria` - 17 edges

## Surprising Connections (you probably didn't know these)
- `ReviewFlowTest` --references--> `Listing`  [EXTRACTED]
  tests/Feature/ReviewFlowTest.php → app/Domain/Listing/Models/Listing.php
- `ReviewFlowTest` --references--> `User`  [EXTRACTED]
  tests/Feature/ReviewFlowTest.php → app/Models/User.php
- `WishlistFlowTest` --references--> `Listing`  [EXTRACTED]
  tests/Feature/WishlistFlowTest.php → app/Domain/Listing/Models/Listing.php
- `WishlistFlowTest` --references--> `User`  [EXTRACTED]
  tests/Feature/WishlistFlowTest.php → app/Models/User.php
- `ToggleWishlistActionTest` --references--> `Listing`  [EXTRACTED]
  tests/Unit/Domain/Wishlist/ToggleWishlistActionTest.php → app/Domain/Listing/Models/Listing.php

## Import Cycles
- None detected.

## Communities (77 total, 15 thin omitted)

### Community 0 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.05
Nodes (33): RejectBookingAction, findByCode(), findById(), getForOwner(), getForUser(), MarkNotificationAsReadAction, OwnerVerificationController, AuthenticatedSessionController (+25 more)

### Community 1 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.06
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 2 - "devDependencies"
Cohesion: 0.05
Nodes (43): clsx, concurrently, eslint, @eslint/js, @inertiajs/react, laravel-vite-plugin, dependencies, clsx (+35 more)

### Community 3 - "Review"
Cohesion: 0.08
Nodes (12): CreateReviewAction, RecalculateListingRatingAction, ReplyReviewAction, ReviewData, Review, EloquentReviewRepository, ReviewController, ReplyReviewRequest (+4 more)

### Community 4 - "Booking.php"
Cohesion: 0.11
Nodes (10): ApproveBookingAction, ProcessPaymentAction, BookingCreatedNotification, BookingStatusChangedNotification, PaymentConfirmedNotification, BookingStatus, Carbon\Carbon, Illuminate\Bus\Queueable (+2 more)

### Community 5 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.11
Nodes (7): ToggleRoomAvailabilityAction, Room, Illuminate\Foundation\Testing\RefreshDatabase, ListingSearchFilterTest, User, NotificationFlowTest, Booking

### Community 6 - "User"
Cohesion: 0.09
Nodes (9): BookingPolicy, ReviewPolicy, User, Illuminate\Contracts\Auth\MustVerifyEmail, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Spatie\Permission\Traits\HasRoles (+1 more)

### Community 7 - "Booking"
Cohesion: 0.11
Nodes (8): CancelBookingAction, Booking, Payment, RoomStatusAudit, EloquentBookingRepository, BookingRepositoryInterface, DomainException, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 8 - "Money"
Cohesion: 0.08
Nodes (6): BookingData, GenderType, StoreBookingRequest, Money, self, Stringable

### Community 9 - "Listing.php"
Cohesion: 0.16
Nodes (9): Facility, ListingImage, RoomPolicy, ListingSeeder, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsToMany, Illuminate\Database\Eloquent\SoftDeletes (+1 more)

### Community 10 - "UserRole.php"
Cohesion: 0.11
Nodes (10): RegisterUserAction, self, RegisterUserData, RegisterRequest, RoleAndPermissionSeeder, Illuminate\Auth\Events\Registered, Illuminate\Support\Facades\Hash, Illuminate\Validation\Rules\Password (+2 more)

### Community 11 - "InvalidArgumentException"
Cohesion: 0.12
Nodes (7): CreateRoomAction, PublishListingAction, UpdateRoomAction, RoomData, VerifyOwnerAction, StoreRoomRequest, InvalidArgumentException

### Community 12 - "TestCase.php"
Cohesion: 0.11
Nodes (7): Illuminate\Foundation\Testing\TestCase, ExampleTest, ReviewFlowTest, WishlistFlowTest, TestCase, ReviewModelTest, WishlistModelTest

### Community 13 - "Bookings/Index.tsx"
Cohesion: 0.14
Nodes (12): Logo(), LogoProps, Badge(), GenderBadge(), ShowProps, SuccessProps, IndexProps, WelcomeProps (+4 more)

### Community 14 - "Button.tsx"
Cohesion: 0.16
Nodes (10): Button(), ButtonProps, Input, InputProps, Select, SelectOption, SelectProps, ReviewFormProps (+2 more)

### Community 15 - "compilerOptions"
Cohesion: 0.10
Nodes (19): resources/js/**/*.d.ts, resources/js/**/*.ts, resources/js/**/*.tsx, vite/client, compilerOptions, baseUrl, esModuleInterop, forceConsistentCasingInFileNames (+11 more)

### Community 16 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): SimulatePaymentRequest, UploadImageRequest, UpdateAvatarRequest, UpdatePasswordRequest, ToggleWishlistRequest, Illuminate\Foundation\Http\FormRequest

### Community 17 - "Rooms/Index.tsx"
Cohesion: 0.20
Nodes (11): FacilityBadge(), FacilityBadgeProps, ListingCard(), ListingCardProps, CreateProps, EditProps, IndexProps, IndexProps (+3 more)

### Community 18 - "Wishlist"
Cohesion: 0.16
Nodes (6): ToggleWishlistAction, Wishlist, EloquentWishlistRepository, Builder, ToggleWishlistActionTest, WishlistRepositoryInterface

### Community 19 - "listing.ts"
Cohesion: 0.20
Nodes (11): BadgeProps, StatusBadge(), RoomCardProps, IndexProps, GenderType, ListingStatus, PaginatedData, PaginationLinks (+3 more)

### Community 20 - "Listings/Show.tsx"
Cohesion: 0.16
Nodes (11): ImageGallery(), ImageGalleryProps, StickyBookingWidget(), StickyBookingWidgetProps, ReviewForm(), ReviewList(), ReviewListProps, ShowProps (+3 more)

### Community 21 - "Listing"
Cohesion: 0.18
Nodes (5): Listing, EloquentListingRepository, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Relations\Relation, ListingRepositoryInterface

### Community 22 - "PublicLayout.tsx"
Cohesion: 0.18
Nodes (7): NotificationBell(), PublicLayout(), PublicLayoutProps, LoginProps, EditProfileProps, AuthUser, SharedProps

### Community 23 - "GenderType.php"
Cohesion: 0.22
Nodes (3): CreateListingAction, ListingData, Illuminate\Validation\Rule

### Community 24 - "Illuminate\Http\UploadedFile"
Cohesion: 0.23
Nodes (6): RoomUnavailableException, UploadListingImageAction, UpdateAvatarAction, Illuminate\Http\UploadedFile, Illuminate\Support\Facades\Storage, RuntimeException

### Community 26 - "ListingPolicy"
Cohesion: 0.17
Nodes (3): ListingPolicy, Listing, PolicyAccessControlTest

### Community 27 - "SearchCriteria"
Cohesion: 0.29
Nodes (4): SearchListingsAction, SearchCriteria, search(), SearchListingsActionTest

### Community 28 - "UserProfileData"
Cohesion: 0.18
Nodes (4): UpdateUserProfileAction, self, UserProfileData, UpdateProfileRequest

### Community 29 - "scripts"
Cohesion: 0.17
Nodes (12): scripts, dev, phpstan, post-autoload-dump, post-root-package-install, post-update-cmd, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+4 more)

### Community 30 - "Pages/Listings/Index.tsx"
Cohesion: 0.21
Nodes (7): AirbnbSearchBar(), AirbnbSearchBarProps, FilterBar(), FilterBarProps, FilterModal(), FilterModalProps, IndexProps

### Community 31 - "Illuminate\Support\Str"
Cohesion: 0.18
Nodes (5): UpdateListingAction, Illuminate\Auth\Events\Lockout, Illuminate\Support\Facades\RateLimiter, Illuminate\Support\Str, Illuminate\Validation\ValidationException

### Community 32 - "Illuminate\Contracts\Pagination\LengthAwarePaginator"
Cohesion: 0.25
Nodes (7): findBySlug(), findForOwner(), getOwnerListings(), getPublishedListings(), getListingReviews(), getUserWishlists(), Illuminate\Contracts\Pagination\LengthAwarePaginator

### Community 33 - "composer.json"
Cohesion: 0.18
Nodes (10): autoload-dev, psr-4, description, license, minimum-stability, name, prefer-stable, Tests\\ (+2 more)

### Community 34 - "require-dev"
Cohesion: 0.18
Nodes (11): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pint, laravel/sail, mockery/mockery, nunomaduro/collision (+3 more)

### Community 35 - "RoleAndPermissionSeeder.php"
Cohesion: 0.20
Nodes (5): Spatie\Permission\DefaultTeamResolver, Spatie\Permission\Models\Permission, Spatie\Permission\Models\Role, Spatie\Permission\PermissionRegistrar, AuthenticationSecurityTest

### Community 36 - "cn"
Cohesion: 0.31
Nodes (8): Card(), CardContent(), CardFooter(), CardHeader(), CardProps, Modal(), ModalProps, cn()

### Community 37 - "NotificationSecurityTest.php"
Cohesion: 0.27
Nodes (4): CreateBookingAction, Booking, Exception, NotificationSecurityTest

### Community 38 - "PolicyCrossIsolationTest"
Cohesion: 0.20
Nodes (4): Booking, Listing, Room, PolicyCrossIsolationTest

### Community 39 - "HandleInertiaRequests.php"
Cohesion: 0.25
Nodes (5): HandleInertiaRequests, Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Inertia\Middleware

### Community 40 - "EmailVerificationTest.php"
Cohesion: 0.22
Nodes (4): Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL, EmailVerificationTest

### Community 41 - "require"
Cohesion: 0.25
Nodes (8): require, inertiajs/inertia-laravel, laravel/framework, laravel/sanctum, laravel/tinker, php, spatie/laravel-permission, spatie/typescript-transformer

### Community 43 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 44 - "Illuminate\Database\Seeder"
Cohesion: 0.38
Nodes (3): DatabaseSeeder, FacilitySeeder, Illuminate\Database\Seeder

### Community 46 - "EloquentSearchRepository"
Cohesion: 0.33
Nodes (4): EloquentSearchRepository, SearchListingsAction, SearchRepositoryInterface, User

### Community 48 - "psr-4"
Cohesion: 0.33
Nodes (6): autoload, psr-4, App\\, App\\Domain\\, Database\\Factories\\, Database\\Seeders\\

### Community 49 - "UserFactory.php"
Cohesion: 0.47
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 51 - "ExpireBookingsAction"
Cohesion: 0.60
Nodes (3): ExpireBookingsCommand, ExpireBookingsAction, Illuminate\Console\Command

### Community 53 - "extra"
Cohesion: 0.40
Nodes (5): dev-master, extra, branch-alias, laravel, dont-discover

### Community 54 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 58 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 61 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **115 isolated node(s):** `Payment`, `PaymentStatus`, `LogoProps`, `SuccessProps`, `WelcomeProps` (+110 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **15 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Http\RedirectResponse`, `Review`, `Booking.php`, `Illuminate\Foundation\Testing\RefreshDatabase`, `Booking`, `Listing.php`, `UserRole.php`, `InvalidArgumentException`, `TestCase.php`, `Wishlist`, `GenderType.php`, `Illuminate\Http\UploadedFile`, `Illuminate\Database\Eloquent\Relations\HasMany`, `ListingPolicy`, `UserProfileData`, `RoleAndPermissionSeeder.php`, `NotificationSecurityTest.php`, `PolicyCrossIsolationTest`, `HandleInertiaRequests.php`, `EmailVerificationTest.php`, `PrivilegeEscalationTest.php`, `Illuminate\Database\Seeder`, `PaymentStatus.php`, `UserFactory.php`, `AvatarSecurityTest`, `.setUp`?**
  _High betweenness centrality (0.170) - this node is a cross-community bridge._
- **Why does `Listing` connect `Listing` to `Illuminate\Http\RedirectResponse`, `Review`, `Booking.php`, `Illuminate\Foundation\Testing\RefreshDatabase`, `Booking`, `Money`, `Listing.php`, `InvalidArgumentException`, `TestCase.php`, `Wishlist`, `GenderType.php`, `Illuminate\Http\UploadedFile`, `Illuminate\Database\Eloquent\Relations\HasMany`, `SearchCriteria`, `Illuminate\Support\Str`, `Illuminate\Contracts\Pagination\LengthAwarePaginator`, `PaymentStatus.php`, `EloquentSearchRepository`, `Illuminate\Database\Eloquent\Relations\HasOne`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `Booking` connect `Booking` to `Illuminate\Http\RedirectResponse`, `Booking.php`, `Money`, `Listing.php`, `PaymentStatus.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\HasOne`?**
  _High betweenness centrality (0.025) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `Room` (e.g. with `.search()` and `.run()`) actually correct?**
  _`Room` has 7 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Payment`, `PaymentStatus`, `LogoProps` to the rest of the system?**
  _115 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\RedirectResponse` be split into smaller, more focused modules?**
  _Cohesion score 0.05407111298127498 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Database\Migrations\Migration` be split into smaller, more focused modules?**
  _Cohesion score 0.05513784461152882 - nodes in this community are weakly interconnected._