# TravelEASE V3 — Render PostgreSQL

This version adapts the original MySQL/mysqli TravelEASE application to PostgreSQL while retaining the existing PHP code through a small mysqli-compatible compatibility layer in `db.php`.

## Render Web Service environment variables
Set these on the TravelEASE Web Service:

- `DB_HOST` = Render PostgreSQL internal hostname
- `DB_PORT` = `5432`
- `DB_NAME` = the PostgreSQL database name shown by Render (for example `travelease1`)
- `DB_USER` = the Render PostgreSQL username
- `DB_PASSWORD` = the Render PostgreSQL password

Do not commit these credentials to GitHub.

## Database import
The included `travelease new db.sql` has been converted to PostgreSQL syntax. It can be imported into the Render PostgreSQL database using the database's PSQL command or an external PostgreSQL client.

The dump creates and populates the TravelEASE tables and synchronizes identity sequences after the data import.

## Important
- The original MySQL dump is no longer present; the included SQL file is PostgreSQL-compatible.
- Do not use MySQL `mysqli` extension. The Docker image installs `pdo_pgsql`.
- The SQL dump and `test_db.php` are not copied into the public Apache document root by the Dockerfile.
