import { GetApp } from '@mui/icons-material';
import {
  Box, Typography, Button, TextField, FormControl, InputLabel, Select, MenuItem, Paper, Grid,
} from '@mui/material';
import { useState, useMemo } from 'react';
import {
  BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer,
  PieChart, Pie, Cell,
} from 'recharts';
import type { Column, HeaderIndicatorData } from '@/components/faenas/faenas-types';
import FaenasDataTable from '@/components/faenas/FaenasDataTable';
import FaenasHeaderIndicator from '@/components/faenas/FaenasHeaderIndicator';

interface PerformanceItem {
  id: string;
  fecha: string;
  actividad: string;
  actividad_id: string;
  centroCosto: string;
  centro_costo_id: string;
  empleado_id: string;
  contratista_id: string | null;
  contratista: string;
  trabajadores: number;
  unidadesProducidas: number;
  diasHombre: number;
  sueldoDia: number;
  sueldoTrato: number;
  costoTotal: number;
}

interface ChartDataItem {
  actividad: string;
  unidadesProducidas: number;
  diasHombre: number;
  costoTotal: number;
  trabajadores: number;
}

interface Props {
  items: PerformanceItem[];
  chartData: ChartDataItem[];
  actividades: Array<{ id: string; nombre: string }>;
  centrosCosto: Array<{ id: string; nombre: string }>;
  contratistas: Array<{ id: string; nombre: string }>;
}

