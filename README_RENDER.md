# TravelEASE - Render deployment notes

## Runtime
- Docker
- PHP 8.3 + Apache
- MySQL 8
- Render Web Service + Render private MySQL service

## Web service environment variables
DB_HOST=<Render MySQL private service hostname>
DB_PORT=3306
DB_NAME=travelease
DB_USER=<MySQL application user>
DB_PASSWORD=<MySQL application password>

Do not commit real passwords or a production `.env` file.

## Important
The SQL dump is intentionally not copied into the public Apache document root by the Dockerfile.
The SQL dump contains existing application/user data, so keep the Git repository private unless the dump is sanitized first.
