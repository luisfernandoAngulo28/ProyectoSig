# 📱 GUÍA DE CONFIGURACIÓN TWILIO SMS

## ✅ Estado Actual

- ✅ **Twilio SDK instalado** (versión 5.42.2)
- ✅ **Variables .env configuradas** (pendiente agregar credenciales reales)
- ✅ **Método sendSMS() implementado** con logging y validación
- ✅ **sendOtpPhone() actualizado** para enviar SMS automáticamente

---

## 🚀 PASOS PARA ACTIVAR ENVÍO DE SMS

### 1. Obtener Credenciales de Twilio

1. **Ir a** [https://www.twilio.com/console](https://www.twilio.com/console)
2. **Crear cuenta** o hacer login
3. **Copiar credenciales** del Dashboard:
   - **Account SID** (ejemplo: `ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`)
   - **Auth Token** (ejemplo: `your_auth_token_here`)

### 2. Obtener Número de Teléfono Twilio

1. **Ir a** Phone Numbers → [Buy a Number](https://www.twilio.com/console/phone-numbers/search)
2. **Filtrar por país**: Perú (+51)
3. **Seleccionar capacidades**: SMS
4. **Comprar número** (costo: ~$1 USD/mes)
5. **Copiar número** (ejemplo: `+51999123456`)

> 💡 **TIP**: Para testing, Twilio ofrece números de prueba gratuitos que solo pueden enviar SMS a números verificados.

### 3. Configurar Variables en .env

Editar el archivo `.env` del proyecto Laravel:

```bash
# Twilio SMS Configuration
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_real_auth_token_here
TWILIO_PHONE_NUMBER=+51999123456
```

**Ubicación**: `taxisapp-backend-laravel-fempile-test\.env`

### 4. Verificar Números de Prueba (Modo Sandbox)

Si estás usando una **cuenta de prueba** de Twilio:

1. Ir a [Verified Caller IDs](https://www.twilio.com/console/phone-numbers/verified)
2. Agregar tu número de celular para testing
3. Twilio enviará un código de verificación
4. Ahora puedes enviar SMS a ese número desde tu app

---

## 🧪 TESTING

### Opción 1: Modo Testing (Con 'code' en response)

**Estado actual**: El código OTP se retorna en el response JSON para facilitar testing.

```json
POST /api-auth/send-otp-phone
{
  "phone": "987654321"
}

RESPONSE:
{
  "status": true,
  "message": "Código OTP enviado correctamente.",
  "data": {
    "code": "123456",  ← Para testing
    "expires_in": 600
  }
}
```

**Ventajas**:
- ✅ No necesitas celular real para probar
- ✅ Flutter puede usar el código del response
- ✅ No gastas créditos de Twilio

### Opción 2: Modo Producción (Sin 'code' en response)

Para remover el código del response:

1. Abrir `app/Http/Controllers/Auth/AuthenticateController.php`
2. Buscar el método `sendOtpPhone()` (línea ~196)
3. Eliminar la línea que retorna 'code':

```php
// ANTES (Testing)
'data' => [
    'code' => $otpCode, // ⚠️ ELIMINAR EN PRODUCCIÓN
    'expires_in' => 600
]

// DESPUÉS (Producción)
'data' => [
    'expires_in' => 600
]
```

Ahora el usuario **solo** recibirá el código por SMS.

---

## 📝 LOGS Y DEBUGGING

### Ver logs de Twilio

Los logs se guardan en `storage/logs/laravel.log`:

```bash
# Ver solo logs relacionados con Twilio
cd taxisapp-backend-laravel-fempile-test
Get-Content storage\logs\laravel.log -Tail 50 | Select-String "Twilio|SMS"
```

**Logs exitosos**:
```
[2026-03-01 22:30:45] local.INFO: SMS sent successfully to: +51987654321
```

**Logs de error**:
```
[2026-03-01 22:30:45] local.ERROR: Twilio SMS Error: [HTTP 401] Unable to create record
[2026-03-01 22:30:45] local.ERROR: Twilio credentials not configured in .env
```

---

## 🔍 TROUBLESHOOTING

### Error: "Twilio credentials not configured"

**Causa**: Variables .env vacías o con valores de ejemplo.

**Solución**:
1. Verificar que `.env` tenga credenciales reales de Twilio
2. Reiniciar servidor PHP: `php artisan serve`

### Error: "Authentication failed"

**Causa**: Account SID o Auth Token incorrectos.

**Solución**:
1. Verificar credenciales en [Twilio Console](https://www.twilio.com/console)
2. Copiar nuevamente SID y Token
3. NO incluir espacios al Final

### Error: "Unable to create record: The 'From' number +51xxx is not a valid"

**Causa**: El número en `TWILIO_PHONE_NUMBER` no te pertenece o está mal escrito.

**Solución**:
1. Verificar número en [Phone Numbers](https://www.twilio.com/console/phone-numbers/incoming)
2. Copiar exactamente con formato `+51` al inicio

### Error: "Destination number is not verified"

**Causa**: Cuenta Twilio en modo Trial, el número de destino no está verificado.

**Solución**:
1. Ir a [Verified Caller IDs](https://www.twilio.com/console/phone-numbers/verified)
2. Agregar el número de testing
3. O actualizar a cuenta de pago (sin necesidad de verificar)

### SMS no llega pero no hay errores

**Posibles causas**:
1. **Carrier bloqueó el mensaje** (spam filter)
2. **Número tiene formato incorrecto** (debe ser 9 dígitos sin +51)
3. **Delay en entrega** (puede tomar 1-2 minutos)

**Solución**:
1. Ver logs de Twilio Console → [Message Logs](https://www.twilio.com/console/sms/logs)
2. Verificar estado del mensaje: `delivered`, `sent`, `failed`, `undelivered`

---

## 💰 COSTOS

### Cuenta Trial (Gratis)
- ✅ $15 USD en créditos
- ✅ Envío a números verificados
- ⚠️ Prefijo "Sent from your Twilio trial account" en mensajes

### Cuenta de Pago
- **SMS Perú**: ~$0.0055 USD por mensaje
- **Número Twilio Perú**: ~$1 USD/mes
- **Ejemplo**: 1000 SMS/mes = $6.50 USD

---

## 📊 TESTING DEL FLUJO COMPLETO

### Test con cURL

```bash
# 1. Enviar OTP
curl -X POST http://localhost:8000/api-auth/send-otp-phone \
  -H "Content-Type: application/json" \
  -d '{"phone":"987654321"}'

# Response:
# {
#   "status": true,
#   "message": "Código OTP enviado correctamente.",
#   "data": {
#     "code": "123456",
#     "expires_in": 600
#   }
# }

# 2. Verificar que llegó SMS a +51987654321
# Mensaje: "Tu código de verificación AnDre Taxi es: 123456. Válido por 10 minutos."

# 3. Verificar OTP
curl -X POST http://localhost:8000/api-auth/verify-otp-phone \
  -H "Content-Type: application/json" \
  -d '{"phone":"987654321","code":"123456"}'

# 4. Completar registro
curl -X POST http://localhost:8000/api-auth/register-with-phone \
  -H "Content-Type: application/json" \
  -d '{
    "temp_token":"abc123...",
    "phone":"987654321",
    "name":"Juan Pérez",
    "gender":"male"
  }'
```

---

## 🎯 PRÓXIMOS PASOS

### Checklist de Producción

- [ ] **Obtener credenciales reales de Twilio**
- [ ] **Configurar variables en .env**
- [ ] **Probar envío de SMS con número real**
- [ ] **Remover 'code' del response de sendOtpPhone()**
- [ ] **Actualizar Flutter para ingresar código manualmente**
- [ ] **Testing end-to-end en dispositivo real**
- [ ] **Configurar rate limiting** (evitar spam de SMS)
- [ ] **Agregar intento máximo de verificaciones** (3 intentos)

### Optimizaciones Futuras

1. **Caché de códigos** para evitar múltiples envíos
2. **Rate limiting por IP** (1 SMS cada 60 segundos)
3. **Blacklist de números** (números fraudulentos)
4. **Templates de Twilio** (mensajes predefinidos)
5. **WhatsApp Business API** (alternativa a SMS)

---

## 📄 DOCUMENTOS RELACIONADOS

- [BACKEND_API_PHONE_REGISTRATION.md](BACKEND_API_PHONE_REGISTRATION.md) - Documentación completa de API
- [ESTADO_IMPLEMENTACION.md](ESTADO_IMPLEMENTACION.md) - Estado actual del proyecto
- [CAMBIOS_SOLICITADOS.md](../CAMBIOS_SOLICITADOS.md) - Requerimientos del cliente

---

## 🆘 SOPORTE

### Documentación Oficial Twilio
- [Twilio PHP SDK](https://www.twilio.com/docs/libraries/php)
- [Sending SMS Messages](https://www.twilio.com/docs/sms/quickstart/php)
- [Error Codes](https://www.twilio.com/docs/api/errors)

### Contacto
- **Dashboard Twilio**: https://www.twilio.com/console
- **Support**: https://support.twilio.com

---

_Última actualización: 1 de marzo de 2026_
_Estado: Twilio SDK instalado y configurado - Listo para testing_
