import { GetApp } from '@mui/icons-material';
import {
  Box, Typography, Button, TextField, FormControl, InputLabel, Select, MenuItem,
} from '@mui/material';
import { useState, useMemo } from 'react';
import type { Column, HeaderIndicatorData } from '@/components/faenas/faenas-types';
import FaenasDataTable from '@/components/faenas/FaenasDataTable';
import FaenasHeaderIndicator from '@/components/faenas/FaenasHeaderIndicator';

interface SalaryItem {
  id: string;
  nombre: string;
  costoEstimado: number;
  costoEmpresa: number;
  sueldoBase: number;
  totalTratos: number;
  sueldoDiario: number;
}

interface Props {
  items: SalaryItem[];
  empleados: Array<{ id: string; nombre: string; apellido: string }>;
}

const columns: Column[] = [
  { key: 'nombre', label: 'Nombre' },
  { key: 'costoEstimado', label: 'Costo estimado', align: 'right', width: 130, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
  { key: 'costoEmpresa', label: 'Costo empresa', align: 'right', width: 130, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
  { key: 'sueldoBase', label: 'Sueldo base', align: 'right', width: 120, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
  { key: 'totalTratos', label: 'Total tratos', align: 'right', width: 120, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
  { key: 'sueldoDiario', label: 'Sueldo diario', align: 'right', width: 120, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
];

export default function SalaryReport({ items, empleados }: Props) {
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');
  const [empleado, setEmpleado] = useState('');

  const filtered = useMemo(() => {
    let data = items;

    if (empleado) {
      data = data.filter((i) => i.id === empleado);
    }

    return data;
  }, [items, empleado]);

  const indicators: HeaderIndicatorData[] = useMemo(() => [
    { label: 'Costo empresa total', value: items.reduce((s, i) => s + i.costoEmpresa, 0), format: 'currency' },
    { label: 'Total tratos', value: items.reduce((s, i) => s + i.totalTratos, 0), format: 'currency' },
    { label: 'Trabajadores', value: items.length, format: 'number' },
  ], [items]);

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Reporte de Sueldos</Typography>
      <FaenasHeaderIndicator indicators={indicators} />
      <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', alignItems: 'center', mb: 2 }}>
        <TextField label="Desde" type="date" size="small" value={dateFrom}
          onChange={(e) => setDateFrom(e.target.value)}
          slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
        <TextField label="Hasta" type="date" size="small" value={dateTo}
          onChange={(e) => setDateTo(e.target.value)}
          slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
        <FormControl size="small" sx={{ minWidth: 180 }}>
          <InputLabel>Empleado</InputLabel>
          <Select value={empleado} label="Empleado" onChange={(e) => setEmpleado(e.target.value)}>
            <MenuItem value="">Todos</MenuItem>
            {empleados.map((e) => (
              <MenuItem key={e.id} value={e.id}>{e.nombre} {e.apellido}</MenuItem>
            ))}
          </Select>
        </FormControl>
        <Button variant="outlined" size="small" color="inherit"
          onClick={() => {
 setDateFrom(''); setDateTo(''); setEmpleado(''); 
}}>Limpiar</Button>
        <Button variant="outlined" size="small" startIcon={<GetApp />} sx={{ ml: 'auto' }}>Exportar</Button>
      </Box>
      <FaenasDataTable columns={columns} items={filtered as unknown as Record<string, unknown>[]} />
    </Box>
  );
}
