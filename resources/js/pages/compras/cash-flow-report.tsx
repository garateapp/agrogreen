import { GetApp } from '@mui/icons-material';
import { Box, Typography, Button, Chip } from '@mui/material';
import { useMemo, useState } from 'react';
import type { Column } from '@/components/compras/compras-types';
import ComprasDataTable from '@/components/compras/ComprasDataTable';
import ComprasFilterBar from '@/components/compras/ComprasFilterBar';

const columns: Column[] = [
  { key: 'fecha', label: 'Fecha', width: 110 },
  { key: 'tipo', label: 'Tipo', width: 100, render: (v) => (
    <Chip label={String(v)} size="small" color={v === 'Ingreso' ? 'success' : 'error'} variant="outlined" />
  )},
  { key: 'descripcion', label: 'Descripción' },
  { key: 'monto', label: 'Monto', align: 'right', width: 120 },
  { key: 'saldoAcumulado', label: 'Saldo Acumulado', align: 'right', width: 150 },
];

interface Props {
  pageTitle: string;
  items: Record<string, unknown>[];
  filterOptions: Record<string, never>;
}

export default function CashFlowReport({ pageTitle, items }: Props) {
  const [search, setSearch] = useState('');
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');

  const filtered = useMemo(() => {
    return items.filter((i) => {
      if (search) {
        const q = search.toLowerCase();
        const match = [String(i.descripcion ?? ''), String(i.tipo ?? '')].some((v) => v.toLowerCase().includes(q));

        if (!match) {
return false;
}
      }

      if (dateFrom && String(i.fecha) < dateFrom) {
return false;
}

      if (dateTo && String(i.fecha) > dateTo) {
return false;
}

      return true;
    });
  }, [items, search, dateFrom, dateTo]);

  const ingresos = filtered.filter((i) => i.tipo === 'Ingreso').reduce((s, i) => s + (i.monto as number), 0);
  const egresos = filtered.filter((i) => i.tipo === 'Egreso').reduce((s, i) => s + Math.abs(i.monto as number), 0);

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>{pageTitle}</Typography>
      <Box sx={{ display: 'flex', gap: 2, mb: 2 }}>
        <Chip label={`Ingresos: $${ingresos.toLocaleString('es-CL')}`} color="success" variant="outlined" size="small" sx={{ fontWeight: 600 }} />
        <Chip label={`Egresos: $${egresos.toLocaleString('es-CL')}`} color="error" variant="outlined" size="small" sx={{ fontWeight: 600 }} />
        <Chip label={`Saldo: $${(ingresos - egresos).toLocaleString('es-CL')}`} color="primary" variant="outlined" size="small" sx={{ fontWeight: 600 }} />
      </Box>
      <ComprasFilterBar
        searchValue={search}
        onSearchChange={setSearch}
        onSearch={() => {}}
        onClear={() => {
 setSearch(''); setDateFrom(''); setDateTo(''); 
}}
        showDateRange
        dateFrom={dateFrom}
        dateTo={dateTo}
        onDateFromChange={setDateFrom}
        onDateToChange={setDateTo}
        actions={
          <Button variant="outlined" size="small" startIcon={<GetApp />}>Exportar</Button>
        }
      />
      <ComprasDataTable columns={columns} items={filtered} />
    </Box>
  );
}
