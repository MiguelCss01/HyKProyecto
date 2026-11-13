#!/usr/bin/env bash
set -e

# Si se usa SQLite, asegurar que el archivo exista
if [ "${DB_CONNECTION}" = "sqlite" ] || [ -z "${DB_CONNECTION}" ]; then
    touch /var/www/html/database/database.sqlite
fi

# Limpiar y preparar configuración
php artisan config:clear
php artisan view:clear

# Ejecutar migraciones automáticamente
php artisan migrate --force --no-interaction

# Crear usuarios iniciales (Admin, Minorista y Mayorista) y las 3 categorías base si no existen aún
php artisan tinker --execute '
if (\App\Models\User::count() === 0) {
    \App\Models\User::create([
        "name" => "Admin HyK",
        "email" => "admin@hyk.com",
        "role" => "admin",
        "password" => bcrypt("password"),
    ]);
    \App\Models\User::create([
        "name" => "Cliente Minorista",
        "email" => "cliente@hyk.com",
        "role" => "cliente",
        "tipo_cliente" => "minorista",
        "dni" => "40123456",
        "telefono" => "3704112233",
        "password" => bcrypt("password"),
    ]);
    \App\Models\User::create([
        "name" => "Cliente Mayorista",
        "email" => "mayorista@hyk.com",
        "role" => "cliente",
        "tipo_cliente" => "mayorista",
        "cuit" => "30-71234567-9",
        "razon_social" => "Comercial Formosa SRL",
        "telefono" => "3704998877",
        "password" => bcrypt("password"),
    ]);
}
foreach (["Alimentos", "Bebidas", "Limpieza"] as $catNombre) {
    \App\Models\Categoria::firstOrCreate(["nombre_categoria" => $catNombre]);
}
'

# Iniciar el servidor en el puerto asignado por Render
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
