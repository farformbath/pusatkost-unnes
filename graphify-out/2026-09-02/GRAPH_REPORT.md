# Graph Report - unneskost2  (2026-09-01)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 956 nodes · 2193 edges · 65 communities (54 shown, 11 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 22 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d347ff39`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\RedirectResponse
- Booking
- Illuminate\Database\Migrations\Migration
- Illuminate\Contracts\Pagination\LengthAwarePaginator
- devDependencies
- Money
- User
- Booking.php
- Listing
- GenderType.php
- LoginRequest
- Button.tsx
- compilerOptions
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Listing
- Illuminate\Foundation\Http\FormRequest
- ListingData
- Listing.php
- Listings/Show.tsx
- Facility
- listing.ts
- Room
- Review
- Badge.tsx
- TestCase.php
- Illuminate\Database\Eloquent\Relations\HasMany
- UserRole.php
- UserProfileData
- Pages/Listings/Index.tsx
- scripts
- composer.json
- require-dev
- Owner/Listings/Index.tsx
- cn
- RoomUnavailableException
- ReviewData
- RegisterRequest
- Logo.tsx
- require
- config
- Room.php
- Wishlist
- psr-4
- RoleAndPermissionSeeder.php
- extra
- logging.php
- post-create-project-cmd
- PaymentData
- .fromArray
- keywords
- console.php
- TestCase
- Illuminate\Contracts\Console\Kernel
- notification.ts
- GenderType

## God Nodes (most connected - your core abstractions)
1. `User` - 123 edges
2. `Listing` - 91 edges
3. `Room` - 50 edges
4. `Booking` - 43 edges
5. `Review` - 24 edges
6. `Button()` - 21 edges
7. `cn()` - 20 edges
8. `Listing` - 18 edges
9. `Facility` - 18 edges
10. `RoomStatusAudit` - 17 edges

## Surprising Connections (you probably didn't know these)
- `PolicyAccessControlTest` --references--> `User`  [EXTRACTED]
  tests/Unit/Domain/User/PolicyAccessControlTest.php → app/Models/User.php
- `CreateReviewActionTest` --references--> `CreateReviewAction`  [EXTRACTED]
  tests/Unit/Domain/Review/CreateReviewActionTest.php → app/Domain/Review/Actions/CreateReviewAction.php
- `CreateReviewActionTest` --references--> `ReplyReviewAction`  [EXTRACTED]
  tests/Unit/Domain/Review/CreateReviewActionTest.php → app/Domain/Review/Actions/ReplyReviewAction.php
- `ToggleWishlistActionTest` --references--> `Listing`  [EXTRACTED]
  tests/Unit/Domain/Wishlist/ToggleWishlistActionTest.php → app/Domain/Listing/Models/Listing.php
- `ToggleWishlistActionTest` --references--> `ToggleWishlistAction`  [EXTRACTED]
  tests/Unit/Domain/Wishlist/ToggleWishlistActionTest.php → app/Domain/Wishlist/Actions/ToggleWishlistAction.php

## Import Cycles
- None detected.

## Communities (65 total, 11 thin omitted)

### Community 0 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.05
Nodes (34): findByCode(), findById(), ToggleRoomAvailabilityAction, findBySlug(), findForOwner(), MarkNotificationAsReadAction, AuthenticatedSessionController, RegisteredUserController (+26 more)

### Community 1 - "Booking"
Cohesion: 0.06
Nodes (17): ExpireBookingsCommand, ApproveBookingAction, CancelBookingAction, ExpireBookingsAction, ProcessPaymentAction, RejectBookingAction, BookingData, Booking (+9 more)

### Community 2 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.06
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 3 - "Illuminate\Contracts\Pagination\LengthAwarePaginator"
Cohesion: 0.06
Nodes (24): getForOwner(), getForUser(), EloquentListingRepository, getOwnerListings(), getPublishedListings(), EloquentReviewRepository, getListingReviews(), SearchListingsAction (+16 more)

### Community 4 - "devDependencies"
Cohesion: 0.05
Nodes (43): clsx, concurrently, eslint, @eslint/js, @inertiajs/react, laravel-vite-plugin, dependencies, clsx (+35 more)

### Community 5 - "Money"
Cohesion: 0.07
Nodes (9): CreateRoomAction, UpdateRoomAction, RoomData, GenderType, StoreRoomRequest, Money, InvalidArgumentException, self (+1 more)

### Community 6 - "User"
Cohesion: 0.09
Nodes (10): ListingPolicy, ReviewPolicy, User, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\SoftDeletes, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Spatie\Permission\Traits\HasRoles (+2 more)

