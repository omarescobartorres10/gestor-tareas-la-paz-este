@echo off
echo ==========================================
echo    INICIANDO SISTEMA DE GESTION DE TAREAS
echo ==========================================
echo.

:: 1. Verificar si XAMPP/MySQL está corriendo (opcional, recordatorio)
echo [1/3] Asegurate de que XAMPP (MySQL) este abierto y corriendo.
echo.

:: 2. Rebuild assets PRODUCTION
echo [2/3] Compilando assets para produccion...
call npm run build
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudieron compilar los assets
    pause
    exit /b 1
)

:: 3. Clear Laravel cache
echo [3/3] Limpiando cache de Laravel...
php artisan optimize:clear

:: 4. Iniciar Laravel Server en una nueva ventana
echo Arrancando Laravel...
start "Laravel Server - NO CERRAR" cmd /k "php artisan serve"

echo.
echo ==========================================
echo              ¡TODO LISTO!
echo ==========================================
echo La aplicacion esta corriendo en: http://localhost:8000
echo.
echo CREDENCIALES DE PRUEBA:
echo - Admin: admin@taskflow.com / admin123
echo - Usuario: cualquiera de la lista / password
echo.
echo NOTA: Cuando termines, simplemente cierra la ventana negra.
pause
