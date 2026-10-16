# Graph Report - unneskost2  (2026-09-01)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 791 nodes · 1797 edges · 56 communities (45 shown, 11 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 20 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d347ff39`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Listing
- Illuminate\Http\RedirectResponse
- Illuminate\Contracts\Pagination\LengthAwarePaginator
- Illuminate\Database\Migrations\Migration
- devDependencies
- GenderType.php
- listing.ts
- compilerOptions
- Bookings/Index.tsx
- Money
- Booking
- Checkout.tsx
- InvalidArgumentException
- Illuminate\Foundation\Testing\RefreshDatabase
- ConcurrencyLockTest.php
- RoomStatus.php
- Listings/Show.tsx
- Button.tsx
- User
- Review
- scripts
- Rooms/Index.tsx
- BookingStatus.php
- RoomStatusAudit
- ReviewData
- composer.json
- require-dev
- CreateReviewActionTest.php
- Badge.tsx
- ListingStatus.php
- require
- cn
- Payment
- config
- UserFactory.php
- ExpireBookingsAction
- ToggleWishlistActionTest.php
- psr-4
- extra
- logging.php
- post-create-project-cmd
- PaymentData
- keywords
- console.php
- Illuminate\Contracts\Console\Kernel
- GenderType

## God Nodes (most connected - your core abstractions)
1. `Listing` - 94 edges
2. `User` - 77 edges
3. `Room` - 51 edges
4. `Booking` - 43 edges
5. `Review` - 25 edges
6. `cn()` - 20 edges
7. `Facility` - 18 edges
8. `Listing` - 18 edges
9. `Button()` - 18 edges
10. `SearchCriteria` - 17 edges

## Surprising Connections (you probably didn't know these)
- `ReviewFlowTest` --references--> `Listing`  [EXTRACTED]
  tests/Feature/ReviewFlowTest.php → app/Domain/Listing/Models/Listing.php
- `WishlistFlowTest` --references--> `Listing`  [EXTRACTED]
  tests/Feature/WishlistFlowTest.php → app/Domain/Listing/Models/Listing.php
- `CreateReviewActionTest` --references--> `Listing`  [EXTRACTED]
  tests/Unit/Domain/Review/CreateReviewActionTest.php → app/Domain/Listing/Models/Listing.php
- `ToggleWishlistActionTest` --references--> `Listing`  [EXTRACTED]
  tests/Unit/Domain/Wishlist/ToggleWishlistActionTest.php → app/Domain/Listing/Models/Listing.php
- `WishlistFlowTest` --references--> `User`  [EXTRACTED]
  tests/Feature/WishlistFlowTest.php → app/Models/User.php

## Import Cycles
- None detected.

## Communities (56 total, 11 thin omitted)

### Community 0 - "Listing"
Cohesion: 0.05
Nodes (25): Facility, Listing, ListingImage, Room, RoomPolicy, SearchCriteria, EloquentSearchRepository, DatabaseSeeder (+17 more)

### Community 1 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.05
Nodes (29): ToggleRoomAvailabilityAction, UpdateListingAction, UploadListingImageAction, BookingController, PaymentController, Controller, BookingManagementController, ListingController (+21 more)

### Community 2 - "Illuminate\Contracts\Pagination\LengthAwarePaginator"
Cohesion: 0.06
Nodes (22): getForOwner(), getForUser(), EloquentListingRepository, findBySlug(), findForOwner(), getOwnerListings(), getPublishedListings(), EloquentReviewRepository (+14 more)

### Community 3 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.07
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 4 - "devDependencies"
Cohesion: 0.05
Nodes (43): clsx, concurrently, eslint, @eslint/js, @inertiajs/react, laravel-vite-plugin, dependencies, clsx (+35 more)

### Community 5 - "GenderType.php"
Cohesion: 0.07
Nodes (11): CreateListingAction, ListingData, SimulatePaymentRequest, StoreListingRequest, UpdateListingRequest, ReplyReviewRequest, SearchListingRequest, ToggleWishlistRequest (+3 more)

### Community 6 - "listing.ts"
Cohesion: 0.15
Nodes (13): PublicLayout(), PublicLayoutProps, AirbnbSearchBar(), AirbnbSearchBarProps, FilterBar(), FilterBarProps, ListingCard(), IndexProps (+5 more)

### Community 7 - "compilerOptions"
Cohesion: 0.10
Nodes (19): resources/js/**/*.d.ts, resources/js/**/*.ts, resources/js/**/*.tsx, vite/client, compilerOptions, baseUrl, esModuleInterop, forceConsistentCasingInFileNames (+11 more)

### Community 8 - "Bookings/Index.tsx"
Cohesion: 0.16
Nodes (10): Logo(), LogoProps, Badge(), ShowProps, IndexProps, WelcomeProps, Booking, BookingStatus (+2 more)

### Community 9 - "Money"
Cohesion: 0.12
Nodes (4): GenderType, Money, self, Stringable

### Community 10 - "Booking"
Cohesion: 0.15
Nodes (6): Booking, BookingPolicy, findByCode(), findById(), EloquentBookingRepository, BookingRepositoryInterface

### Community 11 - "Checkout.tsx"
Cohesion: 0.19
Nodes (10): Input, InputProps, Select, SelectOption, SelectProps, CheckoutProps, CreateProps, EditProps (+2 more)

### Community 12 - "InvalidArgumentException"
Cohesion: 0.17
Nodes (5): CreateRoomAction, UpdateRoomAction, RoomData, StoreRoomRequest, InvalidArgumentException

