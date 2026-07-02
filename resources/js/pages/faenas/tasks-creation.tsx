import { router } from '@inertiajs/react';
import { Add } from '@mui/icons-material';
import {
  Box, Typography, Button, TextField, FormControl, InputLabel, Select, MenuItem,
  Switch, FormControlLabel, Table, TableBody, TableCell, TableContainer,
  TableHead, TableRow, Paper, IconButton,
} from '@mui/material';
import { useState, useMemo } from 'react';

interface TarjaRow {
  id: string;
  empleado: string;
  tipoFaena: string;
  centroCosto: string;
  jornada: string;
  descripcion: string;
  cantidad: string;
  montoPorUnidad: string;
  pagoTotal: string;
}

interface ActividadOption {
  id: string;
  nombre: string;
  tipo_labor: 'dia' | 'trato';
  valor: number | null;
  unidad_medida_id: string | null;
  unidad_medida: { id: string; nombre: string; abreviacion: string } | null;
}

let nextId = 1;
const genId = () => String(nextId++);

const JORNADAS = [
  { value: '8hrs', label: 'Jornada 8 hrs' },
  { value: '10hrs', label: 'Jornada 10 hrs' },
  { value: '6hrs', label: 'Jornada 6 hrs' },
];

interface Props {
  actividades: ActividadOption[];
  centrosCosto: Array<{ id: string; nombre: string }>;
  empleados: Array<{ id: string; nombre: string; apellido: string }>;
  unidades: Array<{ id: string; nombre: string; abreviacion: string }>;
  supervisores: Array<{ id: string; name: string; email: string }>;
}

