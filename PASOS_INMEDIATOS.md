# 🚀 PASOS INMEDIATOS - Deployment TaxisApp a AWS

## ✅ COMPLETADO (Ahora mismo)
- ✓ Código subido a GitHub limpio
- ✓ Middleware CORS implementado
- ✓ .gitignore actualizado
- ✓ Archivos debugbar eliminados (2,512 archivos)
- ✓ Documentación completa creada

---

## 🚨 PASO 1: REVOCAR CREDENCIALES (URGENTE - 10 min)

### A. AWS Access Keys (IAM Console)
1. Ir a: https://console.aws.amazon.com/iam/
2. Click en **Users** → Buscar usuario con keys:
   - `AKIAJ4KGIJVDP5BWQRPQ`
   - `AKIAJCROYXWWANFVUTXA`
   - `AKIAJNDDEZJLWVXMI5VA`
3. Para cada usuario:
   - Click en el usuario → Tab **Security credentials**
   - Encontrar las Access Keys arriba
   - Click **Actions** → **Deactivate** (primero)
   - Luego **Delete** (eliminar permanentemente)

### B. PayPal (si usas producción)
1. Ir a: https://developer.paypal.com/dashboard/
2. Click en tu app → **Credentials**
3. Regenerar **Secret Key**
4. Guardar la nueva (NO commitear)

### C. Verificar que funcionó
```powershell
# En tu terminal, verificar que .env NO tiene las keys antiguas:
cd "c:\Desarrollamelo AppDree\taxisapp-backend-laravel-fempile-test"
Select-String -Path .env -Pattern "AKIAJ4KGIJVDP5BWQRPQ"
# Si encuentra algo, cambiar manualmente en .env
```

---

## 🔑 PASO 2: CREAR NUEVAS CREDENCIALES AWS (15 min)

### A. Crear nuevo usuario IAM
1. AWS Console → IAM → **Users** → **Create user**
   ```
   Nombre: taxisapp-backend-prod
   Access type: ☑ Programmatic access
   ```

2. **Attach policies** (permisos):
   - ☑ `AmazonS3FullAccess`
   - ☑ `AmazonSESFullAccess`
   - ☑ `CloudWatchLogsFullAccess`

3. **Review** → **Create user**

4. **¡IMPORTANTE!** En la pantalla final:
   - Copiar **Access Key ID**
   - Copiar **Secret Access Key**
   - Click **Download .csv** (backup)
   - ⚠️ **No podrás ver el Secret otra vez**

### B. Crear credenciales SES SMTP (para emails)
1. AWS Console → Amazon SES → **SMTP Settings**
2. Click **Create My SMTP Credentials**
3. Nombre: `taxisapp-backend-prod-smtp`
4. **Create**
5. Copiar:
   - SMTP Username
   - SMTP Password

---

## ⚙️ PASO 3: ACTUALIZAR .ENV LOCAL (5 min)

Actualiza tu archivo `.env` local con las NUEVAS credenciales:

```powershell
# Abrir el archivo .env
notepad .env
```

Reemplazar estas líneas con tus nuevas credenciales:

```env
# AWS S3 (nuevas keys de IAM)
AWS_KEY=<Tu_Nueva_Access_Key_ID>
AWS_SECRET=<Tu_Nuevo_Secret_Access_Key>

# AWS SES Email (nuevas SMTP keys)
MAIL_USERNAME=<Tu_Nuevo_SMTP_Username>
MAIL_PASSWORD=<Tu_Nuevo_SMTP_Password>

# PayPal (nueva secret)
PAYPAL_CLIENT_SECRET=<Tu_Nuevo_PayPal_Secret>
```

**Guardar y cerrar.**

---

## 🧪 PASO 4: PROBAR LOCALMENTE (10 min)

Antes de subir a AWS, verifica que todo funcione:

```powershell
# 1. Instalar dependencias
cd "c:\Desarrollamelo AppDree\taxisapp-backend-laravel-fempile-test"
composer install

# 2. Verificar que .env tiene las nuevas keys
php artisan config:clear
php artisan cache:clear

# 3. Probar la aplicación local
php artisan serve

# En otra terminal, probar API:
curl http://localhost:8000/api/v1/health
# Debe responder con status 200
```

---

## 🌩️ PASO 5: CONFIGURAR SERVICIOS AWS (60-90 min)

### A. Crear RDS (Base de Datos MySQL)

1. AWS Console → RDS → **Create database**
   ```
   Engine: MySQL 8.0
   Templates: Free tier (o Production si necesitas más)
   DB instance identifier: taxisapp-prod
   Master username: admin
   Master password: <Generar contraseña segura>
   
   Instance: db.t3.small (o db.t3.micro para testing)
   Storage: 20 GB SSD
   VPC: Default (o tu VPC)
   Public access: Yes (para configuración inicial)
   Security group: Crear nuevo "taxisapp-rds-sg"
   ```

