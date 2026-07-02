import { GetApp } from '@mui/icons-material';
import {
  Box, Typography, Button, TextField, FormControl, InputLabel, Select, MenuItem,
} from '@mui/material';
import { useState, useMemo } from 'react';
import type { Column, HeaderIndicatorData } from '@/components/faenas/faenas-types';
import FaenasDataTable from '@/components/faenas/FaenasDataTable';
import FaenasHeaderIndicator from '@/components/faenas/FaenasHeaderIndicator';

interface TratoItem {
  id: string;
  fecha: string;
  tipoTrato: string;
  cantidad: number;
  monto: number;
  total: number;
  empleado: string;
  centroCosto: string;
  actividad: string;
}

interface Props {
  items: TratoItem[];
  empleados: Array<{ id: string; nombre: string; apellido: string }>;
  centrosCosto: Array<{ id: string; nombre: string }>;
  actividades: Array<{ id: string; nombre: string }>;
  totalTratos: number;
}

const columns: Column[] = [
  { key: 'fecha', label: 'Fecha', width: 100 },
  { key: 'empleado', label: 'Empleado' },
  { key: 'actividad', label: 'Actividad' },
  { key: 'centroCosto', label: 'Centro de costo' },
  { key: 'tipoTrato', label: 'Tipo de trato' },
  { key: 'cantidad', label: 'Cantidad', align: 'right', width: 100 },
  { key: 'monto', label: 'Monto', align: 'right', width: 120, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
  { key: 'total', label: 'Total', align: 'right', width: 130, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
];

export default function AdditionalSalaryReport({ items, empleados, centrosCosto }: Props) {
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');
  const [tipoTrato, setTipoTrato] = useState('');
  const [empleado, setEmpleado] = useState('');
  const [centroCosto, setCentroCosto] = useState('');

  const tiposTrato = useMemo(() => {
    const tipos = new Set(items.map((i) => i.tipoTrato));

    return Array.from(tipos).map((t) => ({ value: t, label: t }));
  }, [items]);

  const filtered = useMemo(() => {
    let data = items;

    if (tipoTrato) {
data = data.filter((i) => i.tipoTrato === tipoTrato);
}

    if (empleado) {
data = data.filter((i) => i.empleado === empleados.find((e) => e.id === empleado)?.nombre + ' ' + empleados.find((e) => e.id === empleado)?.apellido);
}

    if (centroCosto) {
data = data.filter((i) => i.centroCosto === centrosCosto.find((c) => c.id === centroCosto)?.nombre);
}

    return data;
  }, [items, tipoTrato, empleado, centroCosto, empleados, centrosCosto]);

  const totalFiltrado = filtered.reduce((s, i) => s + i.total, 0);
  const indicators: HeaderIndicatorData[] = [
    { label: 'Monto total tratos', value: totalFiltrado, format: 'currency' },
    { label: 'Cantidad registros', value: filtered.length, format: 'number' },
  ];

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Reporte de Tratos</Typography>
      <FaenasHeaderIndicator indicators={indicators} />
      <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', alignItems: 'center', mb: 2 }}>
        <TextField label="Desde" type="date" size="small" value={dateFrom}
          onChange={(e) => setDateFrom(e.target.value)}
          slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
        <TextField label="Hasta" type="date" size="small" value={dateTo}
          onChange={(e) => setDateTo(e.target.value)}
          slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
        <FormControl size="small" sx={{ minWidth: 150 }}>
          <InputLabel>Tipo de trato</InputLabel>
          <Select value={tipoTrato} label="Tipo de trato" onChange={(e) => setTipoTrato(e.target.value)}>
            <MenuItem value="">Todos</MenuItem>
            {tiposTrato.map((t) => (
              <MenuItem key={t.value} value={t.value}>{t.label}</MenuItem>
            ))}
          </Select>
        </FormControl>
        <FormControl size="small" sx={{ minWidth: 160 }}>
          <InputLabel>Empleado</InputLabel>
          <Select value={empleado} label="Empleado" onChange={(e) => setEmpleado(e.target.value)}>
            <MenuItem value="">Todos</MenuItem>
            {empleados.map((e) => (
              <MenuItem key={e.id} value={e.id}>{e.nombre} {e.apellido}</MenuItem>
            ))}
          </Select>
        </FormControl>
        <FormControl size="small" sx={{ minWidth: 150 }}>
          <InputLabel>Centro de costo</InputLabel>
          <Select value={centroCosto} label="Centro de costo" onChange={(e) => setCentroCosto(e.target.value)}>
            <MenuItem value="">Todos</MenuItem>
            {centrosCosto.map((c) => (
              <MenuItem key={c.id} value={c.id}>{c.nombre}</MenuItem>
            ))}
          </Select>
        </FormControl>
        <Button variant="outlined" size="small" color="inherit"
          onClick={() => {
 setDateFrom(''); setDateTo(''); setTipoTrato(''); setEmpleado(''); setCentroCosto(''); 
}}>Limpiar</Button>
        <Button variant="outlined" size="small" startIcon={<GetApp />} sx={{ ml: 'auto' }}>Exportar</Button>
      </Box>
      <FaenasDataTable columns={columns} items={filtered as unknown as Record<string, unknown>[]} />
    </Box>
  );
}
