import { Head, router, useForm } from '@inertiajs/react';
import {
  Box, Typography, Button, Stepper, Step, StepLabel, TextField, MenuItem,
  Stack, Checkbox, FormControlLabel, Paper, Chip, Alert,
} from '@mui/material';
import { useState } from 'react';
import type { Cuartel, Variedad, ProductoSAG, Aplicador, EquipoAplicacion, Lote } from '@/components/agroquimicos/types';

interface Props {
  cuarteles: Cuartel[];
  variedades: Variedad[];
  productosSag: (ProductoSAG & { producto: { id: string; nombre: string }; usos: any[] })[];
  aplicadores: Aplicador[];
  equipos: EquipoAplicacion[];
  lotes: (Lote & { producto: { id: string; nombre: string } })[];
}

export default function AgroquimicosCreate({ cuarteles, variedades, productosSag, aplicadores, equipos, lotes }: Props) {
  const [activeStep, setActiveStep] = useState(0);

  const { data, setData, post, processing, errors, setError } = useForm({
    cuartel_id: '',
    variedad_id: '',
    temporada: '',
    superficie: '',
    fecha_aplicacion: new Date().toISOString().split('T')[0],
    hora_inicio: '',
    hora_termino: '',
    objetivo_tipo: 'plaga',
    objetivo_nombre: '',
    aplicador_id: '',
    supervisor_id: '',
    equipo_id: '',
    observaciones: '',
    estado: 'ejecutada',
    productos: [{ producto_sag_id: '', lote_id: '', dosis: '', unidad_dosis: 'L/ha', cantidad_total: '', volumen_agua: '' }],
    weather: { temperatura: '', humedad: '', viento_velocidad: '', viento_direccion: '', estado_general: '', riesgo_deriva: '', fuente: 'manual' },
    safety: { epp_guantes: false, epp_mascarilla: false, epp_overol: false, epp_botas: false, epp_proteccion_ocular: false, senalizacion_instalada: false, agua_emergencia: null, observaciones: '' },
    envases: [{ producto_sag_id: '', envases_usados: '', capacidad_envase: '', triple_lavado: true, almacenamiento_temporal: '' }],
  });

  const steps = ['Ubicación y Cultivo', 'Producto y Objetivo', 'Dosis y Mezcla', 'Personas y Equipo', 'Condiciones Climáticas', 'Seguridad y Envases', 'Revisión Final'];

  const handleNext = () => setActiveStep((prev) => Math.min(prev + 1, steps.length - 1));
  const handleBack = () => setActiveStep((prev) => Math.max(prev - 1, 0));

  const updateProducto = (index: number, field: string, value: any) => {
    const nuevos = [...data.productos];
    (nuevos[index] as any)[field] = value;
    setData('productos', nuevos);
  };

  const addProducto = () => {
    setData('productos', [...data.productos, { producto_sag_id: '', lote_id: '', dosis: '', unidad_dosis: 'L/ha', cantidad_total: '', volumen_agua: '' }]);
  };

  const removeProducto = (index: number) => {
    if (data.productos.length > 1) {
      setData('productos', data.productos.filter((_, i) => i !== index));
    }
  };

  const handleSubmit = () => {
    post('/agroquimicos', { preserveScroll: true });
  };

  const lotesDisponibles = lotes;

  return (
    <>
      <Head title="Nueva Aplicación" />
      <Box>
        <Typography variant="h5" mb={3}>Nueva Aplicación de Agroquímicos</Typography>

        <Stepper activeStep={activeStep} alternativeLabel sx={{ mb: 4 }}>
          {steps.map((label) => <Step key={label}><StepLabel>{label}</StepLabel></Step>)}
        </Stepper>

        <Paper sx={{ p: 3, mb: 3 }}>
          {activeStep === 0 && (
            <Stack spacing={2}>
              <Typography variant="subtitle1" fontWeight={600}>Ubicación y Cultivo</Typography>
              <TextField select label="Cuartel" value={data.cuartel_id} onChange={(e) => setData('cuartel_id', e.target.value)} error={!!errors.cuartel_id} helperText={errors.cuartel_id} required fullWidth>
                <MenuItem value="">Seleccionar...</MenuItem>
                {cuarteles.map((c) => <MenuItem key={c.id} value={c.id}>{c.nombre}</MenuItem>)}
              </TextField>
              <TextField select label="Variedad" value={data.variedad_id} onChange={(e) => setData('variedad_id', e.target.value)} fullWidth>
                <MenuItem value="">Seleccionar...</MenuItem>
                {variedades.map((v) => <MenuItem key={v.id} value={v.id}>{v.nombre}</MenuItem>)}
              </TextField>
              <TextField label="Temporada" value={data.temporada} onChange={(e) => setData('temporada', e.target.value)} fullWidth />
              <TextField label="Superficie (ha)" type="number" value={data.superficie} onChange={(e) => setData('superficie', e.target.value)} error={!!errors.superficie} helperText={errors.superficie} required fullWidth />
              <TextField label="Fecha Aplicación" type="date" value={data.fecha_aplicacion} onChange={(e) => setData('fecha_aplicacion', e.target.value)} required fullWidth InputLabelProps={{ shrink: true }} />
              <Stack direction="row" spacing={2}>
                <TextField label="Hora Inicio" type="time" value={data.hora_inicio} onChange={(e) => setData('hora_inicio', e.target.value)} fullWidth InputLabelProps={{ shrink: true }} />
                <TextField label="Hora Término" type="time" value={data.hora_termino} onChange={(e) => setData('hora_termino', e.target.value)} fullWidth InputLabelProps={{ shrink: true }} />
              </Stack>
            </Stack>
          )}

          {activeStep === 1 && (
            <Stack spacing={2}>
              <Typography variant="subtitle1" fontWeight={600}>Producto y Objetivo</Typography>
              <TextField select label="Tipo de Objetivo" value={data.objetivo_tipo} onChange={(e) => setData('objetivo_tipo', e.target.value)} required fullWidth>
                <MenuItem value="plaga">Plaga</MenuItem>
                <MenuItem value="enfermedad">Enfermedad</MenuItem>
                <MenuItem value="maleza">Maleza</MenuItem>
                <MenuItem value="regulador">Regulador</MenuItem>
                <MenuItem value="desinfeccion">Desinfección</MenuItem>
                <MenuItem value="otro">Otro</MenuItem>
              </TextField>
              <TextField label="Nombre del Objetivo" value={data.objetivo_nombre} onChange={(e) => setData('objetivo_nombre', e.target.value)} fullWidth placeholder="Ej: Lobesia botrana, Oídio, etc." />
              {data.productos.map((p, i) => (
                <Paper key={i} variant="outlined" sx={{ p: 2 }}>
                  <Typography variant="subtitle2" mb={1}>Producto #{i + 1}</Typography>
                  <TextField select label="Producto SAG" value={p.producto_sag_id} onChange={(e) => updateProducto(i, 'producto_sag_id', e.target.value)} required fullWidth>
                    <MenuItem value="">Seleccionar...</MenuItem>
                    {productosSag.map((ps) => (
                      <MenuItem key={ps.id} value={ps.id}>
                        {ps.nombre_comercial} - {ps.ingrediente_activo} ({ps.nro_autorizacion_sag})
                      </MenuItem>
                    ))}
                  </TextField>
                  {i > 0 && <Button size="small" color="error" onClick={() => removeProducto(i)} sx={{ mt: 1 }}>Eliminar</Button>}
                </Paper>
              ))}
              <Button onClick={addProducto}>+ Agregar Producto</Button>
            </Stack>
          )}

          {activeStep === 2 && (
            <Stack spacing={2}>
              <Typography variant="subtitle1" fontWeight={600}>Dosis y Mezcla</Typography>
              {data.productos.map((p, i) => {
                const selectedPs = productosSag.find((ps) => ps.id === p.producto_sag_id);
                const lotesProd = lotesDisponibles.filter((l) => l.producto_id === selectedPs?.producto?.id);

                return (
                  <Paper key={i} variant="outlined" sx={{ p: 2 }}>
                    <Typography variant="subtitle2">{selectedPs?.nombre_comercial ?? `Producto #${i + 1}`}</Typography>
                    <Stack spacing={2} mt={1}>
                      <TextField select label="Lote (Stock Disponible)" value={p.lote_id} onChange={(e) => updateProducto(i, 'lote_id', e.target.value)} fullWidth>
                        <MenuItem value="">Sin lote (no descontará stock)</MenuItem>
                        {lotesProd.map((l) => (
                          <MenuItem key={l.id} value={l.id}>
                            {l.codigo_lote} - {l.fecha_vencimiento ? `Vence: ${l.fecha_vencimiento} - ` : ''}Stock: {l.cantidad_disponible}
                          </MenuItem>
                        ))}
                      </TextField>
                      <Stack direction="row" spacing={2}>
                        <TextField label="Dosis" type="number" value={p.dosis} onChange={(e) => updateProducto(i, 'dosis', e.target.value)} required fullWidth />
                        <TextField select label="Unidad" value={p.unidad_dosis} onChange={(e) => updateProducto(i, 'unidad_dosis', e.target.value)} required sx={{ minWidth: 120 }}>
                          <MenuItem value="L/ha">L/ha</MenuItem>
                          <MenuItem value="kg/ha">kg/ha</MenuItem>
                          <MenuItem value="cc/ha">cc/ha</MenuItem>
                          <MenuItem value="g/ha">g/ha</MenuItem>
                        </TextField>
                      </Stack>
                      <Stack direction="row" spacing={2}>
                        <TextField label="Cantidad Total" type="number" value={p.cantidad_total} onChange={(e) => updateProducto(i, 'cantidad_total', e.target.value)} required fullWidth />
                        <TextField label="Vol. Agua (L)" type="number" value={p.volumen_agua} onChange={(e) => updateProducto(i, 'volumen_agua', e.target.value)} fullWidth />
                      </Stack>
                      {p.lote_id && (() => {
                        const lote = lotesProd.find((l) => l.id === p.lote_id);
                        const qty = Number(p.cantidad_total);

                        return lote && qty > lote.cantidad_disponible ? (
                          <Alert severity="error">Stock insuficiente. Disponible: {lote.cantidad_disponible}</Alert>
                        ) : null;
                      })()}
                    </Stack>
                  </Paper>
                );
              })}
            </Stack>
          )}

          {activeStep === 3 && (
            <Stack spacing={2}>
              <Typography variant="subtitle1" fontWeight={600}>Personas y Equipo</Typography>
              <TextField select label="Aplicador" value={data.aplicador_id} onChange={(e) => setData('aplicador_id', e.target.value)} fullWidth>
                <MenuItem value="">Seleccionar...</MenuItem>
                {aplicadores.map((a) => (
                  <MenuItem key={a.id} value={a.id}>{a.nombres} {a.apellidos} - {a.rut}</MenuItem>
                ))}
              </TextField>
              <TextField select label="Equipo de Aplicación" value={data.equipo_id} onChange={(e) => setData('equipo_id', e.target.value)} fullWidth>
                <MenuItem value="">Seleccionar...</MenuItem>
                {equipos.map((eq) => (
                  <MenuItem key={eq.id} value={eq.id}>{eq.nombre} ({eq.tipo})</MenuItem>
                ))}
              </TextField>
              <TextField label="Observaciones" value={data.observaciones} onChange={(e) => setData('observaciones', e.target.value)} multiline rows={3} fullWidth />
            </Stack>
          )}

          {activeStep === 4 && (
            <Stack spacing={2}>
              <Typography variant="subtitle1" fontWeight={600}>Condiciones Climáticas</Typography>
              <Stack direction="row" spacing={2}>
                <TextField label="Temperatura (°C)" type="number" value={data.weather.temperatura} onChange={(e) => setData('weather', { ...data.weather, temperatura: e.target.value })} fullWidth />
                <TextField label="Humedad (%)" type="number" value={data.weather.humedad} onChange={(e) => setData('weather', { ...data.weather, humedad: e.target.value })} fullWidth />
              </Stack>
              <Stack direction="row" spacing={2}>
                <TextField label="Viento (km/h)" type="number" value={data.weather.viento_velocidad} onChange={(e) => setData('weather', { ...data.weather, viento_velocidad: e.target.value })} fullWidth />
                <TextField select label="Dirección Viento" value={data.weather.viento_direccion} onChange={(e) => setData('weather', { ...data.weather, viento_direccion: e.target.value })} fullWidth>
                  <MenuItem value="">-</MenuItem>
                  <MenuItem value="N">N</MenuItem>
                  <MenuItem value="NE">NE</MenuItem>
                  <MenuItem value="E">E</MenuItem>
                  <MenuItem value="SE">SE</MenuItem>
                  <MenuItem value="S">S</MenuItem>
                  <MenuItem value="SO">SO</MenuItem>
                  <MenuItem value="O">O</MenuItem>
                  <MenuItem value="NO">NO</MenuItem>
                </TextField>
              </Stack>
              <TextField label="Estado General" value={data.weather.estado_general} onChange={(e) => setData('weather', { ...data.weather, estado_general: e.target.value })} fullWidth placeholder="Ej: Despejado, Nublado, Lluvia ligera" />
              <TextField select label="Riesgo de Deriva" value={data.weather.riesgo_deriva} onChange={(e) => setData('weather', { ...data.weather, riesgo_deriva: e.target.value })} fullWidth>
                <MenuItem value="">Seleccionar...</MenuItem>
                <MenuItem value="bajo">Bajo</MenuItem>
                <MenuItem value="medio">Medio</MenuItem>
                <MenuItem value="alto">Alto</MenuItem>
              </TextField>
              <TextField select label="Fuente" value={data.weather.fuente} onChange={(e) => setData('weather', { ...data.weather, fuente: e.target.value })} fullWidth>
                <MenuItem value="manual">Manual</MenuItem>
                <MenuItem value="estacion">Estación Meteorológica</MenuItem>
                <MenuItem value="api">API</MenuItem>
              </TextField>
              {Number(data.weather.viento_velocidad) > 20 && (
                <Alert severity="warning">Velocidad de viento alta ({data.weather.viento_velocidad} km/h). Riesgo de deriva. Verifique condiciones antes de aplicar.</Alert>
              )}
              {Number(data.weather.temperatura) > 30 && (
                <Alert severity="warning">Temperatura alta ({data.weather.temperatura}°C). Riesgo de evaporación y deriva.</Alert>
              )}
            </Stack>
          )}

          {activeStep === 5 && (
            <Stack spacing={3}>
              <Box>
                <Typography variant="subtitle1" fontWeight={600} mb={1}>Equipos de Protección Personal (EPP)</Typography>
                <Stack direction="row" flexWrap="wrap" gap={1}>
                  <FormControlLabel control={<Checkbox checked={data.safety.epp_guantes} onChange={(e) => setData('safety', { ...data.safety, epp_guantes: e.target.checked })} />} label="Guantes" />
                  <FormControlLabel control={<Checkbox checked={data.safety.epp_mascarilla} onChange={(e) => setData('safety', { ...data.safety, epp_mascarilla: e.target.checked })} />} label="Mascarilla" />
                  <FormControlLabel control={<Checkbox checked={data.safety.epp_overol} onChange={(e) => setData('safety', { ...data.safety, epp_overol: e.target.checked })} />} label="Overol" />
                  <FormControlLabel control={<Checkbox checked={data.safety.epp_botas} onChange={(e) => setData('safety', { ...data.safety, epp_botas: e.target.checked })} />} label="Botas" />
                  <FormControlLabel control={<Checkbox checked={data.safety.epp_proteccion_ocular} onChange={(e) => setData('safety', { ...data.safety, epp_proteccion_ocular: e.target.checked })} />} label="Protección Ocular" />
                </Stack>
              </Box>
              <FormControlLabel control={<Checkbox checked={data.safety.senalizacion_instalada} onChange={(e) => setData('safety', { ...data.safety, senalizacion_instalada: e.target.checked })} />} label="Señalización instalada" />
              <TextField label="Observaciones de Seguridad" value={data.safety.observaciones} onChange={(e) => setData('safety', { ...data.safety, observaciones: e.target.value })} multiline rows={2} fullWidth />

              <Typography variant="subtitle1" fontWeight={600} mb={1}>Envases y Residuos</Typography>
              {data.envases.map((e, i) => (
                <Paper key={i} variant="outlined" sx={{ p: 2 }}>
                  <Typography variant="subtitle2">Envase #{i + 1}</Typography>
                  <Stack spacing={2} mt={1}>
                    <TextField select label="Producto SAG" value={e.producto_sag_id} onChange={(v) => {
                      const env = [...data.envases];
                      env[i] = { ...env[i], producto_sag_id: v.target.value };
                      setData('envases', env);
                    }} fullWidth>
                      <MenuItem value="">Seleccionar...</MenuItem>
                      {productosSag.map((ps) => <MenuItem key={ps.id} value={ps.id}>{ps.nombre_comercial}</MenuItem>)}
                    </TextField>
                    <Stack direction="row" spacing={2}>
                      <TextField label="Envases Usados" type="number" value={e.envases_usados} onChange={(v) => {
                        const env = [...data.envases];
                        env[i] = { ...env[i], envases_usados: v.target.value };
                        setData('envases', env);
                      }} required fullWidth />
                      <TextField label="Capacidad (L)" type="number" value={e.capacidad_envase} onChange={(v) => {
                        const env = [...data.envases];
                        env[i] = { ...env[i], capacidad_envase: v.target.value };
                        setData('envases', env);
                      }} fullWidth />
                    </Stack>
                    <FormControlLabel control={<Checkbox checked={e.triple_lavado} onChange={(v) => {
                      const env = [...data.envases];
                      env[i] = { ...env[i], triple_lavado: v.target.checked };
                      setData('envases', env);
                    }} />} label="Triple Lavado" />
                    <TextField label="Almacenamiento Temporal" value={e.almacenamiento_temporal} onChange={(v) => {
                      const env = [...data.envases];
                      env[i] = { ...env[i], almacenamiento_temporal: v.target.value };
                      setData('envases', env);
                    }} fullWidth />
                  </Stack>
                </Paper>
              ))}
            </Stack>
          )}

          {activeStep === 6 && (
            <Stack spacing={2}>
              <Typography variant="subtitle1" fontWeight={600}>Revisión Final</Typography>
              <Paper variant="outlined" sx={{ p: 2 }}>
                <Typography variant="subtitle2">Ubicación</Typography>
                <Typography variant="body2">Cuartel: {cuarteles.find((c) => c.id === data.cuartel_id)?.nombre ?? '-'}</Typography>
                <Typography variant="body2">Variedad: {variedades.find((v) => v.id === data.variedad_id)?.nombre ?? '-'}</Typography>
                <Typography variant="body2">Superficie: {data.superficie} ha</Typography>
                <Typography variant="body2">Fecha: {data.fecha_aplicacion}</Typography>
              </Paper>
              <Paper variant="outlined" sx={{ p: 2 }}>
                <Typography variant="subtitle2">Productos ({data.productos.length})</Typography>
                {data.productos.map((p, i) => {
                  const ps = productosSag.find((ps) => ps.id === p.producto_sag_id);
                  const l = lotes.find((lt) => lt.id === p.lote_id);

                  return (
                    <Typography key={i} variant="body2">
                      • {ps?.nombre_comercial ?? '?'} — Dosis: {p.dosis} {p.unidad_dosis}, Total: {p.cantidad_total}{l ? ` (Lote: ${l.codigo_lote})` : ''}
                    </Typography>
                  );
                })}
              </Paper>
              <Paper variant="outlined" sx={{ p: 2 }}>
                <Typography variant="subtitle2">Personas</Typography>
                <Typography variant="body2">Aplicador: {aplicadores.find((a) => a.id === data.aplicador_id) ? `${aplicadores.find((a) => a.id === data.aplicador_id)?.nombres} ${aplicadores.find((a) => a.id === data.aplicador_id)?.apellidos}` : 'No asignado'}</Typography>
                <Typography variant="body2">Equipo: {equipos.find((eq) => eq.id === data.equipo_id)?.nombre ?? 'No asignado'}</Typography>
              </Paper>
              {errors.productos && <Alert severity="error">{errors.productos}</Alert>}
            </Stack>
          )}

          <Stack direction="row" justifyContent="space-between" mt={3}>
            <Button disabled={activeStep === 0} onClick={handleBack}>Anterior</Button>
            <Stack direction="row" spacing={1}>
              {activeStep < steps.length - 1 ? (
                <Button variant="contained" onClick={handleNext}>Siguiente</Button>
              ) : (
                <Button variant="contained" onClick={handleSubmit} disabled={processing}>
                  {processing ? 'Guardando...' : 'Guardar Aplicación'}
                </Button>
              )}
            </Stack>
          </Stack>
        </Paper>
      </Box>
    </>
  );
}

AgroquimicosCreate.layout = {
  breadcrumbs: [
    { title: 'Agroquímicos', href: '/agroquimicos' },
    { title: 'Nueva Aplicación', href: '/agroquimicos/create' },
  ],
};
