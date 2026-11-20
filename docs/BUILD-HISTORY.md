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

---

## Next

12. **Authorization and routing** — role middleware, `/dashboard` for admins,
    `/portal` for tenants and customers.
13. **Tenant portal** — real content for non-admin accounts.
14. **Dashboard from real queries** — replace the mock arrays with database
    counts and aggregates.
15. **Polish** — README, styled 403 page, login rate limiting.
16. **Business modules**, one at a time — Ruang & Fasilitas, Pemesanan,
    Kontrak Sewa, Jadwal Penggunaan Ruang, Tagihan & Pembayaran, Laporan.
