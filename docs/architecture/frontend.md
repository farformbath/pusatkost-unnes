# 🎨 Frontend Architecture Specification — UNNESKost v3

Dokumen ini mendefinisikan standar arsitektur frontend, struktur modul berbasis fitur (*Feature-Based Architecture*), design system, dan integrasi tipe data pada **UNNESKost v3**.

---

## 1. Prinsip Utama Frontend

1. **Type-Safety End-to-End (Strict TypeScript):** 100% bebas dari tipe `any`. Semua tipe data model disinkronkan otomatis dari backend menggunakan `spatie/typescript-transformer`.
2. **Inertia.js v3 Native Client:** Menggunakan `router` dan `useForm` / `useHttp` bawaan Inertia.js. **Dilarang menggunakan Axios**.
3. **Feature-Based Organization:** Mengelompokkan komponen, hooks, dan halaman berdasarkan fitur domain.
4. **Radix UI / shadcn Design System:** Komponen headless, accessible (keyboard, focus trap, aria-compliant), dan memiliki state lengkap (*hover, active, disabled, loading, empty, error*).
5. **Form Handling Terstruktur:** Menggunakan `react-hook-form + zod` untuk validasi client-side type-safe.
6. **Mobile-First Responsive:** Desain dioptimalkan untuk perangkat smartphone mahasiswa dengan transisi layout mulus ke desktop.

---

## 2. Struktur Direktori Frontend

```
resources/js/
├── app.tsx                       # Entry point aplikasi Inertia + React 19
├── bootstrap.ts                  # Inisialisasi dasar
├── css/
│   └── app.css                   # Konfigurasi Tailwind CSS v4
├── types/                        # Definisi tipe data TypeScript
│   ├── generated.d.ts            # Tipe otomatis dari spatie/typescript-transformer
│   ├── inertia.d.ts              # Extended Inertia page props (auth, flash, dll)
│   └── global.d.ts               # Tipe utilitas global
├── lib/                          # Helper & Utilities murni
│   ├── cn.ts                     # clsx + tailwind-merge helper
│   ├── formatters.ts             # formatRupiah, formatDate, formatArea
│   └── api.ts                    # Inertia router helper
├── hooks/                        # Custom React Hooks
│   ├── useDebounce.ts            # Debouncing untuk search bar
│   ├── useToast.ts               # Notifikasi toast global
│   └── useMediaQuery.ts          # Responsive breakpoint detector
├── components/
│   ├── ui/                       # Design System Radix/shadcn-style
│   │   ├── Button.tsx
│   │   ├── Input.tsx
│   │   ├── Modal.tsx
│   │   ├── Dialog.tsx
│   │   ├── Badge.tsx
│   │   ├── Card.tsx
│   │   ├── Select.tsx
│   │   ├── Tabs.tsx
│   │   └── Toast.tsx
│   └── shared/                   # Komponen lintas fitur
│       ├── ListingCard.tsx       # Kartu kos publik
│       ├── FilterBar.tsx         # Bar filter harga, fasilitas, lokasi
│       ├── RoomBadge.tsx         # Indikator status kamar (Available/Booked/dll)
│       ├── PhotoCollage.tsx      # Galeri foto interaktif
│       ├── StickyBooking.tsx     # Widget booking sticky di mobile & desktop
│       └── EmptyState.tsx        # Tampilan kosong terstandarisasi
├── layouts/                      # Template layout halaman
│   ├── PublicLayout.tsx          # Layout publik (Navbar pencari kos, Footer)
│   ├── AuthenticatedLayout.tsx   # Layout pengguna terautentikasi
│   ├── OwnerLayout.tsx           # Layout dashboard pemilik kos (Sidebar, Topbar)
│   └── AdminLayout.tsx           # Layout dashboard administrator
└── features/                     # Modul Berbasis Fitur (Feature Modules)
    ├── listings/                 # Halaman Katalog (Index), Detail Kos (Show), Galeri
    ├── booking/                  # BookingWidget, Form Sewa, BookingSummary, CancelDialog
    ├── review/                   # ReviewForm, ReviewList, StarRating
    ├── favorites/                # FavoriteButton, WishlistGrid
    ├── owner/                    # Owner Dashboard, CRUD Kos & Kamar, Kelola Booking
    ├── admin/                    # Admin Dashboard, Moderasi Ulasan, Verifikasi Kos & User
    └── profile/                  # Pengaturan Akun, Ganti Password, Avatar
```

---

## 3. Komponen Design System (Radix / shadcn Style)

Setiap komponen dasar di `resources/js/components/ui/` wajib memiliki properti aksesibilitas dan dukungan ragam varian (*variants*) melalui `class-variance-authority` (cva):

```tsx
// Contoh: resources/js/components/ui/Button.tsx
import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/lib/cn";

const buttonVariants = cva(
  "inline-flex items-center justify-center rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 disabled:pointer-events-none disabled:opacity-50",
  {
    variants: {
      variant: {
        default: "bg-emerald-600 text-white hover:bg-emerald-700 active:bg-emerald-800",
        outline: "border border-zinc-300 bg-transparent hover:bg-zinc-100 text-zinc-800",
        ghost: "hover:bg-zinc-100 text-zinc-700",
        destructive: "bg-red-600 text-white hover:bg-red-700",
      },
      size: {
        sm: "h-8 px-3 text-xs",
        md: "h-10 px-4 py-2",
        lg: "h-12 px-6 text-base",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "md",
    },
  }
);

export interface ButtonProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement>,
    VariantProps<typeof buttonVariants> {
  isLoading?: boolean;
}

export const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant, size, isLoading, children, disabled, ...props }, ref) => {
    return (
      <button
        ref={ref}
        className={cn(buttonVariants({ variant, size, className }))}
        disabled={disabled || isLoading}
        {...props}
      >
        {isLoading && <span className="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" />}
        {children}
      </button>
    );
  }
);
Button.displayName = "Button";
```

---

## 4. Validasi Form dengan React Hook Form + Zod

```tsx
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { router } from "@inertiajs/react";

const bookingSchema = z.object({
  room_id: z.string().uuid("Pilih kamar yang valid"),
  start_date: z.string().min(1, "Tanggal mulai sewa wajib diisi"),
  duration_months: z.number().int().min(1, "Minimal durasi sewa 1 bulan").max(24),
  notes: z.string().optional(),
});

type BookingFormData = z.infer<typeof bookingSchema>;

export function BookingWidget({ roomId }: { roomId: string }) {
  const { register, handleSubmit, formState: { errors, isSubmitting } } = useForm<BookingFormData>({
    resolver: zodResolver(bookingSchema),
    defaultValues: { room_id: roomId, duration_months: 1 },
  });

  const onSubmit = (data: BookingFormData) => {
    router.post("/bookings", data, {
      preserveScroll: true,
    });
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
      {/* Input fields */}
    </form>
  );
}
```

---

## 5. Optimistic UI Updates

Untuk operasi interaktif cepat (seperti tombol Wishlist / Favorit):
- State lokal diupdate secara instan di UI ketika pengguna menekan tombol.
- Request dikirim ke backend di latar belakang melalui Inertia router.
- Jika terjadi error pada jaringan atau server, state otomatis dikembalikan ke kondisi semula (*rollback*) dan memunculkan toast pesan error.
