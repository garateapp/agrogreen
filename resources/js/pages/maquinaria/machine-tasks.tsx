import { router } from '@inertiajs/react';
import { Add, Edit, Delete } from '@mui/icons-material';
import { Box, Typography, Button, IconButton } from '@mui/material';
import { useState, useEffect } from 'react';
import MachineTaskModal from '@/components/maquinaria/MachineTaskModal';
import type { Column } from '@/components/maquinaria/maquinaria-types';
import MaquinariaDataTable from '@/components/maquinaria/MaquinariaDataTable';
import MaquinariaFilterBar from '@/components/maquinaria/MaquinariaFilterBar';
import MaquinariaHeaderIndicator from '@/components/maquinaria/MaquinariaHeaderIndicator';

interface SelectItem {
  id: string;
  nombre: string;
  apellido?: string;
  patente_o_identificador?: string;
  tipo?: string;
}

interface Item {
  id: string;
  fecha: string;
  tractor: { nombre: string; patente_o_identificador?: string } | null;
  operador: { nombre: string; apellido?: string } | null;
  centro_costo: { nombre: string } | null;
  horas_inicio: number;
  horas_fin: number;
  horas_totales: number;
  tractor_id: string;
  operador_id: string;
  centro_costo_id: string;
}

interface Props {
  items: Item[];
  tractores: SelectItem[];
  empleados: SelectItem[];
  centrosCosto: SelectItem[];
}

const columns: Column[] = [
  { key: 'fecha', label: 'Fecha', width: 100 },
  {
    key: 'tractor',
    label: 'Máquina',
    render: (_: unknown, row: Record<string, unknown>) => {
      const t = row.tractor as { nombre: string; patente_o_identificador?: string } | null;

      return t ? `${t.nombre}${t.patente_o_identificador ? ` (${t.patente_o_identificador})` : ''}` : '';
    },
  },
  {
    key: 'operador',
    label: 'Operador',
    render: (_: unknown, row: Record<string, unknown>) => {
      const o = row.operador as { nombre: string; apellido?: string } | null;

      return o ? `${o.nombre} ${o.apellido ?? ''}`.trim() : '';
    },
  },
  {
    key: 'centro_costo',
    label: 'Centro de costo',
    render: (_: unknown, row: Record<string, unknown>) => {
      const c = row.centro_costo as { nombre: string } | null;

      return c?.nombre ?? '';
    },
  },
  { key: 'horas_inicio', label: 'Hrs inicio', align: 'right', width: 80 },
  { key: 'horas_fin', label: 'Hrs fin', align: 'right', width: 80 },
  { key: 'horas_totales', label: 'Hrs totales', align: 'right', width: 90 },
  {
    key: 'acciones', label: 'Acciones', align: 'center', width: 80, sortable: false,
    render: (_: unknown, row: Record<string, unknown>) => (
      <Box sx={{ display: 'flex', gap: 0.5, justifyContent: 'center' }}>
        <IconButton size="small" color="primary" onClick={() => handleEdit(row)}>
          <Edit fontSize="small" />
        </IconButton>
        <IconButton size="small" color="error" onClick={() => handleDelete(row)}>
          <Delete fontSize="small" />
        </IconButton>
      </Box>
    ),
  },
];

function handleEdit(row: Record<string, unknown>) {
  document.dispatchEvent(new CustomEvent('edit-machine-task', { detail: row }));
}

function handleDelete(row: Record<string, unknown>) {
  if (confirm(`¿Eliminar faena de maquinaria?`)) {
    router.delete(`/maquinaria/machine-tasks/${row.id}`, {
      preserveScroll: true,
      onSuccess: () => router.reload(),
    });
  }
}

export default function MachineTasks({ items, tractores, empleados, centrosCosto }: Props) {
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');
  const [modalOpen, setModalOpen] = useState(false);
  const [editingItem, setEditingItem] = useState<Record<string, unknown> | null>(null);

  useEffect(() => {
    const handler = (e: Event) => {
      const detail = (e as CustomEvent).detail;
      setEditingItem(detail);
      setModalOpen(true);
    };
    document.addEventListener('edit-machine-task', handler);

    return () => document.removeEventListener('edit-machine-task', handler);
  });

  const handleOpenCreate = () => {
    setEditingItem(null);
    setModalOpen(true);
  };

  const handleClose = () => {
    setModalOpen(false);
    setEditingItem(null);
  };

  const totalHoras = items.reduce((s, i) => s + (i.horas_totales ?? 0), 0);
  const totalRegistros = items.length;

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Faenas de Maquinaria</Typography>
      <MaquinariaHeaderIndicator indicators={[
        { label: 'Faenas', value: totalRegistros },
        { label: 'Horas totales', value: Math.round(totalHoras * 100) / 100 },
      ]} />
      <MaquinariaFilterBar
        dateFrom={dateFrom} dateTo={dateTo}
        onDateFromChange={setDateFrom} onDateToChange={setDateTo}
        onSearch={() => {}} onClear={() => {
 setDateFrom(''); setDateTo(''); 
}}
        selectValues={{}} onSelectChange={() => {}}
        actions={
          <Button variant="contained" size="small" startIcon={<Add />} onClick={handleOpenCreate}>
            Nueva faena
          </Button>
        }
      />
      <MaquinariaDataTable columns={columns} items={items as unknown as Record<string, unknown>[]} />
      <MachineTaskModal
        open={modalOpen}
        onClose={handleClose}
        tractores={tractores}
        empleados={empleados}
        centrosCosto={centrosCosto}
        item={editingItem}
      />
    </Box>
  );
}
