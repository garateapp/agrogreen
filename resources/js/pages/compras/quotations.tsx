import { router } from '@inertiajs/react';
import { Add, Delete, Edit, GetApp, Visibility } from '@mui/icons-material';
import { Box, Typography, Button, IconButton } from '@mui/material';
import { useMemo, useState } from 'react';
import MotivoDialog from '@/components/bodegaje/MotivoDialog';
import type { Column } from '@/components/compras/compras-types';
import ComprasDataTable from '@/components/compras/ComprasDataTable';
import ComprasFilterBar from '@/components/compras/ComprasFilterBar';
import ComprasHeaderIndicator from '@/components/compras/ComprasHeaderIndicator';
import CotizacionModal from '@/components/compras/CotizacionModal';
import DetailDialog from '@/components/compras/DetailDialog';
import Pagination from '@/components/compras/Pagination';

const baseColumns: Column[] = [
  { key: 'nroSolicitud', label: 'Nº Solicitud', width: 120 },
  { key: 'proveedor', label: 'Proveedor' },
  { key: 'fecha', label: 'Fecha', width: 110 },
  { key: 'producto', label: 'Producto/Servicio' },
  { key: 'montoEstimado', label: 'Monto Estimado', align: 'right', width: 140 },
  { key: 'estado', label: 'Estado', width: 110 },
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
  };
  filters: Record<string, string>;
}

export default function Quotations({ pageTitle, items, filterOptions, filters }: Props) {
  const [search, setSearch] = useState(filters.search ?? '');
  const [selectValues, setSelectValues] = useState<Record<string, string>>({
    proveedor: filters.proveedor_id ?? '',
  });
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

  const totalEst = items.data.reduce((s, i) => s + (i.montoEstimado as number), 0);

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

    if (estadoAprobacion !== 'todos') {
params.estado_aprobacion = estadoAprobacion;
}

    return params;
  };

  const handleSearch = () => {
    router.get('/compras/quotations', { ...buildParams(), page: 1 }, { preserveState: true, replace: true });
  };

  const handleClear = () => {
    setSearch('');
    setSelectValues({});
    setDateFrom('');
    setDateTo('');
    setEstadoAprobacion('todos');
    router.get('/compras/quotations', {}, { preserveState: true, replace: true });
  };

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>{pageTitle}</Typography>
      <ComprasHeaderIndicator countLabel="Solicitudes" countValue={items.meta.total} totalLabel="Monto Total Estimado" totalValue={totalEst} />
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
        estadoAprobacionValue={estadoAprobacion}
        onEstadoAprobacionChange={setEstadoAprobacion}
        actions={
          <>
            <Button variant="contained" size="small" startIcon={<Add />} onClick={() => {
 setEditingItem(null); setModalOpen(true); 
}}>Nueva Solicitud</Button>
            <Button variant="outlined" size="small" startIcon={<GetApp />}>Exportar</Button>
          </>
        }
      />
      <ComprasDataTable columns={columns} items={items.data} />
      <Pagination meta={items.meta} queryParams={buildParams()} baseUrl="/compras/quotations" />

      <CotizacionModal
        open={modalOpen}
        onClose={() => {
 setModalOpen(false); setEditingItem(null); 
}}
        editingItem={editingItem}
        proveedores={filterOptions.proveedores ?? []}
      />

      <DetailDialog
        open={!!detailItem}
        title={`Detalle Cotización: ${detailItem?.nroSolicitud ?? ''}`}
        item={detailItem}
        onClose={() => setDetailItem(null)}
      >
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
          <Typography variant="body2"><strong>Nº Solicitud:</strong> {String(detailItem?.nroSolicitud ?? '')}</Typography>
          <Typography variant="body2"><strong>Proveedor:</strong> {String(detailItem?.proveedor ?? '')}</Typography>
          <Typography variant="body2"><strong>Fecha:</strong> {String(detailItem?.fecha ?? '')}</Typography>
          <Typography variant="body2"><strong>Producto/Servicio:</strong> {String(detailItem?.producto ?? '')}</Typography>
          <Typography variant="body2"><strong>Monto Estimado:</strong> ${(detailItem?.montoEstimado as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>Estado:</strong> {String(detailItem?.estado ?? '')}</Typography>
        </Box>
      </DetailDialog>

      <MotivoDialog
        open={!!deleteTarget}
        title={`Eliminar: ${deleteTarget?.nroSolicitud ?? ''}`}
        onClose={() => setDeleteTarget(null)}
        onConfirm={(motivo) => {
          const target = deleteTarget;

          if (!target) {
return;
}

          router.delete(`/compras/quotations/${target.id}`, {
            data: { motivo },
            preserveScroll: true,
            onSuccess: () => setDeleteTarget(null),
          });
        }}
      />
    </Box>
  );
}
