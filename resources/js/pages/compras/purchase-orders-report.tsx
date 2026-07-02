import { GetApp } from '@mui/icons-material';
import { Box, Typography, Button } from '@mui/material';
import { useMemo, useState } from 'react';
import type { Column } from '@/components/compras/compras-types';
import ComprasDataTable from '@/components/compras/ComprasDataTable';
import ComprasFilterBar from '@/components/compras/ComprasFilterBar';
import ComprasHeaderIndicator from '@/components/compras/ComprasHeaderIndicator';

const columns: Column[] = [
  { key: 'nroOC', label: 'Nº OC', width: 90 },
  { key: 'proveedor', label: 'Proveedor' },
  { key: 'fecha', label: 'Fecha', width: 110 },
  { key: 'monto', label: 'Monto', align: 'right', width: 120 },
  { key: 'aprobadoPor', label: 'Aprobado Por' },
  { key: 'fechaAprobacion', label: 'Fecha Aprobación', width: 140 },
  { key: 'estado', label: 'Estado', width: 100 },
];

interface Props {
  pageTitle: string;
  items: Record<string, unknown>[];
  filterOptions: {
    proveedores?: { value: string; label: string }[];
  };
}

export default function PurchaseOrdersReport({ pageTitle, items, filterOptions }: Props) {
  const [search, setSearch] = useState('');
  const [selectValues, setSelectValues] = useState<Record<string, string>>({});
  const [estadoAprobacion, setEstadoAprobacion] = useState('todos');
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');

  const filtered = useMemo(() => {
    return items.filter((i) => {
      if (search) {
        const q = search.toLowerCase();
        const match = [String(i.nroOC ?? ''), String(i.proveedor ?? '')].some((v) => v.toLowerCase().includes(q));

        if (!match) {
return false;
}
      }

      if (estadoAprobacion !== 'todos' && i.estado !== estadoAprobacion) {
return false;
}

      if (selectValues.proveedor && i.proveedor !== filterOptions.proveedores?.find((p) => p.value === selectValues.proveedor)?.label) {
return false;
}

      if (dateFrom && String(i.fecha) < dateFrom) {
return false;
}

      if (dateTo && String(i.fecha) > dateTo) {
return false;
}

      return true;
    });
  }, [items, search, estadoAprobacion, selectValues, dateFrom, dateTo, filterOptions.proveedores]);

  const totalMonto = filtered.reduce((s, i) => s + (i.monto as number), 0);

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>{pageTitle}</Typography>
      <ComprasHeaderIndicator countLabel="Órdenes" countValue={filtered.length} totalLabel="Monto Total" totalValue={totalMonto} />
      <ComprasFilterBar
        searchValue={search}
        onSearchChange={setSearch}
        onSearch={() => {}}
        onClear={() => {
 setSearch(''); setSelectValues({}); setDateFrom(''); setDateTo(''); setEstadoAprobacion('todos'); 
}}
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
          <Button variant="outlined" size="small" startIcon={<GetApp />}>Exportar</Button>
        }
      />
      <ComprasDataTable columns={columns} items={filtered} />
    </Box>
  );
}
