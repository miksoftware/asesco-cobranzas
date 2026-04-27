# Asesco Cobranzas — Integración con APIs EPS (incluida ADRES)

## Descripción general

Asesco Cobranzas es un **agregador web** que consulta de forma **concurrente** todos los sistemas EPS activos configurados en la plataforma. Para cada sistema consume el endpoint estándar de consulta por cédula y presenta el resultado más reciente en la interfaz.

---

## Flujo de consulta

```
Usuario ingresa cédula
        │
        ▼
ConsultaController@consultar
        │  valida: solo dígitos, 5–15 caracteres
        ▼
EpsConsultaService@consultarCedula
        │  lanza peticiones HTTP concurrentes a todos los sistemas activos
        ▼
  ┌──────────────────────────────────────┐
  │  Sistema 1 (ADRES)  ──► API ADRES   │
  │  Sistema 2 (Coosalud) ──► API …     │
  │  Sistema N …          ──► API …     │
  └──────────────────────────────────────┘
        │  procesa respuestas
        ▼
ConsultaResult::create()   ← guarda el registro más reciente en BD
        │
        ▼
JSON al frontend (Alpine.js) ← renderiza cards expandibles
```

---

## Endpoint estándar consumido

Cada sistema EPS expone el mismo contrato de API:

```
GET {base_url}{endpoint_path}
```

Donde `endpoint_path` por defecto es `/api/consulta/cedula/{cedula}`.

### Headers enviados

```http
Authorization: Bearer <api_token>
Accept: application/json
```

### Configuración por sistema (tabla `eps_systems`)

| Campo           | Descripción                                                   |
|-----------------|---------------------------------------------------------------|
| `name`          | Nombre legible del sistema (ej. `ADRES`)                      |
| `slug`          | Identificador único generado automáticamente                  |
| `base_url`      | URL base del sistema (ej. `https://adres.miksoftware.com`)    |
| `api_token`     | Token Bearer para autenticación (almacenado cifrado)          |
| `endpoint_path` | Ruta del endpoint. Default: `/api/consulta/cedula/{cedula}`   |
| `timeout`       | Tiempo máximo de espera en segundos (default: 15)             |
| `is_active`     | Si está activo se incluye en la consulta concurrente          |

---

## Formato de respuesta que espera Asesco de cada API EPS

Las APIs EPS retornan un **array** en `data`, ordenado del registro más reciente al más antiguo. Asesco extrae automáticamente el **primer elemento** (más reciente) para mostrarlo en la vista.

```json
{
  "success": true,
  "message": "Consulta exitosa.",
  "total": 3,
  "data": [
    { ... },
    { ... },
    { ... }
  ]
}
```

> **Nota:** Asesco utiliza `data[0]` para la vista del agregador. Si la API retorna un objeto único (formato legado), también se soporta.

### Respuesta 404 manejada

```json
{
  "success": false,
  "message": "No se encontraron resultados para la cédula proporcionada.",
  "data": null
}
```

Cuando el sistema retorna `404`, Asesco lo marca como **no encontrado** (sin error).

---

## Campos específicos por sistema EPS

### ADRES

| Campo               | Tipo          | Descripción                                  |
|---------------------|---------------|----------------------------------------------|
| `cedula`            | string        | Número de documento                          |
| `tipo_documento`    | string / null | Tipo de documento (`CC`, `TI`, etc.)         |
| `nombres`           | string / null | Nombres completos                            |
| `apellidos`         | string / null | Apellidos completos                          |
| `fecha_nacimiento`  | string / null | Fecha de nacimiento (`YYYY-MM-DD`)           |
| `departamento`      | string / null | Departamento de residencia                   |
| `municipio`         | string / null | Municipio de residencia                      |
| `estado_afiliacion` | string / null | Estado (`ACTIVO`, `RETIRADO`, etc.)          |
| `entidad_eps`       | string / null | Nombre de la EPS                             |
| `regimen`           | string / null | Régimen (`CONTRIBUTIVO`, `SUBSIDIADO`)       |
| `fecha_afiliacion`  | string / null | Fecha de afiliación                          |
| `fecha_finalizacion`| string / null | Fecha de retiro. `null` si activo            |
| `tipo_afiliado`     | string / null | `COTIZANTE`, `BENEFICIARIO`                  |
| `consultado_en`     | string ISO 8601 | Fecha/hora de la consulta (UTC)            |

### Coosalud

