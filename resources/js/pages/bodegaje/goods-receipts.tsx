import { usePage, router } from '@inertiajs/react';
import { Add, Edit, Delete } from '@mui/icons-material';
import { Box, Typography, Button, IconButton, Tooltip } from '@mui/material';
import { useState } from 'react';
import type { Column } from '@/components/bodegaje/bodegaje-types';
import BodegajeDataTable from '@/components/bodegaje/BodegajeDataTable';
import BodegajeFilterBar from '@/components/bodegaje/BodegajeFilterBar';
import GoodsReceiptModal from '@/components/bodegaje/GoodsReceiptModal';
import MotivoDialog from '@/components/bodegaje/MotivoDialog';

interface PageProps {
  items: Record<string, unknown>[];
  proveedores: Array<{ id: string; razon_social: string; rut: string }>;
  productos: Array<{ id: string; nombre: string }>;
  bodegas: Array<{ id: string; nombre: string }>;
  centroCostos: Array<{ id: string; nombre: string }>;
}

export default function GoodsReceipts() {
  const { items, proveedores, productos, bodegas, centroCostos } = usePage().props as unknown as PageProps;
  const [search, setSearch] = useState('');
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');
  const [modalOpen, setModalOpen] = useState(false);
  const [editingItem, setEditingItem] = useState<Record<string, unknown> | null>(null);

  const [deleteTarget, setDeleteTarget] = useState<Record<string, unknown> | null>(null);

  const handleEdit = (item: Record<string, unknown>) => {
    setEditingItem(item);
    setModalOpen(true);
  };

  const handleDelete = (item: Record<string, unknown>) => {
    setDeleteTarget(item);
  };

  const columns: Column[] = [
    { key: 'numero', label: 'Número', width: 120 },
    { key: 'fecha', label: 'Fecha', width: 120 },
    { key: 'proveedor', label: 'Proveedor', width: 200 },
    { key: 'tipo', label: 'Tipo', width: 100 },
    {
      key: 'total',
      label: 'Total',
      width: 120,
      align: 'right',
      render: (value: unknown) => value ? `$${Number(value).toLocaleString('es-CL')}` : '',
    },
    {
      key: 'acciones',
      label: '',
      width: 80,
      sortable: false,
      render: (_value: unknown, row: Record<string, unknown>) => (
        <Box sx={{ display: 'flex', gap: 0.5 }}>
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
        </Box>
      ),
    },
  ];

  const filtered = items.filter((item) => {
    const s = search.toLowerCase();

    if (s && !String(item.numero).toLowerCase().includes(s) && !String(item.proveedor ?? '').toLowerCase().includes(s)) {
return false;
}

    if (dateFrom && String(item.fecha) < dateFrom) {
return false;
}

    if (dateTo && String(item.fecha) > dateTo) {
return false;
}

    return true;
  });

  const handleClose = () => {
    setModalOpen(false);
    setEditingItem(null);
  };

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Guías de Entrada</Typography>
      <BodegajeFilterBar
        searchValue={search}
        onSearchChange={setSearch}
        onSearch={() => {}}
        onClear={() => {
 setSearch(''); setDateFrom(''); setDateTo(''); 
}}
        showDateRange dateFrom={dateFrom} dateTo={dateTo}
        onDateFromChange={setDateFrom} onDateToChange={setDateTo}
        actions={
          <Button variant="contained" size="small" startIcon={<Add />} onClick={() => setModalOpen(true)}>
            Nueva guía de entrada
          </Button>
        }
      />
      <BodegajeDataTable columns={columns} items={filtered} />
      <GoodsReceiptModal
        open={modalOpen}
        onClose={handleClose}
        proveedores={proveedores}
        productos={productos}
        bodegas={bodegas}
        centroCostos={centroCostos}
        editingItem={editingItem}
      />
      <MotivoDialog
        open={!!deleteTarget}
        title={`Eliminar: ${deleteTarget?.numero ?? ''}`}
        onClose={() => setDeleteTarget(null)}
        onConfirm={(motivo) => {
          if (!deleteTarget) {
return;
}

          router.delete(`/bodegaje/goods-receipts/${deleteTarget.id}`, {
            data: { motivo } as any,
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => setDeleteTarget(null),
          });
        }}
      />
    </Box>
  );
}

GoodsReceipts.layout = {
  breadcrumbs: [
    { title: 'Bodegaje', href: '/bodegaje' },
    { title: 'Guías de Entrada', href: '/bodegaje/goods-receipts' },
  ],
};
