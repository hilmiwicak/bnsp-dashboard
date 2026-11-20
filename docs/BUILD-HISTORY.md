# Build History

A short record of how this project was built, in order. The project is a
**Sistem Informasi Manajemen Kantor Virtual** (virtual office management
system) built with Laravel, made for BNSP certification.

---

## The brief

The mentor's prompts, in order:

1. **Authentication** — login and register, stored in MySQL, validated input,
   `password_hash()` / `password_verify()`, and logout.
2. **Dashboard** — a clean, modern, responsive dashboard following the BankDash
   reference image, adapted to a virtual office.
3. **Dashboard continuation** — standard virtual-office menus plus tenant data
   management: an input form, a table with add / edit / delete / search /
   filter, keeping the reference layout and visual style.

---

## What we built

1. **Project init** — stock Laravel skeleton.
2. **Authentication** — custom session-based login and register, MariaDB-backed
   `users` table, validation, hashing, logout.
3. **Cleanup** — removed the default welcome page and its smoke test.
4. **Dashboard shell** — layout, sidebar navigation, header, stat cards, and
   hand-built SVG charts, following the BankDash reference.
5. **Tenant data module** — `tenants` table, model, controller, request
   validation, and index / create / edit views.
6. **CRUD + search + filters** — add, edit, delete, keyword search, and status
   filter on the tenant table.
7. **Seeders** — demo accounts and eight sample tenants.
8. **Tests** — coverage for navigation, the tenant module, and the seeders.
9. **Typography pass** — refined type scale and spacing across all views.
10. **Scope trim** — dropped *paket layanan* and *layanan tambahan* entirely
    (no column, no menu, no module).
11. **Roles and rename** — added `admin` / `tenant` / `customer` roles with a
    nullable `users.tenant_id`, and renamed the `penyewa` entity to `tenant`
    throughout (table, model, controller, request, views, routes, tests).
12. **Authorization and routing** — role middleware, `/dashboard` for admins,
    `/portal` for tenants and customers, `403` for the wrong role.
13. **Tenant portal** — real content for non-admin accounts, plus a self-service
    tenancy application that promotes a `customer` to `tenant`.
14. **Ruang & Fasilitas** — `rooms` table and full CRUD, with tipe and status
    filters.
15. **Pemesanan** — `bookings` table and full CRUD, with double-booking
    detection (back-to-back slots allowed) and a one-click approve action.
16. **Jadwal Penggunaan Ruang** — read-only schedule derived from approved
    bookings, grouped by date, so Pemesanan stays the single source of truth.
17. **Kontrak Sewa** — `contracts` table and full CRUD, with the duration in
    months derived from the start and end dates.
18. **Tagihan & Pembayaran** — `invoices` table and full CRUD, with a one-click
    *Tandai Lunas* action and a running outstanding total.
19. **Dashboard from real queries** — replaced the mock chart data with live
    tenant counts, booking activity, paid revenue, outstanding bills, and the
    newest tenants.
20. **Laporan** — yearly report with per-month bookings, paid revenue and
    outstanding bills, plus a booking status breakdown.
21. **Logout for every role** — a logout control in the shared dashboard header,
    with the sidebar one pinned so a long menu cannot hide it.

---

## What each role can do

Access is enforced by `EnsureUserHasRole` (alias `role`): a wrong role gets
`403`, a guest is redirected to `/login`.

| Capability | Routes | Guest | Admin | Tenant | Customer |
|---|---|---|---|---|---|
| Login | `GET /login` · `POST /login` | yes | — | — | — |
| Register | `GET /register` · `POST /register` | yes | — | — | — |
| Logout | `POST /logout` | — | yes | yes | yes |
| Dashboard | `GET /dashboard` | → `/login` | read | 403 | 403 |
| Data Tenant | `GET /tenants` · `GET /tenants/create` · `POST /tenants` · `GET /tenants/{tenant}/edit` · `PUT /tenants/{tenant}` · `DELETE /tenants/{tenant}` | → `/login` | **CRUD** | 403 | 403 |
| Data Ruang & Fasilitas | `GET /ruang-fasilitas` · `GET /ruang-fasilitas/create` · `POST /ruang-fasilitas` · `GET /ruang-fasilitas/{room}/edit` · `PUT /ruang-fasilitas/{room}` · `DELETE /ruang-fasilitas/{room}` | → `/login` | **CRUD** | 403 | 403 |
| Pemesanan | `GET /pemesanan` · `GET /pemesanan/create` · `POST /pemesanan` · `GET /pemesanan/{booking}/edit` · `PUT /pemesanan/{booking}` · `DELETE /pemesanan/{booking}` · `PATCH /pemesanan/{booking}/status` | → `/login` | **CRUD** + approve | 403 | 403 |
| Kontrak Sewa | `GET /kontrak-sewa` · `GET /kontrak-sewa/create` · `POST /kontrak-sewa` · `GET /kontrak-sewa/{contract}/edit` · `PUT /kontrak-sewa/{contract}` · `DELETE /kontrak-sewa/{contract}` | → `/login` | **CRUD** | 403 | 403 |
| Jadwal Penggunaan Ruang | `GET /jadwal-ruang` | → `/login` | read | 403 | 403 |
| Tagihan & Pembayaran | `GET /tagihan-pembayaran` · `GET /tagihan-pembayaran/create` · `POST /tagihan-pembayaran` · `GET /tagihan-pembayaran/{invoice}/edit` · `PUT /tagihan-pembayaran/{invoice}` · `DELETE /tagihan-pembayaran/{invoice}` · `PATCH /tagihan-pembayaran/{invoice}/lunas` | → `/login` | **CRUD** + tandai lunas | 403 | 403 |
| Laporan | `GET /laporan` | → `/login` | read | 403 | 403 |
| Portal | `GET /portal` | → `/login` | 403 | read | read |
| Pengajuan sewa | `GET /portal/pengajuan` · `POST /portal/pengajuan` | → `/login` | 403 | ↪ `/portal` | create |

Landing page after login is role-based (`User::homePath()`): admin →
`/dashboard`, tenant and customer → `/portal`.

---

## Data model (relations)

- `users` → `tenants` — many-to-one through a nullable `users.tenant_id`, so
  several PIC accounts can share one tenancy.
- `tenants` ↔ `rooms` — **no direct column**. They are connected only through
  `bookings` (`tenant_id` + `room_id`), i.e. a many-to-many relationship with
  booking data as the pivot.
- `tenants` → `contracts` — one tenant may hold several contracts over time.
  A contract records the period and value, not a specific room.
- `tenants` → `invoices` — one invoice per tenant per billing period (`YYYY-MM`).

---

## Still open

- **Polish** — styled 403 page, README, login rate limiting.
- **Tenant self-service** — tenants cannot book a room, view their own invoices,
  or edit their data; those modules are admin-only.
- **User management** — `users` has no CRUD screen; roles are set by the seeder
  or by the customer → tenant promotion.
- **Contracts are not linked to a room** — see the relations note above.
