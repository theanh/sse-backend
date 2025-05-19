#!/bin/bash

# Set permissions
chmod -R 777 api/storage api/bootstrap/cache

# Start Docker containers
docker compose up -d

# Wait for database to be ready
echo "Waiting for database to be ready..."
sleep 30

# Install dependencies
docker compose exec app composer install

# Copy env
docker compose exec app cp .env.example .env

# Generate application key
docker compose exec app php artisan key:generate

# Run migrations
docker compose exec app php artisan migrate

# Show container status
docker compose ps
