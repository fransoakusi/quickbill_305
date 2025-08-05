@echo off
echo ===============================================
echo  QuickBill 305 Docker Setup for Windows
echo ===============================================

REM Check if Docker is available
docker --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Docker not found!
    echo Please install Docker Desktop for Windows from:
    echo https://docs.docker.com/desktop/install/windows-install/
    pause
    exit /b 1
)

echo Docker is available!
echo Current directory: %CD%
echo.

echo Creating Dockerfile...
(
echo FROM php:8.2-apache
echo WORKDIR /var/www/html
echo.
echo # Install system dependencies
echo RUN apt-get update ^&^& apt-get install -y \
echo     git \
echo     curl \
echo     libpng-dev \
echo     libonig-dev \
echo     libxml2-dev \
echo     libzip-dev \
echo     zip \
echo     unzip \
echo     libfreetype6-dev \
echo     libjpeg62-turbo-dev \
echo     ^&^& rm -rf /var/lib/apt/lists/*
echo.
echo # Install PHP extensions
echo RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
echo     ^&^& docker-php-ext-install -j$^(nproc^) \
echo     pdo_mysql \
echo     mysqli \
echo     mbstring \
echo     exif \
echo     pcntl \
echo     bcmath \
echo     gd \
echo     zip
echo.
echo # Enable Apache modules
echo RUN a2enmod rewrite headers
echo.
echo # Copy application files
echo COPY . /var/www/html/
echo.
echo # Create directories and set permissions
echo RUN mkdir -p uploads/businesses uploads/properties uploads/receipts uploads/bills uploads/imports uploads/temp \
echo     ^&^& mkdir -p storage/logs storage/backups storage/exports storage/cache \
echo     ^&^& chown -R www-data:www-data /var/www/html \
echo     ^&^& chmod -R 755 /var/www/html \
echo     ^&^& chmod -R 777 uploads storage
echo.
echo EXPOSE 80
echo CMD ["apache2-foreground"]
) > Dockerfile

echo Creating docker-compose.yml...
(
echo version: '3.8'
echo.
echo services:
echo   quickbill-app:
echo     build: .
echo     container_name: quickbill_app
echo     restart: unless-stopped
echo     ports:
echo       - "8080:80"
echo     volumes:
echo       - .:/var/www/html
echo       - uploads_data:/var/www/html/uploads
echo       - storage_data:/var/www/html/storage
echo     environment:
echo       - DB_HOST=quickbill-db
echo       - DB_PORT=3306
echo       - DB_NAME=quickbill_db
echo       - DB_USER=quickbill_user
echo       - DB_PASS=quickbill_password
echo     depends_on:
echo       - quickbill-db
echo     networks:
echo       - quickbill-network
echo.
echo   quickbill-db:
echo     image: mysql:8.0
echo     container_name: quickbill_mysql
echo     restart: unless-stopped
echo     ports:
echo       - "3307:3306"
echo     volumes:
echo       - mysql_data:/var/lib/mysql
echo       - ./database/init.sql:/docker-entrypoint-initdb.d/init.sql
echo     environment:
echo       MYSQL_ROOT_PASSWORD: root_password_123
echo       MYSQL_DATABASE: quickbill_db
echo       MYSQL_USER: quickbill_user
echo       MYSQL_PASSWORD: quickbill_password
echo     command: --default-authentication-plugin=mysql_native_password
echo     networks:
echo       - quickbill-network
echo.
echo   quickbill-phpmyadmin:
echo     image: phpmyadmin/phpmyadmin:latest
echo     container_name: quickbill_phpmyadmin
echo     restart: unless-stopped
echo     ports:
echo       - "8081:80"
echo     environment:
echo       PMA_HOST: quickbill-db
echo       PMA_PORT: 3306
echo       PMA_USER: root
echo       PMA_PASSWORD: root_password_123
echo     depends_on:
echo       - quickbill-db
echo     networks:
echo       - quickbill-network
echo.
echo volumes:
echo   mysql_data:
echo     driver: local
echo   uploads_data:
echo     driver: local
echo   storage_data:
echo     driver: local
echo.
echo networks:
echo   quickbill-network:
echo     driver: bridge
) > docker-compose.yml

echo Creating .env file...
(
echo # Database Configuration
echo DB_HOST=quickbill-db
echo DB_PORT=3306
echo DB_NAME=quickbill_db
echo DB_USER=quickbill_user
echo DB_PASS=quickbill_password
echo.
echo # Application Configuration
echo APP_NAME="QuickBill 305"
echo APP_ENV=development
echo APP_DEBUG=true
echo APP_URL=http://localhost:8080
echo.
echo # API Keys ^(Update with your actual keys^)
echo GOOGLE_MAPS_API_KEY=your_google_maps_api_key
echo SMS_API_KEY=your_sms_api_key
echo PAYMENT_GATEWAY_KEY=your_payment_gateway_key
) > .env

echo Creating .dockerignore...
(
echo .git
echo .gitignore
echo README.md
echo docker-compose.yml
echo Dockerfile
echo .env.example
echo node_modules
echo .DS_Store
echo .vscode
echo .idea
echo *.log
echo vendor
echo composer.lock
echo setup-docker.bat
) > .dockerignore

echo Creating directories...
if not exist "database" mkdir database
if not exist "docker" mkdir docker

echo Creating database initialization...
(
echo -- QuickBill 305 Database Initialization
echo CREATE DATABASE IF NOT EXISTS quickbill_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
echo USE quickbill_db;
echo.
echo -- Create users table
echo CREATE TABLE IF NOT EXISTS users ^(
echo     id INT AUTO_INCREMENT PRIMARY KEY,
echo     username VARCHAR^(50^) UNIQUE NOT NULL,
echo     email VARCHAR^(100^) UNIQUE NOT NULL,
echo     password_hash VARCHAR^(255^) NOT NULL,
echo     role ENUM^('admin', 'officer', 'revenue_officer', 'data_collector'^) NOT NULL,
echo     status ENUM^('active', 'inactive', 'suspended'^) DEFAULT 'active',
echo     first_name VARCHAR^(50^),
echo     last_name VARCHAR^(50^),
echo     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
echo     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
echo ^);
echo.
echo -- Insert default admin user ^(password: admin123^)
echo INSERT IGNORE INTO users ^(username, email, password_hash, role, first_name, last_name^) VALUES 
echo ^('admin', 'admin@quickbill305.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System', 'Administrator'^);
echo.
echo -- Create system_settings table
echo CREATE TABLE IF NOT EXISTS system_settings ^(
echo     id INT AUTO_INCREMENT PRIMARY KEY,
echo     setting_key VARCHAR^(100^) UNIQUE NOT NULL,
echo     setting_value TEXT,
echo     description TEXT,
echo     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
echo     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
echo ^);
echo.
echo -- Insert default settings
echo INSERT IGNORE INTO system_settings ^(setting_key, setting_value, description^) VALUES
echo ^('app_name', 'QuickBill 305', 'Application name'^),
echo ^('currency', 'GHS', 'Default currency'^),
echo ^('timezone', 'Africa/Accra', 'Default timezone'^),
echo ^('billing_year', '2025', 'Current billing year'^);
) > database\init.sql

echo Creating Apache configuration...
(
echo ^<VirtualHost *:80^>
echo     ServerName localhost
echo     DocumentRoot /var/www/html
echo     
echo     ^<Directory /var/www/html^>
echo         AllowOverride All
echo         Require all granted
echo         Options -Indexes +FollowSymLinks
echo     ^</Directory^>
echo.
echo     Header always set X-Content-Type-Options nosniff
echo     Header always set X-Frame-Options DENY
echo     Header always set X-XSS-Protection "1; mode=block"
echo.
echo     ErrorLog ${APACHE_LOG_DIR}/error.log
echo     CustomLog ${APACHE_LOG_DIR}/access.log combined
echo ^</VirtualHost^>
) > docker\apache.conf

echo Creating helper batch files...

REM Create build.bat
(
echo @echo off
echo echo Building QuickBill 305 Docker containers...
echo docker-compose build --no-cache
echo echo Build completed!
echo pause
) > build.bat

REM Create start.bat
(
echo @echo off
echo echo Starting QuickBill 305...
echo docker-compose up -d
echo echo.
echo echo Waiting for services to start...
echo timeout /t 15 /nobreak ^> nul
echo echo.
echo echo === QuickBill 305 is now running! ===
echo echo Application: http://localhost:8080
echo echo phpMyAdmin: http://localhost:8081
echo echo Database: localhost:3307
echo echo.
echo echo Default login:
echo echo Username: admin
echo echo Password: admin123
echo echo.
echo echo To view logs: docker-compose logs -f
echo echo To stop: docker-compose down
echo pause
) > start.bat

REM Create stop.bat
(
echo @echo off
echo echo Stopping QuickBill 305...
echo docker-compose down
echo echo All services stopped.
echo pause
) > stop.bat

REM Create logs.bat
(
echo @echo off
echo echo Viewing QuickBill 305 logs...
echo docker-compose logs -f
) > logs.bat

echo.
echo ===============================================
echo  Docker Setup Complete!
echo ===============================================
echo.
echo Files created:
echo   - Dockerfile
echo   - docker-compose.yml
echo   - .env
echo   - .dockerignore
echo   - database\init.sql
echo   - docker\apache.conf
echo   - build.bat
echo   - start.bat
echo   - stop.bat
echo   - logs.bat
echo.
echo IMPORTANT: Update your PHP database config files to use:
echo   $_ENV['DB_HOST'] instead of 'localhost'
echo   $_ENV['DB_USER'] instead of 'root'
echo   $_ENV['DB_PASS'] instead of your current password
echo   $_ENV['DB_NAME'] instead of your current database name
echo.

set /p build="Build and start Docker containers now? (Y/n): "
if /i "%build%"=="n" goto end
if /i "%build%"=="no" goto end

echo.
echo Building containers...
docker-compose build

if errorlevel 1 (
    echo Build failed! Check the error messages above.
    pause
    goto end
)

echo.
echo Starting containers...
docker-compose up -d

if errorlevel 1 (
    echo Start failed! Check the error messages above.
    pause
    goto end
)

echo.
echo Waiting for startup...
timeout /t 15 /nobreak >nul

echo.
echo ===============================================
echo  SUCCESS! QuickBill 305 is running!
echo ===============================================
echo.
echo Access your application:
echo   Application: http://localhost:8080
echo   phpMyAdmin:  http://localhost:8081
echo   Database:    localhost:3307
echo.
echo Default admin login:
echo   Username: admin
echo   Password: admin123
echo.
echo Available commands:
echo   start.bat - Start the application
echo   stop.bat  - Stop the application
echo   logs.bat  - View logs
echo.

:end
pause