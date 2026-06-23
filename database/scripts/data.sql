INSERT INTO `facultades` (`codigo`, `nombre`, `sigla`, `created_at`, `updated_at`) VALUES
(1, 'Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones', 'FICCT', '2026-04-01 09:00:00', '2026-04-01 09:00:00'),
(2, 'Facultad de Ciencias Económicas y Empresariales', 'FCEE', '2026-04-01 09:00:00', '2026-04-01 09:00:00'),
(3, 'Facultad de Humanidades', 'FCH', '2026-04-01 09:00:00', '2026-04-01 09:00:00');

INSERT INTO `modulos` (`codigo`, `nombre`, `ubicacion`, `facultad_codigo`, `created_at`, `updated_at`) VALUES
(1, 'Módulo 236', 'Zona Norte - Campus', 1, '2026-04-01 09:00:00', '2026-04-01 09:00:00'),
(2, 'Módulo 214', 'Zona Norte - Campus', 1, '2026-04-01 09:00:00', '2026-04-01 09:00:00'),
(3, 'Módulo Administrativo', 'Bloque Central', 2, '2026-04-01 09:00:00', '2026-04-01 09:00:00');

INSERT INTO `roles` (`codigo`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(2, 'Docente', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(3, 'Estudiante', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(4, 'Encargado de Laboratorio', '2026-04-03 09:00:00', '2026-04-03 09:00:00');

INSERT INTO `permisos` (`codigo`, `nombre`, `descripcion`, `estado`, `icono`, `ruta`, `created_at`, `updated_at`) VALUES
(1, 'Inicio / Dashboard', 'Panel principal de control', 'activo', 'bi-speedometer2', 'dashboard', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(2, 'Usuarios', 'Gestión de usuarios del sistema', 'activo', 'bi-people-fill', 'users.index', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(3, 'Roles y Permisos', 'Administración de seguridad', 'activo', 'bi-shield-lock-fill', 'roles.index', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(4, 'Catálogo Activos', 'Catálogo general de activos', 'activo', 'bi-box-seam', 'activos.disponibles', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(6, 'Mis Reservas', 'Historial y estado de reservas', 'activo', 'bi-calendar-check', 'reservas.index', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(7, 'Mis Préstamos', 'Historial de préstamos de activos', 'activo', 'bi-clock-history', 'prestamos.index', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(11, 'Monitoreo IoT', 'Dashboard de sensores en tiempo real', 'activo', 'bi-cpu', 'iot.dashboard', '2026-04-03 10:00:00', '2026-04-03 10:00:00'),
(12, 'Centro Aprobaciones', 'Bandeja de solicitudes pendientes', 'activo', 'bi-check-circle-fill', 'admin.aprobaciones', '2026-04-03 10:00:00', '2026-04-03 10:00:00'),
(13, 'Reservar Aula', 'Formulario para nueva reserva', 'activo', 'bi-plus-circle-dotted', 'reservas.crear', '2026-04-03 10:00:00', '2026-04-03 10:00:00');

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (1, 2, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (1, 3, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (1, 4, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (1, 6, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (1, 11, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (1, 12, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (1, 13, '2026-04-03 10:00:00', '2026-04-03 10:00:00'),
(2, 1, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (2, 4, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (2, 6, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (2, 7, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (2, 11, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (2, 13, '2026-04-03 10:00:00', '2026-04-03 10:00:00'),
(3, 1, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (3, 4, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (3, 6, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (3, 7, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (3, 11, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (3, 13, '2026-04-03 10:00:00', '2026-04-03 10:00:00'),
(4, 1, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (4, 4, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (4, 6, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (4, 11, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (4, 12, '2026-04-03 10:00:00', '2026-04-03 10:00:00'), (4, 13, '2026-04-03 10:00:00', '2026-04-03 10:00:00');

INSERT INTO `politicas_liberacion` (`codigo`, `nivel`, `umbral_minutos`, `ventana_histeresis_seg`, `activo`, `creado_en`) VALUES
(1, 'General', 15, 300, 1, '2026-04-03 09:00:00'),
(2, 'Estricta', 10, 120, 1, '2026-04-03 09:00:00');

INSERT INTO `tipo_activo` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Proyector Portátil', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(2, 'Laptop', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(3, 'Kit de Robótica', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(4, 'Microscopio', '2026-04-03 09:00:00', '2026-04-03 09:00:00');

INSERT INTO `users` (`codigo`, `nombre`, `ci`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', '1234567', 'admin@uagrm.edu.bo', '2026-04-04 09:00:00', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-04-04 09:00:00', '2026-04-04 09:00:00'),
(2, 'Juan Perez Docente', '7654321', 'juan.perez@uagrm.edu.bo', '2026-04-04 09:00:00', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-04-04 09:00:00', '2026-04-04 09:00:00'),
(3, 'Maria Estudiante', '1122334', 'maria.est@uagrm.edu.bo', '2026-04-04 09:00:00', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-04-04 09:00:00', '2026-04-04 09:00:00'),
(4, 'Carlos Encargado', '9988776', 'carlos.enc@uagrm.edu.bo', '2026-04-04 09:00:00', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-04-04 09:00:00', '2026-04-04 09:00:00'),
(5, 'Administrador', '1234567890', 'admin@gmail.com', NULL, '$2y$12$E7zgIFowPfDEN4bzdvv8xODBIDxHzdD8VjXHhCh368KWHwfhgxove', NULL, '2026-04-04 10:00:00', '2026-04-04 10:00:00'),
(6, 'Usuario', '1234567890', 'usuario@gmail.com', NULL, '$2y$12$cqVx3htnbZqjRQjmXy9TnuDJW6j4oa1NXP0FiwzrfZ87svS0laG2y', NULL, '2026-04-04 10:05:00', '2026-04-04 10:05:00');

INSERT INTO `usuarios_roles` (`usuario_id`, `rol_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-04-04 09:00:00', '2026-04-04 09:00:00'),
(2, 2, '2026-04-04 09:00:00', '2026-04-04 09:00:00'),
(3, 3, '2026-04-04 09:00:00', '2026-04-04 09:00:00'),
(4, 4, '2026-04-04 09:00:00', '2026-04-04 09:00:00'),
(5, 1, '2026-04-04 10:00:00', '2026-04-04 10:00:00'),
(6, 2, '2026-04-04 10:05:00', '2026-04-04 10:05:00');

INSERT INTO `aulas` (`codigo`, `capacidad`, `tipo`, `estado`, `qr_code`, `modulo_codigo`, `created_at`, `updated_at`) VALUES
(1, 40, 'laboratorio', 'disponible', 'QR-AULA-001', 1, '2026-04-05 09:00:00', '2026-04-05 09:00:00'),
(2, 60, 'aula', 'disponible', 'QR-AULA-002', 1, '2026-04-05 09:00:00', '2026-04-05 09:00:00'),
(3, 100, 'auditorio', 'mantenimiento', 'QR-AUD-001', 2, '2026-04-05 09:00:00', '2026-04-05 09:00:00'),
(4, 30, 'sala_reuniones', 'bloqueada', 'QR-SALA-001', 3, '2026-04-05 09:00:00', '2026-04-05 09:00:00'),
(5, 45, 'laboratorio', 'disponible', 'QR-LAB-NET-2', 1, '2026-04-06 09:00:00', '2026-04-06 09:00:00'),
(6, 80, 'aula', 'ocupada', 'QR-AULA-MAGNA', 2, '2026-04-06 09:00:00', '2026-04-06 09:00:00'),
(7, 25, 'sala_reuniones', 'disponible', 'QR-SALA-DOC', 3, '2026-04-06 09:00:00', '2026-04-06 09:00:00'),
(8, 30, 'laboratorio', 'mantenimiento', 'QR-LAB-ROB', 1, '2026-04-06 09:00:00', '2026-04-06 09:00:00'),
(9, 60, 'aula', 'disponible', 'QR-AULA-236-12', 1, '2026-04-06 09:00:00', '2026-04-06 09:00:00');

INSERT INTO `caracteristicas_aula` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Proyector HDMI', '2026-04-05 09:00:00', '2026-04-05 09:00:00'),
(2, 'Aire Acondicionado', '2026-04-05 09:00:00', '2026-04-05 09:00:00'),
(3, 'Pizarra Inteligente', '2026-04-05 09:00:00', '2026-04-05 09:00:00'),
(4, 'Computadoras i7', '2026-04-05 09:00:00', '2026-04-05 09:00:00');

INSERT INTO `aula_caracteristicas` (`aula_codigo`, `caracteristica_id`) VALUES
(1, 2), (1, 4), (2, 1), (2, 2), (3, 1), (3, 2), (5, 2), (5, 4), (6, 1), (6, 2), (7, 2), (8, 3), (8, 4);

INSERT INTO `activos` (`codigo`, `descripcion`, `tipo_activo_id`, `estado`, `ubicacion_actual`, `qr_code`, `marca`, `modelo`, `created_at`, `updated_at`) VALUES
(1, 'Proyector Epson PowerLite', 1, 'disponible', 'Depósito FICCT', 'ACT-001', 'Epson', 'X41', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(2, 'Laptop Dell Latitude', 2, 'prestado', 'Laboratorio 1', 'ACT-002', 'Dell', '5420', '2026-04-03 09:00:00', '2026-06-23 08:05:00'),
(3, 'Kit Arduino Avanzado', 3, 'disponible', 'Depósito Electrónica', 'ACT-003', 'Arduino', 'Uno R3', '2026-04-03 09:00:00', '2026-06-20 09:00:00'),
(4, 'Proyector Sony', 1, 'mantenimiento', 'Soporte Técnico', 'ACT-004', 'Sony', 'VPL', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(5, 'MacBook Pro M1 - Desarrollo', 2, 'disponible', 'Depósito FICCT', 'ACT-MAC-01', 'Apple', 'MacBook Pro 13', '2026-04-06 09:00:00', '2026-04-06 09:00:00'),
(6, 'Tablet Samsung Galaxy Tab', 2, 'disponible', 'Laboratorio Móvil', 'ACT-TAB-01', 'Samsung', 'S7 FE', '2026-04-06 09:00:00', '2026-04-06 09:00:00'),
(7, 'Kit Raspberry Pi 4 Completo', 3, 'disponible', 'Armario A2', 'ACT-RPI-01', 'Raspberry', 'Pi 4B 8GB', '2026-04-06 09:00:00', '2026-06-21 09:00:00'),
(8, 'Proyector Epson Alta Luminosidad', 1, 'disponible', 'Sala de Juntas', 'ACT-EPS-02', 'Epson', 'PowerLite 900', '2026-04-06 09:00:00', '2026-04-06 09:00:00'),
(9, 'Cámara Canon EOS (Documentación)', 1, 'disponible', 'Comunicación', 'ACT-CAM-01', 'Canon', 'EOS Rebel', '2026-04-06 09:00:00', '2026-04-06 09:00:00'),
(10, 'Multímetro Digital Fluke', 3, 'mantenimiento', 'Taller Electrónica', 'ACT-FLU-01', 'Fluke', '117', '2026-04-06 09:00:00', '2026-04-06 09:00:00');

INSERT INTO `reservas` (`id`, `usuario_id`, `aula_codigo`, `inicio`, `fin`, `proposito`, `asistentes_estimados`, `estado`, `qr_code`, `creado_por`, `cancelado_por`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2026-04-20 07:00:00', '2026-04-20 09:00:00', 'Clase de Redes I', 35, 'completada', 'RES-001', 2, NULL, '2026-04-15 09:00:00', '2026-04-20 09:00:00'),
(2, 2, 2, '2026-07-15 10:00:00', '2026-07-15 12:00:00', 'Examen Parcial', 50, 'confirmada', 'RES-002', 2, NULL, '2026-06-20 10:00:00', '2026-06-20 10:00:00'),
(3, 3, 2, '2026-07-16 14:00:00', '2026-07-16 16:00:00', 'Ayudantía', 20, 'pendiente', 'RES-003', 3, NULL, '2026-06-22 10:00:00', '2026-06-22 10:00:00');

INSERT INTO `reservas_activos` (`id`, `usuario_id`, `activo_codigo`, `inicio_previsto`, `fin_previsto`, `entregado_en`, `devuelto_en`, `condicion_salida`, `condicion_retorno`, `estado`, `created_at`, `updated_at`) VALUES
(1, 2, 2, '2026-06-23 08:00:00', '2026-06-24 10:00:00', '2026-06-23 08:05:00', NULL, 'Bueno', NULL, 'entregado', '2026-06-20 09:00:00', '2026-06-23 08:05:00'),
(2, 2, 1, '2026-04-19 14:00:00', '2026-04-19 16:00:00', '2026-04-19 14:00:00', '2026-04-19 16:10:00', 'Bueno', 'Bueno', 'devuelto', '2026-04-16 09:00:00', '2026-04-19 16:10:00'),
(3, 1, 3, '2026-07-10 09:00:00', '2026-07-10 12:00:00', NULL, NULL, 'Buen estado (Solicitud)', NULL, 'pendiente', '2026-06-20 09:00:00', '2026-06-20 09:00:00'),
(4, 5, 7, '2026-07-17 09:00:00', '2026-07-17 12:00:00', NULL, NULL, 'Buen estado (Solicitud)', NULL, 'pendiente', '2026-06-21 09:00:00', '2026-06-21 09:00:00');

INSERT INTO `bloqueos_aula` (`codigo`, `aula_codigo`, `motivo`, `inicio`, `fin`, `created_at`, `updated_at`) VALUES
(1, 3, 'Mantenimiento de Aire Acondicionado', '2026-04-25 08:00:00', '2026-04-26 18:00:00', '2026-04-24 10:00:00', '2026-04-24 10:00:00');

INSERT INTO `checkins_reserva` (`id`, `reserva_id`, `tipo`, `registrado_en`, `origen`, `created_at`, `updated_at`) VALUES
(1, 1, 'checkin', '2026-04-20 07:05:00', 'qr', '2026-04-20 07:05:00', '2026-04-20 07:05:00'),
(2, 1, 'checkout', '2026-04-20 08:55:00', 'qr', '2026-04-20 08:55:00', '2026-04-20 08:55:00');

INSERT INTO `sensores` (`id`, `tipo`, `hardware_id`, `estado`, `ultimo_heartbeat`, `aula_codigo`, `bateria`, `senal`, `created_at`, `updated_at`) VALUES
(1, 'pir', 'ESP32-001', 'activo', '2026-06-22 10:00:00', 1, 100, -60, '2026-06-22 10:00:00', '2026-06-22 10:00:00'),
(2, 'pir', 'ESP32-002', 'activo', '2026-06-22 10:00:00', 2, 95, -55, '2026-06-22 10:00:00', '2026-06-22 10:00:00');

INSERT INTO `eventos_sensor` (`codigo`, `sensor_id`, `tipo_evento`, `registrado_en`, `payload_json`, `created_at`, `updated_at`) VALUES
(1, 1, 'movimiento', '2026-06-22 10:00:00', '{\"valor\": 1}', '2026-06-22 10:00:00', '2026-06-22 10:00:00');

INSERT INTO `notificaciones` (`codigo`, `usuario_id`, `tipo`, `canal`, `contenido`, `estado_envio`, `enviado_en`, `leida`, `creado_en`) VALUES
(1, 2, 'reserva_confirmada', 'email', 'Su reserva para el aula 1 ha sido confirmada.', 'enviado', '2026-04-15 09:02:00', 0, '2026-04-15 09:02:00');

INSERT INTO `bitacora` (`codigo`, `entidad`, `accion`, `usuario_id`, `detalle_json`, `ip`, `creado_en`) VALUES
(1, 'Reserva', 'Crear', 2, '{\"reserva_id\": 1, \"aula\": \"236\"}', '192.168.1.50', '2026-04-15 09:01:00');

INSERT INTO `asientos_blockchain` (`codigo`, `reserva_id`, `tx_id`, `hash`, `bloque`, `red`, `creado_en`) VALUES
(1, 1, '0x123abc...', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', '1520023', 'polygon', '2026-04-20 09:05:00');
