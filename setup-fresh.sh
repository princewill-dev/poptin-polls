#!/bin/bash

# Forcefully copy .env.example to .env
echo "Creating .env file..."
cp -f backend/.env.example backend/.env

#stopping containers
echo "Stopping Docker containers..."
docker compose down

# Start only backend for migrations
echo "Starting backend container for migrations..."
docker compose up -d backend

# Generate app key
echo "Generating application key..."
docker compose exec backend php artisan key:generate

# Delete the database file to avoid the specific fresh-migration error
echo "Cleaning up existing database file..."
rm -f backend/database.sqlite

# Re-create empty database file and set permissions
touch backend/database.sqlite
chmod 777 backend/database.sqlite

# Run fresh migration and seed
echo "Running fresh migrations and seeding..."
docker compose exec backend php artisan migrate:fresh --seed

# Start the rest of the containers
echo "Starting remaining containers..."
docker compose up -d

echo "-------------------------------------------------------"
echo "Fresh setup complete!"
echo "Create Admin account:    http://localhost:3005/setup"
echo "Frontend:   http://localhost:3005"
echo "Backend API: http://localhost:8005"
echo "Reverb:      ws://localhost:8007"
echo "-------------------------------------------------------"
