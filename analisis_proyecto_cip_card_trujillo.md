# 📋 Análisis Completo del Sistema — CIP CARD Trujillo

> **Framework:** Laravel 12 (PHP 8.2+) | **Frontend:** Blade + Tailwind CSS + Vite | **Auth:** Laravel Breeze + Spatie Permission

---

## 🗺️ Visión General

El sistema **CIP CARD Trujillo** es un portal web institucional para el **Centro de Arbitraje y Resolución de Disputas del Colegio de Ingenieros del Perú — Consejo Departamental de La Libertad (CARD-CIP-CDLL)**. Está compuesto por tres grandes áreas:

1. **Portal Web Público** — Información institucional, comunicados, eventos, calculadoras de tarifas.
2. **Mesa de Partes Virtual** — Módulo para que usuarios (clientes) registren, hagan seguimiento y gestionen sus expedientes de arbitraje y JRD.
3. **Panel de Administración / Gestión de Contenido** — Backend para administradores y gestores de contenido.

---

## 🧱 Stack Tecnológico y Dependencias

| Componente | Tecnología |
|---|---|
| Framework Backend | Laravel 12 |
| PHP mínimo | 8.2 |
| Frontend / Templates | Blade + Tailwind CSS |
| Bundler | Vite |
| Autenticación | Laravel Breeze |
| Roles y Permisos | Spatie Laravel Permission v6 |
| Generación PDF | barryvdh/laravel-dompdf |
| Procesamiento de imágenes | intervention/image v3 |
| Códigos QR | chillerlan/php-qrcode + simplesoftwareio/simple-qrcode + endroid/qr-code |
| Envío de correos | Resend (resend/resend-laravel) |
| Conversión PDF→imagen | spatie/pdf-to-image |

---

## 👥 Roles del Sistema

El sistema usa **Spatie Permission** para el control de acceso basado en roles:

| Rol | Acceso |
|---|---|
| `admin` | Panel de administración completo: gestión de arbitrajes, JRDs, usuarios, solicitudes de repositorio, etapas |
| `gestor_contenido` | Módulo de gestión de contenido: publicaciones, comunicados, eventos, organizacion, documentos, calculadoras, tarifas |
| `mesa_partes` | Mesa de partes: registrar arbitrajes y JRDs, ver sus expedientes, casilla electrónica |
| *(usuario autenticado)* | Perfil propio |

**Middleware de control:** [`CheckRole.php`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Middleware/CheckRole.php) — Valida el rol en la tabla `roles` de Spatie antes de acceder a cada grupo de rutas.

---

## 🗄️ Modelos de Datos (Eloquent)

### Usuarios y Personas

