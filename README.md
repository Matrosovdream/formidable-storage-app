# Formidable Storage App

Laravel application packaged with Docker Compose for both development and production.

## Quick Start

After installing and seeding, sign in with the default admin account:

- **Email:** `matrosovdream@gmail.com`
- **Password:** `123`

Defined in [database/seeders/User/UserSeeder.php](database/seeders/User/UserSeeder.php) — change immediately after first login (or edit the seeder before running it).

---

## Development Installation

Runs on `http://localhost:9090` by default.

```bash
git clone <repo-url> formidable-storage-app
cd formidable-storage-app

cp .env.example .env

docker compose -f compose.dev.yaml up -d --build

docker compose -f compose.dev.yaml exec php-fpm composer install
docker compose -f compose.dev.yaml exec php-fpm php artisan key:generate
docker compose -f compose.dev.yaml exec php-fpm php artisan migrate --seed

docker compose -f compose.dev.yaml exec workspace npm install
docker compose -f compose.dev.yaml exec workspace npm run dev
```

Open `http://localhost:9090` and log in with the credentials above.

Extras:
- Postgres host port: `7432` (configurable via `POSTGRES_HOST_PORT`)
- Redis host port: `7379` (configurable via `REDIS_HOST_PORT`)
- Adminer: `http://localhost:7080`
- Vite: `http://localhost:7173` (configurable via `VITE_PORT`)

---

## Production Installation

Production uses Traefik for ingress (TLS via Let's Encrypt). Traefik must already be running on the host with an external docker network named `traefik-network`, a `websecure` entrypoint, and a `letsencrypt` cert resolver.

```bash
# One-time on the host (if Traefik network doesn't exist yet)
docker network create traefik-network

git clone <repo-url> formidable-storage-app
cd formidable-storage-app

cp .env.example .env
# Edit .env: set APP_ENV=production, APP_DEBUG=false,
# APP_URL=https://your-domain, APP_DOMAIN=your-domain,
# DB_PASSWORD, POSTGRES_PASSWORD, mail settings, etc.

docker compose -f compose.prod.yaml up -d --build

docker compose -f compose.prod.yaml exec php-fpm php artisan key:generate
docker compose -f compose.prod.yaml exec php-fpm php artisan migrate --force --seed
docker compose -f compose.prod.yaml exec php-fpm php artisan config:cache
docker compose -f compose.prod.yaml exec php-fpm php artisan route:cache
docker compose -f compose.prod.yaml exec php-fpm php artisan view:cache
```

Services in production: `web` (nginx, behind Traefik), `php-fpm`, `queue-worker`, `postgres`, `redis`.

---

## Update from Git

### Development

```bash
git pull

docker compose -f compose.dev.yaml up -d --build

docker compose -f compose.dev.yaml exec php-fpm composer install
docker compose -f compose.dev.yaml exec php-fpm php artisan migrate
docker compose -f compose.dev.yaml exec workspace npm install
docker compose -f compose.dev.yaml exec workspace npm run dev
```

### Production

```bash
git pull

docker compose -f compose.prod.yaml build
docker compose -f compose.prod.yaml up -d

docker compose -f compose.prod.yaml exec php-fpm php artisan migrate --force
docker compose -f compose.prod.yaml exec php-fpm php artisan config:cache
docker compose -f compose.prod.yaml exec php-fpm php artisan route:cache
docker compose -f compose.prod.yaml exec php-fpm php artisan view:cache
docker compose -f compose.prod.yaml restart queue-worker
```
