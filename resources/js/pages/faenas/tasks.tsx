import { router } from '@inertiajs/react';
import { Add, Edit, Delete, CheckCircle } from '@mui/icons-material';
import { Box, Typography, Button, IconButton , Tooltip} from '@mui/material';
import { useMemo, useState, } from 'react';
import type { Column, HeaderIndicatorData } from '@/components/faenas/faenas-types';
import FaenasDataTable from '@/components/faenas/FaenasDataTable';
import FaenasFilterBar from '@/components/faenas/FaenasFilterBar';
import FaenasHeaderIndicator from '@/components/faenas/FaenasHeaderIndicator';


interface FaenaItem {
  id: string;
  fecha: string;
  empleado: string;
  jornada: string;
  centroCosto: string;
  tipoFaena: string;
  descripcion: string;
  costo: number;
}

interface Props {
  actividades: Array<{ id: string; nombre: string }>;
  centrosCosto: Array<{ id: string; nombre: string }>;
  items: FaenaItem[];
}

const columns: Column[] = [
  { key: 'fecha', label: 'Fecha', width: 100 },
  { key: 'empleado', label: 'Empleado' },
  { key: 'jornada', label: 'Jornada', width: 100 },
  { key: 'centroCosto', label: 'Centro de costo' },
  { key: 'tipoFaena', label: 'Labores' },
  { key: 'descripcion', label: 'Descripción' },
  { key: 'costo', label: 'Costo', align: 'right', width: 120, render: (v) => `$${Number(v).toLocaleString('es-CL', { minimumFractionDigits: 2 })}` },
  { key: 'acciones', label: 'Acciones', align: 'center', width: 100, sortable: false, render: (_, row) => (
    <Box sx={{ display: 'flex', gap: 0.5, justifyContent: 'center' }}>
      <Tooltip title="Editar">
                 <IconButton size="small" onClick={(e) => {
 e.stopPropagation(); handleEdit(row);
}}>
                   <Edit fontSize="small" />
                 </IconButton>
               </Tooltip>
               <Tooltip title="Eliminar">
                 <IconButton size="small" onClick={(e) => {
 e.stopPropagation(); handleDelete(row);
}} color="error">
                   <Delete fontSize="small" />
                 </IconButton>
               </Tooltip>
      <CheckCircle fontSize="small" color="success" />
    </Box>
  )},
];





export default function Tasks({actividades, centrosCosto, items}: Props) {
  const [selectValues, setSelectValues] = useState<Record<string, string>>({});
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');
const [modalOpen, setModalOpen] = useState(false);
  const [editingItem, setEditingItem] = useState<Record<string, unknown> | null>(null);

  const [deleteTarget, setDeleteTarget] = useState<Record<string, unknown> | null>(null);

  const handleEdit = (item: Record<string, unknown>) => {
    setEditingItem(item);
    setModalOpen(true);
  };
  const selectFilters = useMemo(
    () => [
                {
                    key: 'tipoFaena',
                    label: 'Tipo de labor',
                    options:actividades.map((actividad) => (
                        { value: actividad.id,
                        label: actividad.nombre
                        }
                    )),
                },
                {
                    key: 'centroCosto',
                    label: 'Centro de costo',
                    options:centrosCosto.map((centroCosto) => (
                        { value: centroCosto.id,
                        label: centroCosto.nombre
                        }
                    )),
                },
            ],
     [actividades],[centrosCosto]);





  const indicators: HeaderIndicatorData[] = [
    { label: 'Labores', value: 245 },
    { label: 'Jornadas', value: 97.17, format: 'decimal' },
    { label: 'Sin Jornada', value: 0 },
    { label: 'Horas', value: 1554.7, format: 'decimal' },
    { label: 'Total costos', value: 4757124.22, format: 'currency' },
  ];

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Faenas</Typography>
      <FaenasHeaderIndicator indicators={indicators} />
      <FaenasFilterBar
        selects={selectFilters} selectValues={selectValues}
        onSelectChange={(k, v) => setSelectValues((p) => ({ ...p, [k]: v }))}
        dateFrom={dateFrom} dateTo={dateTo}
        onDateFromChange={setDateFrom} onDateToChange={setDateTo}
        onSearch={() => {}} onClear={() => {
 setSelectValues({}); setDateFrom(''); setDateTo('');
}}
        actions={
          <Button variant="contained" size="small" startIcon={<Add />}  onClick={() => router.visit('/faenas/tasks-creation')}>

            Crear faenas
          </Button>
        }
      />
      <FaenasDataTable columns={columns} items={items} />
    </Box>
  );
}
