# Guía de Despliegue en Dokploy

## Requisitos Previos

- Servidor con Dokploy instalado ([dokploy.com](https://dokploy.com))
- Dominio apuntando al servidor (opcional pero recomendado)
- Repositorio Git con el código del proyecto

---

## Paso 1: Subir Código a Git

```bash
# Inicializar repositorio (si no lo tienes)
git init
git add .
git commit -m "Configuración Docker para Dokploy"

# Subir a tu repositorio (GitHub, GitLab, etc.)
git remote add origin https://github.com/tu-usuario/wisp-internet.git
git push -u origin main
```

---

## Paso 2: Crear Aplicación en Dokploy

1. Inicia sesión en tu panel de Dokploy
2. Ve a **Applications** → **Create Application**
3. Selecciona **Docker Compose** como tipo de despliegue
4. Asigna un nombre: `wisp-internet`

---

## Paso 3: Conectar Repositorio

1. En la pestaña **Git**, conecta tu repositorio
2. Selecciona el branch: `main`
3. Configura el **Compose Path**: `docker-compose.yml`

---

## Paso 4: Configurar Variables de Entorno

En la pestaña **Environment**, agrega las siguientes variables:

### Variables Requeridas

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| `DB_PASSWORD` | Password de la BD | `MiPassSeguro123!` |
| `MYSQL_ROOT_PASSWORD` | Root password MariaDB | `RootPass456!` |
| `BASE_URL` | URL del sistema | `https://midominio.com/` |
| `SECRET_KEY` | Clave encriptación | `openssl rand -hex 32` |
| `SECRET_IV` | IV encriptación | Cadena aleatoria 64 chars |

### Variables Opcionales

| Variable | Valor por defecto |
|----------|-------------------|
| `DB_NAME` | `nacion17_internet` |
| `DB_USER` | `wisp_user` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_PORT` | `8080` |
| `NAME_SYSTEM` | `INTERNET SYSTEM` |

### Generar Claves Seguras

```bash
# Generar SECRET_KEY
openssl rand -hex 32

# Generar SECRET_IV (tomar primeros 64 caracteres)
openssl rand -base64 48
```

---

## Paso 5: Configurar Dominio (Opcional)

1. En la pestaña **Domains**, agrega tu dominio
2. Configura:
   - **Host**: `tu-dominio.com`
   - **Path**: `/`
   - **Port**: `8080`
   - **HTTPS**: Activar con Let's Encrypt

---

## Paso 6: Desplegar

1. Haz clic en **Deploy**
2. Espera a que se complete el build (puede tomar 3-5 minutos)
3. Revisa los logs en caso de errores

---

## Paso 7: Verificar Despliegue

### Acceder al Sistema

- **URL**: `https://tu-dominio.com/` o `http://IP_SERVIDOR:8080/`
- **Usuario**: `admin`
- **Password**: Revisar el hash en `database.sql` o crear nuevo usuario

### Credenciales por Defecto

El usuario `admin` viene con password hasheado en bcrypt. Si necesitas acceder:

1. **Opción A**: Importar la BD directamente (las credenciales ya están incluidas)
2. **Opción B**: Resetear password directamente en la BD:

```sql
-- Conectar al contenedor de BD
docker exec -it wisp-db mysql -u wisp_user -p nacion17_internet

-- Resetear password (bcrypt de "admin123")
UPDATE users SET password = '$2y$12$Gu5JmN.EhGy.d8WdyKCrNe4QrWz8ab4DzTxToN2B7GAJdI7Qtt.gi' WHERE username = 'admin';
```

---

## Comandos Útiles

### Ver logs
```bash
# Logs de la aplicación
docker logs wisp-app -f

# Logs de la base de datos
docker logs wisp-db -f
```

### Reiniciar servicios
```bash
# Reiniciar todo
docker compose restart

# Reiniciar solo la app
docker compose restart app

# Reiniciar solo la BD
docker compose restart db
```

### Acceder a contenedores
```bash
# Shell de la app
docker exec -it wisp-app bash

# Shell de MariaDB
docker exec -it wisp-db mysql -u wisp_user -p nacion17_internet
```

### Backup de base de datos
```bash
# Crear backup
docker exec wisp-db mysqldump -u root -p nacion17_internet > backup_$(date +%Y%m%d).sql

# Restaurar backup
docker exec -i wisp-db mysql -u root -p nacion17_internet < backup.sql
```

---

## Solución de Problemas

### Error: "Connection refused" a la base de datos
- Verifica que `DB_HOST=db` (no `localhost`)
- Asegúrate que MariaDB esté healthy antes de que la app inicie

### Error: "Permission denied" en uploads
```bash
docker exec -it wisp-app bash
chown -R www-data:www-data /var/www/html/Assets/uploads
chmod -R 775 /var/www/html/Assets/uploads
```

### Error: mod_rewrite no funciona
- El Dockerfile ya habilita mod_rewrite
- Verifica que el `.htaccess` esté presente en el contenedor

### La base de datos no se inicializa
- El archivo `database.sql` debe estar en la raíz del proyecto
- MariaDB solo ejecuta scripts de init en el primer levantamiento
- Para re-inicializar: `docker compose down -v` y luego `docker compose up -d`

---

## Actualizaciones Futuras

```bash
# En el servidor de Dokploy, simplemente:
1. Hacer push de cambios al repositorio
2. En Dokploy: clic en "Redeploy"
```

---

## Arquitectura Final

```
┌─────────────────────────────────────────────────┐
│                  Dokploy Server                 │
├─────────────────────────────────────────────────┤
│                                                 │
│  ┌─────────────────┐    ┌─────────────────┐    │
│  │    wisp-app     │    │    wisp-db      │    │
│  │  PHP 8.2+Apache │◄───│  MariaDB 10.11  │    │
│  │    Puerto 80    │    │   Puerto 3306   │    │
│  └────────┬────────┘    └────────┬────────┘    │
│           │                      │              │
│  ┌────────▼────────┐    ┌────────▼────────┐    │
│  │ wisp-uploads    │    │ wisp-db-data    │    │
│  │  (Volumen)      │    │  (Volumen)      │    │
│  └─────────────────┘    └─────────────────┘    │
│                                                 │
│  ┌─────────────────┐    ┌─────────────────┐    │
│  │  wisp-logs      │    │  wisp-cache     │    │
│  │  (Volumen)      │    │  (Volumen)      │    │
│  └─────────────────┘    └─────────────────┘    │
│                                                 │
└─────────────────────────────────────────────────┘
```