| Campo               | Tipo          | Descripción                                  |
|---------------------|---------------|----------------------------------------------|
| `cedula`            | string        | Número de documento                          |
| `tipo_documento`    | string / null | Tipo de documento                            |
| `primer_nombre`     | string / null | Primer nombre                                |
| `segundo_nombre`    | string / null | Segundo nombre                               |
| `primer_apellido`   | string / null | Primer apellido                              |
| `segundo_apellido`  | string / null | Segundo apellido                             |
| `nombre_completo`   | string / null | Nombre completo                              |
| `departamento`      | string / null | Departamento de residencia                   |
| `municipio`         | string / null | Municipio de residencia                      |
| `direccion`         | string / null | Dirección de residencia                      |
| `regimen`           | string / null | Régimen de salud                             |
| `estado_afiliado`   | string / null | Estado del afiliado                          |
| `sede`              | string / null | Sede asignada                                |
| `ips`               | string / null | IPS primaria asignada                        |
| `celular`           | string / null | Celular de contacto                          |
| `telefono_fijo`     | string / null | Teléfono fijo                                |
| `correo`            | string / null | Correo electrónico                           |
| `poblacion_especial`| string / null | Población especial                           |
| `grupo_etnico`      | string / null | Grupo étnico                                 |
| `consultado_en`     | string ISO 8601 | Fecha/hora de la consulta (UTC)            |

### Emssanar EPS

Mismos campos que Coosalud, más los siguientes adicionales:

| Campo             | Tipo           | Descripción                                |
|-------------------|----------------|--------------------------------------------|
| `eps_nombre`      | string / null  | Nombre oficial de la EPS                   |
| `fecha_nacimiento`| string / null  | Fecha de nacimiento (`YYYY-MM-DD`)         |
| `edad`            | integer / null | Edad en años                               |
| `sexo`            | string / null  | `M` o `F`                                  |

### Nueva EPS

| Campo                | Tipo          | Descripción                                |
|----------------------|---------------|--------------------------------------------|
| `cedula`             | string        | Número de documento                        |
| `tipo_documento`     | string / null | Tipo de documento                          |
| `primer_nombre`      | string / null | Primer nombre                              |
| `segundo_nombre`     | string / null | Segundo nombre                             |
| `primer_apellido`    | string / null | Primer apellido                            |
| `segundo_apellido`   | string / null | Segundo apellido                           |
| `sexo`               | string / null | `M` o `F`                                  |
| `celular`            | string / null | Celular de contacto                        |
| `telefono1`          | string / null | Teléfono adicional 1                       |
| `telefono2`          | string / null | Teléfono adicional 2                       |
| `correo_electronico` | string / null | Correo electrónico                         |
| `tipo_afiliado`      | string / null | `COTIZANTE`, `BENEFICIARIO`                |
| `regimen`            | string / null | Régimen de salud                           |
| `categoria`          | string / null | Categoría asignada (`A`, `B`, `C`)         |
| `ips_primaria`       | string / null | IPS primaria asignada                      |
| `departamento`       | string / null | Departamento de residencia                 |
| `municipio`          | string / null | Municipio de residencia                    |
| `consultado_en`      | string ISO 8601 | Fecha/hora de la consulta (UTC)          |

### Salud Total

| Campo                      | Tipo           | Descripción                                       |
|----------------------------|----------------|---------------------------------------------------|
| `cedula`                   | string         | Número de documento                               |
| `tipo_documento`           | string / null  | Tipo de documento                                 |
| `identificacion`           | string / null  | Identificación según portal Salud Total           |
| `nombres`                  | string / null  | Nombre completo                                   |
| `parentesco`               | string / null  | `COTIZANTE`, `BENEFICIARIO`                       |
| `estado_detallado`         | string / null  | Estado extendido (ej. `ACTIVO - COTIZANTE`)       |
| `fecha_nacimiento`         | string / null  | Fecha de nacimiento (`YYYY-MM-DD`)                |
| `edad`                     | integer / null | Edad en años                                      |
| `sexo`                     | string / null  | `M` o `F`                                         |
| `antiguedad_salud_total`   | string / null  | Tiempo en Salud Total (ej. `5 AÑOS 3 MESES`)      |
| `fecha_afiliacion`         | string / null  | Fecha de afiliación                               |
| `eps_anterior`             | string / null  | EPS de procedencia                                |
| `direccion`                | string / null  | Dirección de residencia                           |
| `telefono`                 | string / null  | Teléfono de contacto                              |
| `ciudad`                   | string / null  | Ciudad de residencia                              |
| `ips_medica_asignada`      | string / null  | IPS médica asignada                               |
| `ips_odontologica_asignada`| string / null  | IPS odontológica asignada                         |
| `contrato_empresa_nombre`  | string / null  | Empresa del cotizante                             |
| `consultado_en`            | string ISO 8601 | Fecha/hora de la consulta (UTC)                  |

### Servicio Occidental de Salud (SOS)

Campos principales del afiliado más dos sub-objetos anidados:

**Campos principales**

