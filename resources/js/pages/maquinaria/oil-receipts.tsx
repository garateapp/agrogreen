import { router } from '@inertiajs/react';
import { Add, Edit, Delete } from '@mui/icons-material';
import { Box, Typography, Button, IconButton } from '@mui/material';
import { useState, useEffect } from 'react';
import type { Column } from '@/components/maquinaria/maquinaria-types';
import MaquinariaDataTable from '@/components/maquinaria/MaquinariaDataTable';
import MaquinariaFilterBar from '@/components/maquinaria/MaquinariaFilterBar';
import MaquinariaHeaderIndicator from '@/components/maquinaria/MaquinariaHeaderIndicator';
import OilReceiptModal from '@/components/maquinaria/OilReceiptModal';

interface FaenaItem {
  id: string;
  tractor: { nombre: string } | null;
  fecha: string;
}

interface ProductoItem {
  id: string;
  nombre: string;
}

interface Item {
  id: string;
  uso_maquinaria_id: string;
  producto_id: string;
  cantidad_litros: number;
  costo_total_moneda_base: number;
  usoMaquinaria: { tractor: { nombre: string } | null; fecha: string } | null;
  producto: { nombre: string } | null;
}

interface Props {
  items: Item[];
  faenas: FaenaItem[];
  productos: ProductoItem[];
}

const columns: Column[] = [
  {
    key: 'usoMaquinaria',
    label: 'Faena',
    render: (_: unknown, row: Record<string, unknown>) => {
      const u = row.usoMaquinaria as { tractor: { nombre: string } | null; fecha: string } | null;

      if (!u) {
return '—';
}

      return `${u.fecha} — ${u.tractor?.nombre ?? '?'}`;
    },
  },
  {
    key: 'producto',
    label: 'Producto',
    render: (_: unknown, row: Record<string, unknown>) => {
      const p = row.producto as { nombre: string } | null;

      return p?.nombre ?? '—';
    },
  },
  { key: 'cantidad_litros', label: 'Cantidad (L)', align: 'right', width: 100,
    render: (v: unknown) => Number(v).toLocaleString('es-CL', { maximumFractionDigits: 2 }),
  },
  { key: 'costo_total_moneda_base', label: 'Costo total', align: 'right', width: 130,
    render: (v: unknown) => `$${Number(v).toLocaleString('es-CL')}`,
  },
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
  document.dispatchEvent(new CustomEvent('edit-oil-receipt', { detail: row }));
}

function handleDelete(row: Record<string, unknown>) {
  if (confirm('¿Eliminar salida de producto?')) {
    router.delete(`/maquinaria/oil-receipts/${row.id}`, {
      preserveScroll: true,
      onSuccess: () => router.reload(),
    });
  }
}

export default function OilReceipts({ items, faenas, productos }: Props) {
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');
  const [modalOpen, setModalOpen] = useState(false);
  const [editingItem, setEditingItem] = useState<Record<string, unknown> | null>(null);

  useEffect(() => {
    const handler = (e: Event) => {
      setEditingItem((e as CustomEvent).detail);
      setModalOpen(true);
    };
    document.addEventListener('edit-oil-receipt', handler);

    return () => document.removeEventListener('edit-oil-receipt', handler);
  }, []);

  const handleOpenCreate = () => {
    setEditingItem(null);
    setModalOpen(true);
  };

  const handleClose = () => {
    setModalOpen(false);
    setEditingItem(null);
  };

  const totalCosto = items.reduce((s, i) => s + (i.costo_total_moneda_base ?? 0), 0);

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Salidas de Productos de Maquinaria</Typography>
      <MaquinariaHeaderIndicator indicators={[
        { label: 'Salidas', value: items.length },
        { label: 'Costo total', value: totalCosto, format: 'currency' },
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
            Nueva salida
          </Button>
        }
      />
      <MaquinariaDataTable columns={columns} items={items as unknown as Record<string, unknown>[]} />
      <OilReceiptModal
        open={modalOpen}
        onClose={handleClose}
        faenas={faenas}
        productos={productos}
        item={editingItem}
      />
    </Box>
  );
}
