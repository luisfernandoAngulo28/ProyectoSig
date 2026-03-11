#!/bin/bash

# ============================================
# SCRIPT DE PREPARACIÓN PRE-DEPLOY AWS
# TaxisApp Backend Laravel
# ============================================

echo "🚀 Iniciando preparación para Deploy AWS..."

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: No se encuentra el archivo artisan${NC}"
    echo "Ejecuta este script desde la raíz del proyecto Laravel"
    exit 1
fi

echo -e "${GREEN}✓ Directorio correcto${NC}"

# ============================================
# 1. LIMPIAR ARCHIVOS SENSIBLES
# ============================================
echo ""
echo "📁 Limpiando archivos sensibles..."

# Debugbar
if [ -d "storage/debugbar" ]; then
    rm -rf storage/debugbar/*.json
    echo "  ✓ Archivos debugbar eliminados"
fi

# Logs antiguos
if [ -d "storage/logs" ]; then
    rm -rf storage/logs/*.log
    echo "  ✓ Logs antiguos eliminados"
fi

# Cache
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
echo "  ✓ Cache limpiado"

# ============================================
# 2. VERIFICAR .GITIGNORE
# ============================================
echo ""
echo "🔒 Verificando .gitignore..."

GITIGNORE_ITEMS=(".env" ".env.production" "storage/debugbar/" "storage/logs/" "vendor/" "node_modules/")
MISSING_ITEMS=()

for item in "${GITIGNORE_ITEMS[@]}"; do
    if ! grep -q "^${item}$" .gitignore; then
        MISSING_ITEMS+=("$item")
    fi
done

if [ ${#MISSING_ITEMS[@]} -eq 0 ]; then
    echo -e "${GREEN}  ✓ Todos los archivos críticos están en .gitignore${NC}"
else
    echo -e "${YELLOW}  ⚠️  Faltan items en .gitignore:${NC}"
    for item in "${MISSING_ITEMS[@]}"; do
        echo "     - $item"
    done
    echo ""
    read -p "¿Agregar automáticamente? (s/n): " add_gitignore
    if [ "$add_gitignore" = "s" ]; then
        for item in "${MISSING_ITEMS[@]}"; do
            echo "$item" >> .gitignore
        done
        echo -e "${GREEN}  ✓ Items agregados a .gitignore${NC}"
    fi
fi

# ============================================
# 3. VERIFICAR CREDENCIALES SENSIBLES EN .env
# ============================================
echo ""
echo "⚠️  Verificando credenciales expuestas..."

EXPOSED_KEYS=(
    "AKIAJ4KGI"
    "UDlSAArj"
    "AsO+7nhD"
)

FOUND_EXPOSED=false
for key in "${EXPOSED_KEYS[@]}"; do
    if grep -q "$key" .env 2>/dev/null; then
        FOUND_EXPOSED=true
        break
    fi
done

if [ "$FOUND_EXPOSED" = true ]; then
    echo -e "${RED}❌ CRÍTICO: Credenciales comprometidas detectadas en .env${NC}"
    echo "   Debes cambiar todas las credenciales antes de deploy:"
    echo "   - AWS Keys (IAM)"
    echo "   - SES Keys"
    echo "   - PayPal Keys"
    echo "   Ver: AWS_DEPLOY_GUIDE.md - Fase 1.2"
else
    echo -e "${GREEN}  ✓ No se detectaron credenciales comprometidas${NC}"
fi

# ============================================
# 4. VERIFICAR VARIABLES DE PRODUCCIÓN
# ============================================
echo ""
echo "🔧 Verificando configuración de producción..."

if [ ! -f ".env.production.example" ]; then
    echo -e "${YELLOW}  ⚠️  No existe .env.production.example${NC}"
else
    echo -e "${GREEN}  ✓ Plantilla .env.production.example existe${NC}"
fi

# Verificar variables críticas en .env actual
CRITICAL_VARS=("APP_ENV" "APP_DEBUG" "APP_KEY" "DB_HOST" "AWS_KEY")
echo ""
echo "Variables actuales en .env:"
for var in "${CRITICAL_VARS[@]}"; do
    if [ -f ".env" ]; then
        value=$(grep "^${var}=" .env | cut -d '=' -f2)
        if [ -z "$value" ]; then
            echo -e "  ${RED}✗ $var: NO CONFIGURADO${NC}"
        else
            # Ocultar parte del valor por seguridad
            masked_value="${value:0:10}..."
            if [ "$var" = "APP_ENV" ] && [ "$value" != "production" ]; then
                echo -e "  ${YELLOW}⚠ $var=$value (cambiar a 'production')${NC}"
            elif [ "$var" = "APP_DEBUG" ] && [ "$value" = "true" ]; then
                echo -e "  ${YELLOW}⚠ $var=$value (cambiar a 'false')${NC}"
            else
                echo "  ✓ $var=$masked_value"
            fi
        fi
    fi
done

# ============================================
# 5. OPTIMIZAR DEPENDENCIAS
# ============================================
echo ""
echo "📦 Optimizando dependencias..."

# Verificar composer
if ! command -v composer &> /dev/null; then
    echo -e "${RED}  ✗ Composer no instalado${NC}"
    exit 1
fi

echo "  Ejecutando: composer install --no-dev --optimize-autoloader"
composer install --no-dev --optimize-autoloader --quiet

if [ $? -eq 0 ]; then
    echo -e "${GREEN}  ✓ Dependencias optimizadas${NC}"
else
    echo -e "${RED}  ✗ Error al instalar dependencias${NC}"
    exit 1
fi

# ============================================
# 6. VERIFICAR EXTENSIONES PHP
# ============================================
echo ""
echo "🐘 Verificando extensiones PHP..."

REQUIRED_EXTENSIONS=("openssl" "pdo" "mbstring" "tokenizer" "xml" "ctype" "json" "bcmath" "gd")
MISSING_EXTENSIONS=()

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m | grep -qi "^${ext}$"; then
        MISSING_EXTENSIONS+=("$ext")
    fi
done

if [ ${#MISSING_EXTENSIONS[@]} -eq 0 ]; then
    echo -e "${GREEN}  ✓ Todas las extensiones requeridas están instaladas${NC}"
else
    echo -e "${YELLOW}  ⚠️  Extensiones faltantes:${NC}"
    for ext in "${MISSING_EXTENSIONS[@]}"; do
        echo "     - php-$ext"
    done
    echo "  Instalar con: sudo apt install php8.1-{lista}"
fi

# ============================================
# 7. CREAR CHECKLIST FINAL
# ============================================
echo ""
echo "============================================"
echo "📋 CHECKLIST FINAL PRE-DEPLOY"
echo "============================================"
echo ""
echo "Antes de subir a AWS, verifica:"
echo ""
echo "[ ] 1. Crear NUEVAS credenciales IAM en AWS Console"
echo "[ ] 2. REVOCAR credenciales antiguas expuestas"
echo "[ ] 3. Configurar .env.production con credenciales nuevas"
echo "[ ] 4. Crear RDS (Base de Datos MySQL)"
echo "[ ] 5. Crear S3 Bucket para archivos"
echo "[ ] 6. Crear ElastiCache (Redis)"
echo "[ ] 7. Configurar SES (correo electrónico)"
echo "[ ] 8. Lanzar instancia EC2"
echo "[ ] 9. Configurar Security Groups"
echo "[ ] 10. Instalar software en EC2 (PHP, Nginx, etc)"
echo ""
echo "Ver guía completa: AWS_DEPLOY_GUIDE.md"
echo ""

# ============================================
# 8. CREAR BUNDLE PARA DEPLOY
# ============================================
echo ""
read -p "¿Crear bundle (tar.gz) para deploy? (s/n): " create_bundle

if [ "$create_bundle" = "s" ]; then
    echo "📦 Creando bundle de deployment..."
    
    BUNDLE_NAME="taxisapp-backend-$(date +%Y%m%d-%H%M%S).tar.gz"
    
    # Excluir archivos innecesarios
    tar -czf "../$BUNDLE_NAME" \
        --exclude='.git' \
        --exclude='node_modules' \
        --exclude='storage/debugbar/*' \
        --exclude='storage/logs/*.log' \
        --exclude='.env' \
        --exclude='vendor' \
        .
    
    if [ $? -eq 0 ]; then
        BUNDLE_SIZE=$(du -h "../$BUNDLE_NAME" | cut -f1)
        echo -e "${GREEN}  ✓ Bundle creado: ../$BUNDLE_NAME ($BUNDLE_SIZE)${NC}"
        echo ""
        echo "Para subir a EC2:"
        echo "  scp -i taxisapp-prod-key.pem ../$BUNDLE_NAME ubuntu@[IP-EC2]:~/"
    else
        echo -e "${RED}  ✗ Error al crear bundle${NC}"
    fi
fi

# ============================================
# RESUMEN FINAL
# ============================================
echo ""
echo "============================================"
echo "✅ PREPARACIÓN COMPLETADA"
echo "============================================"
echo ""
echo "Siguiente paso:"
echo "1. Lee AWS_DEPLOY_GUIDE.md FASE 2 en adelante"
echo "2. Configura servicios AWS (RDS, S3, EC2, etc)"
echo "3. Sube el código al servidor EC2"
echo "4. Configura .env en el servidor"
echo "5. Ejecuta migraciones"
echo ""
echo -e "${GREEN}¡Buena suerte con el deploy! 🚀${NC}"
echo ""
