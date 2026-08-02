# Seguridad - INTERNET SYSTEM

## Cambios Implementados

### 1. Bcrypt para Contraseñas
- **Antes**: Contraseñas encriptadas con AES-256-CBC
- **Ahora**: Contraseñas hasheadas con bcrypt (cost: 12)
- **Migración automática**: Las contraseñas antiguas se migran al hacer login
- **Archivo**: `Helpers/Security.php`

### 2. CSRF Token
- Tokens CSRF en formularios
- Validación en endpoints POST
- **Uso en PHP**: `csrf_field()` en formularios
- **Uso en AJAX**: `<meta name="csrf-token" content="...">`

### 3. Rate Limiting
- Máximo 5 intentos de login por 15 minutos
- Máximo 3 intentos de reset de contraseña por hora
- Bloqueo automático con tiempo de espera

### 4. Sanitización Mejorada
- `sanitize_string()` - Limpia entradas de texto
- `sanitize_email()` - Valida y limpia emails
- `sanitize_number()` - Valida números
- Reemplaza la función `strClean()` antigua

### 5. Headers de Seguridad
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN`
- `X-XSS-Protection: 1; mode=block`
- `Strict-Transport-Security` (HSTS)
- `Content-Security-Policy` (CSP)
- `Referrer-Policy`
- `Permissions-Policy`

### 6. Logging de Seguridad
- Logs de intentos de login exitosos/fallidos
- Logs de cambios de contraseña
- Logs de eventos de seguridad
- Ubicación: `Logs/security_YYYY-MM-DD.log`

## Archivos Modificados

```
Helpers/
  ├── Security.php          (NUEVO) Funciones de seguridad
  └── Helpers.php           (MODIFICADO) Incluye Security.php

Controllers/
  ├── Login.php             (MODIFICADO) Bcrypt + Rate Limiting + CSRF
  └── Users.php             (MODIFICADO) Bcrypt + CSRF

Models/
  ├── LoginModel.php        (MODIFICADO) Métodos para bcrypt
  └── UsersModel.php        (MODIFICADO) Métodos para bcrypt

Config/
  └── Config.php            (MODIFICADO) Headers seguridad + sesiones

.htaccess                    (MODIFICADO) Headers adicionales
migrate_passwords.php        (NUEVO) Script de migración
```

## Instrucciones de Migración

### Para sistemas en producción:

1. **Hacer backup de la base de datos**
```bash
mysqldump -u root -p nacion17_internet > backup.sql
```

2. **Ejecutar script de migración**
```bash
php migrate_passwords.php
```

3. **Notificar a usuarios**
- Cada usuario recibirá una contraseña temporal: `ChangeMe[ID]!`
- Deben cambiar su contraseña en el próximo login

4. **Eliminar script de migración**
```bash
rm migrate_passwords.php
```

## Uso de Funciones de Seguridad

### En formularios PHP:
```php
<form method="POST">
    <?php echo csrf_field(); ?>
    <!-- otros campos -->
</form>
```

### En requests AJAX:
```javascript
// Obtener token del meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

fetch('/endpoint', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `csrf_token=${csrfToken}&data=...`
});
```

### Para hashear contraseñas:
```php
// Hashear nueva contraseña
$hash = hash_password($password);

// Verificar contraseña
if (verify_password($password, $hash)) {
    // Contraseña válida
}

// Verificar si necesita rehash
if (needs_rehash($hash)) {
    // Rehashear con cost actualizado
}
```

### Para logging:
```php
// Registrar evento de seguridad
log_security_event('LOGIN_SUCCESS', 'User ID: 123', 'INFO');
log_security_event('LOGIN_FAILED', 'Username: admin', 'WARNING');
```

## Notas Importantes

1. **NO eliminar** el archivo `Helpers/Security.php`
2. **Mantener** el archivo `.gitignore` actualizado para excluir `Logs/`
3. **Monitorear** los logs de seguridad regularmente
4. **Actualizar** los hashes de contraseñas periódicamente si se cambia el cost de bcrypt
