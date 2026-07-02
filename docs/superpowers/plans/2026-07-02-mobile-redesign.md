# Agrogreen Mobile Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans.

**Goal:** Redesign mobile app with offline SQLite, cuarteles support, AgroGreen branding

**Architecture:** Offline-first with expo-sqlite local DB, FIFO sync queue, NetInfo connectivity detection. Backend gets sync endpoint for batch attendance upload.

**Tech Stack:** Expo SDK 54, expo-sqlite, @react-native-community/netinfo, React Native 0.81.5, Laravel 11

---

### Task 1: Backend — Add Cuarteles API + Sync Endpoint

**Files:**
- Create: `app/Http/Controllers/Api/CuartelController.php`
- Modify: `routes/api.php`
- Create: `app/Http/Requests/Api/SyncAttendanceRequest.php`
- Modify: `app/Http/Controllers/Api/TarjetaController.php`

- [ ] Create CuartelController with `index()` returning id, nombre, centro_costo_id, especie_id
- [ ] Add route `GET /api/cuarteles` → CuartelController@index
- [ ] Create SyncAttendanceRequest with validation for array of attendance records
- [ ] Add `sync()` method to TarjetaController that accepts batch of attendance records, validates each, creates faenas_registro entries
- [ ] Add route `POST /api/asistencia/sync` → TarjetaController@sync

### Task 2: Mobile — Install expo-sqlite

- [ ] Run `npx expo install expo-sqlite`

### Task 3: Mobile — Create Local Database Service

**Files:**
- Create: `mobile/services/db.ts`

- [ ] Create `initDatabase()` — create tables: empleados, actividades, cuarteles, tarjetas, pending_attendance, sync_log
- [ ] Create CRUD functions: upsertEmpleados, upsertActividades, upsertCuarteles, upsertTarjetas
- [ ] Create getEmpleados, getActividades, getCuarteles, getTarjetas (read from local DB)
- [ ] Create addPendingAttendance, getPendingAttendance, removePendingAttendance
- [ ] Create getSyncLog, updateSyncLog

### Task 4: Mobile — Create Sync Service

**Files:**
- Create: `mobile/services/sync.ts`

- [ ] Create `syncAll()` — fetch all data from API (empleados, actividades, cuarteles, tarjetas) and upsert to local DB
- [ ] Create `syncPendingAttendance()` — push queued attendance records to API, remove from queue on success
- [ ] Create `startSyncOnReconnect()` — listen to NetInfo, trigger sync when online
- [ ] Export `syncOnStartup()` — called on app init if online

### Task 5: Mobile — Update API Service

**Files:**
- Modify: `mobile/services/api.ts`

- [ ] Add `getCuarteles()` function
- [ ] Add `syncAttendance(records[])` function for batch sync
- [ ] Update `registerAttendance` to return the record for queueing

### Task 6: Mobile — Redesign Splash Screen

**Files:**
- Modify: `mobile/app/_layout.tsx`
- Add: `mobile/assets/agrogreen-logo.png` (copy from public/)

- [ ] Copy logo from web public/ to mobile assets/
- [ ] Redesign loading screen: centered white logo on green gradient background
- [ ] Add "Agrogreen Mobile" subtitle below logo
- [ ] On token check complete, either show login or redirect to tabs

### Task 7: Mobile — Redesign Login Screen (AgroGreen Branding)

**Files:**
- Modify: `mobile/app/login.tsx`

- [ ] Full redesign: green gradient header (top 40% of screen) with white logo + "Agrogreen Mobile"
- [ ] White bottom section with email/password form
- [ ] Lora font for title, Raleway for inputs
- [ ] Green button #2E7D32
- [ ] Background warm off-white #FAFAF5

### Task 8: Mobile — Redesign Scanner Screen

**Files:**
- Modify: `mobile/app/(tabs)/scanner.tsx`

- [ ] Add green gradient header strip with "Escanear QR" title
- [ ] Camera fills remaining space
- [ ] Scan frame with corner accents (green)
- [ ] Instructions text below scan area
- [ ] Proper camera permission UI

### Task 9: Mobile — Create Cuarteles Selector Modal

**Files:**
- Create: `mobile/components/CuartelModal.tsx`

- [ ] Modal with FlatList of cuarteles (nombre, especie)
- [ ] Multi-select with checkboxes
- [ ] Search/filter by name
- [ ] Confirm button to return selected cuarteles
- [ ] AgroGreen styling consistent with other modals

### Task 10: Mobile — Update Attendance Flow with Cuarteles

**Files:**
- Modify: `mobile/app/(tabs)/scanner.tsx`
- Modify: `mobile/components/ActividadModal.tsx`
- Modify: `mobile/services/api.ts`

- [ ] Modify scanner flow: after selecting actividad, show CuartelModal
- [ ] Pass cuarteles IDs to registerAttendance (or queue for offline)
- [ ] Update registerAttendance API call to include cuarteles_ids

### Task 11: Mobile — Create "Cierre Jornada" Screen

**Files:**
- Create: `mobile/app/(tabs)/cierre.tsx`
- Modify: `mobile/app/(tabs)/_layout.tsx`

- [ ] Add third tab "Cierre" with icon
- [ ] Cierre screen shows today's attendance records
- [ ] Option to "cerrar" (close) each activity with cuarteles
- [ ] Pending sync indicator (offline queue count)

### Task 12: Mobile — Redesign Account Screen

**Files:**
- Modify: `mobile/app/(tabs)/historial.tsx`

- [ ] Rename to "Cuenta"
- [ ] Green gradient header
- [ ] User info card with AgroGreen styling
- [ ] Sync status indicator
- [ ] Logout button

### Task 13: Mobile — App Icon and Config

**Files:**
- Copy: `public/agrogreen-logo.png` → `mobile/assets/icon.png` (need resize)
- Modify: `mobile/app.json`

- [ ] Generate proper app icon (1024x1024) from logo
- [ ] Update app.json: icon, adaptive icon background color, name "Agrogreen Mobile"
- [ ] Update splash config if needed
