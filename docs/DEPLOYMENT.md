# Guía de despliegue — Laravel Accesible

> Proyecto: Laravel Accesible
> Última actualización: 2026-10-26
> Estado: Completado en Fase 8

**IMPORTANTE:** No desplegar a producción sin autorización explícita del propietario del proyecto.

---

## Requisitos del servidor

### PHP y extensiones
- PHP >= 8.3 (recomendado 8.5)
- Extensiones requeridas: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `zip`

### Servidor web
- Nginx (recomendado) o Apache con `mod_rewrite`
- Webroot apuntando a `/public`
- HTTPS obligatorio (certificado SSL/TLS vigente)

### Base de datos
- MySQL 8.0+ o MariaDB compatible
- Colación: `utf8mb4_unicode_ci`

### Otros
- Composer 2.x
- Node.js (para build de assets — no requerido en producción si se despliegan assets compilados)
- Acceso SSH
- Cron o supervisor para workers si se usan colas

---

## Variables de entorno críticas

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

APP_LOCALE=es
APP_FALLBACK_LOCALE=en

LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_accesible
DB_USERNAME=usuario_prod
DB_PASSWORD=contraseña_segura

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=redis  # o database si no hay Redis
QUEUE_CONNECTION=sync  # cambiar a redis/database si se usan colas

BCRYPT_ROUNDS=12
```

> Nunca comitear el archivo `.env` al repositorio.

### Headers de seguridad (implementados vía middleware)

El middleware `SecurityHeaders` añade automáticamente en todas las respuestas web:

| Header | Valor | Propósito |
|--------|-------|-----------|
| `X-Content-Type-Options` | `nosniff` | Evita MIME sniffing |
| `X-Frame-Options` | `SAMEORIGIN` | Previene clickjacking |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | Limita información en Referer |
| `Permissions-Policy` | `camera=(), microphone=(), geolocation=()` | Deshabilita APIs sensibles del navegador |
| `X-XSS-Protection` | `0` | Deshabilita filtro XSS obsoleto (reemplazado por CSP) |

### Nota sobre CSP (Content Security Policy)

Una CSP completa requiere nonces para Livewire (scripts inline) y whitelisting de CDNs (highlight.js en `cdn.jsdelivr.net`, YouTube en `youtube-nocookie.com`). Implementar en Fase 9+ o antes del primer deploy, validando que no rompa el panel admin ni la renderización de artículos.

---

## Proceso de despliegue

### Primera vez

```bash
# 1. Clonar repositorio
git clone <repo> laravel-accesible
cd laravel-accesible

# 2. Instalar dependencias PHP (sin dev)
composer install --no-dev --optimize-autoloader

# 3. Copiar y configurar .env
cp .env.example .env
php artisan key:generate

# 4. Ejecutar migraciones
php artisan migrate --force

# 5. Crear enlace de almacenamiento
php artisan storage:link

# 6. Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Permisos
chmod -R 775 storage bootstrap/cache
```

### Actualización (deploy continuo)

```bash
# 1. Activar modo mantenimiento
php artisan down --retry=60

# 2. Actualizar código
git pull origin main

# 3. Dependencias PHP
composer install --no-dev --optimize-autoloader

# 4. Migraciones
php artisan migrate --force

# 5. Limpiar y reconstruir caché
php artisan optimize:clear
php artisan optimize

# 6. Desactivar mantenimiento
php artisan up
```

---

## Checklist pre-despliegue

### Seguridad
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Sin claves ni secretos en el repositorio
- [ ] `APP_URL=https://...` configurado (activa `URL::forceScheme('https')` automáticamente)
- [ ] HTTPS configurado en servidor web (certificado SSL/TLS vigente)
- [ ] `SESSION_SECURE_COOKIE=true` en `.env` de producción
- [ ] `SESSION_ENCRYPT=true` en `.env` de producción
- [ ] `LOG_LEVEL=error` en `.env` de producción
- [ ] Headers de seguridad activos (middleware `SecurityHeaders` registrado en `bootstrap/app.php`) ✅
- [ ] Rate limiting en login activo (5 intentos/min, clave email|IP) ✅
- [ ] Sin registro público habilitado ✅
- [ ] Dependencias auditadas: `composer audit` y `npm audit` sin vulnerabilidades ✅

### Base de datos
- [ ] Migraciones reversibles verificadas
- [ ] Backup previo al despliegue
- [ ] Credenciales solo en `.env`

### Assets
- [ ] `npm run build` ejecutado con éxito
- [ ] Assets compilados incluidos en deploy

### Funcionalidad
- [ ] Tests PHP verdes en staging
- [ ] Build frontend verde
- [ ] Pint sin diferencias
- [ ] Páginas ES y EN navegables
- [ ] Panel admin accesible solo con credenciales correctas
- [ ] Sin borradores visibles en público

### Accesibilidad
- [ ] Revisión manual documentada en `docs/ACCESSIBILITY_CHECKLIST.md`
- [ ] Sin hallazgos críticos/altos abiertos

---

## Rollback

```bash
# En caso de fallo crítico:
php artisan down --retry=60

# Revertir al commit anterior
git checkout <commit-anterior>
composer install --no-dev --optimize-autoloader

# Si hay migración reciente problemática:
php artisan migrate:rollback

php artisan optimize
php artisan up
```

---

## Notas de hosting

- Proveedor confirmado: Pendiente (ver `docs/DECISIONS.md`)
- Laravel Cloud es la opción recomendada para despliegue simplificado.
- Alternativas: Forge + servidor VPS, Railway, Render, DigitalOcean App Platform.

---

## Historial de despliegues

| Fecha | Versión | Entorno | Responsable | Notas |
|-------|---------|---------|-------------|-------|
| — | — | — | — | Pendiente primer deploy |
