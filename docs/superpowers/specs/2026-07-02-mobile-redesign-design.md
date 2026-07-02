# Agrogreen Mobile — Rediseño Offline-First

## Objetivo
Rediseñar la app móvil de AgroGreen con soporte offline, branding corporativo (verde #2E7D32), y flujo completo de asistencia con cuarteles.

## Arquitectura
- Offline-first con SQLite local vía `expo-sqlite`
- Caché local de empleados, actividades, cuarteles, tarjetas
- Cola FIFO de asistencias pendientes de sincronizar
- Sync automático al recuperar conexión (NetInfo)

## Flujo de Asistencia
1. Escanear QR → verificar asignación de tarjeta
2. Si no asignada → seleccionar empleado → asignar
3. Seleccionar actividad
4. Seleccionar 1+ cuarteles (checkboxes)
5. Registrar asistencia (local + encolar si offline)

## Pantallas
- Splash: Logo + "Agrogreen Mobile" + loader
- Login: Logo + email/password + fondo degradado verde
- Escáner: Cámara + marco verde + overlay branding
- Cierre Jornada: Lista de asistencias del día con opción a cerrar

## Diseño Visual
- Header degradado: `#1B5E20 → #2E7D32 → #388E3C`
- Tipografía: Lora (títulos), Raleway (cuerpo)
- Fondo: `#FAFAF5`
- Botones: `#2E7D32`, texto sin mayúsculas, weight 600
- Icono: Logo AgroGreen

## API
- `GET /api/cuarteles` — listar cuarteles (ya existe)
- `POST /api/asistencia/sync` — sync batch de asistencias offline
- `POST /api/tarjetas/asignar` — asignar tarjeta (ya existe)
- `POST /api/asistencia/registrar` — registrar asistencia (ya existe)
