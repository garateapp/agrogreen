import type { PageProps } from '@inertiajs/core';
import { router } from '@inertiajs/react';
import {
  ArrowBack, Edit as EditIcon, ContentCopy as CloneIcon,
  CopyAll as CopyAllIcon,
} from '@mui/icons-material';
import type { SelectChangeEvent } from '@mui/material';
import {
  Box, Button, Card, CardContent, IconButton, MenuItem, Select,
  TextField, Typography, FormControl, InputLabel, Autocomplete,
  Chip, Divider, LinearProgress,
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
  Paper, Pagination, Dialog, DialogTitle, DialogContent, DialogActions,
} from '@mui/material';
import { useState, useMemo } from 'react';

interface GrupoInfo {
  agrupador: string;
  total_cuarteles: number;
  total_hectareas: number;
  total_plantas?: number;
  especies: string[];
  tiene_trato: boolean;
  total_lineas?: number;
  total_jh?: number;
  total_valor?: number;
  rendimiento_promedio?: number;
  valor_unitario_promedio?: number;
  contenedor_cosecha_id?: string | null;
}

interface PresupuestoData {
  id: string;
  anho_fiscal: number;
  mes: number;
  estado: string;
  tipo_cambio_usd: number | null;
  total_valor: number;
  total_jh: number;
  total_lineas: number;
}

interface TemporadaOption {
  value: string;
  label: string;
}

interface ContenedorOption {
  value: string;
  label: string;
  peso_bin_kg: number;
}

interface DetalleRow {
  id: string;
  anho_fiscal: number | null;
  mes: number | null;
  cuartel_id: string;
  cuartel_nombre: string;
  especie: string;
  agrupador: string;
  centro_costo: string;
  actividad_id: string;
  actividad_nombre: string;
  tipo_labor: string;
  contenedor_cosecha_id: string | null;
  rendimiento_promedio: number;
  hectareas: number | null;
  n_plantas: number;
  kilos_estimados: number | null;
  jh_totales: number;
  valor_unitario: number;
  valor_total: number;
}

interface FilterOption {
  value: string;
  label: string;
  tipo_labor?: string;
}

interface FilterOptions {
  agrupadores: FilterOption[];
  actividades: FilterOption[];
  cuarteles: FilterOption[];
  anhos?: FilterOption[];
  meses?: FilterOption[];
}

interface DetalleFilters {
  agrupador?: string;
  actividad_id?: string;
  cuartel_id?: string;
  tipo_labor?: string;
  anho_fiscal?: string;
  mes?: string;
  page?: string;
  per_page?: string;
}

interface PaginatedMeta {
  current_page: number;
  last_page: number;
  total: number;
  from: number;
  to: number;
}

interface Props extends PageProps {
  presupuesto: PresupuestoData | null;
  grupos: GrupoInfo[];
  detallesPaginated?: { data: DetalleRow[]; meta: PaginatedMeta };
  filterOptions?: FilterOptions;
  detalleFilters?: DetalleFilters;
  total_cuarteles: number;
  total_actividades: number;
  total_lineas: number;
  contenedoresCosecha: ContenedorOption[];
  temporadas?: TemporadaOption[];
}

const MESES = [
  { value: 1, label: 'Enero' }, { value: 2, label: 'Febrero' }, { value: 3, label: 'Marzo' },
  { value: 4, label: 'Abril' }, { value: 5, label: 'Mayo' }, { value: 6, label: 'Junio' },
  { value: 7, label: 'Julio' }, { value: 8, label: 'Agosto' }, { value: 9, label: 'Septiembre' },
  { value: 10, label: 'Octubre' }, { value: 11, label: 'Noviembre' }, { value: 12, label: 'Diciembre' },
];

const ANHOS = Array.from({ length: 10 }, (_, i) => 2024 + i);