| Campo                   | Tipo            | Descripción                                   |
|-------------------------|-----------------|-----------------------------------------------|
| `cedula`                | string          | Número de documento                           |
| `tipo_id`               | string / null   | Tipo de documento                             |
| `primer_nombre`         | string / null   | Primer nombre                                 |
| `segundo_nombre`        | string / null   | Segundo nombre                                |
| `primer_apellido`       | string / null   | Primer apellido                               |
| `segundo_apellido`      | string / null   | Segundo apellido                              |
| `nombre_completo`       | string / null   | Nombre completo                               |
| `fecha_nacimiento`      | string / null   | Fecha de nacimiento (`YYYY-MM-DD`)            |
| `genero`                | string / null   | `M` o `F`                                     |
| `parentesco`            | string / null   | Relación con cotizante                        |
| `edad_anos`             | integer / null  | Años de edad                                  |
| `edad_meses`            | integer / null  | Meses complementarios                         |
| `edad_dias`             | integer / null  | Días complementarios                          |
| `rango_salarial`        | string / null   | Rango salarial (ej. `1-2 SMLV`)              |
| `tipo_afiliado`         | string / null   | `COTIZANTE`, `BENEFICIARIO`                   |
| `plan`                  | string / null   | Plan asignado (ej. `POS`)                     |
| `estado`                | string / null   | Estado del afiliado                           |
| `derecho`               | string / null   | `CON DERECHO` / `SIN DERECHO`                 |
| `inicio_vigencia`       | string / null   | Inicio de vigencia (`YYYY-MM-DD`)             |
| `fin_vigencia`          | string / null   | Fin de vigencia. `null` si activo             |
| `ips_primaria`          | string / null   | IPS primaria                                  |
| `semanas_pos_sos`       | integer / null  | Semanas POS en SOS                            |
| `semanas_pos_anterior`  | integer / null  | Semanas POS en EPS anterior                   |
| `semanas_pac_sos`       | integer / null  | Semanas PAC en SOS                            |
| `semanas_pac_anterior`  | integer / null  | Semanas PAC en EPS anterior                   |
| `paga_cuota_moderadora` | boolean / null  | Aplica cuota moderadora                       |
| `paga_copago`           | boolean / null  | Aplica copago                                 |
| `consultado_en`         | string ISO 8601 | Fecha/hora de la consulta (UTC)               |

**Sub-objeto `empleador`**

| Campo          | Tipo          | Descripción                   |
|----------------|---------------|-------------------------------|
| `tipo_id`      | string / null | Tipo de ID del empleador      |
| `numero_id`    | string / null | Número de ID del empleador    |
| `razon_social` | string / null | Razón social del empleador    |

**Sub-objeto `informacion_adicional`**

| Campo               | Tipo           | Descripción                     |
|---------------------|----------------|---------------------------------|
| `estado_civil`      | string / null  | Estado civil                    |
| `telefono`          | string / null  | Teléfono de contacto            |
| `direccion`         | string / null  | Dirección de residencia         |
| `barrio`            | string / null  | Barrio de residencia            |
| `ciudad_residencia` | string / null  | Ciudad de residencia            |
| `departamento`      | string / null  | Departamento de residencia      |
| `semanas_cotizadas` | integer / null | Total semanas cotizadas al SGSS |
| `afp`               | string / null  | Fondo de pensiones AFP          |

### Superargo

Mismos campos que Coosalud (ver tabla Coosalud arriba).

---

## Respuesta del agregador (`POST /consultas/consultar`)

```json
{
  "cedula": "1234567890",
  "total": 6,
  "found": 3,
  "results": [
    {
      "slug": "adres",
      "name": "ADRES",
      "found": true,
      "data": { "cedula": "1234567890", "nombres": "JUAN CARLOS", ... },
      "error": null
    },
    {
      "slug": "coosalud",
      "name": "Coosalud",
      "found": false,
      "data": null,
      "error": null
    },
    {
      "slug": "salud-total",
      "name": "Salud Total",
      "found": false,
      "data": null,
      "error": "Error de conexión: cURL error 28"
    }
  ]
}
```

| Campo     | Tipo    | Descripción                                              |
|-----------|---------|----------------------------------------------------------|
| `cedula`  | string  | Cédula consultada                                        |
| `total`   | integer | Total de sistemas consultados                            |
| `found`   | integer | Cantidad de sistemas donde se encontró al afiliado       |
| `results` | array   | Resultado por sistema (solo sistemas encontrados o error)|

---

## Notas técnicas

- Las peticiones a los sistemas EPS se realizan de forma **concurrente** usando `Http::pool()` de Laravel.
- Si una API no responde dentro del `timeout`, se registra un error de conexión.
- El campo `data` almacenado en `consulta_results` corresponde al **registro más reciente** retornado por la API (`data[0]`), ya que todas las APIs devuelven historial ordenado de más reciente a más antiguo.
- Los tokens de acceso de cada sistema EPS se almacenan **cifrados** en la base de datos.
