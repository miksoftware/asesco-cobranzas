# Documentación: Endpoint de Consulta por Cédula

Esta API permite consultar todos los procesos judiciales y sus respectivas actuaciones asociados a un número de cédula o documento de identidad.

---

## 1. Características Principales

- **Ordenamiento de Radicados**: Se devuelven ordenados **desde el más reciente hasta el más antiguo** (según su última actualización y fecha de radicación).
- **Ordenamiento de Actuaciones**: Todas las actuaciones de cada radicado se entregan ordenadas cronológicamente **de la más reciente a la más antigua**.
- **Información Completa**: Cada proceso incluye sus datos judiciales (ciudad, despacho, tipo, clase, demandantes, demandados, contenido de radicación, estado) y el desglose íntegro de sus actuaciones (fechas, actuaciones, anotaciones, términos).
- **Sin Autenticación Requerida**: El endpoint es público e ideal para integraciones externas, sistemas CRM, aplicaciones móviles, frontends o consultas directas vía Postman/cURL.
- **Normalización Inteligente**: Acepta la cédula con o sin puntos/guiones (por ejemplo, `1098765432` o `1.098.765.432`).

---

## 2. Definición del Endpoint

### URLs Disponibles:
```http
GET /api/procesos/cedula/{cedula}
GET /api/cedula/{cedula}
```

> **Nota:** Puedes usar tu dominio o `http://localhost:8000` (o la URL de tu entorno Laragon).

### Parámetros de Ruta:
| Parámetro | Tipo   | Obligatorio | Descripción |
|-----------|--------|-------------|-------------|
| `cedula`  | string | Sí          | Número de documento o cédula de la persona a consultar. |

### Cabeceras (Headers) Recomendadas:
```http
Accept: application/json
```

---

## 3. Ejemplos de Consulta

### A. Vía cURL (Terminal / Consola)
```bash
curl -X GET "http://localhost:8000/api/procesos/cedula/1098765432" \
     -H "Accept: application/json"
```

### B. Vía JavaScript (`fetch` / Navegador)
```javascript
const cedula = '1098765432';

fetch(`http://localhost:8000/api/procesos/cedula/${cedula}`, {
    headers: {
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    if (data.status === 'success') {
        console.log(`Encontrados ${data.total_procesos} procesos para la cédula ${data.cedula}`);
        data.data.forEach(proceso => {
            console.log(`Radicado: ${proceso.numero_radicado} - Despacho: ${proceso.despacho}`);
            console.log(`Total Actuaciones: ${proceso.total_actuaciones}`);
            proceso.actuaciones.forEach(act => {
                console.log(`  - [${act.fecha_actuacion}] ${act.actuacion}: ${act.anotacion}`);
            });
        });
    } else {
        console.warn(data.message);
    }
})
.catch(error => console.error('Error en la petición:', error));
```

### C. Vía PHP (cURL / Guzzle)
```php
<?php

$cedula = '1098765432';
$url = "http://localhost:8000/api/procesos/cedula/{$cedula}";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$resultado = json_decode($response, true);

if ($httpCode === 200 && $resultado['status'] === 'success') {
    foreach ($resultado['data'] as $proceso) {
        echo "Radicado: " . $proceso['numero_radicado'] . PHP_EOL;
        // ...
    }
} else {
    echo "Mensaje: " . ($resultado['message'] ?? 'Error de conexión');
}
```

### D. Vía Python (`requests`)
```python
import requests

cedula = "1098765432"
url = f"http://localhost:8000/api/procesos/cedula/{cedula}"
headers = {"Accept": "application/json"}

response = requests.get(url, headers=headers)
data = response.json()

if response.status_code == 200:
    print(f"Total procesos: {data['total_procesos']}")
    for proc in data['data']:
        print(f"Radicado: {proc['numero_radicado']} ({proc['ciudad']} - {proc['especialidad']})")
        for act in proc['actuaciones']:
            print(f"  [{act['fecha_actuacion']}] {act['actuacion']}")
else:
    print(f"Aviso: {data.get('message')}")
