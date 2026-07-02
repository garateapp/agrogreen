import { router } from '@inertiajs/react';
import { Add, Edit, Delete } from '@mui/icons-material';
import { Box, Typography, Button, IconButton, Tooltip } from '@mui/material';
import { useState, useMemo } from 'react';
import type { Column } from '@/components/bodegaje/bodegaje-types';
import BodegajeDataTable from '@/components/bodegaje/BodegajeDataTable';
import BodegajeFilterBar from '@/components/bodegaje/BodegajeFilterBar';
import GoodsIssueModal from '@/components/bodegaje/GoodsIssueModal';
import MotivoDialog from '@/components/bodegaje/MotivoDialog';

interface ProductoOption {
  id: string;
  nombre: string;
  unidad: string;
  stockTotal: number;
  stockPorBodega: Record<string, number>;
}

interface Props {
  items: Record<string, unknown>[];
  productos: ProductoOption[];
  bodegas: Array<{ id: string; nombre: string }>;
}

export default function GoodsIssues({ items, productos, bodegas }: Props) {
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
    { key: 'codigo', label: 'Código', width: 120 },
    { key: 'fecha', label: 'Fecha', width: 120 },
    { key: 'bodega', label: 'Bodega', width: 160 },
    {
      key: 'total',
      label: 'Total',
      width: 120,
      align: 'right',
      render: (value: unknown) => value ? Number(value).toLocaleString('es-CL') : '',
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

  const filtered = useMemo(() => {
    const s = search.toLowerCase();

    return items.filter((item) => {
      if (s && !String(item.codigo).toLowerCase().includes(s)) {
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
  }, [items, search, dateFrom, dateTo]);

  const handleClose = () => {
    setModalOpen(false);
    setEditingItem(null);
  };

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Guías de Consumo</Typography>
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
            Nueva guía de consumo
          </Button>
        }
      />
      <BodegajeDataTable columns={columns} items={filtered} />
      <GoodsIssueModal
        open={modalOpen}
        onClose={handleClose}
        productos={productos}
        bodegas={bodegas}
        editingItem={editingItem}
      />
      <MotivoDialog
        open={!!deleteTarget}
        title={`Eliminar: ${deleteTarget?.codigo ?? ''}`}
        onClose={() => setDeleteTarget(null)}
        onConfirm={(motivo) => {
          if (!deleteTarget) {
return;
}

          router.delete(`/bodegaje/goods-issues/${deleteTarget.id}`, {
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
