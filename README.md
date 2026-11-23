# Kantor Virtual — Sistem Informasi Manajemen

A management information system for a **virtual office**: tenants, bookable
rooms and facilities, room bookings, tenancy contracts, monthly invoices, a room
usage schedule, and yearly reports.

Built with Laravel as a BNSP certification project. The interface is in
Indonesian and follows a clean, modern dashboard design.

---

## Features

| Menu | What it does |
|---|---|
| **Dashboard** | Live summary: tenant totals and status split, seven-day booking activity, six-month paid revenue, outstanding bills, newest tenants |
| **Data Tenant** | Full CRUD for tenants, with keyword search and status filter |
| **Data Ruang & Fasilitas** | Full CRUD for rooms and their facilities, filterable by type and status |
| **Pemesanan** | Full CRUD for room bookings, with double-booking detection and one-click approval |
| **Kontrak Sewa** | Full CRUD for tenancy contracts; duration is derived from the contract period |
| **Jadwal Penggunaan Ruang** | Read-only schedule, built from approved bookings and grouped by date |
| **Tagihan & Pembayaran** | Full CRUD for monthly invoices, with one-click settlement and an outstanding total |
| **Laporan** | Yearly report: bookings, paid revenue and outstanding bills per month, plus a booking status breakdown |

Additional behaviour:

- **Authentication** — register, login, logout, with hashed passwords.
- **Roles** — `admin` and `tenant`, enforced per route.
- **Tenant portal** — tenants get a portal instead of the admin area.
- **Self-service application** — a tenant account with no tenancy record yet can
  apply, which creates the tenant record and links it to the account.
- **Tenant login accounts** — creating a tenant from *Data Tenant* also provisions
  a PIC login account with a 12-character temporary password. The password is
  shown once (copyable, masked) and stored only as a hash; an existing unlinked
  account with the same email is linked instead of duplicated. The tenant's
  starting contract is recorded in the same step, so its rent period is filled
  in from day one.
- **Room requests** — a tenant sees the available rooms and can request a booking.
  The request is stored as `Menunggu` and appears in the admin's *Pemesanan* list
  for approval; the admin still picks rooms for bookings they create themselves.
  A request must fall inside the tenant's active contract period, and a tenancy
  whose contract has ended or was cancelled can no longer request rooms. Only an
  account that has never had a contract is unrestricted.
- **Booking conflict detection** — overlapping bookings on the same room and
  date are rejected; back-to-back slots are allowed.
- **Capacity check** — a booking's participant count cannot exceed the capacity of
  the selected room, on both the admin form and the tenant request page.
- **No external chart library** — the bar, pie and line charts are hand-built
  SVG components.

---

## Roles

| Role | Lands on | Access |
|---|---|---|
| `admin` | `/dashboard` | Full CRUD on tenants, rooms, bookings, contracts and invoices |
| `tenant` | `/portal` | Read-only view of their own tenancy, the tenancy application form, and room booking requests |

`admin` is the pengelola (owner/operator) of the virtual office; `tenant` is the
company renting space from it. A newly registered account is a `tenant` that is
not yet linked to a tenancy (`tenant_id` is null) — it stays in that state until
it submits an application and an admin verifies it.

Wrong role gets `403`; guests are redirected to `/login`. The full role × route
matrix is in [`docs/BUILD-HISTORY.md`](docs/BUILD-HISTORY.md).

---

## Tech stack

- **PHP** 8.3+ · **Laravel** 13
- **MySQL / MariaDB** (the project was developed against MariaDB via Laravel Herd)
- **Tailwind CSS** 4 · **Vite** 8
- **PHPUnit** 12 · **Laravel Pint** for code style
- Fonts: Poppins (auth screens), Inter (dashboard), self-hosted through the
  Vite font plugin

---

## Requirements

- PHP 8.3 or newer with the usual Laravel extensions
- Composer 2
- Node.js 20.19+ or 22.12+ (required by Vite 8) and npm
- MySQL or MariaDB

---

## Installation

```bash
# 1. Dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate
```

Point the database settings in `.env` at your MySQL/MariaDB server:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bnsp_laravel
DB_USERNAME=root
DB_PASSWORD=
```

Then create the schema and the sample data:

```bash
php artisan migrate --seed
```

Build the front-end assets, then run the app:

```bash
npm run build      # production build
php artisan serve  # http://127.0.0.1:8000
```

While developing, `composer dev` starts the PHP server, the queue listener, the
log tailer and the Vite dev server together. `composer setup` is a shortcut that
installs dependencies, creates `.env`, generates the key, migrates, and builds
assets — but it does not seed, so run `php artisan db:seed` afterwards.

To start over with fresh sample data:

```bash
php artisan migrate:fresh --seed
```

### Demo accounts

All demo accounts use the password `password`.

| Email | Role | Notes |
|---|---|---|
| `admin@example.com` | Admin | Full management area |
| `tenant@example.com` | Tenant | Linked to tenant `TNT-001` |
| `pendaftar@example.com` | Tenant | Not linked to a tenancy — can submit an application |
| PIC email of each seeded tenant | Tenant | Every seeded tenant (`TNT-001` … `TNT-008`) also gets an account for its PIC email, linked to that tenancy |

So `siti@karyamandiri.id` (PIC of `TNT-002`) can log in straight to `/portal`, and so on for the other seeded tenants.

---

## Testing

```bash
php artisan test
```

Tests run against an in-memory SQLite database, so no MySQL setup is needed and
your development data is untouched. The suite covers authentication, role
access, every module's CRUD, booking conflicts, invoice settlement, the
dashboard aggregates, the reports, and the seeders.

Code style:

```bash
./vendor/bin/pint
```

---

## Project structure

```
app/
  Enums/UserRole.php              admin / tenant
  Http/Controllers/               one controller per module
  Http/Middleware/                EnsureUserHasRole (alias: role)
  Http/Requests/                  validation per entity
  Models/                         Tenant, Room, Booking, Contract, Invoice, User
  Models/Concerns/                HasSequentialCode (TNT-001, RNG-001, …)
  Support/Bulan.php               Indonesian day and month names
resources/views/
  dashboard.blade.php             admin dashboard
  components/dashboard/           sidebar, stat cards, charts, table, form fields
  components/layouts/             auth and dashboard layouts
  {tenants,rooms,bookings,contracts,invoices,schedules,reports,portal}/
database/
  migrations/                     schema
  seeders/                        demo data, safe to run repeatedly
  factories/                      test data
docs/BUILD-HISTORY.md             how the project was built, step by step
```

---

## Notes

- **`paket layanan` and `layanan tambahan` are intentionally out of scope** —
  there is no column, menu, or module for them.
- **Fonts need network access at build time.** `npm run build` downloads Poppins
  and Inter from `fonts.bunny.net`; allow that host if the build fails.
- **Room and tenant deletion cascades** to their bookings, contracts and
  invoices. Deleting a tenant keeps the login accounts, which end up with a
  null `tenant_id`.
- **Tenants have no write access.** Booking rooms and viewing invoices are
  admin-only; a tenant-facing booking flow is the natural next step.
