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
import OrdenCompraModal from '@/components/compras/OrdenCompraModal';
import Pagination from '@/components/compras/Pagination';

const baseColumns: Column[] = [
  { key: 'nroOC', label: 'Nº OC', width: 90 },
  { key: 'proveedor', label: 'Proveedor' },
  { key: 'fechaEmision', label: 'Fecha Emisión', width: 130 },
  { key: 'fechaRecepcion', label: 'Fecha Recepción', width: 140 },
  { key: 'neto', label: 'Neto', align: 'right', width: 110 },
  { key: 'iva', label: 'IVA', align: 'right', width: 100 },
  { key: 'total', label: 'Total', align: 'right', width: 120 },
  { key: 'estadoRecepcion', label: 'Estado Recepción', width: 140 },
  { key: 'estadoAprobacion', label: 'Estado Aprobación', width: 150 },
];

interface PaginatedItems {
  data: Record<string, unknown>[];
  meta: { current_page: number; last_page: number; total: number; from: number; to: number };
}

interface Props {
  pageTitle: string;
  items: PaginatedItems;
  filterOptions: {
    proveedores?: { value: string; label: string }[];
    productos?: { value: string; label: string }[];
    centrosCosto?: { value: string; label: string }[];
  };
  filters: Record<string, string>;
}

export default function PurchaseOrders({ pageTitle, items, filterOptions, filters }: Props) {
  const [search, setSearch] = useState(filters.search ?? '');
  const [selectValues, setSelectValues] = useState<Record<string, string>>({
    proveedor: filters.proveedor_id ?? '',
  });
  const [estadoRecepcion, setEstadoRecepcion] = useState(filters.estado_recepcion ?? 'todos');
  const [estadoAprobacion, setEstadoAprobacion] = useState(filters.estado_aprobacion ?? 'todos');
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

    if (selectValues.proveedor) {
params.proveedor_id = selectValues.proveedor;
}

    if (dateFrom) {
params.date_from = dateFrom;
}

    if (dateTo) {
params.date_to = dateTo;
}

    if (estadoRecepcion !== 'todos') {
params.estado_recepcion = estadoRecepcion;
}

    if (estadoAprobacion !== 'todos') {
params.estado_aprobacion = estadoAprobacion;
}

    return params;
  };

  const handleSearch = () => {
    router.get('/compras/purchase-orders', { ...buildParams(), page: 1 }, { preserveState: true, replace: true });
  };

  const handleClear = () => {
    setSearch('');
    setSelectValues({});
    setDateFrom('');
    setDateTo('');
    setEstadoRecepcion('todos');
    setEstadoAprobacion('todos');
    router.get('/compras/purchase-orders', {}, { preserveState: true, replace: true });
  };

  const handleNew = () => {
 setEditingItem(null); setModalOpen(true); 
};

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>{pageTitle}</Typography>
      <ComprasHeaderIndicator countLabel="Órdenes" countValue={items.meta.total} totalLabel="Total OC" totalValue={totalTot} />
      <ComprasFilterBar
        searchValue={search}
        onSearchChange={setSearch}
        onSearch={handleSearch}
        onClear={handleClear}
        selects={[{ key: 'proveedor', label: 'Proveedor', options: filterOptions.proveedores ?? [] }]}
        selectValues={selectValues}
        onSelectChange={(k, v) => setSelectValues((prev) => ({ ...prev, [k]: v }))}
        showDateRange
        dateFrom={dateFrom}
        dateTo={dateTo}
        onDateFromChange={setDateFrom}
        onDateToChange={setDateTo}
        estadoRecepcionValue={estadoRecepcion}
        onEstadoRecepcionChange={setEstadoRecepcion}
        estadoAprobacionValue={estadoAprobacion}
        onEstadoAprobacionChange={setEstadoAprobacion}
        actions={
          <>
            <Button variant="contained" size="small" startIcon={<Add />} onClick={handleNew}>Nueva OC</Button>
            <Button variant="outlined" size="small" startIcon={<GetApp />}>Exportar</Button>
          </>
        }
      />
      <ComprasDataTable columns={columns} items={items.data} />
      <Pagination meta={items.meta} queryParams={buildParams()} baseUrl="/compras/purchase-orders" />

      <OrdenCompraModal
        open={modalOpen}
        onClose={() => {
 setModalOpen(false); setEditingItem(null); 
}}
        editingItem={editingItem}
        proveedores={filterOptions.proveedores ?? []}
        productos={filterOptions.productos ?? []}
        centrosCosto={filterOptions.centrosCosto ?? []}
      />

      <DetailDialog
        open={!!detailItem}
        title={`Detalle OC: ${detailItem?.nroOC ?? ''}`}
        item={detailItem}
        onClose={() => setDetailItem(null)}
      >
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
          <Typography variant="body2"><strong>Proveedor:</strong> {String(detailItem?.proveedor ?? '')}</Typography>
          <Typography variant="body2"><strong>Fecha Emisión:</strong> {String(detailItem?.fechaEmision ?? '')}</Typography>
          <Typography variant="body2"><strong>Fecha Recepción:</strong> {String(detailItem?.fechaRecepcion ?? '')}</Typography>
          <Typography variant="body2"><strong>Neto:</strong> ${(detailItem?.neto as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>IVA:</strong> ${(detailItem?.iva as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>Total:</strong> ${(detailItem?.total as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>Estado Recepción:</strong> {String(detailItem?.estadoRecepcion ?? '')}</Typography>
          <Typography variant="body2"><strong>Estado Aprobación:</strong> {String(detailItem?.estadoAprobacion ?? '')}</Typography>
        </Box>
      </DetailDialog>

      <MotivoDialog
        open={!!deleteTarget}
        title={`Eliminar: ${deleteTarget?.nroOC ?? ''}`}
        onClose={() => setDeleteTarget(null)}
        onConfirm={(motivo) => {
          const target = deleteTarget;

          if (!target) {
return;
}

          router.delete(`/compras/purchase-orders/${target.id}`, {
            data: { motivo },
            preserveScroll: true,
            onSuccess: () => setDeleteTarget(null),
          });
        }}
      />
    </Box>
  );
}