### Community 13 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.20
Nodes (7): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, ExampleTest, WishlistFlowTest, TestCase, ReviewModelTest, WishlistModelTest

### Community 14 - "ConcurrencyLockTest.php"
Cohesion: 0.17
Nodes (5): CreateBookingAction, BookingData, RoomUnavailableException, StoreBookingRequest, RuntimeException

### Community 16 - "Listings/Show.tsx"
Cohesion: 0.21
Nodes (10): GenderBadge(), FacilityBadge(), FacilityBadgeProps, ListingCardProps, StickyBookingWidget(), StickyBookingWidgetProps, ShowProps, IndexProps (+2 more)

### Community 17 - "Button.tsx"
Cohesion: 0.18
Nodes (9): Button(), ButtonProps, ReviewForm(), ReviewFormProps, ReviewList(), ReviewListProps, SuccessProps, Review (+1 more)

### Community 18 - "User"
Cohesion: 0.20
Nodes (6): ListingPolicy, User, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Spatie\Permission\Traits\HasRoles

### Community 20 - "scripts"
Cohesion: 0.17
Nodes (12): scripts, dev, phpstan, post-autoload-dump, post-root-package-install, post-update-cmd, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+4 more)

### Community 21 - "Rooms/Index.tsx"
Cohesion: 0.23
Nodes (7): Modal(), ModalProps, ImageGallery(), ImageGalleryProps, FilterModal(), FilterModalProps, ListingImage

### Community 23 - "RoomStatusAudit"
Cohesion: 0.27
Nodes (4): CancelBookingAction, RejectBookingAction, RoomStatusAudit, DomainException

### Community 25 - "composer.json"
Cohesion: 0.18
Nodes (10): autoload-dev, psr-4, description, license, minimum-stability, name, prefer-stable, Tests\\ (+2 more)

### Community 26 - "require-dev"
Cohesion: 0.18
Nodes (11): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pint, laravel/sail, mockery/mockery, nunomaduro/collision (+3 more)

### Community 27 - "CreateReviewActionTest.php"
Cohesion: 0.42
Nodes (4): CreateReviewAction, RecalculateListingRatingAction, ReplyReviewAction, CreateReviewActionTest

### Community 28 - "Badge.tsx"
Cohesion: 0.28
Nodes (6): BadgeProps, StatusBadge(), RoomCardProps, ListingStatus, Room, RoomStatus

### Community 30 - "require"
Cohesion: 0.25
Nodes (8): require, inertiajs/inertia-laravel, laravel/framework, laravel/sanctum, laravel/tinker, php, spatie/laravel-permission, spatie/typescript-transformer

### Community 31 - "cn"
Cohesion: 0.43
Nodes (6): Card(), CardContent(), CardFooter(), CardHeader(), CardProps, cn()

### Community 33 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 34 - "UserFactory.php"
Cohesion: 0.38
Nodes (4): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, static

### Community 35 - "ExpireBookingsAction"
Cohesion: 0.47
Nodes (3): ExpireBookingsCommand, ExpireBookingsAction, Illuminate\Console\Command

### Community 37 - "psr-4"
Cohesion: 0.33
Nodes (6): autoload, psr-4, App\\, App\\Domain\\, Database\\Factories\\, Database\\Seeders\\

### Community 38 - "extra"
Cohesion: 0.40
Nodes (5): dev-master, extra, branch-alias, laravel, dont-discover

### Community 39 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 40 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 42 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **111 isolated node(s):** `Controller`, `InputProps`, `SelectOption`, `SelectProps`, `CheckoutProps` (+106 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **11 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Listing` connect `Listing` to `Payment`, `Illuminate\Http\RedirectResponse`, `Illuminate\Contracts\Pagination\LengthAwarePaginator`, `ToggleWishlistActionTest.php`, `GenderType.php`, `Money`, `InvalidArgumentException`, `Illuminate\Foundation\Testing\RefreshDatabase`, `ConcurrencyLockTest.php`, `RoomStatus.php`, `User`, `Review`, `BookingStatus.php`, `CreateReviewActionTest.php`, `ListingStatus.php`?**
  _High betweenness centrality (0.098) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `Listing`, `Illuminate\Http\RedirectResponse`, `Illuminate\Contracts\Pagination\LengthAwarePaginator`, `UserFactory.php`, `Payment`, `GenderType.php`, `ToggleWishlistActionTest.php`, `Booking`, `InvalidArgumentException`, `Illuminate\Foundation\Testing\RefreshDatabase`, `ConcurrencyLockTest.php`, `RoomStatus.php`, `Review`, `BookingStatus.php`, `ReviewData`, `CreateReviewActionTest.php`, `ListingStatus.php`?**
  _High betweenness centrality (0.072) - this node is a cross-community bridge._
- **Why does `Room` connect `Listing` to `Payment`, `Illuminate\Http\RedirectResponse`, `ExpireBookingsAction`, `Money`, `InvalidArgumentException`, `ConcurrencyLockTest.php`, `RoomStatus.php`, `BookingStatus.php`, `RoomStatusAudit`, `ListingStatus.php`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Are the 6 inferred relationships involving `Room` (e.g. with `.search()` and `.run()`) actually correct?**
  _`Room` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Controller`, `InputProps`, `SelectOption` to the rest of the system?**
  _111 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Listing` be split into smaller, more focused modules?**
  _Cohesion score 0.05049088359046283 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Http\RedirectResponse` be split into smaller, more focused modules?**
  _Cohesion score 0.05450165612767239 - nodes in this community are weakly interconnected._