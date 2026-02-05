#!/bin/bash

echo "🚀 Starting Laravel Docker Setup..."

# Check if .env exists, if not copy from .env.docker
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
fi

# Build and start containers
echo "🏗️  Building and starting Docker containers..."
docker-compose up -d --build

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 10

# Install/update composer dependencies
echo "📦 Installing Composer dependencies..."
docker-compose exec app composer install

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Run migrations
echo "🗄️  Running database migrations..."
docker-compose exec app php artisan migrate --force

# Create storage link
echo "🔗 Creating storage symbolic link..."
docker-compose exec app php artisan storage:link

# Set permissions
echo "🔒 Setting permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌐 Your application is now running at:"
echo "   - Laravel App: http://localhost:8000"
echo "   - PHPMyAdmin: http://localhost:8080"
echo ""
echo "📋 Useful commands:"
echo "   - View logs: docker-compose logs -f"
echo "   - Stop containers: docker-compose down"
echo "   - Restart containers: docker-compose restart"
echo "   - Run artisan commands: docker-compose exec app php artisan [command]"
echo ""
