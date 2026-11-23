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
11. **Roles and rename** — added `admin` / `tenant` roles with a
    nullable `users.tenant_id`, and renamed the `penyewa` entity to `tenant`
    throughout (table, model, controller, request, views, routes, tests).
12. **Authorization and routing** — role middleware, `/dashboard` for admins,
    `/portal` for tenants, `403` for the wrong role.
13. **Tenant portal** — real content for tenant accounts, plus a self-service
    tenancy application that links a new `tenant` account to its tenancy record.
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
22. **PIC detail trim** — dropped *Tempat Lahir*, *Tanggal Lahir* and *Jenis
    Kelamin* from the tenant PIC data; a PIC is now only name, email and phone
    number.
23. **Two-role model** — removed the `customer` role; an account is either
    `admin` or `tenant`. "Registered but no tenancy yet" is now just a `tenant`
    with a null `users.tenant_id`, so the self-service application links the
    account to its new tenancy record instead of promoting it.
24. **Tenant login accounts** — creating a tenant from *Data Tenant* now also
    provisions a login account for the PIC. A 12-character temporary password
    is generated and shown **once** on the tenant list with a copy button; only
    its hash is stored. If an account with that email already exists and is not
    linked to another tenancy, it is linked instead of duplicated. Editing a
    tenant keeps the linked account's name and email in sync.
25. **Tenant room requests** — tenants get `/portal/ruang`: the available rooms,
    a request form (room, date, time window, participant count, notes) and their
    own last ten requests with status. A request is always stored as `Menunggu`
    with `tenant_id` taken from the account, never from the form, and overlapping
    slots are rejected by the same rule the admin form uses. The admin approves
    it from *Pemesanan*; once approved it appears on *Jadwal Penggunaan Ruang*.
26. **Requests bounded by the rent period** — a room request must fall inside the
    tenant's current contract (`Aktif` or `Menunggu Verifikasi`), and the rejection
    message names the exact window. A tenancy whose contracts have all ended
    (`Berakhir`) or were cancelled (`Dibatalkan`) can no longer request rooms at
    all — the portal answers *“Masa sewa Anda sudah berakhir”*. Only an account
    that has never had a contract is unrestricted.
27. **Starting contract on tenant creation** — *Tambah Tenant* now also asks for
    *Tanggal Mulai Sewa* and *Durasi Sewa* and records the opening contract in the
    same transaction. The contract status follows the tenant
    status: `Aktif` when the tenant is created active, otherwise `Menunggu
    Verifikasi`. Editing a tenant never touches its contracts.
28. **Room capacity check** — a new `FitsRoomCapacity` rule compares
    `jumlah_peserta` with the selected room's `kapasitas` and rejects an oversize
    booking on both the admin form and the tenant request page. Previously the
    count was capped at a hardcoded 500 with no relation to the room. Neighbouring
    tests now pin `kapasitas` so the outcome is deterministic.
29. **Seeder covers every tenant** — each seeded tenant (`TNT-001` … `TNT-008`)
    now gets a PIC login account on its own PIC email, linked to that tenancy,
    instead of only `TNT-001` having one. The accounts use the demo password
    `password`, and re-running the seeder matches on email so nothing is
    duplicated and no existing password is overwritten.
30. **Contracts drop the rent value** — the unused `nilai_sewa` column is gone.
    It was never read by the dashboard, the report or any invoice, so it was a
    second price that could disagree with the invoices. Money now lives in one
    place: `invoices`.
31. **Room rate snapshot per booking** — `bookings.harga_per_jam` stores the room
    rate at the moment the booking is recorded and is backfilled from `rooms`.
    Raising a room's price later no longer changes what an older booking is worth.
    Swapping a booking to a different room refreshes the snapshot.
32. **Suggested invoice amount** — *Terbitkan Tagihan* gains a **Hitung** panel:
    pick a tenant and an `YYYY-MM` period and `App\Support\TagihanRuang` sums
    every booking with status `Disetujui` or `Selesai` in that month, prorating
    partial hours to the minute (`tarif × menit / 60`, rounded per booking) and
    using the snapshotted rate. The total pre-fills *Jumlah Tagihan*, which stays
    editable, and a warning appears when an invoice already exists for that tenant
    and period.
33. **Invoice explanation note** — `invoices.catatan` is a free-text field, like
    `contracts.catatan`, so the admin can record why a nominal was set. It shows
    under the amount on the invoice list.
