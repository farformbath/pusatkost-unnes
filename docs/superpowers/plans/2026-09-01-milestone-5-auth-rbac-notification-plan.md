# Milestone 5: Autentikasi, RBAC & Notifikasi Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Membangun sistem autentikasi multi-peran (Mahasiswa vs Pemilik Kos), RBAC Spatie & Laravel Policies, sistem notifikasi in-app multi-channel, dan manajemen profil pengguna.

**Architecture:** Modular Monolith, Action Pattern, DTO, Spatie Permission, Laravel Policy, Inertia.js v3, React 19 Strict, Tailwind CSS v4, Pest, PHPStan Level Max.

**Spec:** [`docs/superpowers/specs/2026-09-01-milestone-5-auth-rbac-notification-design.md`](file:///c:/projects/unneskost2/docs/superpowers/specs/2026-09-01-milestone-5-auth-rbac-notification-design.md)

---

## Global Invariants
- `declare(strict_types=1);` pada seluruh berkas PHP baru.
- Strict Zero Axios.
- Strict TypeScript (`"strict": true`, 0 `any`).
- Dilarang hardcode `if ($user->role === 'admin')` di controller; gunakan Laravel Policy & Spatie Permissions `$user->can(...)` atau `$this->authorize(...)`.

---

## Task Breakdown

### Task 1: Database Migration, Spatie Permissions & User Model Extensions
- [ ] Migration `2026_09_01_000012_add_phone_and_avatar_to_users_table.php`.
- [ ] Migration `2026_09_01_000013_create_notifications_table.php` (Laravel standard notifications table).
- [ ] Enum `App\Domain\User\Enums\UserRole.php` (`ADMIN = 'admin'`, `OWNER = 'owner'`, `TENANT = 'tenant'`).
- [ ] Seeder `RoleAndPermissionSeeder.php` (Seed roles: `admin`, `owner`, `tenant` and permissions).
- [ ] Extend `app/Models/User.php` with `phone`, `avatar_url`, and helper methods.
- [ ] **Verification:** Unit tests (`UserRoleTest.php`).

### Task 2: Domain Policies & Access Control
- [ ] Policy `ListingPolicy.php` (Only owner or admin can edit/publish; public can view published).
- [ ] Policy `RoomPolicy.php` (Only owner of parent listing can manage rooms).
- [ ] Policy `BookingPolicy.php` (Tenant can view/cancel own booking; Owner of listing can approve/reject).
- [ ] Policy `ReviewPolicy.php` (Tenant can create review; Owner can reply).
- [ ] Register policies in `AuthServiceProvider` / `AppServiceProvider`.
- [ ] **Verification:** Unit tests (`PolicyAccessControlTest.php`).

### Task 3: Auth & Profile Actions, DTOs & Notification System
- [ ] DTO `RegisterUserData.php` & `UserProfileData.php`.
- [ ] Action `RegisterUserAction.php` (Hash password, assign role, dispatch events).
- [ ] Action `UpdateUserProfileAction.php` (Update profile & password).
- [ ] Notifications:
  - `BookingCreatedNotification.php` (Sent to listing owner when booking submitted).
  - `BookingStatusChangedNotification.php` (Sent to tenant on approve/reject).
  - `PaymentConfirmedNotification.php` (Sent to tenant & owner).
- [ ] Action `MarkNotificationAsReadAction.php`.
- [ ] **Verification:** Unit & Notification tests.

### Task 4: HTTP Layer, FormRequests, Controllers & Routes
- [ ] FormRequests: `LoginRequest.php`, `RegisterRequest.php`, `UpdateProfileRequest.php`.
- [ ] Controllers:
  - `Auth/AuthenticatedSessionController.php` (`GET /login`, `POST /login`, `POST /logout`).
  - `Auth/RegisteredUserController.php` (`GET /register`, `POST /register`).
  - `Profile/ProfileController.php` (`GET /profile`, `PUT /profile`, `PUT /profile/password`).
  - `Notification/NotificationController.php` (`GET /notifications`, `POST /notifications/{id}/read`, `POST /notifications/read-all`).
- [ ] Update `BookingController`, `Owner\BookingManagementController`, `PaymentController`, `ReviewController` to apply policy checks and trigger notifications.
- [ ] Share current authenticated user & unread notification count via `HandleInertiaRequests.php`.
- [ ] Register routes in `routes/web.php`.
- [ ] **Verification:** Feature tests (`AuthFlowTest.php`, `NotificationFlowTest.php`).

### Task 5: Frontend UI (React 19 + Taste Skill)
- [ ] `resources/js/types/auth.ts` & `notification.ts`.
- [ ] `resources/js/Pages/Auth/Login.tsx` (Clean modern login form with demo credential chips).
- [ ] `resources/js/Pages/Auth/Register.tsx` (Role selection tab: Mahasiswa Pencari Kos vs Pemilik Kos).
- [ ] `resources/js/Pages/Profile/Edit.tsx` (Profile information & password update).
- [ ] `resources/js/components/shared/NotificationBell.tsx` (Interactive popover with unread counter and quick read action).
- [ ] Update `resources/js/components/shared/PublicLayout.tsx` (User avatar dropdown, role badge, notification bell, login/register buttons when guest).
- [ ] **Verification:** TypeScript strict check (`tsc --noEmit`), ESLint, and Vite build.

### Task 6: Full Verification Gate & Milestone 5 Completion
- [ ] `composer validate`
- [ ] `./vendor/bin/pint --test`
- [ ] `composer phpstan` (Level Max)
- [ ] `php artisan test` (Pest suite)
- [ ] `npx tsc --noEmit`
- [ ] `npm run lint`
- [ ] `npm run build`
- [ ] `graphify update .`
- [ ] Update `tasks.md` & walkthrough.