2. **Security Group** para RDS:
   - Inbound rules:
     - MySQL/Aurora (3306) desde tu IP actual (para testing)
     - MySQL/Aurora (3306) desde Security Group de EC2 (después)

3. **Copiar el endpoint** (ejemplo: `taxisapp-prod.xxxxx.us-east-1.rds.amazonaws.com`)

### B. Crear S3 Bucket (Almacenamiento)

1. AWS Console → S3 → **Create bucket**
   ```
   Bucket name: taxisapp-production
   Region: us-east-1
   Block all public access: OFF (para imágenes públicas)
   Bucket Versioning: Disable
   ```

2. **CORS Configuration** (importante para imágenes):
   - Click en tu bucket → **Permissions** → **CORS**
   - Agregar:
   ```json
   [
     {
       "AllowedHeaders": ["*"],
       "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
       "AllowedOrigins": ["*"],
       "ExposeHeaders": []
     }
   ]
   ```

3. **Bucket Policy** (para imágenes públicas):
   ```json
   {
     "Version": "2012-10-17",
     "Statement": [
       {
         "Sid": "PublicReadGetObject",
         "Effect": "Allow",
         "Principal": "*",
         "Action": "s3:GetObject",
         "Resource": "arn:aws:s3:::taxisapp-production/*"
       }
     ]
   }
   ```

### C. (Opcional) ElastiCache Redis

1. AWS Console → ElastiCache → **Create**
   ```
   Engine: Redis
   Name: taxisapp-cache
   Node type: cache.t3.micro
   Number of replicas: 0 (para empezar)
   ```

2. Copiar el **Primary Endpoint**

### D. SES - Verificar dominio/email

1. AWS Console → SES → **Verified identities**
2. **Create identity** → **Email address**
3. Agregar: `notifications@tudominio.com` (o tu email)
4. Verificar el email que recibes

---

## 🖥️ PASO 6: CREAR EC2 INSTANCE (30-45 min)

### A. Lanzar instancia

1. EC2 Console → **Launch instance**
   ```
   Name: taxisapp-backend-prod
   AMI: Ubuntu Server 22.04 LTS
   Instance type: t3.medium (o t3.small para empezar)
   Key pair: Crear nuevo "taxisapp-key" (descargar .pem)
   
   Network:
     - VPC: Default
     - Subnet: Default
     - Auto-assign public IP: Enable
   
   Security group: taxisapp-web-sg
     - SSH (22): Tu IP
     - HTTP (80): 0.0.0.0/0
     - HTTPS (443): 0.0.0.0/0
     - Custom TCP (8000): 0.0.0.0/0 (para testing)
   
   Storage: 20 GB gp3
   ```

2. **Launch** → Copiar la **Public IP**

### B. Conectar por SSH

```powershell
# Cambiar permisos de la key (Linux/Mac)
chmod 400 taxisapp-key.pem

# Conectar (reemplazar con tu IP)
ssh -i "taxisapp-key.pem" ubuntu@<Tu-EC2-Public-IP>
```

Para Windows (PowerShell):
```powershell
ssh -i "taxisapp-key.pem" ubuntu@<Tu-EC2-Public-IP>
```

### C. Instalar software necesario

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar PHP 8.1 y extensiones
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-xml \
  php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd \
  php8.1-bcmath php8.1-redis php8.1-intl

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Nginx
sudo apt install -y nginx

# Instalar MySQL Client
sudo apt install -y mysql-client

# Verificar instalaciones
php -v
composer -v
nginx -v
```

---

## 📦 PASO 7: SUBIR CÓDIGO A EC2 (20 min)

### A. Opción 1: Git Clone (Recomendado)

```bash
# En tu EC2
cd /var/www
sudo git clone https://github.com/luisfernandoAngulo28/ProyectoSig.git taxisapp
cd taxisapp

# Instalar dependencias
sudo composer install --no-dev --optimize-autoloader

# Crear .env desde plantilla
sudo cp .env.production.example .env
sudo nano .env
# Pegar tus credenciales aquí
```

### B. Configurar .env en EC2

```bash
sudo nano /var/www/taxisapp/.env
```

Actualizar estas variables con los valores de AWS:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://<Tu-EC2-Public-IP>

# RDS Database
DB_HOST=<Tu-RDS-Endpoint>
DB_DATABASE=taxisapp
DB_USERNAME=admin
DB_PASSWORD=<Tu-RDS-Password>

# S3
AWS_KEY=<Tu-IAM-Key>
AWS_SECRET=<Tu-IAM-Secret>
AWS_BUCKET=taxisapp-production

# Redis (si configuraste ElastiCache)
REDIS_HOST=<Tu-ElastiCache-Endpoint>

# SES Email
MAIL_USERNAME=<Tu-SES-SMTP-Username>
MAIL_PASSWORD=<Tu-SES-SMTP-Password>
```

### C. Configurar permisos

```bash
cd /var/www/taxisapp

# Generar app key
sudo php artisan key:generate
sudo php artisan jwt:secret

# Permisos de storage
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Optimizar
sudo php artisan config:cache
sudo php artisan route:cache
```