### Community 7 - "Booking.php"
Cohesion: 0.10
Nodes (9): BookingCreatedNotification, BookingStatusChangedNotification, PaymentConfirmedNotification, BookingPolicy, SimulatePaymentRequest, BookingStatus, Illuminate\Bus\Queueable, Illuminate\Database\Eloquent\Relations\HasOne (+1 more)

### Community 8 - "Listing"
Cohesion: 0.11
Nodes (6): CreateListingAction, PublishListingAction, Listing, Illuminate\Database\Eloquent\Builder, ReviewFlowTest, WishlistFlowTest

### Community 9 - "GenderType.php"
Cohesion: 0.20
Nodes (4): CreateBookingAction, Illuminate\Database\Eloquent\Relations\Relation, Illuminate\Foundation\Testing\RefreshDatabase, NotificationFlowTest

### Community 10 - "LoginRequest"
Cohesion: 0.12
Nodes (9): LoginRequest, UserFactory, Illuminate\Auth\Events\Lockout, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, Illuminate\Support\Facades\RateLimiter, Illuminate\Support\Str, Illuminate\Validation\ValidationException (+1 more)

### Community 11 - "Button.tsx"
Cohesion: 0.17
Nodes (10): NotificationBell(), PublicLayout(), PublicLayoutProps, Button(), ButtonProps, ReviewFormProps, LoginProps, EditProfileProps (+2 more)

### Community 12 - "compilerOptions"
Cohesion: 0.10
Nodes (19): resources/js/**/*.d.ts, resources/js/**/*.ts, resources/js/**/*.tsx, vite/client, compilerOptions, baseUrl, esModuleInterop, forceConsistentCasingInFileNames (+11 more)

### Community 13 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.18
Nodes (5): Payment, ListingImage, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 14 - "Listing"
Cohesion: 0.19
Nodes (11): Input, InputProps, Select, SelectOption, SelectProps, CheckoutProps, CreateProps, EditProps (+3 more)

### Community 15 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (5): UploadImageRequest, ReplyReviewRequest, SearchListingRequest, ToggleWishlistRequest, Illuminate\Foundation\Http\FormRequest

### Community 16 - "ListingData"
Cohesion: 0.15
Nodes (5): UpdateListingAction, ListingData, StoreListingRequest, UpdateListingRequest, Illuminate\Validation\Rule

### Community 17 - "Listing.php"
Cohesion: 0.17
Nodes (4): ListingPolicy, RoleAndPermissionSeeder, Listing, PolicyAccessControlTest

### Community 18 - "Listings/Show.tsx"
Cohesion: 0.16
Nodes (11): ImageGallery(), ImageGalleryProps, StickyBookingWidget(), StickyBookingWidgetProps, ReviewForm(), ReviewList(), ReviewListProps, ShowProps (+3 more)

### Community 19 - "Facility"
Cohesion: 0.17
Nodes (6): Facility, DatabaseSeeder, FacilitySeeder, ListingSeeder, Illuminate\Database\Eloquent\Relations\BelongsToMany, Illuminate\Database\Seeder

### Community 20 - "listing.ts"
Cohesion: 0.20
Nodes (9): FacilityBadge(), FacilityBadgeProps, ListingCardProps, RoomCardProps, ListingStatus, PaginationLinks, Room, RoomStatus (+1 more)

### Community 21 - "Room"
Cohesion: 0.17
Nodes (5): Room, RoomPolicy, ListingSearchFilterTest, User, Booking

### Community 22 - "Review"
Cohesion: 0.18
Nodes (4): CreateReviewAction, RecalculateListingRatingAction, ReplyReviewAction, Review

### Community 23 - "Badge.tsx"
Cohesion: 0.23
Nodes (9): Badge(), BadgeProps, GenderBadge(), ShowProps, IndexProps, Booking, BookingStatus, Payment (+1 more)

### Community 24 - "TestCase.php"
Cohesion: 0.19
Nodes (4): ExampleTest, ReviewModelTest, ToggleWishlistActionTest, WishlistModelTest

### Community 26 - "UserRole.php"
Cohesion: 0.27
Nodes (3): RegisterUserAction, RegisterUserData, UserRoleTest

### Community 27 - "UserProfileData"
Cohesion: 0.18
Nodes (4): UpdateUserProfileAction, self, UserProfileData, UpdateProfileRequest

