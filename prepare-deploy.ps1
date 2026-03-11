# ============================================
# SCRIPT DE PREPARACIÓN PRE-DEPLOY AWS
# TaxisApp Backend Laravel - VERSION WINDOWS
# ============================================

Write-Host "🚀 Iniciando preparación para Deploy AWS..." -ForegroundColor Green

# Verificar que estamos en el directorio correcto
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Error: No se encuentra el archivo artisan" -ForegroundColor Red
    Write-Host "Ejecuta este script desde la raíz del proyecto Laravel" -ForegroundColor Yellow
    exit 1
}

Write-Host "✓ Directorio correcto" -ForegroundColor Green

# ============================================
# 1. LIMPIAR ARCHIVOS SENSIBLES
# ============================================
Write-Host ""
Write-Host "📁 Limpiando archivos sensibles..." -ForegroundColor Cyan

# Debugbar
if (Test-Path "storage\debugbar") {
    Remove-Item "storage\debugbar\*.json" -Force -ErrorAction SilentlyContinue
    Write-Host "  ✓ Archivos debugbar eliminados" -ForegroundColor Green
}

# Logs antiguos
if (Test-Path "storage\logs") {
    Remove-Item "storage\logs\*.log" -Force -ErrorAction SilentlyContinue
    Write-Host "  ✓ Logs antiguos eliminados" -ForegroundColor Green
}

# Cache
php artisan cache:clear | Out-Null
php artisan config:clear | Out-Null
php artisan route:clear | Out-Null
php artisan view:clear | Out-Null
Write-Host "  ✓ Cache limpiado" -ForegroundColor Green

# ============================================
# 2. VERIFICAR CREDENCIALES EXPUESTAS
# ============================================
Write-Host ""
Write-Host "⚠️  Verificando credenciales expuestas..." -ForegroundColor Yellow

$exposedKeys = @(
    "AKIAJ4KGI",
    "UDlSAArj"
)

$foundExposed = $false
if (Test-Path ".env") {
    $envContent = Get-Content ".env" -Raw
    foreach ($key in $exposedKeys) {
        if ($envContent -match [regex]::Escape($key)) {
            $foundExposed = $true
            break
        }
    }
}

if ($foundExposed) {
    Write-Host "❌ CRÍTICO: Credenciales comprometidas detectadas en .env" -ForegroundColor Red
    Write-Host "   Debes cambiar todas las credenciales antes de deploy:" -ForegroundColor Yellow
    Write-Host "   - AWS Keys (IAM)" -ForegroundColor Yellow
    Write-Host "   - SES Keys" -ForegroundColor Yellow
    Write-Host "   - PayPal Keys" -ForegroundColor Yellow
    Write-Host "   Ver: AWS_DEPLOY_GUIDE.md - Fase 1.2" -ForegroundColor Yellow
} else {
    Write-Host "  ✓ No se detectaron credenciales comprometidas" -ForegroundColor Green
}

# ============================================
# 3. VERIFICAR .GITIGNORE
# ============================================
Write-Host ""
Write-Host "🔒 Verificando .gitignore..." -ForegroundColor Cyan

$gitignoreItems = @(".env", ".env.production", "storage/debugbar/", "storage/logs/", "vendor/", "node_modules/")
$missingItems = @()

if (Test-Path ".gitignore") {
    $gitignoreContent = Get-Content ".gitignore"
    foreach ($item in $gitignoreItems) {
        if ($gitignoreContent -notcontains $item) {
            $missingItems += $item
        }
    }
}

if ($missingItems.Count -eq 0) {
    Write-Host "  ✓ Todos los archivos críticos están en .gitignore" -ForegroundColor Green
} else {
    Write-Host "  ⚠️  Faltan items en .gitignore:" -ForegroundColor Yellow
    foreach ($item in $missingItems) {
        Write-Host "     - $item" -ForegroundColor Yellow
    }
    Write-Host ""
    $addGitignore = Read-Host "¿Agregar automáticamente? (s/n)"
    if ($addGitignore -eq "s") {
        foreach ($item in $missingItems) {
            Add-Content ".gitignore" -Value $item
        }
        Write-Host "  ✓ Items agregados a .gitignore" -ForegroundColor Green
    }
}

# ============================================
# 4. VERIFICAR VARIABLES DE PRODUCCIÓN
# ============================================
Write-Host ""
Write-Host "🔧 Verificando configuración de producción..." -ForegroundColor Cyan

if (-not (Test-Path ".env.production.example")) {
    Write-Host "  ⚠️  No existe .env.production.example" -ForegroundColor Yellow
} else {
    Write-Host "  ✓ Plantilla .env.production.example existe" -ForegroundColor Green
}

# Verificar variables críticas en .env actual
$criticalVars = @("APP_ENV", "APP_DEBUG", "APP_KEY", "DB_HOST", "AWS_KEY")
Write-Host ""
Write-Host "Variables actuales en .env:" -ForegroundColor Cyan

if (Test-Path ".env") {
    $envLines = Get-Content ".env"
    foreach ($var in $criticalVars) {
        $line = $envLines | Where-Object { $_ -match "^$var=" }
        if ($line) {
            $value = ($line -split "=", 2)[1]
            if ([string]::IsNullOrWhiteSpace($value)) {
                Write-Host "  ✗ $var : NO CONFIGURADO" -ForegroundColor Red
            } else {
                $maskedValue = $value.Substring(0, [Math]::Min(10, $value.Length)) + "..."
                if ($var -eq "APP_ENV" -and $value -ne "production") {
                    Write-Host "  ⚠ $var=$value (cambiar a 'production')" -ForegroundColor Yellow
                } elseif ($var -eq "APP_DEBUG" -and $value -eq "true") {
                    Write-Host "  ⚠ $var=$value (cambiar a 'false')" -ForegroundColor Yellow
                } else {
                    Write-Host "  ✓ $var=$maskedValue" -ForegroundColor Green
                }
            }
        } else {
            Write-Host "  ✗ $var : NO ENCONTRADO" -ForegroundColor Red
        }
    }
}

