# QuickBill 305 Dockerization Script for Windows
# Save this file as "dockerize.ps1" in your QuickBill project directory
# Run in PowerShell as Administrator: .\dockerize.ps1

Write-Host "===============================================" -ForegroundColor Blue
Write-Host " QuickBill 305 Windows Docker Setup" -ForegroundColor Blue  
Write-Host "===============================================" -ForegroundColor Blue

# Check if Docker is running
try {
    docker --version | Out-Null
    Write-Host "✓ Docker is available" -ForegroundColor Green
} catch {
    Write-Host "✗ Docker not found. Please install Docker Desktop for Windows" -ForegroundColor Red
    Write-Host "Download from: https://docs.docker.com/desktop/install/windows-install/" -ForegroundColor Yellow
    exit 1
}

Write-Host "Creating Docker files..." -ForegroundColor Cyan

# Create Dockerfile
@"
FROM php:8.2-apache
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpng-dev libxml2-dev libzip-dev zip unzip \
    libfreetype6-dev libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mysqli mbstring gd zip \
    && a2enmod rewrite headers

COPY . /var/www/html/
RUN mkdir -p uploads storage \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 777 uploads storage

EXPOSE 80
CMD ["apache2-foreground"]
"@ | Out-File -FilePath "Dockerfile" -Encoding utf8

# Create docker-compose.yml
@"
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8080:80"
    environment:
      - DB_HOST=database
      - DB_NAME=quickbill_db
      - DB_USER=quickbill_user
      - DB_PASS=quickbill_password
    depends_on:
      - database

  database:
    image: mysql:8.0
    ports:
      - "3307:3306"
    environment:
      MYSQL_ROOT_PASSWORD: rootpass123
      MYSQL_DATABASE: quickbill_db
      MYSQL_USER: quickbill_user
      MYSQL_PASSWORD: quickbill_password
    volumes:
      - mysql_data:/var/lib/mysql

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
      - "8081:80"
    environment:
      PMA_HOST: database
      PMA_USER: root
      PMA_PASSWORD: rootpass123

volumes:
  mysql_data:
"@ | Out-File -FilePath "docker-compose.yml" -Encoding utf8

# Create .env file
@"
DB_HOST=database
DB_NAME=quickbill_db
DB_USER=quickbill_user
DB_PASS=quickbill_password
APP_URL=http://localhost:8080
"@ | Out-File -FilePath ".env" -Encoding utf8

# Create start.bat
@"
@echo off
echo Starting QuickBill 305...
docker-compose up -d
echo.
echo Application: http://localhost:8080
echo phpMyAdmin: http://localhost:8081
echo.
pause
"@ | Out-File -FilePath "start.bat" -Encoding ascii

# Create stop.bat
@"
@echo off
echo Stopping QuickBill 305...
docker-compose down
echo Stopped.
pause
"@ | Out-File -FilePath "stop.bat" -Encoding ascii

Write-Host "✓ Created Dockerfile" -ForegroundColor Green
Write-Host "✓ Created docker-compose.yml" -ForegroundColor Green  
Write-Host "✓ Created .env" -ForegroundColor Green
Write-Host "✓ Created start.bat" -ForegroundColor Green
Write-Host "✓ Created stop.bat" -ForegroundColor Green

Write-Host ""
Write-Host "===============================================" -ForegroundColor Green
Write-Host " Setup Complete!" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Update your PHP database config to use environment variables" -ForegroundColor White
Write-Host "2. Run: docker-compose build" -ForegroundColor White  
Write-Host "3. Run: start.bat (or docker-compose up -d)" -ForegroundColor White
Write-Host ""
Write-Host "Your app will be at: http://localhost:8080" -ForegroundColor Cyan

$build = Read-Host "Build and start now? (Y/n)"
if ($build -eq '' -or $build -eq 'y' -or $build -eq 'Y') {
    Write-Host "Building..." -ForegroundColor Cyan
    docker-compose build
    Write-Host "Starting..." -ForegroundColor Cyan  
    docker-compose up -d
    Start-Sleep 10
    Write-Host ""
    Write-Host "🚀 QuickBill 305 is running!" -ForegroundColor Green
    Write-Host "App: http://localhost:8080" -ForegroundColor Cyan
    Write-Host "DB Admin: http://localhost:8081" -ForegroundColor Cyan
}