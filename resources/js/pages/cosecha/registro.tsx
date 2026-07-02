import { router } from '@inertiajs/react';
import { Head } from '@inertiajs/react';
import { Box, Button, Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle, Typography } from '@mui/material';
import { useState, useMemo } from 'react';
import type { Column } from '@/components/maquinaria/maquinaria-types';
import MaquinariaDataTable from '@/components/maquinaria/MaquinariaDataTable';
import MaquinariaFilterBar from '@/components/maquinaria/MaquinariaFilterBar';
import MaquinariaHeaderIndicator from '@/components/maquinaria/MaquinariaHeaderIndicator';

interface EmpleadoItem { id: string; nombre: string; apellido: string }
interface CuartelItem { id: string; nombre: string }
interface ContenedorItem { id: string; nombre: string; peso_bin_kg: number }
interface CosechaItem {
  id: string; fecha_hora: string; codigo_tarjeta_qr: string;
  peso_bruto: number; peso_tara: number; peso_neto: number;
  empleado: EmpleadoItem | null; cuartel: CuartelItem | null; contenedor: ContenedorItem | null;
}
interface Resumen { total_kilos: number; total_envases: number; empleados_unicos: number; total_bruto: number }

interface Props {
  items: CosechaItem[];
  resumen: Resumen;
  cuarteles: CuartelItem[];
  empleados: EmpleadoItem[];
  contenedores: ContenedorItem[];
  filters: Record<string, string>;
}

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('es-CL', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

const columns: Column[] = [
  {
    key: 'fecha_hora', label: 'Fecha/Hora',
    render: (v: unknown) => formatDate(v as string),
  },
  {
    key: 'empleado', label: 'Empleado',
    render: (_: unknown, row: Record<string, unknown>) => {
      const e = row.empleado as EmpleadoItem | null;

      return e ? `${e.nombre} ${e.apellido}`.trim() : '';
    },
  },
  {
    key: 'cuartel', label: 'Cuartel',
    render: (_: unknown, row: Record<string, unknown>) => {
      const c = row.cuartel as CuartelItem | null;

      return c?.nombre ?? '';
    },
  },
  {
    key: 'contenedor', label: 'Contenedor',
    render: (_: unknown, row: Record<string, unknown>) => {
      const c = row.contenedor as ContenedorItem | null;

      return c?.nombre ?? '';
    },
  },
  { key: 'codigo_tarjeta_qr', label: 'QR Tarjeta', width: 100 },
  { key: 'peso_bruto', label: 'Bruto (kg)', align: 'right', width: 90,
    render: (v: unknown) => Number(v).toLocaleString('es-CL', { minimumFractionDigits: 3, maximumFractionDigits: 3 }),
  },
  { key: 'peso_tara', label: 'Tara (kg)', align: 'right', width: 90,
    render: (v: unknown) => Number(v).toLocaleString('es-CL', { minimumFractionDigits: 3, maximumFractionDigits: 3 }),
  },
  { key: 'peso_neto', label: 'Neto (kg)', align: 'right', width: 90,
    render: (v: unknown) => Number(v).toLocaleString('es-CL', { minimumFractionDigits: 3, maximumFractionDigits: 3 }),
  },
];

export default function RegistroCosecha({ items, resumen, cuarteles, empleados, filters }: Props) {
  const [dateFrom, setDateFrom] = useState(filters.fecha_desde ?? '');
  const [dateTo, setDateTo] = useState(filters.fecha_hasta ?? '');
  const [cuartelFilter, setCuartelFilter] = useState(filters.cuartel_id ?? '');
  const [empleadoFilter, setEmpleadoFilter] = useState(filters.empleado_id ?? '');
  const [dialogOpen, setDialogOpen] = useState(false);

  const handleCerrarJornada = () => {
    router.post('/cosecha/cerrar-jornada', {}, { preserveState: true });
    setDialogOpen(false);
  };

  const selectValues = useMemo(() => ({
    cuartel_id: cuartelFilter,
    empleado_id: empleadoFilter,
  }), [cuartelFilter, empleadoFilter]);

  const handleSearch = () => {
    router.get('/cosecha/registro', {
      fecha_desde: dateFrom || undefined,
      fecha_hasta: dateTo || undefined,
      cuartel_id: cuartelFilter || undefined,
      empleado_id: empleadoFilter || undefined,
    }, { preserveState: true, replace: true });
  };

  const handleClear = () => {
    setDateFrom(''); setDateTo(''); setCuartelFilter(''); setEmpleadoFilter('');
    router.get('/cosecha/registro', {}, { preserveState: true, replace: true });
  };

  return (
    <>
      <Head title="Registro de Cosecha" />
      <Box>
        <Typography variant="h5" sx={{ mb: 1 }}>Registro de Cosecha</Typography>
        <MaquinariaHeaderIndicator indicators={[
          { label: 'Total kilos netos', value: resumen.total_kilos, format: 'number' },
          { label: 'Total bruto', value: resumen.total_bruto, format: 'number' },
          { label: 'Envases vaciados', value: resumen.total_envases },
          { label: 'Empleados únicos', value: resumen.empleados_unicos },
        ]} />
        <MaquinariaFilterBar
          dateFrom={dateFrom} dateTo={dateTo}
          onDateFromChange={setDateFrom} onDateToChange={setDateTo}
          onSearch={handleSearch} onClear={handleClear}
          selectValues={selectValues}
          onSelectChange={(key, value) => {
            if (key === 'cuartel_id') {
setCuartelFilter(value);
}

            if (key === 'empleado_id') {
setEmpleadoFilter(value);
}
          }}
          selects={[
            { key: 'cuartel_id', label: 'Cuartel', options: cuarteles.map(c => ({ value: c.id, label: c.nombre })) },
            { key: 'empleado_id', label: 'Empleado', options: empleados.map(e => ({ value: e.id, label: `${e.nombre} ${e.apellido}`.trim() })) },
          ]}
        />
        <Box sx={{ display: 'flex', gap: 2, alignItems: 'center', mb: 2 }}>
          <Button variant="contained" color="primary" onClick={() => setDialogOpen(true)}>
            Cerrar Jornada
          </Button>
        </Box>
        <MaquinariaDataTable columns={columns} items={items as unknown as Record<string, unknown>[]} />
      </Box>
      <Dialog open={dialogOpen} onClose={() => setDialogOpen(false)}>
        <DialogTitle>Cerrar Jornada</DialogTitle>
        <DialogContent>
          <DialogContentText>
            Se cerrará la jornada de hoy y se generarán las faenas correspondientes en base a los registros de cosecha del día. ¿Deseas continuar?
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDialogOpen(false)}>Cancelar</Button>
          <Button onClick={handleCerrarJornada} variant="contained" color="primary" autoFocus>
            Confirmar
          </Button>
        </DialogActions>
      </Dialog>
    </>
  );
}

RegistroCosecha.layout = {
  breadcrumbs: [
    { title: 'Cosecha', href: '/cosecha/registro' },
    { title: 'Registro de Cosecha', href: '/cosecha/registro' },
  ],
};
