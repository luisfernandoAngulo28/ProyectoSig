# 🚀 GUÍA COMPLETA: DEPLOY AWS - TaxisApp Backend

---

## ✅ FASE 1: PREPARACIÓN LOCAL (30-60 min)

### 1.1 Seguridad Crítica

#### Cambiar variables de entorno
```bash
# Copiar archivo de ejemplo
cp .env.production.example .env.production

# Generar nuevas keys
php artisan key:generate --env=production
php artisan jwt:secret --env=production
```

#### Limpiar archivos sensibles
```bash
# Eliminar debugbar con credenciales expuestas
rm -rf storage/debugbar/*
echo '*' > storage/debugbar/.gitignore
echo '!.gitignore' >> storage/debugbar/.gitignore

# Limpiar logs
rm -rf storage/logs/*.log

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 1.2 Optimización de Performance
```bash
# Optimizar Composer (solo producción)
composer install --no-dev --optimize-autoloader

# Cachear configuraciones (SOLO después de configurar .env correctamente)
php artisan config:cache
php artisan route:cache
# php artisan view:cache  # Opcional, útil si usas Blade

# Optimizar autoload
composer dump-autoload --optimize
```

### 1.3 Verificar Dependencias
```bash
# Verificar que todas las dependencias estén instaladas
composer validate
composer install --no-interaction

# Verificar versión PHP
php -v  # Debe ser >= 7.4
```

---

## ☁️ FASE 2: CONFIGURAR SERVICIOS AWS (60-90 min)

### 2.1 Crear RDS (Base de Datos MySQL)

**Pasos en AWS Console:**
1. Ir a **RDS** → Create Database
2. Configuración:
   ```
   Engine: MySQL 8.0
   Template: Production (o Dev/Test para ahorrar)
   DB Instance: db.t3.small (2 vCPU, 2GB RAM)
   Storage: 20GB SSD (gp3)
   Multi-AZ: NO (para desarrollo) / YES (para producción)
   VPC: Default VPC
   Public Access: NO (más seguro)
   Security Group: Crear nuevo "taxisapp-rds-sg"
     - TCP 3306 desde Security Group de EC2
   ```
3. **Credenciales:**
   ```
   Master Username: taxisapp_admin
   Master Password: [GUARDAR EN LUGAR SEGURO]
   Database Name: taxisapp
   ```
4. Esperar 5-10 minutos hasta que esté "Available"
5. **Copiar el Endpoint**: `taxisapp.xxxxxxxxxxxx.us-east-1.rds.amazonaws.com`

**Migrar Base de Datos:**
```bash
# Desde tu máquina local, exportar BD actual
mysqldump -u root taxisapp > taxisapp_backup.sql

# Conectar a RDS e importar
mysql -h taxisapp.xxxxx.rds.amazonaws.com -u taxisapp_admin -p taxisapp < taxisapp_backup.sql
```

### 2.2 Crear S3 Bucket (Almacenamiento de Archivos)

**Pasos:**
1. Ir a **S3** → Create Bucket
2. Configuración:
   ```
   Bucket Name: taxisapp-production-files
   Region: us-east-1 (mismo que RDS)
   Block Public Access: DESMARCAR (necesitas acceso público para imágenes)
   Versioning: Opcional (recomendado)
   ```
3. **Crear Carpetas**:
   ```
   /drivers/photos/
   /drivers/vehicles/
   /users/photos/
   /tmp/
   ```
4. **Configurar CORS del Bucket**:
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

### 2.3 Crear Usuario IAM (Acceso Programático)

**Pasos:**
1. Ir a **IAM** → Users → Create User
2. Nombre: `taxisapp-backend-prod`
3. Access Type: **Programmatic access** (no Console)
4. Permissions:
   ```
   - AmazonS3FullAccess
   - AmazonSESFullAccess
   ```
5. **Guardar Access Key y Secret Key**

**⚠️ IMPORTANTE**: Revocar las credenciales antiguas expuestas:
```
AKIAJ4KGIJVDP5BWQRPQ
```

### 2.4 Configurar ElastiCache (Redis - Opcional pero Recomendado)

**Pasos:**
1. Ir a **ElastiCache** → Redis → Create
2. Configuración:
   ```
   Cluster Mode: Disabled
   Name: taxisapp-cache
   Node Type: cache.t3.micro (0.5GB)
   Number of Replicas: 0 (1+ para producción)
   Subnet Group: Default
   Security Group: Crear "taxisapp-redis-sg"
     - TCP 6379 desde SG de EC2
   ```
3. **Copiar Primary Endpoint**: `taxisapp-cache.xxxxx.0001.use1.cache.amazonaws.com:6379`

### 2.5 Configurar SES (Correo Electrónico)

**Pasos:**
1. Ir a **SES** → Verified Identities
2. **Verificar dominio:**
   ```
   Domain: tudominio.com
   Agregar registros DNS (TXT, CNAME) en tu proveedor
   ```
3. **Crear credenciales SMTP:**
   - SES → SMTP Settings → Create SMTP Credentials
   - Guardar Username y Password

**⚠️ Salir de Sandbox Mode:**
- Por defecto SES está en sandbox (solo emails verificados)
- Solicitar producción: SES → Account Dashboard → Request Production Access

---

## 🖥️ FASE 3: CONFIGURAR EC2 (60-90 min)

### 3.1 Lanzar Instancia EC2

**Pasos:**
1. Ir a **EC2** → Launch Instance
2. Configuración:
   ```
   Name: taxisapp-backend-prod
   AMI: Ubuntu Server 22.04 LTS
   Instance Type: t3.medium (2 vCPU, 4GB RAM)
   Key Pair: Crear nuevo "taxisapp-prod-key" (GUARDAR .pem)
   VPC: Default
   Security Group: Crear "taxisapp-backend-sg"
     - SSH (22): Tu IP
     - HTTP (80): 0.0.0.0/0
     - HTTPS (443): 0.0.0.0/0
   Storage: 30GB SSD (gp3)
   ```

### 3.2 Conectar a EC2
```bash
# Dar permisos a la key
chmod 400 taxisapp-prod-key.pem

