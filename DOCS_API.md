# API REST de dispositivos IoT

## Salud del servicio
`GET /api/v1/health`

## Registrar una lectura
`POST /api/v1/readings`

Encabezados:

```http
Content-Type: application/json
Accept: application/json
X-Device-Token: IOT-DEMO-001-SECRET-2026
```

Cuerpo:

```json
{
  "serial": "IOT-DEMO-001",
  "temperature": 24.8,
  "humidity": 58.2,
  "battery": 87,
  "wifi_signal": -62,
  "firmware_version": "1.0.0"
}
```

También se acepta `api_token` dentro del JSON, aunque para uso normal se recomienda `X-Device-Token`.

Respuesta correcta:

```json
{
  "ok": true,
  "message": "Lectura registrada correctamente.",
  "reading_id": 123,
  "device": "IOT-DEMO-001",
  "server_time": "2026-10-09T16:00:00-06:00"
}
```

## Consultar configuración del dispositivo
`POST /api/v1/device/config`

```json
{
  "serial": "IOT-DEMO-001"
}
```

Con el mismo encabezado `X-Device-Token`. Retorna intervalo de lectura y rangos configurados para temperatura y humedad.

## Códigos de estado
- `201`: lectura aceptada.
- `401`: token inválido.
- `404`: dispositivo inexistente o inactivo.
- `422`: payload inválido.
