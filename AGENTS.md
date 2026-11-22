# AGENTS.md

A Laravel virtual-office management app (Sistem Informasi Manajemen Kantor Virtual) — Blade + Tailwind + MariaDB, with two roles: `admin` (pengelola/pemilik) and `tenant` (penyewa).

## Conventions

- UI copy is Indonesian. Auth screens and validation messages are English. Keep each that way.
- List pages are tables with search + filter; each module has create, edit, delete. Match that when adding a module.

## Where to look

- `README.md` — setup, demo accounts, role → route matrix.
- `docs/BUILD-HISTORY.md` — build steps, data model, test walkthrough, open gaps.
- `resources/css/app.css` — design tokens (`--color-brand-*`, `--color-dash-*`).
- `resources/views/components` — dashboard UI primitives (sidebar, data-table, badge, charts) to reuse rather than rebuild.