# Conectar
ssh -i taxisapp-prod-key.pem ubuntu@[IP-PUBLICA-EC2]
```

### 3.3 Instalar Software en EC2
```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar PHP 8.1 y extensiones
sudo apt install -y php8.1 php8.1-fpm php8.1-cli php8.1-mysql php8.1-xml \
  php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath \
  php8.1-redis php8.1-tokenizer php8.1-json php8.1-intl

# Instalar Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Instalar Nginx
sudo apt install -y nginx

# Instalar MySQL Client (para conectar a RDS)
sudo apt install -y mysql-client

# Instalar Redis Client (para ElastiCache)
sudo apt install -y redis-tools

# Instalar Git
sudo apt install -y git

# Instalar Supervisor (para queues)
sudo apt install -y supervisor
```

### 3.4 Configurar Nginx
```bash
# Crear archivo de configuración
sudo nano /etc/nginx/sites-available/taxisapp
```

**Contenido:**
```nginx
server {
    listen 80;
    server_name api.tudominio.com;
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

    # Aumentar tamaños para uploads de fotos
    client_max_body_size 20M;
    client_body_buffer_size 128k;
}
```

**Habilitar sitio:**
```bash
sudo ln -s /etc/nginx/sites-available/taxisapp /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 📦 FASE 4: DEPLOY DEL CÓDIGO (30-45 min)

### 4.1 Clonar Repositorio
```bash
# Crear directorio
sudo mkdir -p /var/www/taxisapp
sudo chown -R ubuntu:ubuntu /var/www/taxisapp

# Clonar (si tienes Git privado)
cd /var/www
git clone https://github.com/tu-usuario/taxisapp-backend.git taxisapp

# O subir con SCP/SFTP desde local
# scp -r -i taxisapp-prod-key.pem ./taxisapp-backend ubuntu@[IP-EC2]:/var/www/taxisapp
```

### 4.2 Configurar Permisos
```bash
cd /var/www/taxisapp

# Instalar dependencias
composer install --no-dev --optimize-autoloader

# Configurar permisos
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 4.3 Configurar Variables de Entorno
```bash
# Copiar archivo de producción
cp .env.production.example .env

# Editar con credenciales reales
nano .env
```

**Rellenar:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.tudominio.com

DB_HOST=[ENDPOINT-RDS]
DB_USERNAME=taxisapp_admin
DB_PASSWORD=[PASSWORD-RDS]

AWS_KEY=[NEW-IAM-ACCESS-KEY]
AWS_SECRET=[NEW-IAM-SECRET]
AWS_BUCKET=taxisapp-production-files

REDIS_HOST=[ENDPOINT-ELASTICACHE]

MAIL_USERNAME=[SES-SMTP-USER]
MAIL_PASSWORD=[SES-SMTP-PASSWORD]
```

### 4.4 Ejecutar Migraciones
```bash
# Verificar conexión a BD
php artisan migrate:status

# Ejecutar migraciones pendientes (CUIDADO en producción)
php artisan migrate --force

# Seeders si es necesario
# php artisan db:seed --force
```

### 4.5 Optimizar
```bash
# Cachear configuraciones
php artisan config:cache
php artisan route:cache

# Optimizar
php artisan optimize
```

---

## 🔐 FASE 5: SSL/HTTPS (30 min)

