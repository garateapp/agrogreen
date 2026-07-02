import { router } from '@inertiajs/react';
import { Delete, Edit, GetApp, Visibility } from '@mui/icons-material';
import { Box, Typography, Button, IconButton } from '@mui/material';
import { useMemo, useState } from 'react';
import MotivoDialog from '@/components/bodegaje/MotivoDialog';
import type { Column } from '@/components/compras/compras-types';
import ComprasDataTable from '@/components/compras/ComprasDataTable';
import ComprasFilterBar from '@/components/compras/ComprasFilterBar';
import ComprasHeaderIndicator from '@/components/compras/ComprasHeaderIndicator';
import DetailDialog from '@/components/compras/DetailDialog';
import Pagination from '@/components/compras/Pagination';
import PagoModal from '@/components/compras/PagoModal';

const baseColumns: Column[] = [
  { key: 'fechaPago', label: 'Fecha Pago', width: 120 },
  { key: 'proveedor', label: 'Proveedor' },
  { key: 'nroDocumento', label: 'Nº Documento', width: 130 },
  { key: 'monto', label: 'Monto', align: 'right', width: 120 },
  { key: 'medioPago', label: 'Medio Pago', width: 120 },
  { key: 'banco', label: 'Banco', width: 120 },
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
    ordenesCompra?: { value: string; label: string }[];
    metodosPago?: { value: string; label: string }[];
  };
  filters: Record<string, string>;
}

export default function Payments({ pageTitle, items, filterOptions, filters }: Props) {
  const [search, setSearch] = useState(filters.search ?? '');
  const [selectValues, setSelectValues] = useState<Record<string, string>>({
    medioPago: filters.medio_pago ?? '',
  });
  const [estadoPago, setEstadoPago] = useState(filters.estado_pago ?? 'todos');
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

  const totalMonto = items.data.reduce((s, i) => s + (i.monto as number), 0);

  const buildParams = () => {
    const params: Record<string, string> = {};

    if (search) {
params.search = search;
}

    if (selectValues.medioPago) {
params.medio_pago = selectValues.medioPago;
}

    if (dateFrom) {
params.date_from = dateFrom;
}

    if (dateTo) {
params.date_to = dateTo;
}

    if (estadoPago !== 'todos') {
params.estado_pago = estadoPago;
}

    return params;
  };

  const handleSearch = () => {
    router.get('/compras/payments', { ...buildParams(), page: 1 }, { preserveState: true, replace: true });
  };

  const handleClear = () => {
    setSearch('');
    setSelectValues({ medioPago: '' });
    setDateFrom('');
    setDateTo('');
    setEstadoPago('todos');
    router.get('/compras/payments', {}, { preserveState: true, replace: true });
  };

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>{pageTitle}</Typography>
      <ComprasHeaderIndicator countLabel="Pagos" countValue={items.meta.total} totalLabel="Total Pagado" totalValue={totalMonto} />
      <ComprasFilterBar
        searchValue={search}
        onSearchChange={setSearch}
        onSearch={handleSearch}
        onClear={handleClear}
        selects={[
          { key: 'proveedor', label: 'Proveedor', options: [] },
          { key: 'medioPago', label: 'Medio Pago', options: filterOptions.metodosPago ?? [] },
        ]}
        selectValues={selectValues}
        onSelectChange={(k, v) => setSelectValues((prev) => ({ ...prev, [k]: v }))}
        showDateRange
        dateRangeLabel="Fecha Pago"
        dateFrom={dateFrom}
        dateTo={dateTo}
        onDateFromChange={setDateFrom}
        onDateToChange={setDateTo}
        estadoPagoValue={estadoPago}
        onEstadoPagoChange={setEstadoPago}
        actions={
          <>
            <Button variant="contained" size="small" startIcon={<GetApp />} onClick={() => {
 setEditingItem(null); setModalOpen(true); 
}}>Nuevo Pago</Button>
            <Button variant="outlined" size="small" startIcon={<GetApp />}>Exportar</Button>
          </>
        }
      />
      <ComprasDataTable columns={columns} items={items.data} />
      <Pagination meta={items.meta} queryParams={buildParams()} baseUrl="/compras/payments" />

      <PagoModal
        open={modalOpen}
        onClose={() => {
 setModalOpen(false); setEditingItem(null); 
}}
        editingItem={editingItem}
        ordenesCompra={filterOptions.ordenesCompra ?? []}
        metodosPago={filterOptions.metodosPago ?? []}
      />

      <DetailDialog
        open={!!detailItem}
        title="Detalle Pago"
        item={detailItem}
        onClose={() => setDetailItem(null)}
      >
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
          <Typography variant="body2"><strong>Fecha Pago:</strong> {String(detailItem?.fechaPago ?? '')}</Typography>
          <Typography variant="body2"><strong>Proveedor:</strong> {String(detailItem?.proveedor ?? '')}</Typography>
          <Typography variant="body2"><strong>Nº Documento:</strong> {String(detailItem?.nroDocumento ?? '')}</Typography>
          <Typography variant="body2"><strong>Monto:</strong> ${(detailItem?.monto as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>Medio Pago:</strong> {String(detailItem?.medioPago ?? '')}</Typography>
          <Typography variant="body2"><strong>Banco:</strong> {String(detailItem?.banco ?? '')}</Typography>
          <Typography variant="body2"><strong>Estado:</strong> {String(detailItem?.estado ?? '')}</Typography>
        </Box>
      </DetailDialog>

      <MotivoDialog
        open={!!deleteTarget}
        title="Eliminar Pago"
        onClose={() => setDeleteTarget(null)}
        onConfirm={(motivo) => {
          const target = deleteTarget;

          if (!target) {
return;
}

          router.delete(`/compras/payments/${target.id}`, {
            data: { motivo },
            preserveScroll: true,
            onSuccess: () => setDeleteTarget(null),
          });
        }}
      />
    </Box>
  );
}
