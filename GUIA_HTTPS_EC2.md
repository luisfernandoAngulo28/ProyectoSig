# 🔒 Guía: Configurar HTTPS en el Servidor EC2

## Prerequisitos
- Acceso SSH al servidor EC2: `ssh ubuntu@18.225.57.224`
- Un dominio apuntando a la IP del servidor (recomendado)
- Nginx instalado en el servidor

---

## Opción A: Con dominio (Recomendado)

### 1. Apuntar dominio al servidor
En tu proveedor DNS, crea un registro A:
```
api.tudominio.com → 18.225.57.224
```

### 2. Instalar Certbot (Let's Encrypt)
```bash
# Conectar al servidor
ssh ubuntu@18.225.57.224

# Instalar Certbot
sudo apt update
sudo apt install -y certbot python3-certbot-nginx
```

### 3. Obtener certificado SSL
```bash
sudo certbot --nginx -d api.tudominio.com
```

Certbot te pedirá:
- Email para notificaciones
- Aceptar términos
- Redireccionar HTTP a HTTPS (seleccionar **2**)

### 4. Verificar renovación automática
```bash
sudo certbot renew --dry-run
```

### 5. Actualizar configuración de Nginx
El archivo `nginx_taxisapp.conf` debería quedar como:
```nginx
server {
    listen 80;
    server_name api.tudominio.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl;
    server_name api.tudominio.com;

    ssl_certificate /etc/letsencrypt/live/api.tudominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.tudominio.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    root /var/www/taxisapp/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 6. Reiniciar Nginx
```bash
sudo nginx -t
sudo systemctl restart nginx
```

---

## Opción B: Sin dominio (SSL con IP directa)

> ⚠️ No se recomienda para producción. Let's Encrypt no emite certificados para IPs.

Se puede usar un certificado autofirmado:
```bash
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/taxisapp.key \
  -out /etc/ssl/certs/taxisapp.crt \
  -subj "/CN=18.225.57.224"
```

Y configurar Nginx con esos certificados. Pero las apps Flutter mostrarán advertencias SSL.

---

## Después de configurar HTTPS

### Actualizar las URLs en Flutter (ambas apps)

**App Pasajero** (`taxisapp-flutter-fempile/lib/environments/environment.dart`):
```dart
// Cambiar de:
static const String url = 'http://18.225.57.224';
static const String socketUrl = 'http://18.225.57.224';

// A (con dominio):
static const String url = 'https://api.tudominio.com';
static const String socketUrl = 'https://api.tudominio.com';
```

**App Conductor** (`taxisapp-driver-flutter-fempile/lib/environment/environment.dart`):
Hacer el mismo cambio.

### Actualizar el .env del backend
```env
APP_URL=https://api.tudominio.com
```

### Security Group de EC2
Asegúrate de que el puerto **443 (HTTPS)** esté abierto en el Security Group de AWS:
- Tipo: HTTPS
- Puerto: 443
- Origen: 0.0.0.0/0

---

## Verificar
```bash
# Probar que responde por HTTPS
curl -I https://api.tudominio.com

# Verificar certificado
openssl s_client -connect api.tudominio.com:443 -servername api.tudominio.com
```
