import { router } from '@inertiajs/react';
import { Add, Delete, Edit, GetApp, Visibility } from '@mui/icons-material';
import { Box, Typography, Button, IconButton } from '@mui/material';
import { useMemo, useState } from 'react';
import MotivoDialog from '@/components/bodegaje/MotivoDialog';
import type { Column } from '@/components/compras/compras-types';
import ComprasDataTable from '@/components/compras/ComprasDataTable';
import ComprasFilterBar from '@/components/compras/ComprasFilterBar';
import ComprasHeaderIndicator from '@/components/compras/ComprasHeaderIndicator';
import DetailDialog from '@/components/compras/DetailDialog';
import IngresoModal from '@/components/compras/IngresoModal';
import Pagination from '@/components/compras/Pagination';

const baseColumns: Column[] = [
  { key: 'folio', label: 'Folio', width: 90 },
  { key: 'cliente', label: 'Cliente' },
  { key: 'tipoDoc', label: 'Tipo Doc.', width: 110 },
  { key: 'fecha', label: 'Fecha', width: 140 },
  { key: 'neto', label: 'Neto', align: 'right', width: 110 },
  { key: 'iva', label: 'IVA', align: 'right', width: 100 },
  { key: 'total', label: 'Total', align: 'right', width: 120 },
  { key: 'estado', label: 'Estado', width: 100 },
];

interface PaginatedItems {
  data: Record<string, unknown>[];
  meta: { current_page: number; last_page: number; total: number; from: number; to: number };
}

interface Props {
  pageTitle: string;
  items: PaginatedItems;
  filterOptions: {
    clientes?: { value: string; label: string }[];
  };
  filters: Record<string, string>;
}

export default function Incomes({ pageTitle, items, filterOptions, filters }: Props) {
  const [search, setSearch] = useState(filters.search ?? '');
  const [dateFrom, setDateFrom] = useState(filters.date_from ?? '');
  const [dateTo, setDateTo] = useState(filters.date_to ?? '');

  const [modalOpen, setModalOpen] = useState(false);
  const [editingItem, setEditingItem] = useState<Record<string, unknown> | null>(null);
  const [detailItem, setDetailItem] = useState<Record<string, unknown> | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<Record<string, unknown> | null>(null);

  const columns = useMemo<Column[]>(() => [
    ...baseColumns,
    {
      key: 'acciones', label: '', width: 80, sortable: false,
      render: (_, row) => (
        <Box sx={{ display: 'flex', gap: 0.5 }}>
          <IconButton size="small" onClick={(e) => {
 e.stopPropagation(); setDetailItem(row); 
}}>
            <Visibility fontSize="small" />
          </IconButton>
          <IconButton size="small" onClick={(e) => {
 e.stopPropagation(); setEditingItem(row); setModalOpen(true); 
}}>
            <Edit fontSize="small" />
          </IconButton>
          <IconButton size="small" onClick={(e) => {
 e.stopPropagation(); setDeleteTarget(row); 
}} color="error">
            <Delete fontSize="small" />
          </IconButton>
        </Box>
      ),
    },
  ], []);

  const totalTot = items.data.reduce((s, i) => s + (i.total as number), 0);

  const buildParams = () => {
    const params: Record<string, string> = {};

    if (search) {
params.search = search;
}

    if (dateFrom) {
params.date_from = dateFrom;
}

    if (dateTo) {
params.date_to = dateTo;
}

    return params;
  };

  const handleSearch = () => {
    router.get('/compras/incomes', { ...buildParams(), page: 1 }, { preserveState: true, replace: true });
  };

  const handleClear = () => {
    setSearch('');
    setDateFrom('');
    setDateTo('');
    router.get('/compras/incomes', {}, { preserveState: true, replace: true });
  };

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>{pageTitle}</Typography>
      <ComprasHeaderIndicator countLabel="Ingresos" countValue={items.meta.total} totalLabel="Total Ingresos" totalValue={totalTot} />
      <ComprasFilterBar
        searchValue={search}
        onSearchChange={setSearch}
        onSearch={handleSearch}
        onClear={handleClear}
        showDateRange
        dateFrom={dateFrom}
        dateTo={dateTo}
        onDateFromChange={setDateFrom}
        onDateToChange={setDateTo}
        actions={
          <>
            <Button variant="contained" size="small" startIcon={<Add />} onClick={() => {
 setEditingItem(null); setModalOpen(true); 
}}>Nuevo Ingreso</Button>
            <Button variant="outlined" size="small" startIcon={<GetApp />}>Exportar</Button>
          </>
        }
      />
      <ComprasDataTable columns={columns} items={items.data} />
      <Pagination meta={items.meta} queryParams={buildParams()} baseUrl="/compras/incomes" />

      <IngresoModal
        open={modalOpen}
        onClose={() => {
 setModalOpen(false); setEditingItem(null); 
}}
        editingItem={editingItem}
        clientes={filterOptions.clientes ?? []}
      />

      <DetailDialog
        open={!!detailItem}
        title={`Detalle Ingreso: ${detailItem?.folio ?? ''}`}
        item={detailItem}
        onClose={() => setDetailItem(null)}
      >
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
          <Typography variant="body2"><strong>Folio:</strong> {String(detailItem?.folio ?? '')}</Typography>
          <Typography variant="body2"><strong>Cliente:</strong> {String(detailItem?.cliente ?? '')}</Typography>
          <Typography variant="body2"><strong>Tipo Doc.:</strong> {String(detailItem?.tipoDoc ?? '')}</Typography>
          <Typography variant="body2"><strong>Fecha:</strong> {String(detailItem?.fecha ?? '')}</Typography>
          <Typography variant="body2"><strong>Neto:</strong> ${(detailItem?.neto as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>IVA:</strong> ${(detailItem?.iva as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>Total:</strong> ${(detailItem?.total as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>Estado:</strong> {String(detailItem?.estado ?? '')}</Typography>
        </Box>
      </DetailDialog>

      <MotivoDialog
        open={!!deleteTarget}
        title={`Eliminar: ${deleteTarget?.folio ?? ''}`}
        onClose={() => setDeleteTarget(null)}
        onConfirm={(motivo) => {
          const target = deleteTarget;

          if (!target) {
return;
}

          router.delete(`/compras/incomes/${target.id}`, {
            data: { motivo },
            preserveScroll: true,
            onSuccess: () => setDeleteTarget(null),
          });
        }}
      />
    </Box>
  );
}
