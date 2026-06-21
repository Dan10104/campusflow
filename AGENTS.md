# CampusFlow — Reglas obligatorias para Codex

## Objetivo general

Rediseñar únicamente la interfaz frontend de CampusFlow y Nexora Tech, conservando intacta toda la funcionalidad existente.

## Tecnologías actuales

* Laravel 12
* Inertia.js
* Vue 3
* Vite
* Bootstrap
* Tailwind CSS
* SCSS

## Archivos y carpetas permitidos

Codex puede analizar todo el proyecto, pero solo puede modificar:

* resources/js/
* resources/css/
* resources/sass/
* resources/views/app.blade.php
* public/assets/
* tailwind.config.js
* vite.config.js
* package.json, únicamente cuando sea estrictamente necesario y con autorización previa

## Archivos y carpetas prohibidos

No modificar bajo ninguna circunstancia:

* app/
* routes/
* database/
* config/
* bootstrap/
* tests/
* composer.json
* composer.lock
* artisan
* .env
* .env.example
* storage/
* archivos JSON de Firebase
* controladores
* modelos
* migraciones
* seeders
* servicios
* middleware
* rutas web o API
* lógica de autenticación
* base de datos

## Secretos

* No abrir, copiar, mostrar ni modificar el archivo `.env`.
* No abrir, copiar, mostrar ni modificar credenciales de Firebase.
* No incluir credenciales en resúmenes, código, comentarios ni respuestas.

## Reglas funcionales

* Preservar todos los `route(...)`.
* Preservar nombres de rutas.
* Preservar propiedades recibidas mediante Inertia.
* Preservar nombres de formularios y campos.
* Preservar métodos POST, PUT, PATCH y DELETE.
* Preservar permisos dinámicos del menú.
* Preservar autenticación, cierre de sesión y CSRF.
* Preservar slots y eventos existentes.
* No eliminar funciones porque parezcan innecesarias.
* No instalar dependencias sin autorización.
* No actualizar versiones de paquetes.
* No modificar la lógica del sistema.

## Reglas de diseño

La nueva identidad visual debe corresponder a:

* Empresa: Nexora Tech
* Producto: CampusFlow
* Estilo: plataforma SaaS universitaria moderna
* Apariencia: profesional, tecnológica, académica y limpia

Paleta principal:

* Azul marino: #0B1F3A
* Azul principal: #2563EB
* Cian: #06B6D4
* Fondo claro: #F5F7FB
* Superficie: #FFFFFF
* Texto principal: #0F172A
* Texto secundario: #64748B
* Bordes: #E2E8F0

Usar variables CSS para colores, superficies, bordes, sombras y modo oscuro.

## Validación obligatoria

Después de cada modificación:

1. Ejecutar `npm run build`.
2. No ignorar errores de compilación.
3. Informar los archivos modificados.
4. Explicar qué se cambió.
5. Explicar qué funciones se preservaron.
6. No continuar con otra fase sin autorización.
