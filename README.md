# WISP - Sistema de Gestión para Proveedores de Internet (ISP)

Sistema de gestión integral para proveedores de servicios de Internet (ISP):
clientes, planes, facturación, cobranzas, tickets de soporte, instalaciones,
almacén y más.

## Requisitos

- PHP 7.4+ (probado en PHP 8.5)
- MariaDB / MySQL
- Apache (con mod_rewrite) o servidor compatible con `.htaccess`

## Instalación

1. Clonar el repositorio y copiar la configuración de entorno:

```bash
cp .env.example .env
```

2. Completar `.env` con los datos de tu entorno (nunca subir este archivo).

3. Crear la base de datos e importar `nacion17_internet.sql` (opcional, no se distribuye en el repositorio).

4. Configurar el vhost de Apache apuntando a la raíz del proyecto.

5. Instalar dependencias de Composer (dompdf, phpspreadsheet):

```bash
composer install
```

6. Acceder al sistema y autenticarse.

## Estructura

```
Config/        Configuración central (lee variables desde .env)
Controllers/   Controladores por módulo
Models/        Modelos de datos
Views/         Vistas por módulo
Libraries/     Librerías (dompdf, phpmailer, phpspreadsheet, etc.)
Helpers/       Funciones auxiliares
Assets/        CSS, JS, imágenes
```

## Seguridad

- Las credenciales y claves viven en `.env` (excluido del repositorio).
- `Config.php` usa valores de respaldo genéricos; no incluye credenciales reales.
- No subir a repositorios públicos: `.env`, dumps `.sql`, logs ni `vendor/`.
