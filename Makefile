.PHONY: install dev build fresh seed clear help

# ─── Default ──────────────────────────────────────────────────────────────────
help:
	@echo ""
	@echo "  make install   Full setup from scratch (first time)"
	@echo "  make build     Build all frontend assets"
	@echo "  make dev       Start all dev servers (Laravel + Vite)"
	@echo "  make fresh     Wipe DB and re-seed"
	@echo "  make clear     Clear all Laravel caches"
	@echo ""

# ─── Full install (clone → ready to use) ─────────────────────────────────────
install:
	composer install
	@if [ ! -f .env ]; then cp .env.example .env; fi
	php artisan key:generate
	php artisan migrate
	php artisan db:seed
	npm install
	npm run build
	cd Modules/CurrencyExchange && npm install && npm run build

# ─── Build all frontend assets ───────────────────────────────────────────────
build:
	npm run build
	cd Modules/CurrencyExchange && npm run build

# ─── Start all dev servers ────────────────────────────────────────────────────
dev:
	composer run dev

# ─── Wipe database and re-seed ───────────────────────────────────────────────
fresh:
	php artisan migrate:fresh --seed

# ─── Clear all Laravel caches ────────────────────────────────────────────────
clear:
	php artisan config:clear
	php artisan cache:clear
	php artisan route:clear
	php artisan view:clear
