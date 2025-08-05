@echo off
echo Starting QuickBill 305...
docker-compose up -d
echo.
echo Waiting for services to start...
timeout /t 15 /nobreak > nul
echo.
echo === QuickBill 305 is now running! ===
echo Application: http://localhost:8080
echo phpMyAdmin: http://localhost:8081
echo Database: localhost:3307
echo.
echo Default login:
echo Username: admin
echo Password: admin123
echo.
echo To view logs: docker-compose logs -f
echo To stop: docker-compose down
pause
