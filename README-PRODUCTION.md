# Boilerplate12

![forthebadge](https://forthebadge.com/images/badges/built-with-love.svg) ![forthebadge](https://forthebadge.com/images/badges/check-it-out.svg)

## Production Setup Guide

### 1) Firewall Setup
Open port 443 in your server to enable https request from outside.

### 2) Docker Setup
- Install [Docker Engine](https://docs.docker.com/engine/install/) in the server.
- Run additional command [Post Installation Steps - Manage Docker As Non Root User](https://docs.docker.com/engine/install/linux-postinstall/#manage-docker-as-a-non-root-user).
- Run additional command [Post Installation Steps - Docker Start On Boot](https://docs.docker.com/engine/install/linux-postinstall/#configure-docker-to-start-on-boot).
- Install [Docker Compose](https://docs.docker.com/compose/install/) in the server.
- Don't forget to restart the server.

### 3) Application Setup
- Clone repository to your workspace folder and enter the folder root in terminal.
- Copy compose-prod.yml to compose.yml.

```bash
cp compose-prod.yml compose.yml
```

- Copy environment keys and change related variables.

```bash
cp .env.prod.example .env
```
- Update environment variables
    - `DOCKER_USER_ID` set to current user id.
    - `DOCKER_USER_GROUP_ID` set to current user group id.
- Start the containers. It will take some time to build the containers in the first run.

```bash
docker compose up -d
```

- Generate application key.

```bash
docker compose exec app php artisan key:generate --force
```

### 4) Database Setup
- Migrate database and seed default data.

```bash
docker compose exec app php artisan migrate:fresh --seed --force
```

### 5) Storage Link

Link storage folder with the public folder

```bash
docker compose exec app php artisan storage:link
```

### 6) Optimization

Optimize application

```bash
docker compose exec app bash ./deployment/optimize.sh
```

## Updating Codebase Guide

Git pull latest code from repository and rebuild the images:

```bash
COMPOSE_BAKE=true docker compose build \
    && docker compose down \
    && docker compose up -d \
    && docker compose exec app bash ./deployment/optimize.sh \
    && docker system prune -f
```
