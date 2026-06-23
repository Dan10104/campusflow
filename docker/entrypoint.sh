#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

PORT="${PORT:-8080}"
export PORT

sed "s/__PORT__/${PORT}/g" /etc/apache2/sites-available/campusflow.conf.template > /etc/apache2/sites-available/000-default.conf
printf 'Listen %s\n' "${PORT}" > /etc/apache2/ports.conf

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
    APP_KEY="$(php artisan key:generate --show --no-ansi)"
    export APP_KEY
    echo "WARN: APP_KEY no estaba definido. Se genero una clave efimera para esta ejecucion local."
    echo "WARN: Para sesiones persistentes, genera una clave y definela fuera de la imagen."
fi

if [ ! -L public/storage ]; then
    if [ -e public/storage ]; then
        echo "WARN: public/storage existe y no es un enlace simbolico; se omite storage:link."
    else
        php artisan storage:link --quiet || true
    fi
fi

wait_for_mysql() {
    if [ "${DB_CONNECTION:-}" != "mysql" ]; then
        return 0
    fi

    local timeout="${DB_WAIT_TIMEOUT:-60}"
    local elapsed=0

    if [ -n "${DB_SOCKET:-}" ]; then
        echo "Esperando MySQL mediante socket ${DB_SOCKET} por hasta ${timeout}s..."
    else
        echo "Esperando MySQL en ${DB_HOST:-mysql}:${DB_PORT:-3306} por hasta ${timeout}s..."
    fi

    until php -r '
        $host = getenv("DB_HOST") ?: "mysql";
        $port = getenv("DB_PORT") ?: "3306";
        $socket = getenv("DB_SOCKET") ?: "";
        $db = getenv("DB_DATABASE") ?: "ProyectoFinal";
        $user = getenv("DB_USERNAME") ?: "campusflow";
        $pass = getenv("DB_PASSWORD") ?: "";
        $dsn = $socket !== ""
            ? "mysql:unix_socket={$socket};dbname={$db};charset=utf8mb4"
            : "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        try {
            new PDO(
                $dsn,
                $user,
                $pass,
                [PDO::ATTR_TIMEOUT => 2]
            );
            exit(0);
        } catch (Throwable $exception) {
            fwrite(STDERR, $exception->getMessage() . PHP_EOL);
            exit(1);
        }
    ' >/tmp/campusflow-mysql-check.log 2>&1; do
        elapsed=$((elapsed + 2))

        if [ "${elapsed}" -ge "${timeout}" ]; then
            echo "ERROR: MySQL no estuvo disponible dentro de ${timeout}s."
            cat /tmp/campusflow-mysql-check.log || true
            exit 1
        fi

        sleep 2
    done
}

wait_for_mysql

php artisan optimize:clear --quiet || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
else
    echo "RUN_MIGRATIONS no es true; no se ejecutan migraciones."
fi

php artisan config:cache
php artisan view:cache

if php artisan route:cache; then
    echo "Rutas cacheadas correctamente."
else
    echo "WARN: route:cache no es compatible con las rutas actuales; se continua sin cache de rutas."
    php artisan route:clear --quiet || true
fi

exec apache2-foreground
