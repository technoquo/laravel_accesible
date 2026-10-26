# Guía de despliegue — Laravel Accesible

> Proyecto: Laravel Accesible
> Última actualización: 2026-10-26
> Estado: Borrador — pendiente completar en Fase 8

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

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_accesible
DB_USERNAME=usuario_prod
DB_PASSWORD=contraseña_segura

SESSION_DRIVER=database
CACHE_STORE=redis  # o database si no hay Redis
QUEUE_CONNECTION=sync  # cambiar a redis/database si se usan colas
```

> Nunca comitear el archivo `.env` al repositorio.

---

## Proceso de despliegue (pendiente detallar en Fase 8)

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

## Checklist pre-despliegue (pendiente Fase 8)

### Seguridad
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Sin claves ni secretos en el repositorio
- [ ] HTTPS configurado y forzado
- [ ] Headers de seguridad (CSP, HSTS, X-Frame-Options)
- [ ] Rate limiting activo en rutas sensibles
- [ ] Sin registro público habilitado

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
