# Instalación en macOS + Herd

```bash
cd /ruta/al/proyecto
composer install
cp .env.example .env
php artisan key:generate
```

Cree la base `iot_bodega` en MySQL y configure `.env`.

Después:

```bash
php artisan migrate --seed
php artisan optimize:clear
```

Abra el dominio que Herd haya asignado a la carpeta.

Para permitir que un Wemos de la misma red alcance el backend durante pruebas:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Use la IP local de la Mac en el firmware, nunca `localhost`, porque para el Wemos `localhost` es el propio microcontrolador.