### Community 28 - "Pages/Listings/Index.tsx"
Cohesion: 0.19
Nodes (8): AirbnbSearchBar(), AirbnbSearchBarProps, FilterBar(), FilterBarProps, FilterModal(), FilterModalProps, IndexProps, GenderType

### Community 29 - "scripts"
Cohesion: 0.17
Nodes (12): scripts, dev, phpstan, post-autoload-dump, post-root-package-install, post-update-cmd, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+4 more)

### Community 30 - "composer.json"
Cohesion: 0.18
Nodes (10): autoload-dev, psr-4, description, license, minimum-stability, name, prefer-stable, Tests\\ (+2 more)

### Community 31 - "require-dev"
Cohesion: 0.18
Nodes (11): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pint, laravel/sail, mockery/mockery, nunomaduro/collision (+3 more)

### Community 32 - "Owner/Listings/Index.tsx"
Cohesion: 0.25
Nodes (6): StatusBadge(), ListingCard(), IndexProps, IndexProps, PaginatedData, Wishlist

### Community 33 - "cn"
Cohesion: 0.31
Nodes (8): Card(), CardContent(), CardFooter(), CardHeader(), CardProps, Modal(), ModalProps, cn()

### Community 34 - "RoomUnavailableException"
Cohesion: 0.31
Nodes (5): RoomUnavailableException, UploadListingImageAction, Illuminate\Http\UploadedFile, Illuminate\Support\Facades\Storage, RuntimeException

### Community 36 - "RegisterRequest"
Cohesion: 0.20
Nodes (3): RegisterRequest, UpdatePasswordRequest, Illuminate\Validation\Rules\Password

### Community 37 - "Logo.tsx"
Cohesion: 0.28
Nodes (4): Logo(), LogoProps, SuccessProps, WelcomeProps

### Community 38 - "require"
Cohesion: 0.25
Nodes (8): require, inertiajs/inertia-laravel, laravel/framework, laravel/sanctum, laravel/tinker, php, spatie/laravel-permission, spatie/typescript-transformer

### Community 39 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 42 - "psr-4"
Cohesion: 0.33
Nodes (6): autoload, psr-4, App\\, App\\Domain\\, Database\\Factories\\, Database\\Seeders\\

### Community 43 - "RoleAndPermissionSeeder.php"
Cohesion: 0.40
Nodes (4): Spatie\Permission\DefaultTeamResolver, Spatie\Permission\Models\Permission, Spatie\Permission\Models\Role, Spatie\Permission\PermissionRegistrar

### Community 44 - "extra"
Cohesion: 0.40
Nodes (5): dev-master, extra, branch-alias, laravel, dont-discover

### Community 45 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 46 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 49 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **115 isolated node(s):** `Controller`, `ButtonProps`, `ReviewFormProps`, `PublicLayoutProps`, `LoginProps` (+110 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **11 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Http\RedirectResponse`, `Booking`, `RoomUnavailableException`, `Booking.php`, `Room.php`, `GenderType.php`, `LoginRequest`, `Listing`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Listing.php`, `Facility`, `Room`, `Review`, `TestCase.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `UserRole.php`, `UserProfileData`?**
  _High betweenness centrality (0.141) - this node is a cross-community bridge._
- **Why does `Listing` connect `Listing` to `Illuminate\Http\RedirectResponse`, `RoomUnavailableException`, `Illuminate\Contracts\Pagination\LengthAwarePaginator`, `Money`, `User`, `Booking.php`, `GenderType.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ListingData`, `Listing.php`, `Facility`, `Room`, `Review`, `TestCase.php`, `Illuminate\Database\Eloquent\Relations\HasMany`?**
  _High betweenness centrality (0.074) - this node is a cross-community bridge._
- **Why does `Room` connect `Room` to `Illuminate\Http\RedirectResponse`, `Booking`, `Illuminate\Contracts\Pagination\LengthAwarePaginator`, `Money`, `User`, `Booking.php`, `Room.php`, `Listing`, `GenderType.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Facility`, `Illuminate\Database\Eloquent\Relations\HasMany`?**
  _High betweenness centrality (0.034) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `Room` (e.g. with `.search()` and `.run()`) actually correct?**
  _`Room` has 7 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Controller`, `ButtonProps`, `ReviewFormProps` to the rest of the system?**
  _115 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\RedirectResponse` be split into smaller, more focused modules?**
  _Cohesion score 0.050284031138228484 - nodes in this community are weakly interconnected._
- **Should `Booking` be split into smaller, more focused modules?**
  _Cohesion score 0.05649717514124294 - nodes in this community are weakly interconnected._