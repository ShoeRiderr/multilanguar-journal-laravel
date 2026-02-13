# Testing the New Build Flow

This document demonstrates how the new host-based asset building strategy works.

## Overview

The new build strategy fixes the Wayfinder Docker build issue by:
1. Building assets ON THE HOST (where Docker containers are already running)
2. Wayfinder can then use `docker compose exec app php artisan` to generate TypeScript types
3. The Dockerfile simply copies the pre-built assets

## Test Scenario 1: Development Mode

```bash
# 1. Start backend containers
docker compose up -d app mysql redis

# 2. Install Node dependencies (if not already installed)
npm install

# 3. Start Vite dev server (with hot reload)
npm run dev
```

**Expected Result:**
- Backend containers start successfully
- Vite dev server starts on http://localhost:5173
- Wayfinder generates TypeScript types using `docker compose exec app php artisan`
- Hot module replacement (HMR) works correctly
- TypeScript types are available in `resources/js/routes`, `resources/js/actions`, etc.

## Test Scenario 2: Production Build

```bash
# 1. Start backend containers (needed for Wayfinder)
docker compose up -d app mysql redis

# 2. Wait for MySQL to be ready
sleep 10

# 3. Install Node dependencies
npm ci

# 4. Build assets on host
npm run build

# 5. Verify build output
ls -la public/build/

# 6. Rebuild Docker image (copies pre-built assets)
docker compose build --no-cache app

# 7. Deploy with new image
docker compose up -d

# 8. Verify deployment
docker compose ps
curl http://localhost
```

**Expected Result:**
- Backend containers start successfully
- `npm run build` completes without errors
- Wayfinder successfully generates TypeScript types during build
- `public/build/` directory contains:
  - `manifest.json`
  - JavaScript bundles
  - CSS files
  - Other assets
- Docker image builds successfully and includes the pre-built assets
- Application serves correctly with compiled assets

## Test Scenario 3: Using deploy.sh Script

```bash
# Run the automated deployment script
./deploy.sh
```

**Expected Result:**
- Script pulls latest code from git
- Backend containers start
- MySQL health check passes
- Node dependencies install
- Assets build successfully
- Docker image rebuilds
- All containers restart
- Migrations run
- Laravel optimization completes
- Application is fully deployed and functional

## Test Scenario 4: Using Makefile Commands

```bash
# Development mode (backend + frontend with hot reload)
make dev

# Build assets (requires running backend)
make build

# Full deployment
make deploy

# View all available commands
make help
```

**Expected Result:**
- `make dev` starts backend and Vite dev server
- `make build` successfully builds assets
- `make deploy` runs the full deployment flow
- `make help` displays all available commands

## Validation Checklist

After implementing the changes, verify:

- [ ] Dockerfile has NO Node build stage
- [ ] Dockerfile copies `public/build` from host
- [ ] vite.config.ts uses `docker compose exec -T app php artisan wayfinder:generate --with-form`
- [ ] deploy.sh starts backend BEFORE building assets
- [ ] deploy.sh runs `npm run build` on the host
- [ ] deploy.sh rebuilds Docker image AFTER assets are built
- [ ] docker-compose.yml mounts `./public/build` as a volume
- [ ] Makefile has `dev`, `build`, and `deploy` commands
- [ ] README.md documents the new build architecture
- [ ] docs/HETZNER_DEPLOYMENT.md explains the build flow
- [ ] .gitignore excludes `public/build/` and backup files

## Troubleshooting Tests

### Test: Build fails with "docker compose exec: command not found"

```bash
# Simulate the error by building without backend running
docker compose down
npm run build
```

**Expected Result:**
- Build should fail with an error indicating that `docker compose exec` cannot connect
- Error message should be clear about the missing backend

**Fix:**
```bash
docker compose up -d app mysql
npm run build
```

### Test: Missing TypeScript types

```bash
# Check if Wayfinder types are generated
ls -la resources/js/routes/
ls -la resources/js/actions/
```

**Expected Result:**
- TypeScript type files should be present
- Files should be recent (matching build time)

## Performance Comparison

### Old Approach (Multi-stage Docker build)
- Build time: ~5-10 minutes
- **FAILS** due to Wayfinder requiring PHP which isn't available in Node container
- Requires workarounds (SKIP_WAYFINDER=true)

### New Approach (Host-based build)
- Backend startup: ~10-20 seconds
- Asset build: ~30-60 seconds
- Docker rebuild: ~1-2 minutes
- Total: ~2-3 minutes
- **SUCCEEDS** because Wayfinder can use `docker compose exec`

## Conclusion

The new host-based asset building strategy:
1. ✅ Solves the Wayfinder PHP dependency issue
2. ✅ Reduces build complexity
3. ✅ Improves build reliability
4. ✅ Maintains fast build times
5. ✅ Works consistently in development and production
