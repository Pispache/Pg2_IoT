# Sistema IoT para monitoreo de temperatura y humedad en bodega de medicamentos

Proyecto final desarrollado para funcionar con **Laravel 13 + MySQL + Herd** y recibir datos reales desde **Wemos D1 Mini / ESP8266 + DHT11**.

## Módulos incluidos

- Inicio y cierre de sesión.
- Roles: Administrador, Supervisor y Auditor / Consulta.
- Usuarios.
- Bodegas y áreas.
- Dispositivos IoT con token individual.
- API REST para temperatura, humedad, batería y señal Wi-Fi.
- Historial de lecturas.
- Dashboard con resumen y gráfica.
- Reglas configurables de alerta.
- Generación y resolución automática de alertas.
- Gestión manual de alertas.
- Reportes filtrables.
- Exportación CSV.
- Exportación PDF con Dompdf.
- Bitácora de auditoría.
- Firmware de ejemplo para Wemos D1 Mini.
- Dos redes Wi-Fi configurables en el firmware.

## Requisitos

- macOS con Herd.
- PHP 8.3 o superior (Laravel 13).
- Composer.
- MySQL.
- Arduino IDE para cargar el firmware al Wemos.
- Librería `DHT sensor library` instalada en Arduino IDE.

## Instalación rápida con Herd

1. Descomprima el proyecto dentro de una carpeta administrada por Herd, por ejemplo:

```bash
cd ~/Herd
git-or-copy iot-bodega-final
cd iot-bodega-final
```

2. Instale dependencias:

```bash
composer install
```

3. Cree el archivo de entorno:

```bash
cp .env.example .env
php artisan key:generate
```

4. Cree una base de datos MySQL llamada:

```text
iot_bodega
```

5. Revise `.env` y confirme sus credenciales MySQL.

6. Cree tablas y datos demo:

```bash
php artisan migrate --seed
```

7. Limpie cachés:

```bash
php artisan optimize:clear
```

8. Abra el sitio de Herd. Si la carpeta se llama `iot-bodega-final`, normalmente será algo similar a:

```text
http://iot-bodega-final.test
```

## Usuarios de demostración

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | admin@iot.test | Admin123! |
| Supervisor | supervisor@iot.test | Supervisor123! |
| Auditor | auditor@iot.test | Auditor123! |

Cambie estas contraseñas para una implementación real.

## Dispositivo de demostración

- Serial: `IOT-DEMO-001`
- Token: `IOT-DEMO-001-SECRET-2026`
- Intervalo: 60 minutos

El seeder crea 25 lecturas de demostración para que el dashboard muestre datos inmediatamente.

## Probar la API desde la Mac

```bash
curl -X POST http://iot-bodega-final.test/api/v1/readings \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -H 'X-Device-Token: IOT-DEMO-001-SECRET-2026' \
  -d '{
    "serial":"IOT-DEMO-001",
    "temperature":24.8,
    "humidity":58.2,
    "battery":87,
    "wifi_signal":-62,
    "firmware_version":"1.0.0"
  }'
```

## Conectar un Wemos físico

El ESP8266 debe poder llegar a la Mac por la red local. Para una demostración sencilla puede ejecutar Laravel escuchando en toda la interfaz de red:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Obtenga la IP local de la Mac y coloque en `firmware/wemos_d1_mini_iot.ino`:

```cpp
const char* API_URL = "http://IP_DE_LA_MAC:8000/api/v1/readings";
```

Después configure:

```cpp
WIFI_PRIMARY_SSID
WIFI_PRIMARY_PASS
WIFI_SECONDARY_SSID
WIFI_SECONDARY_PASS
DEVICE_SERIAL
DEVICE_TOKEN
```

El firmware ya incluye reconexión y cambio a una segunda red Wi-Fi.

## Seguridad del token

Los tokens creados desde la plataforma no se almacenan en texto plano. La base de datos conserva un hash SHA-256 y los últimos cuatro caracteres únicamente para identificación. El token completo se muestra una sola vez cuando se crea o regenera.

El dispositivo demo es una excepción intencional para facilitar la presentación académica.

## Estructura de API

Consulte `DOCS_API.md`.

## Firmware

Consulte:

```text
firmware/wemos_d1_mini_iot.ino
```

## Flujo de una lectura

1. DHT11 captura temperatura y humedad.
2. Wemos D1 Mini procesa la lectura.
3. El ESP8266 se conecta por Wi-Fi.
4. Envía un `POST /api/v1/readings` con su token.
5. Laravel valida el dispositivo.
6. MySQL almacena la lectura.
7. Se actualiza `last_seen_at`.
8. El servicio de alertas evalúa los rangos.
9. Si existe una desviación, se crea una alerta.
10. Dashboard, historial y reportes muestran la información.

## Preparación para la presentación final

Para una demostración estable:

1. Ejecute `php artisan migrate:fresh --seed` antes de la presentación si desea reiniciar datos.
2. Inicie sesión como Administrador.
3. Muestre Bodegas → Áreas → Dispositivos.
4. Muestre el token del dispositivo demo o cree un dispositivo nuevo.
5. Envíe una lectura normal mediante Wemos o `curl`.
6. Envíe una lectura fuera de rango, por ejemplo 31 °C, y abra Alertas.
7. Muestre Historial y Reportes PDF.
8. Inicie sesión como Auditor para demostrar las restricciones por rol.

## Nota sobre dependencias

Como es habitual en proyectos Laravel entregables, la carpeta `vendor` no se incluye en el ZIP. Se reconstruye con `composer install`. No se requieren dependencias de Node.js para la interfaz.
