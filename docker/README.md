# Docker para CampusFlow

Esta configuracion levanta CampusFlow con Apache/PHP 8.3 y MySQL 8.4 para desarrollo, demostracion local y preparacion de despliegue posterior en Cloud Run, App Runner o Azure Container Apps.

## Preparar variables locales

No copies `.env` dentro de la imagen. Para una demo local puedes crear un archivo de entorno fuera del build:

```powershell
Copy-Item .env.docker.example .env.docker
```

Genera una clave de aplicacion sin exponer credenciales reales:

```powershell
php artisan key:generate --show
```

Copia el valor resultante en `CAMPUSFLOW_APP_KEY` dentro de `.env.docker`. Si no defines `CAMPUSFLOW_APP_KEY`, el contenedor genera una clave efimera al iniciar; sirve para una prueba rapida, pero invalida sesiones/cookies al recrear el contenedor.

Para usar ese archivo, agrega `--env-file .env.docker` a los comandos de Compose.

## Levantar servicios

```powershell
docker compose --env-file .env.docker config
docker compose --env-file .env.docker build --no-cache
docker compose --env-file .env.docker up -d
docker compose --env-file .env.docker ps
```

La aplicacion queda disponible en:

```text
http://localhost:8080
```

El puerto puede cambiarse con `PORT`.

## Migraciones

Las migraciones se ejecutan en el arranque solo cuando:

```text
RUN_MIGRATIONS=true
```

El entrypoint usa:

```text
php artisan migrate --force
```

No ejecuta `migrate:fresh`, `migrate:refresh`, `db:wipe`, `TRUNCATE` ni seeders.

## Carga manual de datos de demostracion

No se importa `database/scripts/data.sql` automaticamente. Despues de levantar los contenedores y ejecutar migraciones, importa una sola vez con:

```powershell
docker compose exec app campusflow-import-demo-data
```

El script:

1. verifica que las migraciones respondan;
2. consulta tablas de demostracion;
3. aborta si detecta datos existentes;
4. solicita escribir `IMPORTAR`;
5. importa `database/scripts/data.sql`.

No ejecutes el script si ya cargaste datos o si estas usando una base de datos de trabajo.

## Validaciones utiles

```powershell
docker compose exec app php artisan about
docker compose exec app php artisan migrate:status
docker compose exec app php artisan route:list
```

Endpoints publicos a revisar:

```text
http://localhost:8080
http://localhost:8080/manifest.webmanifest
http://localhost:8080/sw.js
http://localhost:8080/offline.html
http://localhost:8080/terminos-y-condiciones
http://localhost:8080/politica-de-privacidad
```

## Detener y volver a levantar

```powershell
docker compose down
docker compose up -d
```

`docker compose down` conserva el volumen `mysql_data`. No uses `docker compose down -v` salvo que quieras eliminar la base local.
