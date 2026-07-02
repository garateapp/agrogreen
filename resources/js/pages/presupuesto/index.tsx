import type { PageProps } from '@inertiajs/core';
import { router } from '@inertiajs/react';
import {
  Add, Edit, Delete, Visibility, Close,
  ContentCopy as CloneIcon, CalendarMonth as TemporadaIcon,
} from '@mui/icons-material';
import type { SelectChangeEvent } from '@mui/material';
import {
  Box, Button, Card, CardContent, Chip, IconButton, MenuItem, Select,
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
  Paper, TextField, Typography, FormControl, InputLabel, Grid, Divider,
  Dialog, DialogTitle, DialogContent, DialogActions,
} from '@mui/material';
import { useState, useEffect } from 'react';

interface TemporadaItem {
  id: string;
  nombre: string;
  fecha_inicio: string | null;
  fecha_fin: string | null;
}

interface Presupuesto {
  id: string;
  temporada_id: string | null;
  temporada_nombre: string | null;
  anho_fiscal: number;
  mes: number;
  estado: string;
  tipo_cambio_usd: number | null;
  total_lineas: number;
  monto_total: number;
  created_at: string;
}

interface GastoActividad {
  actividad_id: string;
  actividad_nombre: string;
  icono: string;
  color: string;
  tipo_labor: string;
  total_jh: number;
  total_valor: number;
  lineas: number;
}

interface GastoAgrupador {
  agrupador: string;
  lineas: number;
  total_jh: number;
  total_valor: number;
  porcentaje: number;
}

interface GrupoInfo {
  agrupador: string;
  total_cuarteles: number;
  total_hectareas: number;
  especies: string[];
  tiene_trato: boolean;
  total_lineas: number;
  total_jh: number;
  total_valor: number;
  rendimiento_promedio: number;
  valor_unitario_promedio: number;
  contenedor_cosecha_id: string | null;
}

interface DashboardData {
  presupuesto: {
    id: string;
    anho_fiscal: number;
    mes: number;
    estado: string;
    total_valor: number;
    total_jh: number;
    total_lineas: number;
  };
  gastosPorActividad: GastoActividad[];
  gastosPorAgrupador: GastoAgrupador[];
  grupos: GrupoInfo[];
}

interface Props extends PageProps {
  presupuestos: Presupuesto[];
  filters: Record<string, string>;
  dashboard: DashboardData | null;
  selectedId: string | null;
  temporadas?: TemporadaItem[];
  autoOpenTemporadas?: boolean;
}

const MESES = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

function renderBar(value: number, max: number, color: string) {
  const pct = max > 0 ? (value / max) * 100 : 0;

  return (
    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, width: '100%' }}>
      <Box sx={{ flexGrow: 1, bgcolor: 'grey.200', borderRadius: 1, height: 20, overflow: 'hidden' }}>
        <Box sx={{ width: `${pct}%`, bgcolor: color, height: '100%', borderRadius: 1, transition: 'width 0.3s' }} />
      </Box>
      <Typography variant="caption" sx={{ minWidth: 80, textAlign: 'right', fontWeight: 600, fontVariantNumeric: 'tabular-nums' }}>
        ${value.toLocaleString('es-CL', { minimumFractionDigits: 0 })}
      </Typography>
    </Box>
  );
}

