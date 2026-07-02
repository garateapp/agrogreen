import { router } from '@inertiajs/react';
import { Add, Delete, Edit, GetApp, Visibility } from '@mui/icons-material';
import { Box, Typography, Button, IconButton, Chip } from '@mui/material';
import { useMemo, useState } from 'react';
import MotivoDialog from '@/components/bodegaje/MotivoDialog';
import type { Column } from '@/components/compras/compras-types';
import ComprasDataTable from '@/components/compras/ComprasDataTable';
import ComprasFilterBar from '@/components/compras/ComprasFilterBar';
import ComprasHeaderIndicator from '@/components/compras/ComprasHeaderIndicator';
import DetailDialog from '@/components/compras/DetailDialog';
import EgresoModal from '@/components/compras/EgresoModal';
import Pagination from '@/components/compras/Pagination';

interface SelectOption {
  value: string;
  label: string;
}

const baseColumns: Column[] = [
  {
    key: 'tipo', label: 'Tipo', width: 80,
    render: (_, row) => (
      <Chip
        label={row.tipo_origen === 'directo' ? 'Directo' : 'OC'}
        size="small"
        color={row.tipo_origen === 'directo' ? 'warning' : 'primary'}
        variant="outlined"
      />
    ),
  },
  { key: 'folio', label: 'Folio', width: 90 },
  { key: 'proveedor', label: 'Proveedor' },
  { key: 'centro_costo', label: 'Centro Costo', width: 140 },
  { key: 'item_gasto', label: 'Item Gasto', width: 130 },
  { key: 'tipoDoc', label: 'Tipo Doc.', width: 110 },
  { key: 'fechaRecepcion', label: 'Fecha Recepción', width: 140 },
  { key: 'neto', label: 'Neto', align: 'right', width: 110 },
  { key: 'iva', label: 'IVA', align: 'right', width: 100 },
  { key: 'total', label: 'Total', align: 'right', width: 120 },
  { key: 'estadoRecepcion', label: 'Estado Recepción', width: 140 },
  { key: 'estadoPago', label: 'Estado Pago', width: 120 },
];

interface PaginatedItems {
  data: Record<string, unknown>[];
  meta: { current_page: number; last_page: number; total: number; from: number; to: number };
}

interface Props {
  pageTitle: string;
  items: PaginatedItems;
  filterOptions: {
    ordenesCompra?: SelectOption[];
    proveedores?: SelectOption[];
    centrosCosto?: SelectOption[];
    itemsGasto?: SelectOption[];
  };
  filters: Record<string, string>;
}

export default function Invoices({ pageTitle, items, filterOptions, filters }: Props) {
  const [search, setSearch] = useState(filters.search ?? '');
  const [selectValues, setSelectValues] = useState<Record<string, string>>({});
  const [estadoPago, setEstadoPago] = useState(filters.estado_pago ?? 'todos');
  const [estadoRecepcion, setEstadoRecepcion] = useState(filters.estado_recepcion ?? 'todos');
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

    if (estadoPago !== 'todos') {
 params.estado_pago = estadoPago; 
}

    if (estadoRecepcion !== 'todos') {
 params.estado_recepcion = estadoRecepcion; 
}

    return params;
  };

  const handleSearch = () => {
    router.get('/compras/invoices', { ...buildParams(), page: 1 }, { preserveState: true, replace: true });
  };

  const handleClear = () => {
    setSearch('');
    setSelectValues({});
    setDateFrom('');
    setDateTo('');
    setEstadoPago('todos');
    setEstadoRecepcion('todos');
    router.get('/compras/invoices', {}, { preserveState: true, replace: true });
  };

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>{pageTitle}</Typography>
      <ComprasHeaderIndicator countLabel="Egresos" countValue={items.meta.total} totalLabel="Total Egresos" totalValue={totalTot} />
      <ComprasFilterBar
        searchValue={search}
        onSearchChange={setSearch}
        onSearch={handleSearch}
        onClear={handleClear}
        selects={[{ key: 'proveedor', label: 'Proveedor', options: [] }]}
        selectValues={selectValues}
        onSelectChange={(k, v) => setSelectValues((prev) => ({ ...prev, [k]: v }))}
        showDateRange
        dateFrom={dateFrom}
        dateTo={dateTo}
        onDateFromChange={setDateFrom}
        onDateToChange={setDateTo}
        estadoPagoValue={estadoPago}
        onEstadoPagoChange={setEstadoPago}
        estadoRecepcionValue={estadoRecepcion}
        onEstadoRecepcionChange={setEstadoRecepcion}
        actions={
          <>
            <Button variant="contained" size="small" startIcon={<Add />} onClick={() => {
 setEditingItem(null); setModalOpen(true); 
}}>Nuevo Egreso</Button>
            <Button variant="outlined" size="small" startIcon={<GetApp />}>Exportar</Button>
          </>
        }
      />
      <ComprasDataTable columns={columns} items={items.data} />
      <Pagination meta={items.meta} queryParams={buildParams()} baseUrl="/compras/invoices" />

      <EgresoModal
        open={modalOpen}
        onClose={() => {
 setModalOpen(false); setEditingItem(null); 
}}
        editingItem={editingItem}
        ordenesCompra={filterOptions.ordenesCompra ?? []}
        proveedores={filterOptions.proveedores ?? []}
        centrosCosto={filterOptions.centrosCosto ?? []}
        itemsGasto={filterOptions.itemsGasto ?? []}
      />

      <DetailDialog
        open={!!detailItem}
        title={`Detalle Egreso: ${detailItem?.folio ?? ''}`}
        item={detailItem}
        onClose={() => setDetailItem(null)}
      >
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
          <Typography variant="body2">
            <strong>Tipo:</strong>{' '}
            <Chip
              label={detailItem?.tipo_origen === 'directo' ? 'Gasto Directo' : 'Asociado a OC'}
              size="small"
              color={detailItem?.tipo_origen === 'directo' ? 'warning' : 'primary'}
              variant="outlined"
            />
          </Typography>
          <Typography variant="body2"><strong>Folio:</strong> {String(detailItem?.folio ?? '')}</Typography>
          <Typography variant="body2"><strong>Proveedor:</strong> {String(detailItem?.proveedor ?? '')}</Typography>
          {detailItem?.tipo_origen === 'directo' && (
            <>
              <Typography variant="body2"><strong>Centro Costo:</strong> {String(detailItem?.centro_costo ?? '')}</Typography>
              <Typography variant="body2"><strong>Item Gasto:</strong> {String(detailItem?.item_gasto ?? '')}</Typography>
            </>
          )}
          <Typography variant="body2"><strong>Tipo Doc.:</strong> {String(detailItem?.tipoDoc ?? '')}</Typography>
          <Typography variant="body2"><strong>Fecha Recepción:</strong> {String(detailItem?.fechaRecepcion ?? '')}</Typography>
          <Typography variant="body2"><strong>Neto:</strong> ${(detailItem?.neto as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>IVA:</strong> ${(detailItem?.iva as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>Total:</strong> ${(detailItem?.total as number ?? 0).toLocaleString('es-CL')}</Typography>
          <Typography variant="body2"><strong>Estado Pago:</strong> {String(detailItem?.estadoPago ?? '')}</Typography>
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

          router.delete(`/compras/invoices/${target.id}`, {
            data: { motivo },
            preserveScroll: true,
            onSuccess: () => setDeleteTarget(null),
          });
        }}
      />
    </Box>
  );
}
