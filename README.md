# Laravel Accesible

Blog técnico bilingüe (es/en) para desarrolladores Laravel, enfocado en enseñar accesibilidad web con ejemplos reproducibles y referencias WCAG 2.2. El sitio aspira a conformidad WCAG 2.2 AA.

## Stack tecnológico

- **Backend:** Laravel 13, PHP >= 8.3 (recomendado 8.5)
- **Frontend:** Blade, Livewire 4, Tailwind CSS, Alpine.js (vía Livewire)
- **Base de datos:** MySQL 8+ o MariaDB compatible
- **Tests:** Pest, Playwright + @axe-core/playwright
- **Calidad de código:** Laravel Pint

## Idiomas soportados

| Prefijo | Idioma | Portada |
|---------|--------|---------|
| `/es`   | Español (predeterminado) | `/es` |
| `/en`   | English | `/en` |

## Requisitos locales

- PHP >= 8.3 con extensiones: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `zip`
- Composer 2.x
- Node.js 20+
- MySQL 8+ o MariaDB

## Instalación local

```bash
git clone <repo> laravel-accesible
cd laravel-accesible

composer install
cp .env.example .env
php artisan key:generate

# Configurar DB en .env, luego:
php artisan migrate
php artisan db:seed --class=DatabaseSeeder  # solo entorno local

npm install
npm run dev
```

## Comandos frecuentes

```bash
# Tests
php artisan test --compact

# Formateo de código
vendor/bin/pint --dirty

# Build de assets
npm run build

# Linting y compilación
npm run dev
```

## Documentación del proyecto

| Documento | Descripción |
|-----------|-------------|
| [`docs/LARAVEL_ACCESIBLE_PLAN.md`](docs/LARAVEL_ACCESIBLE_PLAN.md) | Plan maestro de fases y criterios de aceptación |
| [`docs/PROGRESS.md`](docs/PROGRESS.md) | Estado actual de cada fase y registro de merges |
| [`docs/DECISIONS.md`](docs/DECISIONS.md) | Decisiones de arquitectura y tecnología |
| [`docs/ACCESSIBILITY_CHECKLIST.md`](docs/ACCESSIBILITY_CHECKLIST.md) | Checklist WCAG 2.2 AA y registro de hallazgos |
| [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) | Guía de despliegue, rollback y checklist pre-deploy |

## Protocolo Git

- `main` está protegida: **no hacer commits ni merges directos**.
- Cada fase trabaja en su propia rama (ver tabla en `docs/PROGRESS.md`).
- Los Pull Requests son creados por el agente; **el merge lo realiza manualmente el propietario**.
- Ver sección "Protocolo Git obligatorio" en `docs/LARAVEL_ACCESIBLE_PLAN.md`.

## Licencia

Pendiente de definir.
