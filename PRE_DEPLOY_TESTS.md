# 🔍 VERIFICACIÓN PRE-DEPLOY - Comandos de Prueba Local

## ⚙️ Verificar Estado Actual del Backend

### 1. Verificar versión de PHP y extensiones
```bash
php -v
php -m | grep -E "openssl|pdo|mbstring|tokenizer|xml|ctype|json|bcmath|gd|redis"
```

### 2. Verificar dependencias de Composer
```bash
composer validate
composer diagnose
```

### 3. Verificar configuración de Laravel
```bash
# Ver todas las configuraciones
php artisan config:show

# Verificar conexión a BD
php artisan migrate:status
```

### 4. Probar conectividad a servicios externos
```bash
# Test de AWS S3 (desde Tinker)
php artisan tinker
>>> Storage::disk('s3')->exists('test.txt');
>>> exit

# Test de envío de correo (opcional)
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('tu@email.com')->subject('Test'); });
>>> exit
```

### 5. Verificar rutas y endpoints
```bash
# Listar todas las rutas API
php artisan route:list | grep api

# Rutas críticas que deben existir:
# - POST /api-auth/send-otp-phone
# - POST /api-auth/verify-otp-phone
# - POST /api-auth/register-with-phone
# - POST /api-auth/send-otp-phone-driver
# - POST /api-auth/update-facial-photo
```

### 6. Verificar Jobs y Queues
```bash
# Ver configuración de queue
php artisan queue:failed

# Procesar un job de prueba
php artisan queue:work --once
```

---

## 🧪 Tests de Integración Manual

### Test 1: Registro de Usuario (API)
```bash
# Enviar OTP a teléfono (usa tu número real)
curl -X POST http://localhost:8000/api-auth/send-otp-phone \
  -H "Content-Type: application/json" \
  -d '{"phone": "+59170000000"}'

# Respuesta esperada:
# {"success": true, "message": "OTP enviado"}

# Verificar OTP en BD (solo en desarrollo)
# SELECT * FROM otps WHERE phone = '+59170000000' ORDER BY id DESC LIMIT 1;
```

### Test 2: Subir Foto a S3
```bash
php artisan tinker

# Crear archivo de prueba
>>> $content = base64_encode(file_get_contents('public/test-image.jpg'));
>>> Storage::disk('s3')->put('test/prueba.jpg', base64_decode($content));
>>> Storage::disk('s3')->url('test/prueba.jpg');

# Debe devolver URL pública de S3
# https://taxisapp-production-files.s3.amazonaws.com/test/prueba.jpg
```

### Test 3: Verificar Middleware CORS
```bash
# Hacer request desde otra origen
curl -X OPTIONS http://localhost:8000/api-auth/send-otp-phone \
  -H "Origin: http://localhost:3000" \
  -H "Access-Control-Request-Method: POST" \
  -H "Access-Control-Request-Headers: Content-Type" \
  -v

# Debe incluir headers:
# Access-Control-Allow-Origin: *
# Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
```

### Test 4: Autenticación JWT
```bash
# Login (ajusta credenciales)
curl -X POST http://localhost:8000/api-auth/authenticate \
  -H "Content-Type: application/json" \
  -d '{"email": "test@test.com", "password": "password"}'

# Respuesta esperada:
# {"token": "eyJ0eXAiOiJKV1QiLCJhbGc..."}

# Test con token
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGc..."
curl -X GET http://localhost:8000/api/check-login \
  -H "Authorization: Bearer $TOKEN"
```

---

## 🔒 Verificar Seguridad

### 1. Variables de entorno sensibles NO están en Git
```bash
# Verificar .env no está trackeado
git status | grep .env

# Debe mostrar: nothing to commit (o no mencionar .env)

# Verificar .gitignore
cat .gitignore | grep -E "\.env|debugbar|logs"

# Debe incluir:
# .env
# .env.production
# storage/debugbar/
# storage/logs/
```

### 2. Buscar credenciales hardcodeadas en código
```bash
# Buscar en todo el código
grep -r "AKIAJ4KGIJVDP5BWQRPQ" app/ config/ database/
grep -r "UDlSAArjlowsyeMqNFoZjoRx" app/ config/ database/
grep -r "password.*=.*['\"][^'\"]*['\"]" app/ | grep -v "env("

# NO debe encontrar nada
```

