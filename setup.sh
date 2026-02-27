#!/bin/bash

# Forcefully copy .env.example to .env
echo "Creating .env file..."
cp -f backend/.env.example backend/.env

# Create database file if it doesn't exist
touch backend/database.sqlite

# Ensure permissions are correct
chmod 777 backend/database.sqlite

# Start only backend for migrations
echo "Starting backend container for migrations..."
docker compose up -d backend

# Generate app key
echo "Generating application key..."
docker compose exec backend php artisan key:generate

docker compose exec backend php artisan migrate --seed

# Start the rest of the containers
echo "Starting remaining containers..."
docker compose up -d

echo "-------------------------------------------------------"
echo "Setup complete!"
echo "Frontend:   http://localhost:3005"
echo "Backend API: http://localhost:8005"
echo "Reverb:      ws://localhost:8007"
echo "-------------------------------------------------------"