| Modelo | Tabla | Descripción |
|---|---|---|
| [`User`](file:///c:/Proyectos/cip_card_trujillo/app/Models/User.php) | `users` | Modelo de autenticación. Usa `HasRoles` (Spatie). Tiene SoftDeletes. Relación 1:1 con `Persona`. |
| [`Persona`](file:///c:/Proyectos/cip_card_trujillo/app/Models/Persona.php) | `persona` | Datos personales del usuario (DNI, correo de contacto). Linked a `User` por `user_id`. |

### Contenido Web

| Modelo | Tabla | Descripción |
|---|---|---|
| [`Publicacion`](file:///c:/Proyectos/cip_card_trujillo/app/Models/Publicacion.php) | `publicaciones` | Publicaciones con sección, título, descripción y estado activo. Tiene muchos `DetallePublicacion`. |
| [`DetallePublicacion`](file:///c:/Proyectos/cip_card_trujillo/app/Models/DetallePublicacion.php) | `detalle_publicaciones` | Imágenes/detalles de una publicación (grupo: principal, galería). |
| [`Comunicado`](file:///c:/Proyectos/cip_card_trujillo/app/Models/Comunicado.php) | `comunicados` | Comunicados institucionales con estado activo/inactivo. |
| [`Evento`](file:///c:/Proyectos/cip_card_trujillo/app/Models/Evento.php) | `eventos` | Eventos con fecha, activo. Tiene muchos `DetalleEvento`. |
| [`DetalleEvento`](file:///c:/Proyectos/cip_card_trujillo/app/Models/DetalleEvento.php) | `detalle_eventos` | Imágenes de un evento (principal, galería). |
| [`OrganizacionCard`](file:///c:/Proyectos/cip_card_trujillo/app/Models/OrganizacionCard.php) | `organizacion_card` | Tarjetas de miembros organizacionales (árbitros, adjudicadores, directivos). Tiene campos: `grupo`, `orden`, `cargo`, `especialidad`, `ruta_imagen`, `ruta_cv`. |
| [`Documentacion`](file:///c:/Proyectos/cip_card_trujillo/app/Models/Documentacion.php) | `documentos` | Documentos institucionales con sección, categoría, fecha de publicación. |
| [`TarifaEscala`](file:///c:/Proyectos/cip_card_trujillo/app/Models/TarifaEscala.php) | `tarifas_escalas` | Escalas de tarifas para calculadoras (arbitraje y JPRD). Tipos: `arbitro_unico`, `tribunal_arbitral`, `gastos_administrativos`. |
| [`TarifaConfiguracion`](file:///c:/Proyectos/cip_card_trujillo/app/Models/TarifaConfiguracion.php) | `tarifas_configuraciones` | Config global (ej: porcentaje IGV). |

### Mesa de Partes — Arbitraje

| Modelo | Tabla | Descripción |
|---|---|---|
| [`Arbitraje`](file:///c:/Proyectos/cip_card_trujillo/app/Models/Arbitraje.php) | `arbitraje` | Expediente de arbitraje. Tiene `user_id`, `numero_expediente`, `estado`, `tipo_arbitraje` (normal/emergencia). Relaciones: `procesos`, `personas`, `user`. |
| [`ProcesoDeArbitraje`](file:///c:/Proyectos/cip_card_trujillo/app/Models/ProcesoDeArbitraje.php) | `proceso_arbitrajes` | Etapa activa de un arbitraje. Estados: `iniciado`, `finalizado`. Linked a `EtapaArbitral`. |
| [`ProcesoArbitrajePersona`](file:///c:/Proyectos/cip_card_trujillo/app/Models/ProcesoArbitrajePersona.php) | `proceso_arbitraje_personas` | Partes del arbitraje (Demandante, Demandado). Campos: `dni`, `nombres_apellidos`, `razon_social`, `correo`, `tipo`. |
| [`ProcesoArbitrajeDocumento`](file:///c:/Proyectos/cip_card_trujillo/app/Models/ProcesoArbitrajeDocumento.php) | `proceso_arbitraje_documentos` | Documentos adjuntos (voucher, escrito, otro). Linked a `User` por `user_id`. |
| [`EtapaArbitral`](file:///c:/Proyectos/cip_card_trujillo/app/Models/EtapaArbitral.php) | `etapas_arbitrales` | Catálogo de etapas configurables del proceso arbitral. |

### Mesa de Partes — JRD (Junta de Resolución de Disputas)

| Modelo | Tabla | Descripción |
|---|---|---|
| [`Jrd`](file:///c:/Proyectos/cip_card_trujillo/app/Models/Jrd.php) | `jrd` | Expediente de JRD. Tiene `user_id`, `numero_expediente`, `estado`. Relaciones: `procesos`, `personas`, `procesoActivo`. |
| [`ProcesoJrd`](file:///c:/Proyectos/cip_card_trujillo/app/Models/ProcesoJrd.php) | `proceso_jrd` | Etapa activa de un JRD. Estados: `activo`, `finalizado`, `observado`. Linked a `EtapaJrd`. |
| [`ProcesoJrdPersona`](file:///c:/Proyectos/cip_card_trujillo/app/Models/ProcesoJrdPersona.php) | `proceso_jrd_personas` | Partes del JRD (Solicitante, Emplazado). |
| [`ProcesoJrdDocumento`](file:///c:/Proyectos/cip_card_trujillo/app/Models/ProcesoJrdDocumento.php) | `proceso_jrd_documentos` | Documentos adjuntos al JRD (voucher, escrito, otro). |
| [`EtapaJrd`](file:///c:/Proyectos/cip_card_trujillo/app/Models/EtapaJrd.php) | `etapas_jrd` | Catálogo de etapas configurables del proceso JRD. |

### Casilla Electrónica y Repositorio

| Modelo | Tabla | Descripción |
|---|---|---|
| [`CasillaElectronica`](file:///c:/Proyectos/cip_card_trujillo/app/Models/CasillaElectronica.php) | `casilla_electronica` | Notificaciones internas. Tiene: `user_id`, `emisor_id`, `arbitraje_id`, `jrd_id`, `asunto`, `comentario`, `estado` (no leido/leido). |
| [`SolicitudRepositorio`](file:///c:/Proyectos/cip_card_trujillo/app/Models/SolicitudRepositorio.php) | `solicitudes_repositorio` | Solicitudes de acceso al repositorio de laudos (DNI + foto). Estados: `pendiente`, `aprobado`, `rechazado`. |

### Módulo Eventos CIPCDLL

| Modelo | Tabla | Descripción |
|---|---|---|
| [`PadronCipcdll`](file:///c:/Proyectos/cip_card_trujillo/app/Models/PadronCipcdll.php) | `padron_cipcdll` | Padrón de colegiados (CIP, DNI, nombres, capítulo). |
| [`AsistenteCipcdll`](file:///c:/Proyectos/cip_card_trujillo/app/Models/AsistenteCipcdll.php) | `asistentes_cipcdll` | Asistentes registrados a eventos. Estado: `registrado`, `aprobado`, `rechazado`. |
| [`AsistenteFinalCipcdll`](file:///c:/Proyectos/cip_card_trujillo/app/Models/AsistenteFinalCipcdll.php) | `asistentes_final_cipcdll` | Tabla final de asistentes confirmados para el envío de tarjetas. |

---

## 🌐 Portal Web Público

### Rutas y Controlador Principal [`PageController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/PageController.php)

| Método | Ruta | Función | Vista |
|---|---|---|---|
| `welcome()` | `GET /` | Página de inicio. Carga: popup activo, slider principal, próximos eventos (hasta 4). | `welcome.blade.php` |
| `misionVision()` | `GET /mision-vision` | Vista estática de misión y visión. | `contenido/mision-vision` |
| `presentacion()` | `GET /presentacion` | Carga última publicación activa de sección `presentacion` + documento adjunto. | `contenido/presentacion` |
| `comunicados()` | `GET /comunicados` | Lista paginada (12/página) de comunicados activos. | `contenido/comunicados` |
| `eventos()` | `GET /eventos` | Lista de eventos: evento destacado (más próximo) + lista paginada del resto. | `contenido/eventos` |
| `detalleEvento($id)` | `GET /detalle-evento/{id}` | Detalle de un evento con galería de imágenes. | `contenido/detalle-evento` |
| `organoDireccion()` | `GET /organo-direccion` | Lista miembros de los 4 grupos organizacionales + documento resolución. | `contenido/organo-direccion` |
| `organoDecision()` | `GET /organo-decision` | Lista miembros del órgano de decisión. | `contenido/organo-decision` |
| `organoGestionSecretaria()` | `GET /organo-gestion-secretaria` | Lista miembros de Secretaría General, Arbitral y Técnica. | `contenido/organo-gestion_secretaria` |
| `organigrama()` | `GET /organigrama` | Vista estática del organigrama. | `contenido/organigrama` |
| `certificaciones()` | `GET /certificaciones` | Documentos de sección `certificaciones`. | `contenido/certificaciones` |
| `politicas()` | `GET /politicas` | Documentos de sección `politicas`. | `contenido/politicas` |
| `licencias()` | `GET /licencias` | Vista estática de licencias. | `contenido/licencias` |
| `contactos()` | `GET /contactos` | Vista estática de contactos. | `contenido/contactos` |
| `arbitral()` | `GET /arbitral` | Cláusula arbitral. | `contenido/clausulas/arbitral` |
| `juntaResDisputas()` | `GET /junta-res-disputas` | Cláusula JRD. | `contenido/clausulas/junta-res-disputas` |
| `disputeReview()` | `GET /dispute-review` | Cláusula Dispute Review. | `contenido/clausulas/dispute-review` |
| `disputeAvoidanceRes()` | `GET /dispute-avoidance-res` | Cláusula Dispute Avoidance. | `contenido/clausulas/dispute-avoidance-res` |
| `convocatoria()` | `GET /convocatoria` | Documentos de convocatorias activas y vigentes. | `contenido/convocatoria` |

### Institución Arbitral — Subpáginas

| Método | Ruta | Función |
|---|---|---|
| `institucionArbitral()` | `GET /institucion-arbitral` | Vista general de la institución arbitral. |
| `arbitralNormativa()` | `GET /inst/normativa` | Documentos de categoría `normativa` sección `institucion`. |
| `arbitralNomina()` | `GET /inst/nomina` | Nómina de árbitros: documentos + tarjetas OrganizacionCard grupo `arbitros-nomina`. |
| `arbitralIncorporacion()` | `GET /inst/requisitos-incorporacion` | Documentos de requisitos e incorporación. |
| `arbitralTarifario()` | `GET /inst/tarifario` | Documentos de tarifario. |
| `arbitralSolicitar()` | `GET /inst/solicitar` | Formularios de solicitud descargables. |
| `arbitralRepositorio()` | `GET /inst/repositorio` | Repositorio de laudos (documentos informativos). |

### Junta de Prevención y Resolución de Disputas — Subpáginas

| Método | Ruta | Función |
|---|---|---|
| `juntaPrevencion()` | `GET /junta-prevencion` | Vista general JPRD. |
| `juntaPrevencionNormativa()` | `GET /jun-prev/normativa` | Normativa JPRD sección `junta`. |
| `juntaPrevencionNomina()` | `GET /jun-prev/nomina` | Nómina adjudicadores + documentos. |
| `juntaPrevencionIncorporacion()` | `GET /jun-prev/requisitos-incorporacion` | Requisitos JPRD. |
| `juntaPrevencionTarifario()` | `GET /jun-prev/tarifario` | Tarifario JPRD. |
| `juntaPrevencionSolicitar()` | `GET /jun-prev/solicitar` | Formularios de solicitud JPRD. |
| `juntaPrevencionRepositorio()` | `GET /jun-prev/repositorio` | Repositorio JPRD. |

### Calculadoras de Tarifas

| Método | Ruta | Descripción |
|---|---|---|
| `calculadoraArbitraje()` | `GET /calculadora/arbitraje/ver` | Carga escalas activas de `servicio_arbitral` + IGV. Renderiza la calculadora interactiva. |
| `calcJunta()` | `GET /calculadora/junta/calc` | Carga escalas activas de `junta_prevencion` + IGV. Renderiza calculadora JPRD. |
| `exportarPdfArbitraje()` | `POST /calculadora/arbitrajec/pdf` | Recibe `monto`, `tipo_cuantia`, `tipo_organo`, `cantidad_pretensiones`. Calcula honorarios y gastos. Genera y descarga PDF (DomPDF). |
| `exportarPdfJunta()` | `POST /calculadora/juntap/pdf` | Recibe `monto`, `tipo_miembro`. Calcula tasa administrativa + honorarios. Genera y descarga PDF. |

### Gestión de Documentos Públicos

| Controlador | Ruta | Función |
|---|---|---|
| [`DocumentoController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/DocumentoController.php) | `GET /documentos/ver/{filename}` | Muestra un documento en el navegador (PDF inline). |
| [`DocumentoController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/DocumentoController.php) | `GET /documentos/descargar/{filename}` | Fuerza la descarga de un archivo. |

### Solicitud de Acceso al Repositorio (pública)

| Controlador | Ruta | Función |
|---|---|---|
| [`RepoSolicitudController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/RepoSolicitudController.php) | `POST /solicitudes-repo` | Usuario envía solicitud con nombre, email, DNI y foto del DNI. Verifica duplicados. Guarda en BD. |

---

## 🏢 Mesa de Partes Virtual (rol: `mesa_partes`)

Todas las rutas del siguiente bloque tienen el prefijo `/mesa-partes` y requieren autenticación + rol `mesa_partes`.

### Dashboard del Usuario

| Controlador | Ruta | Función | Vista |
|---|---|---|---|
| [`DashboardController::index()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/DashboardController.php) | `GET /mesa-partes/dashboard` | Carga estadísticas del usuario: conteo de arbitrajes (total, pendientes, en revisión, concluidos) y JRDs (pendientes, en revisión, concluidos). Busca expedientes donde el usuario es titular O parte involucrada. | `mesa-partes/dashboard` |

#### Métodos privados de `DashboardController`

| Método | Descripción |
|---|---|
| `calcularJrdPendientes($jrds)` | Filtra JRDs en estados: `validando`, `pendiente`, `iniciado`. |
| `calcularJrdEnRevision($jrds)` | Filtra JRDs en estados: `en proceso`, `en revision`. |
| `calcularJrdConcluidos($jrds)` | Filtra JRDs en estados: `terminado`, `concluido`, `finalizado`, `completado`. |
| `obtenerEstadisticasJrd($id)` | Retorna estadísticas detalladas de un JRD (% de avance, proceso actual). |
| `obtenerConteoPorEstado()` | Agrupa todos los JRDs del usuario por estado normalizado. |
| `obtenerProcesosActivos()` | Lista el proceso actual de cada JRD activo del usuario. |

### Información del Usuario

| Controlador | Ruta | Función |
|---|---|---|
| [`PersonaController::actualizar()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/PersonaController.php) | `GET /mesa-partes/actualizar` | Formulario para ver/actualizar datos personales. |
| [`PersonaController::update()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/PersonaController.php) | `PUT /mesa-partes/persona/update` | Actualiza los datos personales del usuario. |
| [`PersonaController::store()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/PersonaController.php) | `POST /mesa-partes/persona/store` | Crea registro de Persona si aún no existe para el usuario. |
| [`PersonaController::buscarPorUsuario()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/PersonaController.php) | `GET /mesa-partes/persona/buscar` | Busca y retorna la Persona vinculada al usuario autenticado (JSON). |

### Módulo Arbitraje (Cliente)

| Controlador | Ruta | Función | Vista |
|---|---|---|---|
| `static view` | `GET /mesa-partes/arbitraje` | Vista del formulario de registro de arbitraje. | `mesa-partes/arbitraje` |
| [`ArbitrajeRegistroController::store()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/ArbitrajeRegistroController.php) | `POST /mesa-partes/arbitraje/registrar` | **Registro completo de arbitraje** (transacción atómica): 1) Genera número de expediente (`EXP 001-YYYY-ARB-CARD-CIP-CDLL`), 2) Crea Arbitraje, 3) Registra personas (Demandante/Demandado), 4) Crea primer ProcesoDeArbitraje en primera EtapaArbitral activa, 5) Sube voucher y escrito (storage public), 6) Guarda enlace Drive opcional. Estado inicial: `validando`. | — (JSON) |
| [`ArbitrajeController::registros()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/ArbitrajeController.php) | `GET /mesa-partes/arbitraje/registros` | Lista todos los arbitrajes del usuario autenticado. | `mesa-partes/RegistrosArbitraje` |
| [`ArbitrajeController::obtenerArbitrajes()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/ArbitrajeController.php) | `GET /mesa-partes/arbitraje/obtener` | API JSON: lista arbitrajes del usuario con procesos y documentos. |
| [`ProcesoArbitrajeDocumentoController::store()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/ProcesoArbitrajeDocumentoController.php) | `POST /mesa-partes/arbitraje/{id_arbitraje}/documentos2` | Subir documento adicional al proceso de arbitraje (desde mesa partes). |

#### Flujo de estados del Arbitraje

```
validando → iniciado → en proceso → terminado
                  ↘ observado (voucher rechazado)
                  ↘ archivado
```

### Módulo JRD — Junta de Resolución de Disputas (Cliente)

| Controlador | Ruta | Función | Vista |
|---|---|---|---|
| `static view` | `GET /mesa-partes/jrd` | Formulario de registro de JRD. | `mesa-partes/jrd` |
| [`JrdRegistroController::store()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/JrdRegistroController.php) | `POST /mesa-partes/jrd` | **Registro completo de JRD** (transacción): 1) Busca primera EtapaJrd activa, 2) Genera número de expediente (`EXP 001-YYYY-JPRD-CARD-CIP-CDLL`), 3) Crea JRD, 4) Crea ProcesoJrd inicial en estado `activo`, 5) Registra personas (Solicitante/Emplazado), 6) Sube voucher, escrito y link Drive. | — (JSON) |
| [`JrdController::misJrd()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/JrdController.php) | `GET /mesa-partes/jrd/mis-jrd` | API JSON: lista JRDs del usuario con procesos, documentos y etapas. |
| [`JrdController::obtenerJrdMesaPartes()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/JrdController.php) | `GET /mesa-partes/jrd/obtener/mesapartes` | API JSON: obtiene JRDs del usuario para la vista de seguimiento. |
| `static view` | `GET /mesa-partes/registros-jrd` | Vista de registros de JRD. | `mesa-partes/RegistrosJRD` |
| [`JrdDocumentoController::storeMesaPartes()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/JrdDocumentoController.php) | `POST /mesa-partes/jrd/{id}/documentos/mesapartes` | Subir documento adicional al proceso JRD (desde mesa partes). |

#### Flujo de estados del JRD

```
en proceso (inicial) → [Admin valida voucher] → siguiente etapa → ... → terminado
                  ↘ observado (voucher rechazado)
                  ↘ archivado
```

### Casilla Electrónica (Bandeja de Notificaciones)

| Controlador | Ruta | Función |
|---|---|---|
| [`CasillaElectronicaController::index()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CasillaElectronicaController.php) | `GET /mesa-partes/casilla` | Lista las notificaciones del usuario (enviadas por el admin al aprobar/rechazar vouchers, avanzar etapas). |
| `show()` | `GET /mesa-partes/casilla/{id}` | Ver el detalle de una notificación específica. |
| `destroy()` | `DELETE /mesa-partes/casilla/{id}` | Eliminar una notificación. |

---

## ⚙️ Panel de Administración (rol: `admin`)

### Dashboard Admin

| Ruta | Vista |
|---|---|
| `GET /admin/dashboard` | `Admin/dashboard` |

### Gestión de Arbitrajes (Admin)

Controlador: [`AdminArbitrajeController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/AdminArbitrajeController.php)

| Método | Ruta | Descripción |
|---|---|---|
| `index()` | `GET /admin/arbitraje` | Vista principal de arbitrajes admin. |
| `obtenerTodos()` | `GET /arbitrajes/obtener` | **API JSON completa**: lista todos los arbitrajes con personas, procesos, etapas y documentos. Permite filtro por DNI. Determina el rol de quién subió cada documento (Demandante/Demandado/Admin). |
| `detalle($id)` | `GET /arbitrajes/{id}/detalle` | Vista de detalle completo de un arbitraje (procesos ordenados desc). |
| `aceptar($id)` | `POST /arbitrajes/{id}/aceptar` | Acepta arbitraje: cambia estado a `iniciado`, crea primer ProcesoDeArbitraje en primera EtapaArbitral activa (si no existe). Notifica al titular. |
| `rechazar($req, $id)` | `POST /arbitrajes/{id}/rechazar` | Rechaza arbitraje: cambia estado a `observado`, agrega observación al voucher. Notifica al titular con motivo. |
| `archivar($req, $id)` | `POST /arbitrajes/{id}/archivar` | Archiva el arbitraje: estado `archivado`, agrega fecha de finalización. Notifica a involucrados. |
| `pasarSiguienteProceso($req, $id)` | `POST /arbitraje/{id_arbitraje}/siguiente-proceso` | Finaliza el proceso actual e inicia el siguiente según el orden de EtapasArbitrales activas. Si no hay más etapas, termina el arbitraje. Notifica a todos los involucrados. |

#### Método privado auxiliar

| Método | Descripción |
|---|---|
| `determinarRolSubidor($doc, $arbitraje)` | Determina si el documento fue subido por el Demandante, Demandado o Administrador, comparando el DNI del usuario con las personas del arbitraje. |

### Gestión de JRD (Admin)

Controlador: [`AdminJrdController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/AdminJrdController.php)

| Método | Ruta | Descripción |
|---|---|---|
| `index()` | `GET /jrd` | Vista principal de JRDs (Admin). |
| `obtenerJrd($req)` | `GET /jrd/obtener` | **API JSON completa**: lista todos los JRDs con personas, procesos, etapas y documentos. Permite filtro por DNI. |
| `detalle($id)` | `GET /jrd/{id}` | Vista detalle de un JRD con etapas activas disponibles. |
| `obtenerUno($id)` | `GET /jrd/obtener/{id}` | API JSON: datos completos de un JRD individual. |
| `aceptarVoucher($req, $id_jrd)` | `POST /jrd/{id_jrd}/voucher/aceptar` | Acepta el voucher: finaliza proceso activo, avanza a siguiente etapa JRD. Notifica al titular. Si es la última etapa, finaliza el JRD. |
| `rechazarVoucher($req, $id_jrd)` | `POST /jrd/{id_jrd}/voucher/rechazar` | Rechaza voucher con motivo: JRD pasa a `observado`, proceso activo a `observado`. Notifica al titular. |
| `archivar($req, $id_jrd)` | `POST /jrd/{id_jrd}/archivar` | Archiva el JRD. Notifica a involucrados. |

### Etapas del Proceso (Admin)

| Controlador | Ruta | Función |
|---|---|---|
| [`EtapaArbitralController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/EtapaArbitralController.php) | `GET /admin/arbitraje/etapas` | Lista etapas arbitrales configuradas. |
| | `POST /admin/arbitraje/etapas` | Crear nueva etapa arbitral. |
| | `PUT /admin/arbitraje/etapas/{id}` | Editar etapa arbitral. |
| | `DELETE /admin/arbitraje/etapas/{id}` | Eliminar etapa arbitral. |
| | `GET /admin/arbitraje/etapas/toggle/{id}` | Activar/Desactivar etapa. |
| [`EtapaJrdController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/EtapaJrdController.php) | `GET /admin/jrd/etapas` | Lista etapas JRD configuradas. |
| | `POST /admin/jrd/etapas` | Crear nueva etapa JRD. |
| | `PUT /admin/jrd/etapas/{id}` | Editar etapa JRD. |
| | `DELETE /admin/jrd/etapas/{id}` | Eliminar etapa JRD. |
| | `GET /admin/jrd/etapas/toggle/{id}` | Activar/Desactivar etapa JRD. |

### Procesos de Arbitraje (API)

Controlador: [`ProcesoDeArbitrajeController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/ProcesoDeArbitrajeController.php)

| Método | Ruta | Descripción |
|---|---|---|
| `index($id)` | `GET /procesos/arbitraje/{id_arbitraje}` | Lista todos los procesos de un arbitraje. |
| `show($id)` | `GET /procesos/{id_proceso}` | Detalle de un proceso específico. |
| `pasarSiguiente($req, $id)` | `POST /procesos/{id_proceso}/siguiente` | Avanza al siguiente proceso. |
| `crearPrimerProceso($req, $id)` | `POST /procesos/arbitraje/{id_arbitraje}/primer-proceso` | Crea el proceso inicial de un arbitraje. |
| `obtenerProcesoActivo($id)` | `GET /procesos/arbitraje/{id_arbitraje}/activo` | Retorna el proceso activo de un arbitraje. |
| `obtenerConEtapas($id)` | `GET /procesos/arbitraje/{id_arbitraje}/completo` | Retorna todos los procesos con sus etapas. |

### Procesos JRD (API)

Controlador: [`JrdProcesoController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/JrdProcesoController.php)

| Método | Ruta | Descripción |
|---|---|---|
| `pasarSiguienteProceso($req, $id)` | `POST /jrd/{id_jrd}/proceso/siguiente` | Avanza al siguiente proceso JRD. |
| `crearProceso($req, $id)` | `POST /jrd/{id_jrd}/proceso/crear` | Crea un nuevo proceso para un JRD. |
| `actualizarEstadoProceso($req, $id_jrd, $id)` | `POST /jrd/{id_jrd}/proceso/{id_proceso}/actualizar` | Actualiza el estado de un proceso JRD. |

### Documentos (Admin)

| Controlador | Ruta | Descripción |
|---|---|---|
| [`ProcesoArbitrajeDocumentoController::store()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/ProcesoArbitrajeDocumentoController.php) | `POST /arbitraje/{id_arbitraje}/documentos` | Subir documento al proceso de arbitraje (desde admin). |
| [`ProcesoArbitrajeDocumentoController::comentar()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/ProcesoArbitrajeDocumentoController.php) | `PUT /documentos/{id}/comentar` | Agregar/modificar observación en un documento. |
| [`JrdDocumentoController::store()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/JrdDocumentoController.php) | `POST /jrd/{id_jrd}/documentos` | Subir documento al proceso JRD (desde admin). |
| [`VoucherController::procesar()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/VoucherController.php) | `POST /arbitrajes/{id_arbitraje}/voucher/{id_documento}/procesar` | Procesar (aceptar/rechazar) voucher de un documento específico. |

### Gestión de Permisos — Solicitudes de Repositorio

Controlador: [`RepoSolicitudController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/RepoSolicitudController.php) — Prefijo: `/admin/gestion-permisos`

| Método | Ruta | Descripción |
|---|---|---|
| `index()` | `GET /` | Lista solicitudes con filtro por estado y búsqueda. |
| `show($id)` | `GET /{id}` | Ver detalle de una solicitud con la foto del DNI. |
| `updateState($req, $id)` | `PUT /{id}` | Cambiar estado: `aprobado`, `rechazado`, `pendiente`. |
| `destroy($id)` | `DELETE /{id}` | Eliminar solicitud + foto del storage. |

### Gestión de Usuarios

Controlador: [`UsuariosController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/UsuariosController.php) — Resource: `admin-usuarios`

| Método | Descripción |
|---|---|
| `index()` | Lista usuarios activos con sus roles y persona. Permite filtro por rol y búsqueda por nombre/DNI. |
| `create()` | Formulario de creación con todos los roles disponibles. |
| `store()` | Crea User + asigna rol + crea Persona vinculada (con DNI). |
| `edit($id)` | Formulario de edición. |
| `update($req, $id)` | Actualiza email, contraseña (opcional) y sincroniza rol. |
| `destroy($id)` | Desactivación lógica: `activo = 0` (no elimina físicamente). |

---

## 📝 Gestor de Contenido (rol: `gestor_contenido`)

Acceso desde `GET /gestion-contenido` → Vista principal del gestor.

### Publicaciones

Controlador: [`PublicacionController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/PublicacionController.php) — Resource: `publicaciones`

| Método | Descripción |
|---|---|
| `index()` | Lista paginada (10/página). Filtros: sección, estado, mes, año. |
| `create()` | Formulario de creación. |
| `store()` | Guarda publicación + procesa imágenes con Intervention Image (convierte a WebP). Soporta: imagen principal + galería. |
| `edit($id)` | Formulario de edición con los detalles actuales. |
| `update()` | Actualiza publicación + gestiona cambios en la galería. |
| `destroy()` | Elimina publicación y sus detalles/archivos. |
| `toggleEstado()` | `PUT /gestor/publicaciones/{id}/estado` — Alterna estado activo/inactivo. |

**Secciones de publicaciones:** `inicio_popup`, `inicio_slider`, `presentacion`, y otras.

### Comunicados

Controlador: [`ComunicadoController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/ComunicadoController.php) — Resource: `gestor/comunicados`

| Método | Descripción |
|---|---|
| `index()` | Lista paginada de comunicados. |
| `create()` / `store()` | Crear comunicado. |
| `edit()` / `update()` | Editar comunicado. |
| `destroy()` | Eliminar comunicado. |
| `toggleEstado()` | Activar/desactivar comunicado. |

### Eventos

Controlador: [`EventoController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/EventoController.php) — Resource: `gestor/eventos`

| Método | Descripción |
|---|---|
| `index()` | Lista paginada de eventos. |
| `create()` / `store()` | Crear evento con fecha e imágenes. |
| `edit()` / `update()` | Editar evento. |
| `destroy()` | Eliminar evento. |
| `toggleEstado()` | Activar/desactivar evento. |

### Organización (Tarjetas de Miembros)

Controlador: [`OrganizacionCardController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/OrganizacionCardController.php) — Resource: `gestor/organizacion`

| Método | Descripción |
|---|---|
| `index()` | Lista miembros por grupo. |
| `create()` / `store()` | Crear tarjeta de miembro con foto y CV. |
| `edit()` / `update()` | Editar datos de miembro. |
| `destroy()` | Eliminar miembro. |
| `toggleEstado()` | Activar/desactivar miembro. |

**Grupos disponibles:** `organo_direccion`, `organo_decision`, `organo_gestion_secretaria_general`, `organo_gestion_secretaria_arbitral`, `organo_gestion_secretaria_tecnica`, `arbitros-nomina`, `adjudicadores-nomina`.

### Documentación

Controlador: [`DocumentacionController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/DocumentacionController.php) — Resource: `gestor/documentos`

| Método | Descripción |
|---|---|
| `index()` | Lista documentos con filtros (sección, categoría). |
| `create()` / `store()` | Subir documento (PDF) con sección, categoría y fecha de publicación. |
| `edit()` / `update()` | Editar metadatos del documento. |
| `destroy()` | Eliminar documento. |
| `toggleEstado()` | Activar/desactivar documento (controla visibilidad pública). |

**Secciones/Categorías:** `presentacion`, `organizacion`, `certificaciones`, `politicas`, `convocatorias`, `institucion`, `junta` / `normativa`, `tarifario`, `incorporacion`, `requisitos`, `solicitar`, `repositorio`.

### Calculadoras

Controlador: [`CalculadoraController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CalculadoraController.php) — Resource: `gestor/calculadoras`

CRUD para gestionar las escalas de tarifas (`TarifaEscala`) que alimentan las calculadoras públicas.

### Tarifas de Configuración

Controlador: [`TarifaConfiguracionController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/TarifaConfiguracionController.php) — Resource: `gestor/tarifas_config`

CRUD para valores de configuración globales (como el porcentaje de IGV).

---

## 🎫 Módulo de Eventos CIPCDLL (Sistema de Asistencia con QR)

Sistema independiente con sesión propia (no usa auth de Laravel). Acceso por `/login-eventos`.

### Flujo de Registro

1. **Validación**: Ingresa CIP + DNI → verifica contra el padrón (`PadronCipcdll`)
2. **Registro**: Si es válido, registra asistente (máx. aforo: 316 personas)
3. **Estado inicial**: `registrado`

### Rutas y Funciones

| Controlador | Ruta | Descripción |
|---|---|---|
| [`AuthController::login()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/AuthController.php) | `POST /login-eventos` | Login con credenciales de sesión. |
| [`AuthController::logout()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/AuthController.php) | `POST /logout-eventos` | Cierre de sesión. |
| [`CipcdllController::verificarEvento()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CipcdllController.php) | `GET /eventoscipcdll` | Muestra el formulario público de registro al evento. |
| [`CipcdllController::validarCipDni()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CipcdllController.php) | `POST /validar-cip-dni` | Valida CIP + DNI contra el padrón. Retorna nombres y capítulo. |
| [`CipcdllController::registrarAsistente()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CipcdllController.php) | `POST /registrar-asistente` | Registra asistente (verifica aforo y duplicados). |
| [`CipcdllController::cambiarEstado()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CipcdllController.php) | `POST /cambiar-estado` | Cambia estado de un asistente (registrado→aprobado/rechazado). |
| [`CipcdllController::listarTodosAgrupados()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CipcdllController.php) | `GET /asistentes` | Lista todos los asistentes agrupados con resumen. |
| [`CipcdllController::listarPorEstado()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CipcdllController.php) | `GET /asistentes/{estado}` | Filtra asistentes por estado. |
| [`CipcdllController::verPendientes()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CipcdllController.php) | `GET /ver-pendientes` | Lista asistentes pendientes. |
| [`CipcdllController::verAprobados()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CipcdllController.php) | `GET /ver-aprobados` | Lista asistentes aprobados. |
| [`CipcdllController::verRechazados()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CipcdllController.php) | `GET /ver-rechazados` | Lista asistentes rechazados. |
| [`CipcdllController::validarAsistenciaBatch()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/CipcdllController.php) | `POST /validar-asistencias-batch` | Valida múltiples asistencias en lote (optimizado). |

### Control de Acceso por QR

| Controlador | Ruta | Descripción |
|---|---|---|
| [`AsistenciaQrController::buscarPorDni()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/AsistenciaQrController.php) | `POST /buscar-por-dni` | Busca asistente por DNI. |
| [`AsistenciaQrController::buscarPorDniGet()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/AsistenciaQrController.php) | `GET /buscar-por-dni/{dni}` | Busca asistente por DNI (GET). Usado desde lectura de QR. |
| [`AsistenciaQrController::marcarAsistenciaQr()`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/AsistenciaQrController.php) | `POST /marcar-asistencia-qr/{dni}` | Marca asistencia cuando se escanea el QR en el evento. |

### Envío de Tarjetas Digitales

Controlador: [`EnviarTarjetaController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/EnviarTarjetaController.php)

| Método | Ruta | Descripción |
|---|---|---|
| `enviarATodos()` | `POST /enviar-tarjetas` | Envío masivo por lotes (hasta 50/lote) via **Resend API**. Verifica carpeta de tarjetas y envía correo con adjunto a cada asistente. |
| `enviarPorCip()` | `GET /enviar-tarjeta/{cip}` | Envía tarjeta a un asistente individual por CIP. |

Controlador: [`AsistentasCipcdllFinalController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/AsistentasCipcdllFinalController.php)

| Método | Ruta | Descripción |
|---|---|---|
| `index()` | `GET /envio-tarjetas` | Vista de gestión de envío de tarjetas. |
| `generarTarjetas()` | `POST /generar-tarjetas` | Genera las tarjetas digitales (imágenes) para los asistentes aprobados. |

### Vistas del Módulo CIPCDLL

| Vista | Descripción |
|---|---|
| `eventoscipcdll/login.blade.php` | Login del sistema de eventos |
| `eventoscipcdll/eventoscipcdll.blade.php` | Formulario público de inscripción |
| `eventoscipcdll/asistencia.blade.php` | Panel de control de asistencia con lector QR |
| `eventoscipcdll/validacion.blade.php` | Vista de validación de asistentes |
| `eventoscipcdll/dashboard.blade.php` | Dashboard del operador del evento |
| `eventoscipcdll/envio-tarjetas.blade.php` | Gestión de envío de tarjetas |
| `eventoscipcdll/evento-lleno.blade.php` | Página mostrada cuando se agota el aforo |

---

## 🔔 Servicio de Notificaciones

Clase: [`NotificacionService`](file:///c:/Proyectos/cip_card_trujillo/app/Services/NotificacionService.php)

Inserta registros en la tabla `casilla_electronica` (bandeja de entrada del usuario).

| Método | Descripción |
|---|---|
| `notificarInvolucrados($registro, $tipo, $asunto, $comentario)` | Notifica a **todas las partes** de un arbitraje/JRD (busca sus DNIs, los cruza con la tabla `persona` para obtener `user_id`). No notifica al emisor. Usa bulk insert. |
| `notificarTitular($registro, $tipo, $asunto, $comentario)` | Notifica **solo al dueño** del trámite (`user_id` del registro). No notifica al emisor. |

**Triggers de notificación:**

| Evento | Tipo | Notificados |
|---|---|---|
| Arbitraje aceptado | Titular | Solo el titular |
| Arbitraje rechazado/observado | Titular | Solo el titular |
| Arbitraje archivado | Involucrados | Todas las partes |
| Arbitraje avanza a siguiente etapa | Involucrados | Todas las partes |
| Arbitraje finalizado | Involucrados | Todas las partes |
| JRD - Voucher aprobado | Titular | Solo el titular |
| JRD - Voucher rechazado | Titular | Solo el titular |
| JRD - Avanza etapa | Involucrados | Todas las partes |
| JRD - Finalizado | Involucrados | Todas las partes |
| JRD - Archivado | Involucrados | Todas las partes |

---

## 🗺️ Mapa de Vistas (Blade Templates)

### Portal Público
```
resources/views/
├── welcome.blade.php              ← Página de inicio
├── layouts/
│   ├── app.blade.php              ← Layout autenticado
│   ├── guest.blade.php            ← Layout invitado
│   └── navigation.blade.php      ← Barra de navegación
└── contenido/
    ├── mision-vision.blade.php
    ├── presentacion.blade.php
    ├── comunicados.blade.php
    ├── eventos.blade.php
    ├── detalle-evento.blade.php
    ├── organigrama.blade.php
    ├── organo-direccion.blade.php
    ├── organo-decision.blade.php
    ├── organo-gestion_secretaria.blade.php
    ├── certificaciones.blade.php
    ├── politicas.blade.php
    ├── licencias.blade.php
    ├── contactos.blade.php
    ├── convocatoria.blade.php
    ├── mision-vision.blade.php
    ├── clausulas/
    │   ├── arbitral.blade.php
    │   ├── junta-res-disputas.blade.php
    │   ├── dispute-review.blade.php
    │   └── dispute-avoidance-res.blade.php
    ├── servicios/
    │   ├── institucion-arbitral.blade.php
    │   ├── junta-prevencion.blade.php
    │   ├── arbitral/
    │   │   ├── normativa.blade.php
    │   │   ├── nomina-arbitros.blade.php
    │   │   ├── requisitos.blade.php
    │   │   ├── tarifario.blade.php
    │   │   ├── solicitar.blade.php
    │   │   └── laudos.blade.php
    │   └── jrd/
    │       ├── normativa.blade.php
    │       ├── nomina-adjudicadores.blade.php
    │       ├── requisitos.blade.php
    │       ├── tarifario.blade.php
    │       ├── solicitar.blade.php
    │       └── laudos.blade.php
    └── calculadoras/
        ├── arbitraje.blade.php
        ├── junta.blade.php
        ├── pdf-arbitraje.blade.php    ← Template PDF
        └── pdf-junta.blade.php        ← Template PDF
```

### Gestor de Contenido
```
resources/views/gestion-contenido/
├── main.blade.php                 ← Dashboard del gestor
├── publicaciones/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── comunicados/
├── eventos/
├── organizacion-card/
├── documentacion/
├── calculadoras/
└── tarifas_config/
```

### Mesa de Partes
```
resources/views/mesa-partes/
├── app.blade.php                  ← Layout de la mesa de partes
├── login.blade.php
├── register.blade.php
├── dashboard.blade.php            ← Dashboard del usuario
├── actualizar.blade.php           ← Actualizar datos personales
├── arbitraje.blade.php            ← Formulario de registro de arbitraje
├── jrd.blade.php                  ← Formulario de registro de JRD
├── RegistrosArbitraje.blade.php   ← Lista de arbitrajes del usuario
├── RegistrosJRD.blade.php         ← Lista de JRDs del usuario
├── mesa-virtual.blade.php         ← Mesa virtual
└── casilla/                       ← Casilla electrónica
```

### Panel de Administración
```
resources/views/Admin/
├── app.blade.php                  ← Layout admin
├── dashboard.blade.php
├── Arbitraje.blade.php            ← Lista arbitrajes admin
├── arbitraje-detalle.blade.php    ← Detalle arbitraje admin
├── crear-arbitraje.blade.php
├── Jrd.blade.php                  ← Lista JRDs admin
├── Jrd-detalle.blade.php          ← Detalle JRD admin
├── crear-jrd.blade.php
├── etapa-arbitral.blade.php
├── solicitudes-repo/
│   ├── index.blade.php
│   └── show.blade.php
└── usuarios/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

### Módulo CIPCDLL
```
resources/views/eventoscipcdll/
├── layout.blade.php
├── login.blade.php
├── dashboard.blade.php
├── asistencia.blade.php
├── validacion.blade.php
├── envio-tarjetas.blade.php
├── eventoscipcdll.blade.php       ← Formulario público inscripción
└── evento-lleno.blade.php
```

---

## 🔒 Flujo de Autenticación

El sistema tiene **dos sistemas de autenticación paralelos**:

### 1. Laravel Auth (principal)
- Usa **Laravel Breeze** con middleware `auth` de Illuminate.
- Rutas en `routes/auth.php`.
- Control de acceso por roles Spatie (`checkrole:admin`, `checkrole:mesa_partes`, `checkrole:gestor_contenido`).

### 2. Sesión de Eventos CIPCDLL (secundario)
- Sistema propio basado en `session('usuario')`.
- Login/logout via [`AuthController`](file:///c:/Proyectos/cip_card_trujillo/app/Http/Controllers/AuthController.php).
- Verificación manual en cada ruta de eventos.

---

## 📊 Generación de Expedientes

El sistema genera números de expediente automáticos con correlativo anual:

| Tipo | Formato |
|---|---|
| Arbitraje normal | `EXP 001-2025-ARB-CARD-CIP-CDLL` |
| Arbitraje emergencia | `EXP 001-2025-ARBEME-CARD-CIP-CDLL` |
| JRD | `EXP 001-2025-JPRD-CARD-CIP-CDLL` |

El correlativo se calcula con `lockForUpdate()` para evitar duplicados en concurrencia.

---

## 🔧 Archivos de Diagnóstico (solo desarrollo)

El archivo `web.php` contiene rutas de diagnóstico activas que **no deberían estar en producción**:

| Ruta | Propósito |
|---|---|
| `GET /sherlock-holmes` | Busca archivos `.webp` en todo el proyecto y verifica permisos de escritura. |
| `GET /php-check` | Verifica extensiones PHP activas (GD, upload sizes). |
| `GET /fix-permissions-final` | Ejecuta `chmod -R 777` en storage (Linux). |
| `GET /debug-mesa-error` | Instancia el DashboardController y ejecuta `index()` para capturar errores 500. |

> [!CAUTION]
> Estas rutas de diagnóstico son un riesgo de seguridad en producción. Se recomienda eliminarlas o protegerlas con middleware de autenticación de admin.

---

## 📁 Base de Datos

El proyecto incluye archivos SQL de respaldo en `database/`:

| Archivo | Descripción |
|---|---|
| `base.sql` | Estructura base de la BD |
| `bd_cip_card (1).sql` | Versión con datos |
| `3-12_Contenido_cip_card_trujillo.sql` | Contenido actualizado al 12 de marzo |

### Migraciones formales

| Migración | Tabla |
|---|---|
| `create_users_table` | `users` |
| `create_cache_table` | `cache` |
| `create_jobs_table` | `jobs` |
| `create_permission_tables` | Tablas de Spatie Permission |
| `create_arbitrajes_table` | `arbitraje` |
| `create_proceso_arbitrajes_table` | `proceso_arbitrajes` |
| `create_proceso_arbitraje_personas_table` | `proceso_arbitraje_personas` |
| `create_proceso_arbitraje_documentos_table` | `proceso_arbitraje_documentos` |

> [!NOTE]
> Muchas tablas del sistema (jrd, jrd_procesos, casilla_electronica, padron_cipcdll, etc.) están en los archivos SQL pero **no tienen migraciones Laravel**. La base de datos se gestiona principalmente por los archivos `.sql`.

---

*Documento generado automáticamente — CIP CARD Trujillo — Sistema de Gestión Institucional*