export default function PresupuestoIndex({ presupuestos, filters, dashboard, selectedId, temporadas: initialTemporadas, autoOpenTemporadas }: Props) {
  const [searchAnho, setSearchAnho] = useState(filters.anho_fiscal ?? '');
  const [searchEstado, setSearchEstado] = useState(filters.estado ?? '');

  // ─── Temporada management ───
  const [tempOpen, setTempOpen] = useState(autoOpenTemporadas ?? false);
  const [temporadas, setTemporadas] = useState<TemporadaItem[]>(initialTemporadas ?? []);
  const [tempName, setTempName] = useState('');
  const [tempStart, setTempStart] = useState('');
  const [tempEnd, setTempEnd] = useState('');
  const [editingTemp, setEditingTemp] = useState<TemporadaItem | null>(null);
  const [tempSaving, setTempSaving] = useState(false);

  const loadTemporadas = () => {
    router.get('/presupuesto/temporadas', {}, {
      preserveState: true,
      onSuccess: (page: any) => {
        if (page.props.temporadas) {
          setTemporadas(page.props.temporadas);
        }
      },
    });
  };

  useEffect(() => {
    if (tempOpen && temporadas.length === 0) {
      loadTemporadas();
    }
  }, [tempOpen, temporadas.length]);

  const openNewTemp = () => {
    setEditingTemp(null);
    setTempName('');
    setTempStart('');
    setTempEnd('');
  };

  const openEditTemp = (t: TemporadaItem) => {
    setEditingTemp(t);
    setTempName(t.nombre);
    setTempStart(t.fecha_inicio ?? '');
    setTempEnd(t.fecha_fin ?? '');
  };

  const saveTemporada = () => {
    setTempSaving(true);
    const payload = { nombre: tempName, fecha_inicio: tempStart || null, fecha_fin: tempEnd || null };

    if (editingTemp) {
      router.put(`/presupuesto/temporadas/${editingTemp.id}`, payload, {
        onFinish: () => {
 setTempSaving(false); loadTemporadas(); 
},
      });
    } else {
      router.post('/presupuesto/temporadas', payload, {
        onFinish: () => {
 setTempSaving(false); loadTemporadas(); openNewTemp(); 
},
      });
    }
  };

  const deleteTemporada = (id: string) => {
    if (confirm('¿Eliminar esta temporada?')) {
      router.delete(`/presupuesto/temporadas/${id}`, {
        onFinish: () => loadTemporadas(),
      });
    }
  };

  const handleClone = (id: string) => {
    router.post(`/presupuesto/${id}/clone`);
  };

  const handleSearch = () => {
    const params: Record<string, string> = {};

    if (searchAnho) {
params.anho_fiscal = searchAnho;
}

    if (searchEstado) {
params.estado = searchEstado;
}

    router.get('/presupuesto', params);
  };

  const handleDelete = (id: string) => {
    if (confirm('¿Eliminar este presupuesto? Se borrarán todas las líneas asociadas.')) {
      router.delete(`/presupuesto/${id}`);
    }
  };

  const showDashboard = (id: string) => {
    const params: Record<string, string> = { selected_id: id };

    if (searchAnho) {
params.anho_fiscal = searchAnho;
}

    if (searchEstado) {
params.estado = searchEstado;
}

    router.get('/presupuesto', params);
  };

  const closeDashboard = () => {
    const params: Record<string, string> = {};

    if (searchAnho) {
params.anho_fiscal = searchAnho;
}

    if (searchEstado) {
params.estado = searchEstado;
}

    router.get('/presupuesto', params);
  };

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h5" sx={{ mb: 3, fontWeight: 600 }}>Presupuestos</Typography>

      <Box sx={{ display: 'flex', gap: 2, mb: 2, alignItems: 'end', flexWrap: 'wrap' }}>
        <TextField label="Año Fiscal" type="number" size="small" value={searchAnho} onChange={e => setSearchAnho(e.target.value)} sx={{ width: 120 }} />
        <FormControl size="small" sx={{ minWidth: 120 }}>
          <InputLabel>Estado</InputLabel>
          <Select value={searchEstado} label="Estado" onChange={(e: SelectChangeEvent) => setSearchEstado(e.target.value)}>
            <MenuItem value="">Todos</MenuItem>
            <MenuItem value="borrador">Borrador</MenuItem>
            <MenuItem value="aprobado">Aprobado</MenuItem>
            <MenuItem value="cerrado">Cerrado</MenuItem>
          </Select>
        </FormControl>
        <Button variant="outlined" onClick={handleSearch}>Buscar</Button>
        <Button variant="outlined" startIcon={<TemporadaIcon />} onClick={() => {
 openNewTemp(); setTempOpen(true); 
}}>
          Temporadas
        </Button>
        <Button variant="contained" startIcon={<Add />} onClick={() => router.get('/presupuesto/create')} sx={{ ml: 'auto' }}>Nuevo Presupuesto</Button>
      </Box>

      {presupuestos.length === 0 ? (
        <Card sx={{ py: 6, textAlign: 'center' }}>
          <CardContent>
            <Typography color="text.secondary">No hay presupuestos registrados</Typography>
            <Button variant="contained" startIcon={<Add />} onClick={() => router.get('/presupuesto/create')} sx={{ mt: 2 }}>
              Crear Primer Presupuesto
            </Button>
          </CardContent>
        </Card>
      ) : (
        <TableContainer component={Paper}>
          <Table size="small">
            <TableHead>
              <TableRow>
                <TableCell>Temporada</TableCell>
                <TableCell>Año Fiscal</TableCell>
                <TableCell>Mes</TableCell>
                <TableCell>Estado</TableCell>
                <TableCell align="right">Líneas</TableCell>
                <TableCell align="right">Monto Total</TableCell>
                <TableCell>Creado</TableCell>
                <TableCell align="center">Acciones</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {presupuestos.map(p => (
                <TableRow
                  key={p.id}
                  hover
                  onClick={() => showDashboard(p.id)}
                  sx={{ cursor: 'pointer', bgcolor: selectedId === p.id ? 'action.selected' : 'inherit' }}
                >
                  <TableCell>{p.temporada_nombre ?? '—'}</TableCell>
                  <TableCell>{p.anho_fiscal}</TableCell>
                  <TableCell>{MESES[p.mes] ?? p.mes}</TableCell>
                  <TableCell>
                    <Chip label={p.estado}
                      color={p.estado === 'aprobado' ? 'success' : p.estado === 'cerrado' ? 'default' : 'warning'}
                      size="small" />
                  </TableCell>
                  <TableCell align="right">{p.total_lineas}</TableCell>
                  <TableCell align="right">${(p.monto_total ?? 0).toLocaleString('es-CL')}</TableCell>
                  <TableCell>{p.created_at}</TableCell>
                  <TableCell align="center" onClick={e => e.stopPropagation()}>
                    <IconButton size="small" title="Ver Dashboard" onClick={() => showDashboard(p.id)}>
                      <Visibility fontSize="small" />
                    </IconButton>
                    <IconButton size="small" title="Clonar" onClick={() => handleClone(p.id)}>
                      <CloneIcon fontSize="small" />
                    </IconButton>
                    <IconButton size="small" title="Editar" onClick={() => router.get(`/presupuesto/${p.id}/edit`)}>
                      <Edit fontSize="small" />
                    </IconButton>
                    <IconButton size="small" title="Eliminar" onClick={() => handleDelete(p.id)} color="error">
                      <Delete fontSize="small" />
                    </IconButton>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      )}

      {dashboard && (
        <>
          <Divider sx={{ my: 3 }} />
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2, mb: 3 }}>
            <Typography variant="h6" sx={{ fontWeight: 700 }}>
              Dashboard — {dashboard.presupuesto.anho_fiscal}{dashboard.presupuesto.mes ? ` / ${MESES[dashboard.presupuesto.mes]}` : ''}
            </Typography>
            <IconButton size="small" onClick={closeDashboard} title="Cerrar"><Close /></IconButton>
          </Box>

          <Grid container spacing={2} sx={{ mb: 3 }}>
            <Grid size={{ xs: 12, sm: 4 }}>
              <Card sx={{ bgcolor: 'primary.main', color: 'primary.contrastText' }}>
                <CardContent>
                  <Typography variant="overline">Total Presupuesto</Typography>
                  <Typography variant="h4" sx={{ fontWeight: 700 }}>
                    ${dashboard.presupuesto.total_valor.toLocaleString('es-CL', { minimumFractionDigits: 0 })}
                  </Typography>
                </CardContent>
              </Card>
            </Grid>
            <Grid size={{ xs: 12, sm: 4 }}>
              <Card sx={{ bgcolor: 'success.main', color: 'success.contrastText' }}>
                <CardContent>
                  <Typography variant="overline">Total JH</Typography>
                  <Typography variant="h4" sx={{ fontWeight: 700 }}>
                    {dashboard.presupuesto.total_jh.toLocaleString('es-CL', { minimumFractionDigits: 2 })}
                  </Typography>
                </CardContent>
              </Card>
            </Grid>
            <Grid size={{ xs: 12, sm: 4 }}>
              <Card>
                <CardContent>
                  <Typography variant="overline" color="text.secondary">Líneas</Typography>
                  <Typography variant="h4" sx={{ fontWeight: 700 }}>{dashboard.presupuesto.total_lineas}</Typography>
                </CardContent>
              </Card>
            </Grid>
          </Grid>

          <Grid container spacing={3} sx={{ mb: 3 }}>
            <Grid size={{ xs: 12, md: 6 }}>
              <Card>
                <CardContent>
                  <Typography variant="subtitle1" sx={{ fontWeight: 600, mb: 2 }}>
                    Gastos por Agrupador
                  </Typography>
                  {dashboard.gastosPorAgrupador.map(g => (
                    <Box key={g.agrupador} sx={{ mb: 1.5 }}>
                      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 0.5 }}>
                        <Typography variant="body2" sx={{ fontWeight: 600 }}>{g.agrupador}</Typography>
                        <Typography variant="caption" color="text.secondary">
                          ${g.total_valor.toLocaleString('es-CL', { minimumFractionDigits: 0 })}
                        </Typography>
                      </Box>
                      {renderBar(g.total_valor, dashboard.gastosPorAgrupador[0].total_valor, '#4CAF50')}
                    </Box>
                  ))}
                </CardContent>
              </Card>
            </Grid>

            <Grid size={{ xs: 12, md: 6 }}>
              <Card>
                <CardContent>
                  <Typography variant="subtitle1" sx={{ fontWeight: 600, mb: 2 }}>
                    Gastos por Actividad (Top 10)
                  </Typography>
                  {dashboard.gastosPorActividad.slice(0, 10).map(a => (
                    <Box key={a.actividad_id} sx={{ mb: 1.5 }}>
                      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 0.5 }}>
                        <Typography variant="body2" sx={{ fontWeight: 600 }}>{a.actividad_nombre}</Typography>
                        <Typography variant="caption" color="text.secondary">
                          ${a.total_valor.toLocaleString('es-CL', { minimumFractionDigits: 0 })}
                        </Typography>
                      </Box>
                      {renderBar(a.total_valor, dashboard.gastosPorActividad[0].total_valor, a.color || '#1976d2')}
                    </Box>
                  ))}
                </CardContent>
              </Card>
            </Grid>
          </Grid>

          <Typography variant="h6" sx={{ fontWeight: 600, mb: 2 }}>
            Resumen por Agrupador
          </Typography>

          {dashboard.grupos.map(g => (
            <Card key={g.agrupador} sx={{ mb: 2, borderLeft: 4, borderColor: 'primary.main' }}>
              <CardContent>
                <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 1, flexWrap: 'wrap', gap: 1 }}>
                  <Box>
                    <Typography variant="subtitle1" sx={{ fontWeight: 700 }}>{g.agrupador}</Typography>
                    <Box sx={{ display: 'flex', gap: 1, flexWrap: 'wrap', mt: 0.5 }}>
                      <Chip label={`${g.total_cuarteles} cuarteles`} size="small" variant="outlined" />
                      <Chip label={`${g.total_hectareas.toFixed(1)} ha`} size="small" variant="outlined" />
                      {g.especies.map(e => <Chip key={e} label={e} size="small" variant="outlined" />)}
                    </Box>
                  </Box>
                  <Typography variant="body2" color="text.secondary">
                    {g.total_lineas.toLocaleString()} líneas · JH: {g.total_jh.toFixed(2)} · $
                    {g.total_valor.toLocaleString('es-CL', { minimumFractionDigits: 0 })}
                  </Typography>
                </Box>
                <Divider sx={{ mb: 1 }} />
                <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap' }}>
                  <Typography variant="body2">
                    <b>Rend. Promedio:</b> {g.rendimiento_promedio.toFixed(2)}
                  </Typography>
                  <Typography variant="body2">
                    <b>Val. Unit. Promedio:</b> ${g.valor_unitario_promedio.toLocaleString('es-CL', { minimumFractionDigits: 0 })}
                  </Typography>
                </Box>
              </CardContent>
            </Card>
          ))}
        </>
      )}

      {/* ─── Temporadas Dialog ─── */}
      <Dialog open={tempOpen} onClose={() => setTempOpen(false)} maxWidth="sm" fullWidth>
        <DialogTitle>Gestión de Temporadas</DialogTitle>
        <DialogContent>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2, pt: 1 }}>
            <Box sx={{ display: 'flex', gap: 1, alignItems: 'end' }}>
              <TextField size="small" label="Nombre" value={tempName} onChange={e => setTempName(e.target.value)} sx={{ flex: 1 }} />
              <TextField size="small" label="Inicio" type="date" value={tempStart}
                onChange={e => setTempStart(e.target.value)}
                slotProps={{ htmlInput: { placeholder: 'yyyy-mm-dd' } }}
                sx={{ width: 150 }} />
              <TextField size="small" label="Fin" type="date" value={tempEnd}
                onChange={e => setTempEnd(e.target.value)}
                slotProps={{ htmlInput: { placeholder: 'yyyy-mm-dd' } }}
                sx={{ width: 150 }} />
              <Button variant="contained" size="small" onClick={saveTemporada} disabled={tempSaving || !tempName.trim()}>
                {editingTemp ? 'Actualizar' : 'Agregar'}
              </Button>
            </Box>
            <Divider />
            {temporadas.length === 0 ? (
              <Typography variant="body2" color="text.secondary" sx={{ textAlign: 'center', py: 2 }}>
                No hay temporadas registradas
              </Typography>
            ) : (
              temporadas.map(t => (
                <Box key={t.id} sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: 1 }}>
                  <Box sx={{ flex: 1 }}>
                    <Typography variant="body2" sx={{ fontWeight: 600 }}>{t.nombre}</Typography>
                    <Typography variant="caption" color="text.secondary">
                      {t.fecha_inicio ?? '—'} &rarr; {t.fecha_fin ?? '—'}
                    </Typography>
                  </Box>
                  <IconButton size="small" onClick={() => openEditTemp(t)}><Edit fontSize="small" /></IconButton>
                  <IconButton size="small" onClick={() => deleteTemporada(t.id)} color="error"><Delete fontSize="small" /></IconButton>
                </Box>
              ))
            )}
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setTempOpen(false)}>Cerrar</Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}
