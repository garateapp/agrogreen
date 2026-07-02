import { Head } from '@inertiajs/react';
import { Box, Typography, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Paper, Chip } from '@mui/material';
import type { Column } from '@/components/maquinaria/maquinaria-types';
import MaquinariaDataTable from '@/components/maquinaria/MaquinariaDataTable';
import MaquinariaHeaderIndicator from '@/components/maquinaria/MaquinariaHeaderIndicator';

interface ContenedorCosechaItem { id: string; nombre: string; unidades_por_bin: number; peso_bin_kg: number }
interface BinItem {
  id: string; folio: string; estado: string;
  fecha_apertura: string; fecha_cierre: string | null;
  contenedor_cosecha: ContenedorCosechaItem | null;
}
interface CuartelResumen { cuartel_id: string; total_envases: number; total_kilos: number; cuartel: { nombre: string } | null }
interface CuartelItem { id: string; nombre: string }
interface ResumenGeneral { total_bins_abiertos: number; total_bins_cerrados: number; total_kilos_hoy: number }

interface Props {
  bins: BinItem[];
  resumenPorCuartel: CuartelResumen[];
  cuarteles: CuartelItem[];
  resumenGeneral: ResumenGeneral;
}

const binColumns: Column[] = [
  { key: 'folio', label: 'Folio QR', width: 120 },
  {
    key: 'contenedor_cosecha', label: 'Tipo envase',
    render: (_: unknown, row: Record<string, unknown>) => {
      const c = row.contenedor_cosecha as ContenedorCosechaItem | null;

      return c?.nombre ?? '';
    },
  },
  {
    key: 'estado', label: 'Estado', width: 100,
    render: (v: unknown) => {
      const estado = v as string;

      return (
        <Chip label={estado === 'abierto' ? 'Abierto' : 'Cerrado'}
          color={estado === 'abierto' ? 'success' : 'default'}
          size="small" variant="outlined" />
      );
    },
  },
  {
    key: 'fecha_apertura', label: 'Apertura',
    render: (v: unknown) => v ? new Date(v as string).toLocaleString('es-CL') : '',
  },
  {
    key: 'fecha_cierre', label: 'Cierre',
    render: (v: unknown) => v ? new Date(v as string).toLocaleString('es-CL') : '—',
  },
];

const cuartelResumenColumns: Column[] = [
  {
    key: 'cuartel', label: 'Cuartel',
    render: (_: unknown, row: Record<string, unknown>) => {
      const c = row.cuartel as { nombre: string } | null;

      return c?.nombre ?? '—';
    },
  },
  { key: 'total_envases', label: 'Envases', align: 'right', width: 100 },
  {
    key: 'total_kilos', label: 'Kilos netos', align: 'right', width: 120,
    render: (v: unknown) => Number(v).toLocaleString('es-CL', { minimumFractionDigits: 1, maximumFractionDigits: 1 }),
  },
];

export default function ContenedoresCosecha({ bins, resumenPorCuartel, cuarteles, resumenGeneral }: Props) {
  return (
    <>
      <Head title="Contenedores de Cosecha" />
      <Box>
        <Typography variant="h5" sx={{ mb: 1 }}>Contenedores de Cosecha</Typography>
        <MaquinariaHeaderIndicator indicators={[
          { label: 'Bins abiertos', value: resumenGeneral.total_bins_abiertos },
          { label: 'Bins cerrados', value: resumenGeneral.total_bins_cerrados },
          { label: 'Kilos recolectados hoy', value: resumenGeneral.total_kilos_hoy, format: 'number' },
        ]} />

        <Typography variant="subtitle1" sx={{ mt: 3, mb: 1, fontWeight: 600 }}>Bins (contenedores físicos)</Typography>
        <MaquinariaDataTable columns={binColumns} items={bins as unknown as Record<string, unknown>[]} />

        <Typography variant="subtitle1" sx={{ mt: 4, mb: 1, fontWeight: 600 }}>Resumen por cuartel (hoy)</Typography>
        <MaquinariaDataTable columns={cuartelResumenColumns} items={resumenPorCuartel as unknown as Record<string, unknown>[]} />
      </Box>
    </>
  );
}

ContenedoresCosecha.layout = {
  breadcrumbs: [
    { title: 'Cosecha', href: '/cosecha/contenedores' },
    { title: 'Contenedores', href: '/cosecha/contenedores' },
  ],
};
