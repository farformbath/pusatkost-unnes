# 🤝 Panduan Kontribusi & Git Workflow — UNNESKost v3

Dokumen ini memandu pengembang dan AI coding agent dalam konvensi Git commit, penamaan branch, dan kepatuhan Definition of Done (DoD).

---

## 1. Konvensi Pesan Commit (Conventional Commits)

Format: `<type>(<scope>): <deskripsi singkat>`

### Tipe Commit (`type`):
- `feat`: Penambahan fitur baru (misal: `feat(booking): implement BookRoomAction with pessimistic lock`)
- `fix`: Perbaikan bug (misal: `fix(auth): prevent session timeout on role switch`)
- `refactor`: Refactoring kode tanpa mengubah fungsionalitas (misal: `refactor(listing): extract RoomAvailabilityService`)
- `test`: Penambahan atau perbaikan unit/feature test (misal: `test(booking): add concurrency double-booking test`)
- `docs`: Perubahan atau penambahan dokumentasi (misal: `docs(api): add REST API envelope specification`)
- `chore`: Konfigurasi tooling, dependensi, atau build scripts (misal: `chore(ci): update GitHub Actions workflow`)

---

## 2. Strategi Branching

- `main` / `master`: Branch produksi yang stabil dan terproteksi (selalu hijau di CI).
- `develop`: Branch integrasi fitur utama.
- `feat/<nama-fitur>`: Branch untuk pengerjaan fitur baru (misal: `feat/m1-listing-crud`).
- `fix/<nama-bug>`: Branch perbaikan bug (misal: `fix/search-fallback-db`).

---

## 3. Checklist Sebelum Merge / PR (Definition of Done)

Sebelum kode di-merge atau diselesaikan:
- [ ] `tsc --noEmit` bersih tanpa eror TypeScript (0 `any`).
- [ ] `phpstan analyse` level max bersih tanpa eror.
- [ ] `vendor/bin/pint --test` rapi sesuai standar format Laravel.
- [ ] `npm run lint` dan `npm run format` bersih.
- [ ] Semua test Pest (`composer test`) dan Vitest (`npm run test`) hijau.
- [ ] Tidak ada logika bisnis di Controller atau Route closure.
- [ ] Responsif di perangkat mobile dan desktop dengan state lengkap (*hover, active, disabled, loading, empty, error*).