### D. Migrar base de datos

```bash
# Conectar a RDS para crear BD
mysql -h <Tu-RDS-Endpoint> -u admin -p

# En MySQL:
CREATE DATABASE taxisapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Ejecutar migraciones
cd /var/www/taxisapp
sudo php artisan migrate --force

# (Opcional) Seeders
sudo php artisan db:seed --force
```

---

## 🌐 PASO 8: CONFIGURAR NGINX (15 min)

```bash
sudo nano /etc/nginx/sites-available/taxisapp
```

Pegar esta configuración:

```nginx
server {
    listen 80;
    server_name <Tu-IP-o-Dominio>;
    root /var/www/taxisapp/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Activar sitio:

```bash
sudo ln -s /etc/nginx/sites-available/taxisapp /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
```

---

## ✅ PASO 9: VERIFICAR QUE FUNCIONA (10 min)

### A. Probar API

```bash
# Desde tu máquina local
curl http://<Tu-EC2-IP>/api/v1

# Debe responder con JSON (API info)
```

### B. Probar endpoints principales

```bash
# Health check
curl http://<Tu-EC2-IP>/api/v1/health

# Register (test)
curl -X POST http://<Tu-EC2-IP>/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"phone":"59168063825","name":"Test User"}'
```

### C. Verificar CORS

```bash
curl -I http://<Tu-EC2-IP>/api/v1 \
  -H "Origin: http://localhost:3000"

# Debe incluir headers:
# Access-Control-Allow-Origin: *
```

---

## 🔒 PASO 10: SSL/HTTPS (Opcional - 30 min)

Si tienes un dominio (ej: `api.tuapp.com`):

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtener certificado
sudo certbot --nginx -d api.tuapp.com

# Renovación automática ya está configurada
```

---

## 📱 PASO 11: ACTUALIZAR APPS FLUTTER (15 min)

### A. Actualizar URLs en apps móviles

**Driver App:**
```dart
// lib/config/environment.dart (o similar)
static const String baseUrl = 'http://<Tu-EC2-IP>/api/v1';
```

**Passenger App:**
```dart
// lib/config/environment.dart
static const String baseUrl = 'http://<Tu-EC2-IP>/api/v1';
```

### B. Rebuild y probar

```bash
cd taxisapp-driver-flutter-fempile
flutter clean
flutter pub get
flutter run

# Probar login/registro con el backend en AWS
```

---

## 📊 RESUMEN DE COSTOS MENSUALES

| Servicio | Configuración | Costo |
|----------|---------------|-------|
| EC2 t3.medium | 24/7 | ~$30 |
| RDS db.t3.small | MySQL 8.0, 20GB | ~$25 |
| S3 | ~5GB fotos | ~$1 |
| ElastiCache (opcional) | cache.t3.micro | ~$12 |
| SES | Emails (primeros 62k gratis) | $0 |
| Data Transfer | ~100GB salida | ~$9 |
| **TOTAL ESTIMADO** | | **~$65-80/mes** |

---

## 🆘 TROUBLESHOOTING

### Error: "Connection refused" al conectar a RDS
- Verificar Security Group permite conexión desde EC2
- Verificar endpoint correcto en .env
- Probar: `mysql -h <RDS-endpoint> -u admin -p`

### Error: "S3 Access Denied"
- Verificar IAM user tiene política `AmazonS3FullAccess`
- Verificar AWS_KEY y AWS_SECRET correctos en .env
- Verificar nombre del bucket correcto

### App móvil no conecta
- Verificar CORS headers en respuesta API
- Verificar Security Group EC2 permite puerto 80/443
- Verificar URL correcta en apps (sin trailing slash)

### Error 500 en Laravel
```bash
# Ver logs en EC2
sudo tail -f /var/www/taxisapp/storage/logs/laravel.log

# Ver logs de Nginx
sudo tail -f /var/log/nginx/error.log
```

---

## ✅ CHECKLIST FINAL

Antes de considerar el deploy completo:

- [ ] Credenciales antiguas revocadas en AWS
- [ ] Nuevas credenciales IAM creadas y configuradas
- [ ] RDS creado y base de datos migrada
- [ ] S3 bucket creado con CORS configurado
- [ ] EC2 instancia corriendo con código deployado
- [ ] Nginx configurado y funcionando
- [ ] API responde correctamente (curl tests)
- [ ] CORS headers presentes en respuestas
- [ ] Apps móviles conectan correctamente
- [ ] Emails de prueba funcionan (SES)
- [ ] Subida de fotos funciona (S3)
- [ ] SSL configurado (si tienes dominio)

---

## 📞 SOPORTE

Si tienes problemas, revisa:
- `AWS_DEPLOY_GUIDE.md` - Guía detallada completa
- `PRE_DEPLOY_TESTS.md` - Tests de verificación
- Laravel logs: `/var/www/taxisapp/storage/logs/laravel.log`

**¡Éxito con tu deployment! 🚀**