34. **Batch invoice generation** — the **Hitung** panel takes a *Dari Bulan* and an
    optional *Sampai Bulan*. With a range selected it previews every month with
    its booking count, duration and amount, then `POST /tagihan-pembayaran/generate`
    creates one invoice per month (`periode`, computed `jumlah`, due date at that
    month's end, status `Belum Dibayar`, shared `catatan`). Months that already have
    an invoice or no approved bookings are skipped and counted in the flash
    message. A range is capped at 24 months.
35. **Month pickers** — the billing period is chosen from a list of month names
    (`November 2026`) instead of typed, in both the **Hitung** panel and the invoice
    form. The list spans 24 months back to 12 forward and always includes the value
    currently selected, so editing an old invoice still shows its period.

---

## What each role can do

Access is enforced by `EnsureUserHasRole` (alias `role`): a wrong role gets
`403`, a guest is redirected to `/login`. `admin` is the pengelola
(owner/operator) of the virtual office; `tenant` is the company renting space
from it.

| Capability | Routes | Guest | Admin | Tenant |
|---|---|---|---|---|
| Login | `GET /login` · `POST /login` | yes | — | — |
| Register | `GET /register` · `POST /register` | yes | — | — |
| Logout | `POST /logout` | — | yes | yes |
| Dashboard | `GET /dashboard` | → `/login` | read | 403 |
| Data Tenant | `GET /tenants` · `GET /tenants/create` · `POST /tenants` · `GET /tenants/{tenant}/edit` · `PUT /tenants/{tenant}` · `DELETE /tenants/{tenant}` | → `/login` | **CRUD** | 403 |
| Data Ruang & Fasilitas | `GET /ruang-fasilitas` · `GET /ruang-fasilitas/create` · `POST /ruang-fasilitas` · `GET /ruang-fasilitas/{room}/edit` · `PUT /ruang-fasilitas/{room}` · `DELETE /ruang-fasilitas/{room}` | → `/login` | **CRUD** | 403 |
| Pemesanan | `GET /pemesanan` · `GET /pemesanan/create` · `POST /pemesanan` · `GET /pemesanan/{booking}/edit` · `PUT /pemesanan/{booking}` · `DELETE /pemesanan/{booking}` · `PATCH /pemesanan/{booking}/status` | → `/login` | **CRUD** + approve | 403 |
| Kontrak Sewa | `GET /kontrak-sewa` · `GET /kontrak-sewa/create` · `POST /kontrak-sewa` · `GET /kontrak-sewa/{contract}/edit` · `PUT /kontrak-sewa/{contract}` · `DELETE /kontrak-sewa/{contract}` | → `/login` | **CRUD** | 403 |
| Jadwal Penggunaan Ruang | `GET /jadwal-ruang` | → `/login` | read | 403 |
| Tagihan & Pembayaran | `GET /tagihan-pembayaran` · `GET /tagihan-pembayaran/create` · `POST /tagihan-pembayaran` · `GET /tagihan-pembayaran/{invoice}/edit` · `PUT /tagihan-pembayaran/{invoice}` · `DELETE /tagihan-pembayaran/{invoice}` · `PATCH /tagihan-pembayaran/{invoice}/lunas` | → `/login` | **CRUD** + tandai lunas | 403 |
| Laporan | `GET /laporan` | → `/login` | read | 403 |
| Portal | `GET /portal` | → `/login` | 403 | read |
| Pengajuan sewa | `GET /portal/pengajuan` · `POST /portal/pengajuan` | → `/login` | 403 | ↪ `/portal` |
| Permintaan ruang | `GET /portal/ruang` · `POST /portal/ruang` | → `/login` | 403 | create |

Landing page after login is role-based (`User::homePath()`): admin →
`/dashboard`, tenant → `/portal`.

---

## Data model (relations)

- `users` → `tenants` — many-to-one through a nullable `users.tenant_id`, so
  several PIC accounts can share one tenancy.
- `tenants` ↔ `rooms` — **no direct column**. They are connected only through
  `bookings` (`tenant_id` + `room_id`), i.e. a many-to-many relationship with
  booking data as the pivot.
- `tenants` → `contracts` — one tenant may hold several contracts over time.
  A contract records only the period and its status, not a specific room and no
  longer a price.
- `tenants` → `invoices` — an invoice bills one tenant for one `YYYY-MM` period.
  The period is **not unique**, so the create form warns when one already exists.

---

## Test walkthrough

Manual flows for checking the interface. Start from a known state:

```bash
php artisan migrate:fresh --seed
```

That gives 8 tenants, 6 rooms, 6 bookings, 7 contracts and 24 invoices, so the
next generated codes are `TNT-009`, `RNG-007`, `PMN-007`, `KTR-008`, `INV-025`.
All demo accounts use the password `password`. Every seeded tenant also has a
PIC account on its own email (e.g. `budi@nusantaradigital.co.id` for
`TNT-001`), so any of them can log in and see their portal.

### 1. Admin — add a tenant

1. Log in as `admin@example.com`, land on **Ringkasan**.
2. Open **Data Tenant** — 8 rows, header reads “8 tenant terdaftar”.
3. Click **Tambah Tenant**.
4. Fill the form. *Kode Tenant* is prefilled `TNT-009`; complete *Informasi PIC*,
   *Informasi Perusahaan*, the tenant *Status*, then *Detail Sewa* — *Tanggal
   Mulai Sewa* and *Durasi Sewa* in months.
5. Submit **Simpan Data Tenant** — back on the list with a green confirmation
   banner and the new tenant's **Masa Sewa** already filled from the contract.
6. Type the new PIC name into **Pencarian** — only that row remains. Click
   **Reset** to clear the filter.
7. Go to **Dashboard** — *Total Tenant* is now 9 and *Tenant Terbaru* lists the
   new tenant with its status badge.

### 2. Admin — book a room, approve it, see the schedule

1. Open **Pemesanan** → **Tambah Pemesanan** (*kode* prefilled `PMN-007`).
2. Pick a tenant and a room, set a date a few days ahead, hours `09:00`–`11:00`,
   status **Menunggu**, then **Simpan Pemesanan**.
3. In the list the row shows an amber *Menunggu* badge and a **Setujui** button.
   Click it — the badge turns green *Disetujui*.
4. Open **Jadwal Penggunaan Ruang** — the booking appears under its date with
   `09:00 – 11:00`, the room, the tenant and the participant count.
5. Conflict check: add another booking on the same room and date from `10:00`
   to `12:00`. Submitting returns a red error on *Jam Mulai* and no row is
   created.
6. Back-to-back check: `11:00`–`12:00` on that same room and date is accepted.

### 3. Admin — contract, invoice, payment, report

1. **Kontrak Sewa** → **Tambah Kontrak** (*nomor* prefilled `KTR-008`). Set the
   period and status **Aktif**, then **Simpan Kontrak**. The *Durasi* column shows
   the months between the two dates. Contracts carry no price any more.
2. **Tagihan & Pembayaran** → **Terbitkan Tagihan** (*nomor* prefilled
   `INV-025`). In the **Hitung dari Pemakaian Ruang** panel pick a tenant and a
   *Dari Bulan*, then **Hitung** — the total of that tenant's `Disetujui` and
   `Selesai` bookings appears and *Jumlah Tagihan* fills in. Adjust it if you
   like, add a *Catatan* explaining the figure, then **Simpan Tagihan**.
   Filling *Sampai Bulan* instead shows one row per month and a **Terbitkan N
   Tagihan** button that creates them all at once.
3. The row shows an amber *Belum Dibayar* badge and a **Tandai Lunas** button.
   Click it — the badge turns green *Lunas* and the *Pembayaran* column fills in
   today's date.
4. The outstanding total in the card header drops by the amount you paid.
5. **Dashboard** — *Pendapatan Lunas* increases and *Riwayat Pendapatan* lifts
   for the current month.
6. **Laporan** — the current year's month row shows the booking count and the
   paid amount; the totals row matches the stat cards.

### 4. Tenant — self-register and apply for a tenancy

1. Log out, then **Register** a new account.
2. You land on **Portal** with the amber *Menunggu Verifikasi* badge and the
   card “Pengajuan sewa belum dibuat”.
3. Click **Ajukan Sewa** — the form has no *Kode Tenant* or *Status* field, and
   *Tanggal Mulai Sewa* cannot be earlier than today.
4. Submit **Kirim Pengajuan** — back on the portal with a confirmation naming
   the new tenant code.
5. The account is now a tenant: visiting `/portal/pengajuan` redirects to
   `/portal`, and the sidebar lists only **Portal**.

### 5. Admin — verify the applicant, tenant sees active membership

1. Log in as admin, find the new tenant in **Data Tenant**, open **Edit**.
2. Change *Status Tenant* to **Aktif**, submit **Perbarui Data Tenant**. This also
   activates the tenant's pending contract.
3. **Dashboard** — the *Menunggu Verifikasi* count drops and the “tenant aktif”
   hint rises.
4. Log in as that tenant — the portal shows a green *Aktif* badge,
   “Keanggotaan aktif”, and a *Data Sewa* card with the tenant code, company,
   PIC, start date, duration and end date.

### 6. Tenant — request a room, admin approves

1. Log in as a tenant with a verified tenancy, then open **Ruang & Fasilitas**
   in the sidebar.
2. The page lists the rooms with status **Tersedia**, plus **Permintaan Saya**.
3. Pick a room, a date from today onward and inside your contract period, then
   **Kirim Permintaan** — the request appears with an amber *Menunggu* badge.
4. Submitting an overlapping window on the same room is rejected; a room that is
   not *Tersedia* cannot be selected, and a date outside the contract period is
   rejected with the allowed window in the message.
5. Log in as admin, open **Pemesanan**, and approve it. The booking now shows on
   **Jadwal Penggunaan Ruang**, and the tenant sees the green *Disetujui* badge.

### 7. Every role — guard and logout

1. As a tenant, open `/dashboard`, `/tenants`, `/pemesanan` and
   `/laporan` — each returns **403**.
2. Click **Logout** in the header — redirected to `/login` with a confirmation.
3. While logged out, open `/portal` — redirected to `/login`.
4. Log in as admin and open `/portal` — **403**, because the portal is for
   tenants only.

---

## Still open

- **Polish** — styled 403 page, README, login rate limiting.
- **Tenant self-service** — tenants can request room bookings but cannot view
  their own invoices or edit their data; those modules are admin-only.
- **User management** — `users` has no CRUD screen. Accounts come from
  self-registration, the seeder, or automatic provisioning when an admin creates
  a tenant.
- **Change password** — there is no self-service password change. The temporary
  password shown once at tenant creation is the only credential the admin can
  hand over; a change-password screen is the next step.
- **Contracts are not linked to a room** — see the relations note above.