export default function PresupuestoForm({
  presupuesto, grupos,
  detallesPaginated, filterOptions, detalleFilters,
  total_cuarteles, total_actividades, total_lineas,
  contenedoresCosecha, temporadas,
}: Props) {
  const isEdit = presupuesto !== null;

  const [temporadaId, setTemporadaId] = useState(presupuesto?.temporada_id ?? '');
  const [anhoFiscal, setAnhoFiscal] = useState(presupuesto?.anho_fiscal?.toString() ?? '');
  const [mes, setMes] = useState(presupuesto?.mes?.toString() ?? '');
  const [tipoCambio, setTipoCambio] = useState(presupuesto?.tipo_cambio_usd?.toString() ?? '');
  const [estado, setEstado] = useState(presupuesto?.estado ?? 'borrador');
  const [saving, setSaving] = useState(false);
  const [formError, setFormError] = useState('');

  const [grupoForms, setGrupoForms] = useState<Record<string, {
    rendimiento: string; valorUnitario: string; contenedorId: string | null; kilos: string;
  }>>(() => {
    const init: Record<string, any> = {};

    for (const g of grupos) {
      init[g.agrupador] = {
        rendimiento: g.rendimiento_promedio?.toString() ?? '',
        valorUnitario: g.valor_unitario_promedio?.toString() ?? '',
        contenedorId: g.contenedor_cosecha_id ?? null,
        kilos: '',
      };
    }

    return init;
  });

  const updateGrupo = (agrupador: string, field: string, value: string | null) => {
    setGrupoForms(prev => ({
      ...prev,
      [agrupador]: { ...prev[agrupador], [field]: value },
    }));
  };

  const contenedorMap = useMemo(() => new Map(contenedoresCosecha.map(c => [c.value, c])), [contenedoresCosecha]);

  const previewTotals = useMemo(() => {
    let totalJH = 0;
    let totalValor = 0;

    for (const g of grupos) {
      const f = grupoForms[g.agrupador];

      if (!f) {
continue;
}

      const rend = parseFloat(f.rendimiento) || 0;
      const valU = parseFloat(f.valorUnitario) || 0;
      const kilos = parseFloat(f.kilos) || 0;
      const cont = f.contenedorId ? contenedorMap.get(f.contenedorId) : undefined;
      const kgBin = cont?.peso_bin_kg ?? 1;

      let grupoJH = 0;

      if (g.tiene_trato) {
        const contenedores = kgBin > 0 ? kilos / kgBin : 0;
        grupoJH = rend > 0 ? contenedores / rend : 0;
      } else {
        grupoJH = rend > 0 ? g.total_hectareas / rend : 0;
      }

      grupoJH *= g.total_cuarteles;
      const grupoValor = grupoJH * valU;

      totalJH += grupoJH;
      totalValor += grupoValor;
    }

    return { totalJH, totalValor };
  }, [grupos, grupoForms, contenedorMap]);

  const hasAnyValues = useMemo(() => {
    return Object.values(grupoForms).some(f => f.rendimiento || f.valorUnitario);
  }, [grupoForms]);

  const [editDialogOpen, setEditDialogOpen] = useState(false);
  const [editTarget, setEditTarget] = useState<DetalleRow | null>(null);
  const [editRend, setEditRend] = useState('');
  const [editValUnit, setEditValUnit] = useState('');
  const [editContId, setEditContId] = useState<string | null>(null);
  const [editKilos, setEditKilos] = useState('');
  const [editAnho, setEditAnho] = useState('');
  const [editMes, setEditMes] = useState('');
  const [editSaving, setEditSaving] = useState(false);

  const openEditDialog = (row: DetalleRow) => {
    setEditTarget(row);
    setEditRend(row.rendimiento_promedio.toString());
    setEditValUnit(row.valor_unitario.toString());
    setEditContId(row.contenedor_cosecha_id);
    setEditKilos(row.kilos_estimados?.toString() ?? '');
    setEditAnho(row.anho_fiscal?.toString() ?? '');
    setEditMes(row.mes?.toString() ?? '');
    setEditDialogOpen(true);
  };

  const handleEditSave = () => {
    if (!editTarget) {
return;
}

    setEditSaving(true);
    router.put(`/presupuesto/${presupuesto!.id}/detalle/${editTarget.id}`, {
      rendimiento_promedio: editRend ? parseFloat(editRend) : null,
      valor_unitario: editValUnit ? parseFloat(editValUnit) : null,
      contenedor_cosecha_id: editContId || null,
      kilos_estimados: editKilos ? parseFloat(editKilos) : null,
      anho_fiscal: editAnho ? parseInt(editAnho) : null,
      mes: editMes ? parseInt(editMes) : null,
    }, {
      onFinish: () => {
 setEditSaving(false); setEditDialogOpen(false);
},
    });
  };

  const handleClone = (row: DetalleRow) => {
    router.post(`/presupuesto/${presupuesto!.id}/detalle/${row.id}/clone`);
  };

  const handleCopyToAgrupador = (row: DetalleRow) => {
    router.post(`/presupuesto/${presupuesto!.id}/detalle/${row.id}/copy-to-agrupador`);
  };

  const handleSubmit = () => {
    setFormError('');
    setSaving(true);

    const gruposPayload = grupos.map(g => {
      const f = grupoForms[g.agrupador];

      return {
        agrupador: g.agrupador,
        rendimiento_promedio: f?.rendimiento ? parseFloat(f.rendimiento) : null,
        valor_unitario: f?.valorUnitario ? parseFloat(f.valorUnitario) : null,
        contenedor_cosecha_id: f?.contenedorId || null,
        kilos_estimados: f?.kilos ? parseFloat(f.kilos) : null,
      };
    });

    const payload: Record<string, any> = {
      temporada_id: temporadaId || null,
      anho_fiscal: anhoFiscal ? parseInt(anhoFiscal) : null,
      mes: mes ? parseInt(mes) : null,
      tipo_cambio_usd: tipoCambio ? parseFloat(tipoCambio) : null,
    };

    if (isEdit) {
      payload.estado = estado;
    } else {
      payload.grupos = gruposPayload;
    }

    if (isEdit) {
      router.put(`/presupuesto/${presupuesto.id}`, payload, {
        onFinish: () => setSaving(false),
      });
    } else {
      router.post('/presupuesto', payload, {
        onFinish: () => setSaving(false),
      });
    }
  };

  return (
    <Box sx={{ p: 3 }}>
      <Box sx={{ display: 'flex', alignItems: 'center', gap: 2, mb: 3 }}>
        <IconButton onClick={() => router.get('/presupuesto')}><ArrowBack /></IconButton>
        <Typography variant="h5" sx={{ fontWeight: 700 }}>
          {isEdit ? 'Editar Presupuesto' : 'Nuevo Presupuesto'}
        </Typography>
      </Box>

      <Card sx={{ mb: 3 }}>
        <CardContent sx={{ display: 'flex', gap: 3, flexWrap: 'wrap', alignItems: 'center' }}>
          <FormControl size="small" sx={{ minWidth: 180 }}>
            <InputLabel>Temporada</InputLabel>
            <Select value={temporadaId} label="Temporada" onChange={(e: SelectChangeEvent) => setTemporadaId(e.target.value)}>
              <MenuItem value=""><em>— Opcional</em></MenuItem>
              {temporadas?.map(t => <MenuItem key={t.value} value={t.value}>{t.label}</MenuItem>)}
            </Select>
          </FormControl>
          <FormControl size="small" sx={{ minWidth: 140 }}>
            <InputLabel>Año Fiscal</InputLabel>
            <Select value={anhoFiscal} label="Año Fiscal" onChange={(e: SelectChangeEvent) => setAnhoFiscal(e.target.value)}>
              <MenuItem value=""><em>— Opcional</em></MenuItem>
              {ANHOS.map(a => <MenuItem key={a} value={a}>{a}</MenuItem>)}
            </Select>
          </FormControl>
          <FormControl size="small" sx={{ minWidth: 150 }}>
            <InputLabel>Mes</InputLabel>
            <Select value={mes} label="Mes" onChange={(e: SelectChangeEvent) => setMes(e.target.value)}>
              <MenuItem value=""><em>— Opcional</em></MenuItem>
              {MESES.map(m => <MenuItem key={m.value} value={m.value}>{m.label}</MenuItem>)}
            </Select>
          </FormControl>
          <TextField label="Tipo Cambio USD" type="number" size="small" value={tipoCambio}
            onChange={e => setTipoCambio(e.target.value)}
            slotProps={{ htmlInput: { min: 0, step: 1 } }}
            sx={{ width: 160 }} />
          {isEdit && (
            <FormControl size="small" sx={{ minWidth: 130 }}>
              <InputLabel>Estado</InputLabel>
              <Select value={estado} label="Estado" onChange={(e: SelectChangeEvent) => setEstado(e.target.value)}>
                <MenuItem value="borrador">Borrador</MenuItem>
                <MenuItem value="aprobado">Aprobado</MenuItem>
                <MenuItem value="cerrado">Cerrado</MenuItem>
              </Select>
            </FormControl>
          )}
        </CardContent>
      </Card>

      <Card sx={{ mb: 3 }}>
        <CardContent sx={{ display: 'flex', gap: 4, flexWrap: 'wrap' }}>
          <Box>
            <Typography variant="overline" color="text.secondary">Cuarteles</Typography>
            <Typography variant="h5" sx={{ fontWeight: 700 }}>{total_cuarteles}</Typography>
          </Box>
          <Box>
            <Typography variant="overline" color="text.secondary">Actividades</Typography>
            <Typography variant="h5" sx={{ fontWeight: 700 }}>{total_actividades}</Typography>
          </Box>
          <Box>
            <Typography variant="overline" color="text.secondary">Líneas a Generar</Typography>
            <Typography variant="h5" sx={{ fontWeight: 700 }}>{total_lineas.toLocaleString()}</Typography>
          </Box>
          <Box>
            <Typography variant="overline" color="text.secondary">Grupos</Typography>
            <Typography variant="h5" sx={{ fontWeight: 700 }}>{grupos.length}</Typography>
          </Box>
        </CardContent>
      </Card>

      {hasAnyValues && (
        <Card sx={{ mb: 3, bgcolor: 'primary.main', color: 'primary.contrastText' }}>
          <CardContent sx={{ display: 'flex', gap: 4, flexWrap: 'wrap' }}>
            <Box>
              <Typography variant="overline">JH Estimado Total</Typography>
              <Typography variant="h5" sx={{ fontWeight: 700 }}>
                {previewTotals.totalJH.toFixed(2)}
              </Typography>
            </Box>
            <Box>
              <Typography variant="overline">Valor Estimado Total</Typography>
              <Typography variant="h5" sx={{ fontWeight: 700 }}>
                ${previewTotals.totalValor.toLocaleString('es-CL', { minimumFractionDigits: 0 })}
              </Typography>
            </Box>
          </CardContent>
        </Card>
      )}

      {/* ─── GROUP CARDS ─── */}
      {grupos.map(g => {
        const f = grupoForms[g.agrupador];

        if (!f) {
return null;
}


      })}

      {/* ─── DETAIL LINES (edit mode only) ─── */}
      {isEdit && detallesPaginated && (
        <>
          <Divider sx={{ my: 3 }} />
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2, mb: 2 }}>
            <Typography variant="h6" sx={{ fontWeight: 700 }}>
              Líneas de Presupuesto
            </Typography>
            <Typography variant="body2" color="text.secondary">
              {detallesPaginated.meta.total.toLocaleString()} líneas totales
            </Typography>
          </Box>

          <Card sx={{ mb: 2 }}>
            <CardContent sx={{ display: 'flex', gap: 2, flexWrap: 'wrap', alignItems: 'end' }}>
              <FormControl size="small" sx={{ minWidth: 160 }}>
                <InputLabel>Agrupador</InputLabel>
                <Select
                  value={detalleFilters?.agrupador ?? ''}
                  label="Agrupador"
                  onChange={e => {
                    const p = new URLSearchParams();

                    if (detalleFilters) {
Object.entries(detalleFilters).forEach(([k, v]) => v && p.set(k, v));
}

                    p.set('agrupador', e.target.value);
                    p.delete('page');
                    router.get(`/presupuesto/${presupuesto!.id}/edit?${p.toString()}`);
                  }}
                >
                  <MenuItem value=""><em>Todos</em></MenuItem>
                  {filterOptions?.agrupadores?.map(o => (
                    <MenuItem key={o.value} value={o.value}>{o.label}</MenuItem>
                  ))}
                </Select>
              </FormControl>
              <FormControl size="small" sx={{ minWidth: 200 }}>
                <InputLabel>Actividad</InputLabel>
                <Select
                  value={detalleFilters?.actividad_id ?? ''}
                  label="Actividad"
                  onChange={e => {
                    const p = new URLSearchParams();

                    if (detalleFilters) {
Object.entries(detalleFilters).forEach(([k, v]) => v && p.set(k, v));
}

                    p.set('actividad_id', e.target.value);
                    p.delete('page');
                    router.get(`/presupuesto/${presupuesto!.id}/edit?${p.toString()}`);
                  }}
                >
                  <MenuItem value=""><em>Todas</em></MenuItem>
                  {filterOptions?.actividades?.map(o => (
                    <MenuItem key={o.value} value={o.value}>{o.label}</MenuItem>
                  ))}
                </Select>
              </FormControl>
              <FormControl size="small" sx={{ minWidth: 180 }}>
                <InputLabel>Tipo Labor</InputLabel>
                <Select
                  value={detalleFilters?.tipo_labor ?? ''}
                  label="Tipo Labor"
                  onChange={e => {
                    const p = new URLSearchParams();

                    if (detalleFilters) {
Object.entries(detalleFilters).forEach(([k, v]) => v && p.set(k, v));
}

                    p.set('tipo_labor', e.target.value);
                    p.delete('page');
                    router.get(`/presupuesto/${presupuesto!.id}/edit?${p.toString()}`);
                  }}
                >
                  <MenuItem value=""><em>Todos</em></MenuItem>
                  <MenuItem value="dia">Día</MenuItem>
                  <MenuItem value="trato">Trato</MenuItem>
                </Select>
              </FormControl>
              <FormControl size="small" sx={{ minWidth: 180 }}>
                <InputLabel>Cuartel</InputLabel>
                <Select
                  value={detalleFilters?.cuartel_id ?? ''}
                  label="Cuartel"
                  onChange={e => {
                    const p = new URLSearchParams();

                    if (detalleFilters) {
Object.entries(detalleFilters).forEach(([k, v]) => v && p.set(k, v));
}

                    p.set('cuartel_id', e.target.value);
                    p.delete('page');
                    router.get(`/presupuesto/${presupuesto!.id}/edit?${p.toString()}`);
                  }}
                >
                  <MenuItem value=""><em>Todos</em></MenuItem>
                  {filterOptions?.cuarteles?.map(o => (
                    <MenuItem key={o.value} value={o.value}>{o.label}</MenuItem>
                  ))}
                </Select>
              </FormControl>
              <FormControl size="small" sx={{ minWidth: 130 }}>
                <InputLabel>Año</InputLabel>
                <Select
                  value={detalleFilters?.anho_fiscal ?? ''}
                  label="Año"
                  onChange={e => {
                    const p = new URLSearchParams();

                    if (detalleFilters) {
Object.entries(detalleFilters).forEach(([k, v]) => v && p.set(k, v));
}

                    p.set('anho_fiscal', e.target.value);
                    p.delete('page');
                    router.get(`/presupuesto/${presupuesto!.id}/edit?${p.toString()}`);
                  }}
                >
                  <MenuItem value=""><em>Todos</em></MenuItem>
                  {filterOptions?.anhos?.map(o => (
                    <MenuItem key={o.value} value={o.value}>{o.label}</MenuItem>
                  ))}
                </Select>
              </FormControl>
              <FormControl size="small" sx={{ minWidth: 150 }}>
                <InputLabel>Mes</InputLabel>
                <Select
                  value={detalleFilters?.mes ?? ''}
                  label="Mes"
                  onChange={e => {
                    const p = new URLSearchParams();

                    if (detalleFilters) {
Object.entries(detalleFilters).forEach(([k, v]) => v && p.set(k, v));
}

                    p.set('mes', e.target.value);
                    p.delete('page');
                    router.get(`/presupuesto/${presupuesto!.id}/edit?${p.toString()}`);
                  }}
                >
                  <MenuItem value=""><em>Todos</em></MenuItem>
                  {filterOptions?.meses?.map(o => (
                    <MenuItem key={o.value} value={o.value}>{o.label}</MenuItem>
                  ))}
                </Select>
              </FormControl>
            </CardContent>
          </Card>

          <TableContainer component={Paper} sx={{ mb: 2, maxHeight: 600 }}>
            <Table size="small" stickyHeader>
              <TableHead>
                <TableRow>
                  <TableCell sx={{ fontWeight: 700 }}>Cuartel</TableCell>
                  <TableCell sx={{ fontWeight: 700 }}>Centro Costo</TableCell>
                  <TableCell sx={{ fontWeight: 700 }}>Agrupador</TableCell>
                  <TableCell sx={{ fontWeight: 700 }}>Especie</TableCell>
                  <TableCell sx={{ fontWeight: 700 }}>Actividad</TableCell>
                  <TableCell sx={{ fontWeight: 700 }}>Tipo</TableCell>
                  <TableCell sx={{ fontWeight: 700 }} align="center">Año</TableCell>
                  <TableCell sx={{ fontWeight: 700 }} align="center">Mes</TableCell>
                  <TableCell sx={{ fontWeight: 700 }} align="right">Ha</TableCell>
                  <TableCell sx={{ fontWeight: 700 }} align="right">Rend.</TableCell>
                  <TableCell sx={{ fontWeight: 700 }} align="right">JH</TableCell>
                  <TableCell sx={{ fontWeight: 700 }} align="right">Val. Unit.</TableCell>
                  <TableCell sx={{ fontWeight: 700 }} align="right">Valor Total</TableCell>
                  <TableCell sx={{ fontWeight: 700 }} align="center">Acciones</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {detallesPaginated.data.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={14} align="center">
                      <Typography variant="body2" color="text.secondary" sx={{ py: 4 }}>
                        No hay líneas para los filtros seleccionados
                      </Typography>
                    </TableCell>
                  </TableRow>
                ) : (
                  detallesPaginated.data.map(row => (
                    <TableRow key={row.id} hover>
                      <TableCell>{row.cuartel_nombre}</TableCell>
                      <TableCell>{row.centro_costo}</TableCell>
                      <TableCell>{row.agrupador}</TableCell>
                      <TableCell>{row.especie}</TableCell>
                      <TableCell>{row.actividad_nombre}</TableCell>
                      <TableCell>
                        <Chip
                          label={row.tipo_labor === 'trato' ? 'Trato' : 'Día'}
                          size="small"
                          color={row.tipo_labor === 'trato' ? 'warning' : 'default'}
                          variant="outlined"
                        />
                      </TableCell>
                      <TableCell align="center">{row.anho_fiscal ?? '—'}</TableCell>
                      <TableCell align="center">{row.mes ?? '—'}</TableCell>
                      <TableCell align="right">{row.hectareas?.toFixed(1) ?? '—'}</TableCell>
                      <TableCell align="right">{row.rendimiento_promedio.toFixed(2)}</TableCell>
                      <TableCell align="right">{row.jh_totales.toFixed(2)}</TableCell>
                      <TableCell align="right">${row.valor_unitario.toLocaleString('es-CL', { minimumFractionDigits: 0 })}</TableCell>
                      <TableCell align="right" sx={{ fontWeight: 600 }}>
                        ${row.valor_total.toLocaleString('es-CL', { minimumFractionDigits: 0 })}
                      </TableCell>
                      <TableCell align="center">
                        <IconButton size="small" title="Editar" onClick={() => openEditDialog(row)}>
                          <EditIcon fontSize="small" />
                        </IconButton>
                        <IconButton size="small" title="Clonar" onClick={() => handleClone(row)}>
                          <CloneIcon fontSize="small" />
                        </IconButton>
                        <IconButton size="small" title="Copiar al agrupador" onClick={() => handleCopyToAgrupador(row)}>
                          <CopyAllIcon fontSize="small" />
                        </IconButton>
                      </TableCell>
                    </TableRow>
                  ))
                )}
              </TableBody>
            </Table>
          </TableContainer>

          {detallesPaginated.meta.last_page > 1 && (
            <Box sx={{ display: 'flex', justifyContent: 'center', mb: 3 }}>
              <Pagination
                count={detallesPaginated.meta.last_page}
                page={detallesPaginated.meta.current_page}
                onChange={(_, page) => {
                  const p = new URLSearchParams();

                  if (detalleFilters) {
Object.entries(detalleFilters).forEach(([k, v]) => v && p.set(k, v));
}

                  p.set('page', page.toString());
                  router.get(`/presupuesto/${presupuesto!.id}/edit?${p.toString()}`);
                }}
                color="primary"
                showFirstButton
                showLastButton
              />
            </Box>
          )}
        </>
      )}

      {/* ─── EDIT DIALOG ─── */}
      <Dialog open={editDialogOpen} onClose={() => setEditDialogOpen(false)} maxWidth="sm" fullWidth>
        <DialogTitle>Editar Línea</DialogTitle>
        <DialogContent>
          {editTarget && (
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2, pt: 1 }}>
              <Typography variant="body2" color="text.secondary">
                {editTarget.cuartel_nombre} — {editTarget.actividad_nombre}
              </Typography>
              <TextField
                size="small" type="number" label="Rendimiento Promedio"
                value={editRend}
                onChange={e => setEditRend(e.target.value)}
                slotProps={{ htmlInput: { min: 0, step: 0.01 } }}
                fullWidth
              />
              <TextField
                size="small" type="number" label="Valor Unitario ($)"
                value={editValUnit}
                onChange={e => setEditValUnit(e.target.value)}
                slotProps={{ htmlInput: { min: 0, step: 1 } }}
                fullWidth
              />
              <Autocomplete
                size="small"
                options={contenedoresCosecha}
                value={contenedoresCosecha.find(c => c.value === editContId) ?? null}
                onChange={(_, v) => setEditContId(v?.value ?? null)}
                getOptionLabel={o => o.label}
                renderInput={p => <TextField {...p} label="Contenedor Cosecha" />}
              />
              <Box sx={{ display: 'flex', gap: 2 }}>
                <FormControl size="small" fullWidth>
                  <InputLabel>Año Fiscal</InputLabel>
                  <Select value={editAnho} label="Año Fiscal" onChange={e => setEditAnho(e.target.value)}>
                    <MenuItem value=""><em>— Opcional</em></MenuItem>
                    {ANHOS.map(a => <MenuItem key={a} value={a}>{a}</MenuItem>)}
                  </Select>
                </FormControl>
                <FormControl size="small" fullWidth>
                  <InputLabel>Mes</InputLabel>
                  <Select value={editMes} label="Mes" onChange={e => setEditMes(e.target.value)}>
                    <MenuItem value=""><em>— Opcional</em></MenuItem>
                    {MESES.map(m => <MenuItem key={m.value} value={m.value}>{m.label}</MenuItem>)}
                  </Select>
                </FormControl>
              </Box>
              {editTarget.tipo_labor === 'trato' && (
                <TextField
                  size="small" type="number" label="Kilos Estimados"
                  value={editKilos}
                  onChange={e => setEditKilos(e.target.value)}
                  slotProps={{ htmlInput: { min: 0, step: 100 } }}
                  fullWidth
                />
              )}
            </Box>
          )}
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setEditDialogOpen(false)} disabled={editSaving}>Cancelar</Button>
          <Button variant="contained" onClick={handleEditSave} disabled={editSaving}>
            {editSaving ? 'Guardando...' : 'Guardar'}
          </Button>
        </DialogActions>
      </Dialog>

      {/* ─── SUBMIT ─── */}
      <Box sx={{ display: 'flex', gap: 2, justifyContent: 'flex-end', alignItems: 'center', mt: 3 }}>
        {formError && <Typography color="error" variant="body2">{formError}</Typography>}
        <Button onClick={() => router.get('/presupuesto')} disabled={saving}>Cancelar</Button>
        <Button variant="contained" onClick={handleSubmit} disabled={saving}>
          {saving ? (
            <>
              <LinearProgress sx={{ width: 80, mr: 1 }} />
              Guardando...
            </>
          ) : (
            'Guardar'
          )}
        </Button>
      </Box>
    </Box>
  );
}
