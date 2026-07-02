import { GetApp } from '@mui/icons-material';
import { Box, Typography, Button, FormControl, InputLabel, Select, MenuItem } from '@mui/material';
import { useState } from 'react';
import type { Column } from '@/components/maquinaria/maquinaria-types';
import MaquinariaDataTable from '@/components/maquinaria/MaquinariaDataTable';
import MaquinariaHeaderIndicator from '@/components/maquinaria/MaquinariaHeaderIndicator';

interface TractorItem {
  id: string;
  nombre: string;
  patente_o_identificador: string;
  tipo: string;
}

interface ReportItem {
  id: string;
  maquina: string;
  patente: string;
  tipo: string;
  horas: number;
  costoConsumos: number;
  costoTotal: number;
  costoHora: number;
}

interface Props {
  items: ReportItem[];
  tractores: TractorItem[];
}

const columns: Column[] = [
  {
    key: 'maquina',
    label: 'Máquina',
    render: (_: unknown, row: Record<string, unknown>) => {
      const tipo = row.tipo as string;
      const patente = row.patente as string;

      return `${row.maquina}${patente ? ` (${patente})` : ''}${tipo ? ` — ${tipo}` : ''}`;
    },
  },
  { key: 'horas', label: 'Horas', align: 'right', width: 80,
    render: (v: unknown) => Number(v).toLocaleString('es-CL', { maximumFractionDigits: 2 }),
  },
  { key: 'costoConsumos', label: 'Costo consumos', align: 'right', width: 140,
    render: (v: unknown) => `$${Number(v).toLocaleString('es-CL')}`,
  },
  { key: 'costoTotal', label: 'Costo total', align: 'right', width: 130,
    render: (v: unknown) => `$${Number(v).toLocaleString('es-CL')}`,
  },
  { key: 'costoHora', label: 'Costo hora prom.', align: 'right', width: 140,
    render: (v: unknown) => `$${Number(v).toLocaleString('es-CL')}`,
  },
];

export default function MachineReport({ items, tractores }: Props) {
  const [maquina, setMaquina] = useState('');

  const filtered = maquina ? items.filter((i) => i.id === maquina) : items;

  const totalCosto = filtered.reduce((s, i) => s + i.costoTotal, 0);
  const totalHoras = filtered.reduce((s, i) => s + i.horas, 0);

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Reporte de Maquinaria</Typography>
      <MaquinariaHeaderIndicator indicators={[
        { label: 'Costo total', value: totalCosto, format: 'currency' },
        { label: 'Horas totales', value: Math.round(totalHoras * 100) / 100 },
        { label: 'Costo hora prom.', value: totalHoras > 0 ? Math.round(totalCosto / totalHoras) : 0, format: 'currency' },
      ]} />
      <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', alignItems: 'center', mb: 2 }}>
        <FormControl size="small" sx={{ minWidth: 200 }}>
          <InputLabel>Maquinaria</InputLabel>
          <Select value={maquina} label="Maquinaria" onChange={(e) => setMaquina(e.target.value)}>
            <MenuItem value="">Todas</MenuItem>
            {tractores.map((t) => (
              <MenuItem key={t.id} value={t.id}>{t.nombre}</MenuItem>
            ))}
          </Select>
        </FormControl>
        <Button variant="outlined" size="small" color="inherit"
          onClick={() => setMaquina('')}>Limpiar</Button>
        <Button variant="outlined" size="small" startIcon={<GetApp />} sx={{ ml: 'auto' }}>Exportar</Button>
      </Box>
      <MaquinariaDataTable columns={columns} items={filtered as unknown as Record<string, unknown>[]} />
    </Box>
  );
}