const COLORS = ['#4F46E5', '#06B6D4', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6'];

const columns: Column[] = [
  { key: 'actividad', label: 'Actividad' },
  { key: 'centroCosto', label: 'Centro de costo' },
  { key: 'contratista', label: 'Contratista' },
  { key: 'trabajadores', label: 'Trabajadores', align: 'right', width: 120 },
  { key: 'unidadesProducidas', label: 'Unidades producidas', align: 'right', width: 160 },
  { key: 'diasHombre', label: 'Días Hombre', align: 'right', width: 110 },
  { key: 'sueldoDia', label: 'Sueldo día', align: 'right', width: 110, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
  { key: 'sueldoTrato', label: 'Sueldo trato', align: 'right', width: 110, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
  { key: 'costoTotal', label: 'Costo total', align: 'right', width: 130, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
];

export default function TasksPerformance({ items, actividades, centrosCosto, contratistas }: Props) {
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');
  const [centroCosto, setCentroCosto] = useState('');
  const [actividad, setActividad] = useState('');
  const [contratistaId, setContratistaId] = useState('');

  const filtered = useMemo(() => {
    if (!centroCosto && !actividad && !contratistaId) {
return items;
}

    return items.filter((i) => {
      if (centroCosto && i.centro_costo_id !== centroCosto) {
return false;
}

      if (actividad && i.actividad_id !== actividad) {
return false;
}

      if (contratistaId && i.contratista_id !== contratistaId) {
return false;
}

      return true;
    });
  }, [items, centroCosto, actividad, contratistaId]);

  const chartFiltered = useMemo(() => {
    const grouped = new Map<string, ChartDataItem>();
    filtered.forEach((i) => {
      const act = i.actividad || 'Sin actividad';
      const existing = grouped.get(act) || { actividad: act, unidadesProducidas: 0, diasHombre: 0, costoTotal: 0, trabajadores: 0 };
      existing.unidadesProducidas += i.unidadesProducidas;
      existing.diasHombre += i.diasHombre;
      existing.costoTotal += i.costoTotal;
      existing.trabajadores = i.trabajadores;
      grouped.set(act, existing);
    });

    return Array.from(grouped.values()).map((g) => ({
      ...g,
      unidadesProducidas: Math.round(g.unidadesProducidas),
      diasHombre: Math.round(g.diasHombre * 10) / 10,
      costoTotal: Math.round(g.costoTotal),
    }));
  }, [filtered]);

  const totalCosto = filtered.reduce((s, i) => s + i.costoTotal, 0);
  const indicators: HeaderIndicatorData[] = useMemo(() => [
    { label: 'Costo total', value: totalCosto, format: 'currency' },
    { label: 'Registros', value: filtered.length, format: 'number' },
    { label: 'Días Hombre', value: Math.round(filtered.reduce((s, i) => s + i.diasHombre, 0) * 10) / 10, format: 'decimal' },
  ], [filtered, totalCosto]);

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Rendimiento por Faenas</Typography>
      <FaenasHeaderIndicator indicators={indicators} />

      <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', alignItems: 'center', mb: 2 }}>
        <TextField label="Desde" type="date" size="small" value={dateFrom}
          onChange={(e) => setDateFrom(e.target.value)}
          slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
        <TextField label="Hasta" type="date" size="small" value={dateTo}
          onChange={(e) => setDateTo(e.target.value)}
          slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
        <FormControl size="small" sx={{ minWidth: 150 }}>
          <InputLabel>Centro de costo</InputLabel>
          <Select value={centroCosto} label="Centro de costo" onChange={(e) => setCentroCosto(e.target.value)}>
            <MenuItem value="">Todos</MenuItem>
            {centrosCosto.map((c) => (
              <MenuItem key={c.id} value={c.id}>{c.nombre}</MenuItem>
            ))}
          </Select>
        </FormControl>
        <FormControl size="small" sx={{ minWidth: 150 }}>
          <InputLabel>Actividad</InputLabel>
          <Select value={actividad} label="Actividad" onChange={(e) => setActividad(e.target.value)}>
            <MenuItem value="">Todas</MenuItem>
            {actividades.map((a) => (
              <MenuItem key={a.id} value={a.id}>{a.nombre}</MenuItem>
            ))}
          </Select>
        </FormControl>
        <FormControl size="small" sx={{ minWidth: 150 }}>
          <InputLabel>Contratista</InputLabel>
          <Select value={contratistaId} label="Contratista" onChange={(e) => setContratistaId(e.target.value)}>
            <MenuItem value="">Todos</MenuItem>
            {contratistas.map((c) => (
              <MenuItem key={c.id} value={c.id}>{c.nombre}</MenuItem>
            ))}
          </Select>
        </FormControl>
        <Button variant="outlined" size="small" color="inherit"
          onClick={() => {
 setDateFrom(''); setDateTo(''); setCentroCosto(''); setActividad(''); setContratistaId(''); 
}}>Limpiar</Button>
        <Button variant="outlined" size="small" startIcon={<GetApp />} sx={{ ml: 'auto' }}>Exportar</Button>
      </Box>

      <Grid container spacing={2} sx={{ mb: 2 }}>
        <Grid size={{ xs: 12, md: 8 }}>
          <Paper variant="outlined" sx={{ p: 2, borderRadius: 2 }}>
            <Typography variant="subtitle2" sx={{ mb: 1, fontWeight: 600 }}>Costo total por actividad</Typography>
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={chartFiltered}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="actividad" tick={{ fontSize: 12 }} />
                <YAxis tick={{ fontSize: 12 }} />
                <Tooltip formatter={(v: number) => `$${v.toLocaleString('es-CL')}`} />
                <Legend />
                <Bar dataKey="costoTotal" fill="#4F46E5" name="Costo total" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </Paper>
        </Grid>
        <Grid size={{ xs: 12, md: 4 }}>
          <Paper variant="outlined" sx={{ p: 2, borderRadius: 2 }}>
            <Typography variant="subtitle2" sx={{ mb: 1, fontWeight: 600 }}>Distribución por actividad</Typography>
            <ResponsiveContainer width="100%" height={300}>
              <PieChart>
                <Pie
                  data={chartFiltered}
                  dataKey="costoTotal"
                  nameKey="actividad"
                  cx="50%"
                  cy="50%"
                  outerRadius={90}
                  label={({ actividad, percent }) => `${actividad} (${(percent * 100).toFixed(0)}%)`}
                >
                  {chartFiltered.map((_, idx) => (
                    <Cell key={idx} fill={COLORS[idx % COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip formatter={(v: number) => `$${v.toLocaleString('es-CL')}`} />
              </PieChart>
            </ResponsiveContainer>
          </Paper>
        </Grid>
        <Grid size={{ xs: 12, md: 6 }}>
          <Paper variant="outlined" sx={{ p: 2, borderRadius: 2 }}>
            <Typography variant="subtitle2" sx={{ mb: 1, fontWeight: 600 }}>Unidades producidas por actividad</Typography>
            <ResponsiveContainer width="100%" height={250}>
              <BarChart data={chartFiltered}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="actividad" tick={{ fontSize: 12 }} />
                <YAxis tick={{ fontSize: 12 }} />
                <Tooltip />
                <Legend />
                <Bar dataKey="unidadesProducidas" fill="#10B981" name="Unidades" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </Paper>
        </Grid>
        <Grid size={{ xs: 12, md: 6 }}>
          <Paper variant="outlined" sx={{ p: 2, borderRadius: 2 }}>
            <Typography variant="subtitle2" sx={{ mb: 1, fontWeight: 600 }}>Días Hombre por actividad</Typography>
            <ResponsiveContainer width="100%" height={250}>
              <BarChart data={chartFiltered}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="actividad" tick={{ fontSize: 12 }} />
                <YAxis tick={{ fontSize: 12 }} />
                <Tooltip />
                <Legend />
                <Bar dataKey="diasHombre" fill="#06B6D4" name="Días Hombre" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </Paper>
        </Grid>
      </Grid>

      <FaenasDataTable columns={columns} items={filtered as unknown as Record<string, unknown>[]} />
    </Box>
  );
}