export default function TasksCreation({ actividades, centrosCosto, empleados, supervisores }: Props) {
  const [fecha, setFecha] = useState(new Date().toISOString().slice(0, 10));
  const [jefe, setJefe] = useState('');
  const [distribuirHa, setDistribuirHa] = useState(false);
  const [rows, setRows] = useState<TarjaRow[]>([
    { id: genId(), empleado: '', tipoFaena: '', centroCosto: '', jornada: '', descripcion: '', cantidad: '', montoPorUnidad: '', pagoTotal: '' },
  ]);

  const actividadesMap = useMemo(() => {
    const map = new Map<string, ActividadOption>();

    for (const a of actividades) {
      map.set(a.id, a);
    }

    return map;
  }, [actividades]);

  // Memorizamos las opciones para evitar re-cálculos innecesarios
  const empleadoOptions = useMemo(() =>
    empleados.map(e => ({ value: e.id, label: `${e.nombre} ${e.apellido}` })),
  [empleados]);

  const actividadOptions = useMemo(() =>
    actividades.map(a => ({ value: a.id, label: a.nombre })),
  [actividades]);

  const centroCostoOptions = useMemo(() =>
    centrosCosto.map(c => ({ value: c.id, label: c.nombre })),
  [centrosCosto]);

  const supervisorOptions = useMemo(() =>
    supervisores.map(s => ({ value: s.id, label: s.name })),
  [supervisores]);

  const handleChange = (id: string, field: keyof TarjaRow, value: string) => {
    setRows((prev) => prev.map((r) => {
      if (r.id !== id) {
        return r;
      }

      const updated = { ...r, [field]: value } as TarjaRow;

      if (field === 'tipoFaena') {
        const act = actividadesMap.get(value);

        if (act?.tipo_labor === 'trato') {
          updated.cantidad = '';
          updated.montoPorUnidad = act.valor != null ? String(act.valor) : '';
          updated.pagoTotal = '';
        } else {
          updated.cantidad = '';
          updated.montoPorUnidad = '';
        }
      }

      if (field === 'cantidad' || field === 'montoPorUnidad') {
        const act = actividadesMap.get(r.tipoFaena);

        if (act?.tipo_labor === 'trato') {
          const qty = parseFloat(field === 'cantidad' ? value : updated.cantidad);
          const price = parseFloat(field === 'montoPorUnidad' ? value : updated.montoPorUnidad);

          if (!isNaN(qty) && !isNaN(price)) {
            updated.pagoTotal = (qty * price).toFixed(2);
          } else {
            updated.pagoTotal = '';
          }
        }
      }

      return updated;
    }));
  };

  const addRow = () => {
    setRows((prev) => [...prev, { id: genId(), empleado: '', tipoFaena: '', centroCosto: '', jornada: '', descripcion: '', cantidad: '', montoPorUnidad: '', pagoTotal: '' }]);
  };

  const handleSubmit = () => {
    const empleados = rows
      .filter((r) => r.empleado && r.tipoFaena && r.centroCosto && r.jornada && r.pagoTotal)
      .map((r) => ({
        empleado_id: r.empleado,
        actividad_id: r.tipoFaena,
        centro_costo_id: r.centroCosto,
        jornada: r.jornada,
        horas_trabajadas: parseFloat(r.jornada.replace('hrs', '')) || 0,
        cantidad: r.cantidad || null,
        monto_por_unidad: r.montoPorUnidad || null,
        pago_total: parseFloat(r.pagoTotal) || 0,
      }));

    if (empleados.length === 0) {
      return;
    }

    router.post('/faenas/tasks-creation', {
      fecha,
      supervisor_id: jefe || undefined,
      empleados,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        setFecha(new Date().toISOString().slice(0, 10));
        setJefe('');
        setRows([{ id: genId(), empleado: '', tipoFaena: '', centroCosto: '', jornada: '', descripcion: '', cantidad: '', montoPorUnidad: '', pagoTotal: '' }]);
      },
      onError: (errors) => {
        console.error('Error al guardar:', errors);
      },
    });
  };

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 2 }}>Tarja Diaria — Planilla de Carga Masiva</Typography>

      {/* Header */}
      <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap', alignItems: 'center', mb: 2 }}>
        <TextField label="Fecha" type="date" size="small" value={fecha}
          onChange={(e) => setFecha(e.target.value)}
          slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
        <FormControl size="small" sx={{ minWidth: 180 }}>
          <InputLabel>Jefe (supervisor)</InputLabel>
          <Select value={jefe} label="Jefe (supervisor)" onChange={(e) => setJefe(e.target.value)}>
            <MenuItem value="">Sin jefe asignado</MenuItem>
            {supervisorOptions.map((s) => (
              <MenuItem key={s.value} value={s.value}>{s.label}</MenuItem>
            ))}
          </Select>
        </FormControl>
        <FormControlLabel
          control={<Switch size="small" checked={distribuirHa} onChange={(e) => setDistribuirHa(e.target.checked)} />}
          label="Distribuir por hectárea"
        />
      </Box>

      {/* Spreadsheet grid */}
      <TableContainer component={Paper} variant="outlined" sx={{ borderRadius: 2 }}>
        <Table size="small">
          <TableHead>
            <TableRow>
              <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }}>Empleado</TableCell>
              <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }}>Labor</TableCell>
              <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }}>Centro de costo</TableCell>
              <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }}>Jornada</TableCell>
              <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }}>Descripción</TableCell>
              <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="right">Cantidad</TableCell>
              <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="right">Pago por unidad</TableCell>
              <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="right">Pago total</TableCell>
              <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="center">Acciones</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {rows.map((row) => (
              <TableRow key={row.id}>
                <TableCell sx={{ p: 0.5, minWidth: 180 }}>
                  <FormControl fullWidth size="small">
                    <Select value={row.empleado} onChange={(e) => handleChange(row.id, 'empleado', e.target.value)}
                      displayEmpty
                    >
                      <MenuItem value="" disabled>Seleccionar</MenuItem>
                      {empleadoOptions.map((e) => (
                        <MenuItem key={e.value} value={e.value}>{e.label}</MenuItem>
                      ))}
                    </Select>
                  </FormControl>
                </TableCell>
                <TableCell sx={{ p: 0.5, minWidth: 140 }}>
                  <FormControl fullWidth size="small">
                    <Select value={row.tipoFaena} onChange={(e) => handleChange(row.id, 'tipoFaena', e.target.value)}
                      displayEmpty
                    >
                      <MenuItem value="" disabled>Seleccionar</MenuItem>
                      {actividadOptions.map((t) => (
                        <MenuItem key={t.value} value={t.value}>{t.label}</MenuItem>
                      ))}
                    </Select>
                  </FormControl>
                </TableCell>
                <TableCell sx={{ p: 0.5, minWidth: 140 }}>
                  <FormControl fullWidth size="small">
                    <Select value={row.centroCosto} onChange={(e) => handleChange(row.id, 'centroCosto', e.target.value)}
                      displayEmpty
                    >
                      <MenuItem value="" disabled>Seleccionar</MenuItem>
                      {centroCostoOptions.map((c) => (
                        <MenuItem key={c.value} value={c.value}>{c.label}</MenuItem>
                      ))}
                    </Select>
                  </FormControl>
                </TableCell>
                <TableCell sx={{ p: 0.5, minWidth: 130 }}>
                  <FormControl fullWidth size="small">
                    <Select value={row.jornada} onChange={(e) => handleChange(row.id, 'jornada', e.target.value)}
                      displayEmpty
                    >
                      <MenuItem value="" disabled>Seleccionar</MenuItem>
                      {JORNADAS.map((j) => (
                        <MenuItem key={j.value} value={j.value}>{j.label}</MenuItem>
                      ))}
                    </Select>
                  </FormControl>
                </TableCell>
                <TableCell sx={{ p: 0.5, minWidth: 160 }}>
                  <TextField fullWidth size="small" value={row.descripcion}
                    onChange={(e) => handleChange(row.id, 'descripcion', e.target.value)}
                    placeholder="Nota..."
                  />
                </TableCell>
                <TableCell sx={{ p: 0.5, minWidth: 100 }}>
                  {(() => {
                    const act = actividadesMap.get(row.tipoFaena);
                    const esTrato = act?.tipo_labor === 'trato';

                    return (
                      <TextField fullWidth size="small" type="number"
                        value={row.cantidad}
                        onChange={(e) => handleChange(row.id, 'cantidad', e.target.value)}
                        placeholder="0"
                        disabled={!esTrato}
                        slotProps={{
                          input: esTrato && act?.unidad_medida
                            ? { endAdornment: <Box component="span" sx={{ ml: 0.5, color: 'text.secondary', fontSize: '0.75rem', whiteSpace: 'nowrap' }}>{act.unidad_medida.abreviacion}</Box> }
                            : undefined,
                        }}
                      />
                    );
                  })()}
                </TableCell>
                <TableCell sx={{ p: 0.5, minWidth: 110 }}>
                  {(() => {
                    const act = actividadesMap.get(row.tipoFaena);
                    const esTrato = act?.tipo_labor === 'trato';

                    return (
                      <TextField fullWidth size="small" type="number"
                        value={row.montoPorUnidad}
                        onChange={(e) => handleChange(row.id, 'montoPorUnidad', e.target.value)}
                        placeholder="$0"
                        disabled={!esTrato}
                      />
                    );
                  })()}
                </TableCell>
                <TableCell sx={{ p: 0.5, minWidth: 110 }}>
                  {(() => {
                    const act = actividadesMap.get(row.tipoFaena);
                    const esTrato = act?.tipo_labor === 'trato';

                    return (
                      <TextField fullWidth size="small"
                        value={row.pagoTotal}
                        onChange={(e) => handleChange(row.id, 'pagoTotal', e.target.value)}
                        placeholder="$0"
                        slotProps={{ input: { readOnly: esTrato } }}
                        sx={esTrato ? { '& .MuiInputBase-root': { bgcolor: 'action.hover' } } : undefined}
                      />
                    );
                  })()}
                </TableCell>
                <TableCell sx={{ p: 0.5 }} align="center">
                  <IconButton size="small" color="primary" onClick={addRow}>
                    <Add fontSize="small" />
                  </IconButton>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>

      {/* Actions */}
      <Box sx={{ display: 'flex', gap: 1.5, mt: 2 }}>
        <Button variant="contained" onClick={handleSubmit}>Aceptar</Button>
        <Button variant="outlined" color="inherit" onClick={() => setRows([])}>Limpiar tabla</Button>
        <Button variant="outlined" color="inherit">Buscar</Button>
      </Box>
    </Box>
  );
}