### 3. Verificar archivos debugbar se pueden borrar
```bash
ls -lh storage/debugbar/
# Si hay archivos .json, borrarlos:
rm -rf storage/debugbar/*.json
```

### 4. Verificar configuración de producción
```bash
# Ver variables que se usarán en producción
grep -E "^(APP_ENV|APP_DEBUG|APP_KEY|DB_|AWS_|MAIL_)" .env.production.example

# Todas deben estar definidas y correctas
```

---

## 📊 Estado de Migraciones

### Verificar migraciones pendientes
```bash
# Ver estado
php artisan migrate:status

# Debe mostrar todas con estado "Ran"
# Si hay "Pending", ejecutar:
# php artisan migrate --step
```

### Backup de BD antes de deploy
```bash
# Exportar BD completa
php artisan db:seed --class=BackupSeeder  # Si tienes uno
# O manual:
mysqldump -u root taxisapp > backup_local_$(date +%Y%m%d).sql
```

---

## 🧹 Limpieza Pre-Deploy

### Ejecutar limpieza completa
```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Limpiar logs y debugbar
rm -rf storage/logs/*.log
rm -rf storage/debugbar/*.json

# Limpiar archivos temporales
rm -rf public/tmp/*
rm -rf storage/app/tmp/*
```

### Optimizar para producción
```bash
# Instalar solo dependencias de producción
composer install --no-dev --optimize-autoloader

# Verificar tamaño del proyecto
du -sh .
# Debe ser < 100MB sin vendor/
```

---

## ✅ Checklist Final de Verificación

Marca cada ítem después de verificar:

### Configuración
- [ ] `APP_ENV=production` en .env.production
- [ ] `APP_DEBUG=false` en .env.production
- [ ] `APP_KEY` generado con `php artisan key:generate`
- [ ] `JWT_SECRET` generado con `php artisan jwt:secret`
- [ ] `DB_PASSWORD` NO está vacío
- [ ] `AWS_KEY` y `AWS_SECRET` son NUEVOS (no los expuestos)

### Seguridad
- [ ] `.env` está en `.gitignore`
- [ ] `storage/debugbar/` eliminado
- [ ] `storage/logs/` limpio
- [ ] No hay credenciales hardcodeadas en código
- [ ] Credentials antiguas REVOCADAS en AWS

### Funcionalidad
- [ ] `php artisan migrate:status` → Todo "Ran"
- [ ] `php artisan route:list` muestra todos los endpoints
- [ ] Test de S3 funciona localmente
- [ ] Test de correo funciona (opcional)
- [ ] CORS middleware configurado

### Archivos
- [ ] `composer.lock` actualizado
- [ ] No hay archivos `.DS_Store` o `Thumbs.db`
- [ ] `vendor/` y `node_modules/` en .gitignore
- [ ] README.md actualizado con info de deploy

### Backup
- [ ] Backup de BD local creado
- [ ] Código subido a Git (branch main/master)
- [ ] Tag de versión creado: `git tag v1.0.0-production`

---

## 🚀 Comandos Post-Deploy en EC2

Después de subir el código a EC2, ejecutar:

```bash
# 1. Permisos
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 2. Dependencias
composer install --no-dev --optimize-autoloader

# 3. Configuración
php artisan config:cache
php artisan route:cache
php artisan optimize

# 4. Migraciones
php artisan migrate --force

# 5. Seeds (si es primera vez)
# php artisan db:seed --force

# 6. Verificar
php artisan --version
php artisan route:list | head -20
```

---

## 🆘 Troubleshooting Común

### Error: "Class not found"
```bash
composer dump-autoload
php artisan clear-compiled
php artisan optimize
```

### Error: "No connection could be made"
```bash
# Verificar BD
php artisan tinker
>>> DB::connection()->getPdo();
```

### Error: "Storage not configured"
```bash
# Verificar config
php artisan config:show filesystems
```

### Error: "Token mismatch"
```bash
# Limpiar sesiones y cache
php artisan cache:clear
php artisan config:clear
rm -rf bootstrap/cache/*.php
```

---

**Después de completar todas las verificaciones**, estás listo para seguir con `AWS_DEPLOY_GUIDE.md` FASE 2. 🚀