```

---

## 4. Estructura de Datos que Recibirás

### Respuesta Exitosa (`HTTP 200 OK`)

```json
{
  "status": "success",
  "message": "Procesos y actuaciones consultados correctamente.",
  "cedula": "1098765432",
  "total_procesos": 2,
  "data": [
    {
      "id": 15,
      "numero_radicado": "11001310300520230012300",
      "cedula": "1098765432",
      "ciudad": "BOGOTÁ D.C.",
      "especialidad": "CIVIL",
      "despacho": "JUZGADO 05 CIVIL DEL CIRCUITO DE BOGOTÁ",
      "ponente": "DRA. CLAUDIA MARCELA CASTRO",
      "tipo": "PROCESO EJECUTIVO",
      "clase": "EJECUTIVO SINGULAR",
      "recurso": "NINGUNO",
      "ubicacion_expediente": "SECRETARÍA",
      "demandantes": [
        "BANCO POPULAR S.A."
      ],
      "demandados": [
        "CARLOS ALBERTO GÓMEZ"
      ],
      "contenido_radicacion": "DEMANDA EJECUTIVA DE MAYOR CUANTÍA...",
      "metodo_consulta": "consulta_procesos",
      "estado": "completado",
      "error_mensaje": null,
      "fecha_registro": "2026-08-15 10:20:00",
      "ultima_actualizacion": "2026-09-08 22:30:15",
      "total_actuaciones": 3,
      "actuaciones": [
        {
          "id": 45,
          "proceso_id": 15,
          "fecha_actuacion": "2026-09-01",
          "actuacion": "AUTO DE LIQUIDACIÓN DE CRÉDITO",
          "anotacion": "SE APRUEBA LIQUIDACIÓN PRESENTADA POR LA PARTE DEMANDANTE",
          "fecha_inicia_termino": "2026-09-02",
          "fecha_finaliza_termino": "2026-09-05",
          "fecha_registro": "2026-09-01",
          "created_at": "2026-09-01 14:05:22",
          "updated_at": "2026-09-01 14:05:22"
        },
        {
          "id": 40,
          "proceso_id": 15,
          "fecha_actuacion": "2026-08-20",
          "actuacion": "NOTIFICACIÓN POR ESTADO",
          "anotacion": "ESTADO NRO. 045 FIJADO EN LA PÁGINA WEB",
          "fecha_inicia_termino": "2026-08-21",
          "fecha_finaliza_termino": "2026-08-23",
          "fecha_registro": "2026-08-20",
          "created_at": "2026-08-20 09:12:00",
          "updated_at": "2026-08-20 09:12:00"
        },
        {
          "id": 35,
          "proceso_id": 15,
          "fecha_actuacion": "2026-08-10",
          "actuacion": "MANDAMIENTO DE PAGO",
          "anotacion": "LIBRA MANDAMIENTO DE PAGO EN FAVOR DE LA DEMANDANTE",
          "fecha_inicia_termino": "2026-08-11",
          "fecha_finaliza_termino": "2026-08-15",
          "fecha_registro": "2026-08-10",
          "created_at": "2026-08-10 11:30:00",
          "updated_at": "2026-08-10 11:30:00"
        }
      ]
    },
    {
      "id": 12,
      "numero_radicado": "05001310500120220034500",
      "cedula": "1098765432",
      "ciudad": "MEDELLÍN",
      "especialidad": "LABORAL",
      "despacho": "JUZGADO 01 LABORAL DEL CIRCUITO DE MEDELLÍN",
      "ponente": "DR. JAIRO RESTREPO",
      "tipo": "ORDINARIO LABORAL",
      "clase": "PRIMERA INSTANCIA",
      "recurso": null,
      "ubicacion_expediente": "DESPACHO PARA FALLO",
      "demandantes": [
        "CARLOS ALBERTO GÓMEZ"
      ],
      "demandados": [
        "CONSTRUCTORA DEL VALLE S.A."
      ],
      "contenido_radicacion": "DEMANDA ORDINARIA LABORAL...",
      "metodo_consulta": "radicado_unificada",
      "estado": "completado",
      "error_mensaje": null,
      "fecha_registro": "2026-08-01 08:15:00",
      "ultima_actualizacion": "2026-08-25 18:40:10",
      "total_actuaciones": 1,
      "actuaciones": [
        {
          "id": 20,
          "proceso_id": 12,
          "fecha_actuacion": "2026-08-25",
          "actuacion": "FIJACIÓN FECHA DE AUDIENCIA",
          "anotacion": "AUDIENCIA DE TRÁMITE Y JUZGAMIENTO PARA EL DÍA 15/10/2026",
          "fecha_inicia_termino": null,
          "fecha_finaliza_termino": null,
          "fecha_registro": "2026-08-25",
          "created_at": "2026-08-25 18:40:10",
          "updated_at": "2026-08-25 18:40:10"
        }
      ]
    }
  ]
}
```

---

## 5. Descripción de los Campos Retornados

### Nivel Raíz (Respuesta General)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `status` | string | Estado general de la consulta (`success`, `not_found`, `error`). |
| `message` | string | Mensaje explicativo legible. |
| `cedula` | string | Número de cédula consultado. |
| `total_procesos` | int | Cantidad total de procesos encontrados para esta cédula. |
| `data` | array | Lista de todos los procesos (de más reciente a más antiguo). |

### Objeto `proceso` (Dentro de `data`)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | int | Identificador único del proceso en la base de datos. |
| `numero_radicado` | string | Número de radicado único (23 dígitos). |
| `cedula` | string | Cédula asociada a este radicado. |
| `ciudad` | string | Ciudad donde radica el proceso (ej: "BOGOTÁ D.C."). |
| `especialidad` | string | Especialidad jurídica (ej: "CIVIL", "LABORAL", "FAMILIA"). |
| `despacho` | string | Nombre completo del juzgado o tribunal. |
| `ponente` | string | Juez o magistrado ponente del caso. |
| `tipo` | string | Tipo de proceso judicial (ej: "EJECUTIVO"). |
| `clase` | string | Clase de proceso (ej: "DECLARATIVO"). |
| `recurso` | string | Recurso interpuesto si aplica. |
| `ubicacion_expediente` | string | Ubicación física o digital del expediente. |
| `demandantes` | array | Lista de nombres de las partes demandantes. |
| `demandados` | array | Lista de nombres de las partes demandadas. |
| `contenido_radicacion` | string | Resumen o contenido inicial de la radicación. |
| `metodo_consulta` | string | Modo empleado (`consulta_procesos` o `radicado_unificada`). |
| `estado` | string | Estado de la consulta (`pendiente`, `procesando`, `completado`, `error`). |
| `error_mensaje` | string | Mensaje de error si la consulta al portal falló (o `null`). |
| `fecha_registro` | string | Fecha y hora en que se cargó el proceso al sistema (`Y-m-d H:i:s`). |
| `ultima_actualizacion` | string | Fecha y hora de la última actualización del proceso (`Y-m-d H:i:s`). |
| `total_actuaciones` | int | Cantidad de actuaciones registradas en este radicado. |
| `actuaciones` | array | Lista ordenada de actuaciones (de más reciente a más antigua). |

### Objeto `actuacion` (Dentro de `actuaciones`)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | int | Identificador único de la actuación. |
| `proceso_id` | int | ID del proceso al que pertenece. |
| `fecha_actuacion` | string | Fecha en que el juzgado emitió la actuación (`Y-m-d`). |
| `actuacion` | string | Nombre o título de la actuación judicial. |
| `anotacion` | string | Texto descriptivo o resumen de la providencia. |
| `fecha_inicia_termino` | string | Fecha de inicio de términos procesales (`Y-m-d` o `null`). |
| `fecha_finaliza_termino` | string | Fecha de vencimiento de términos procesales (`Y-m-d` o `null`). |
| `fecha_registro` | string | Fecha en que el juzgado registró la actuación (`Y-m-d` o `null`). |
| `created_at` | string | Fecha y hora de creación del registro en el sistema. |
| `updated_at` | string | Fecha y hora de actualización en el sistema. |

---

## 6. Respuestas de Error

### Cédula no encontrada (`HTTP 404 Not Found`)
Si no hay radicados registrados para la cédula enviada:
```json
{
  "status": "not_found",
  "message": "No se encontraron procesos judiciales asociados a la cédula '999999999'.",
  "cedula": "999999999",
  "total_procesos": 0,
  "data": []
}
```

### Cédula vacía (`HTTP 400 Bad Request`)
Si se envía el parámetro vacío o solo con espacios en blanco:
```json
{
  "status": "error",
  "message": "Debe proporcionar un número de cédula válido.",
  "cedula": "",
  "total_procesos": 0,
  "data": []
}
```