### Instalar Certbot (Let's Encrypt)
```bash
sudo apt install -y certbot python3-certbot-nginx

# Obtener certificado SSL
sudo certbot --nginx -d api.tudominio.com

# Renovación automática
sudo certbot renew --dry-run
```

---

## 🔄 FASE 6: QUEUES Y WORKERS (20 min)

### Configurar Supervisor
```bash
sudo nano /etc/supervisor/conf.d/taxisapp-worker.conf
```

**Contenido:**
```ini
[program:taxisapp-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/taxisapp/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/taxisapp/storage/logs/worker.log
stopwaitsecs=3600
```

**Iniciar:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start taxisapp-worker:*
```

---

## 📊 FASE 7: MONITOREO (20 min)

### CloudWatch Logs
```bash
# Instalar agente CloudWatch
wget https://s3.amazonaws.com/amazoncloudwatch-agent/ubuntu/amd64/latest/amazon-cloudwatch-agent.deb
sudo dpkg -i -E ./amazon-cloudwatch-agent.deb

# Configurar para enviar logs Laravel
```

### Logs Laravel
```bash
# Ver logs en tiempo real
tail -f /var/www/taxisapp/storage/logs/laravel.log

# Rotar logs (agregar a cron)
sudo nano /etc/logrotate.d/taxisapp
```

---

## ✅ FASE 8: VERIFICACIÓN FINAL

### Checklist Post-Deploy
- [ ] ✅ Visitar https://api.tudominio.com → Ver página Laravel
- [ ] ✅ Probar endpoint: `GET /api/check-login`
- [ ] ✅ App Mobile puede conectarse al backend
- [ ] ✅ Registro de usuario funciona
- [ ] ✅ Login funciona
- [ ] ✅ Subida de fotos funciona (S3)
- [ ] ✅ Notificaciones push funcionan
- [ ] ✅ WhatsApp (Twilio) funciona
- [ ] ✅ Logs no muestran errores
- [ ] ✅ Certificado SSL válido
- [ ] ✅ Workers corriendo: `sudo supervisorctl status`

---

## 💰 ESTIMACIÓN DE COSTOS AWS

### Configuración Mínima (Dev/Test)
```
EC2 t3.medium:          ~$30/mes
RDS db.t3.small:        ~$25/mes
S3 (50GB):              ~$1/mes
ElastiCache t3.micro:   ~$12/mes
Data Transfer:          ~$10/mes
-------------------------
TOTAL:                  ~$80-100/mes
```

### Configuración Producción
```
EC2 t3.large:           ~$60/mes
RDS db.t3.medium:       ~$50/mes
S3 (200GB):             ~$5/mes
ElastiCache t3.small:   ~$25/mes
Load Balancer:          ~$20/mes
Data Transfer:          ~$30/mes
-------------------------
TOTAL:                  ~$190-220/mes
```

---

## 🆘 TROUBLESHOOTING COMÚN

### Error: "500 Internal Server Error"
```bash
# Ver logs
tail -100 /var/www/taxisapp/storage/logs/laravel.log

# Verificar permisos
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Limpiar cache
php artisan cache:clear
php artisan config:clear
```

### Error: "No se pueden subir fotos"
```bash
# Verificar credenciales S3 en .env
php artisan tinker
>>> Storage::disk('s3')->put('test.txt', 'test');
```

### Error: "No hay conexión a BD"
```bash
# Verificar Security Group de RDS permite conexión desde EC2
# Probar conexión manual
mysql -h [RDS-ENDPOINT] -u taxisapp_admin -p taxisapp
```

---

## 📝 COMANDOS ÚTILES POST-DEPLOY

### Deploy de Actualizaciones
```bash
# Script de deploy
cd /var/www/taxisapp
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan optimize
sudo supervisorctl restart taxisapp-worker:*
```

### Backup de BD
```bash
# Crear backup
mysqldump -h [RDS-ENDPOINT] -u taxisapp_admin -p taxisapp > backup_$(date +%Y%m%d).sql

# Subir a S3
aws s3 cp backup_$(date +%Y%m%d).sql s3://taxisapp-production-files/backups/
```

---

## 🎯 PRÓXIMOS PASOS RECOMENDADOS

1. **Configurar Load Balancer** (>1000 usuarios)
2. **Auto Scaling** (escalabilidad automática)
3. **CloudFront CDN** (archivos estáticos)
4. **Route 53** (DNS administrado por AWS)
5. **WAF** (Firewall de aplicaciones)
6. **Backups automáticos** (AWS Backup)
7. **CI/CD Pipeline** (GitHub Actions + AWS CodeDeploy)

---

**¿Tienes dudas sobre algún paso? ¡Pregúntame!**
