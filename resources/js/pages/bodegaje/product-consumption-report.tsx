import { GetApp } from '@mui/icons-material';
import { Box, Typography, Button, TextField } from '@mui/material';
import { useState, useMemo } from 'react';
import type { Column } from '@/components/bodegaje/bodegaje-types';
import BodegajeDataTable from '@/components/bodegaje/BodegajeDataTable';

const columns: Column[] = [
  { key: 'producto', label: 'Producto' },
  { key: 'unidad', label: 'Unidad', width: 80, align: 'center' },
  { key: 'cantidad', label: 'Cantidad consumida', align: 'right', width: 140 },
  { key: 'precioUnitario', label: 'Precio U.', align: 'right', width: 110, render: (v) => v ? `$${Number(v).toLocaleString('es-CL')}` : '' },
  { key: 'total', label: 'Total', align: 'right', width: 130, render: (v) => v ? `$${Number(v).toLocaleString('es-CL')}` : '' },
];

interface Props {
  items: Record<string, unknown>[];
}

export default function ProductConsumptionReport({ items }: Props) {
  const [productoFilter, setProductoFilter] = useState('');

  const filtered = useMemo(() => {
    const s = productoFilter.toLowerCase();

    if (!s) {
return items;
}

    return items.filter((item) => String(item.producto).toLowerCase().includes(s));
  }, [items, productoFilter]);

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Reporte de Consumo de Productos</Typography>

      <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', alignItems: 'center', mb: 2 }}>
        <TextField placeholder="Producto..." size="small" value={productoFilter}
          onChange={(e) => setProductoFilter(e.target.value)} sx={{ maxWidth: 180 }} />
        <Button variant="outlined" size="small" color="inherit"
          onClick={() => setProductoFilter('')}>
          Limpiar
        </Button>
        <Button variant="outlined" size="small" startIcon={<GetApp />} sx={{ ml: 'auto' }}>Exportar</Button>
      </Box>

      <BodegajeDataTable columns={columns} items={filtered} />
    </Box>
  );
}