# ============================================
# 5. OPTIMIZAR DEPENDENCIAS
# ============================================
Write-Host ""
Write-Host "📦 Optimizando dependencias..." -ForegroundColor Cyan

# Verificar composer
$composerExists = Get-Command composer -ErrorAction SilentlyContinue
if (-not $composerExists) {
    Write-Host "  ✗ Composer no instalado" -ForegroundColor Red
    exit 1
}

Write-Host "  Ejecutando: composer install --no-dev --optimize-autoloader" -ForegroundColor Gray
$output = composer install --no-dev --optimize-autoloader 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Dependencias optimizadas" -ForegroundColor Green
} else {
    Write-Host "  ✗ Error al instalar dependencias" -ForegroundColor Red
    Write-Host $output -ForegroundColor Red
    exit 1
}

# ============================================
# 6. VERIFICAR EXTENSIONES PHP
# ============================================
Write-Host ""
Write-Host "🐘 Verificando extensiones PHP..." -ForegroundColor Cyan

$requiredExtensions = @("openssl", "pdo", "mbstring", "tokenizer", "xml", "ctype", "json", "bcmath", "gd")
$installedExtensions = php -m
$missingExtensions = @()

foreach ($ext in $requiredExtensions) {
    if ($installedExtensions -notcontains $ext) {
        $missingExtensions += $ext
    }
}

if ($missingExtensions.Count -eq 0) {
    Write-Host "  ✓ Todas las extensiones requeridas están instaladas" -ForegroundColor Green
} else {
    Write-Host "  ⚠️  Extensiones faltantes:" -ForegroundColor Yellow
    foreach ($ext in $missingExtensions) {
        Write-Host "     - php-$ext" -ForegroundColor Yellow
    }
}

# ============================================
# 7. CREAR CHECKLIST FINAL
# ============================================
Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "📋 CHECKLIST FINAL PRE-DEPLOY" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Antes de subir a AWS, verifica:" -ForegroundColor White
Write-Host ""
Write-Host "[ ] 1. Crear NUEVAS credenciales IAM en AWS Console" -ForegroundColor White
Write-Host "[ ] 2. REVOCAR credenciales antiguas expuestas" -ForegroundColor White
Write-Host "[ ] 3. Configurar .env.production con credenciales nuevas" -ForegroundColor White
Write-Host "[ ] 4. Crear RDS (Base de Datos MySQL)" -ForegroundColor White
Write-Host "[ ] 5. Crear S3 Bucket para archivos" -ForegroundColor White
Write-Host "[ ] 6. Crear ElastiCache (Redis)" -ForegroundColor White
Write-Host "[ ] 7. Configurar SES (correo electrónico)" -ForegroundColor White
Write-Host "[ ] 8. Lanzar instancia EC2" -ForegroundColor White
Write-Host "[ ] 9. Configurar Security Groups" -ForegroundColor White
Write-Host "[ ] 10. Instalar software en EC2 (PHP, Nginx, etc)" -ForegroundColor White
Write-Host ""
Write-Host "Ver guía completa: AWS_DEPLOY_GUIDE.md" -ForegroundColor Yellow
Write-Host ""

# ============================================
# 8. CREAR BUNDLE PARA DEPLOY
# ============================================
Write-Host ""
$createBundle = Read-Host "¿Crear bundle (ZIP) para deploy? (s/n)"

if ($createBundle -eq "s") {
    Write-Host "📦 Creando bundle de deployment..." -ForegroundColor Cyan
    
    $bundleName = "taxisapp-backend-$(Get-Date -Format 'yyyyMMdd-HHmmss').zip"
    $bundlePath = "..\$bundleName"
    
    # Crear archivo ZIP excluyendo archivos innecesarios
    $excludePatterns = @(
        ".git",
        "node_modules",
        "storage\debugbar\*",
        "storage\logs\*.log",
        ".env",
        "vendor"
    )
    
    # Comprimir archivos
    $compress = @{
        Path = Get-ChildItem -Path . -Exclude $excludePatterns
        DestinationPath = $bundlePath
        CompressionLevel = "Optimal"
    }
    
    try {
        Compress-Archive @compress -Force
        $bundleSize = (Get-Item $bundlePath).Length / 1MB
        Write-Host "  ✓ Bundle creado: $bundlePath ($([Math]::Round($bundleSize, 2)) MB)" -ForegroundColor Green
        Write-Host ""
        Write-Host "Para subir a EC2 puedes usar WinSCP, FileZilla o SCP desde WSL" -ForegroundColor Yellow
    } catch {
        Write-Host "  ✗ Error al crear bundle: $_" -ForegroundColor Red
    }
}

# ============================================
# RESUMEN FINAL
# ============================================
Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "✅ PREPARACIÓN COMPLETADA" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "Siguiente paso:" -ForegroundColor White
Write-Host "1. Lee AWS_DEPLOY_GUIDE.md FASE 2 en adelante" -ForegroundColor White
Write-Host "2. Configura servicios AWS (RDS, S3, EC2, etc)" -ForegroundColor White
Write-Host "3. Sube el código al servidor EC2" -ForegroundColor White
Write-Host "4. Configura .env en el servidor" -ForegroundColor White
Write-Host "5. Ejecuta migraciones" -ForegroundColor White
Write-Host ""
Write-Host "¡Buena suerte con el deploy! 🚀" -ForegroundColor Green
Write-Host ""

# Pausar para que el usuario vea los resultados
Write-Host "Presiona cualquier tecla para salir..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
