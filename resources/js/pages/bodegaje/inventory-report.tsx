import { GetApp } from '@mui/icons-material';
import { Box, Typography, Button, TextField, FormControl, InputLabel, Select, MenuItem } from '@mui/material';
import { useState, useMemo } from 'react';
import type { Column } from '@/components/bodegaje/bodegaje-types';
import BodegajeDataTable from '@/components/bodegaje/BodegajeDataTable';
import BodegajeHeaderIndicator from '@/components/bodegaje/BodegajeHeaderIndicator';

const columns: Column[] = [
  { key: 'producto', label: 'Producto' },
  { key: 'unidad', label: 'Unidad', width: 80, align: 'center' },
  { key: 'bodega', label: 'Bodega' },
  { key: 'inicial', label: 'Inicial', align: 'right', width: 100 },
  { key: 'entradas', label: 'Entradas', align: 'right', width: 100 },
  { key: 'salidas', label: 'Salidas', align: 'right', width: 100 },
  { key: 'stock', label: 'Stock', align: 'right', width: 100 },
  { key: 'valorUnitario', label: 'Valor unitario', align: 'right', width: 120 },
  { key: 'subtotal', label: 'SubTotal', align: 'right', width: 130, render: (v) => `$${Number(v).toLocaleString('es-CL')}` },
];

interface Props {
  items: Record<string, unknown>[];
  bodegas: Array<{ id: string; nombre: string }>;
}

export default function InventoryReport({ items, bodegas }: Props) {
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');
  const [bodega, setBodega] = useState('');
  const [productoFilter, setProductoFilter] = useState('');

  const filtered = useMemo(() => {
    const s = productoFilter.toLowerCase();

    return items.filter((item) => {
      if (bodega && String(item.bodega_id) !== bodega) {
return false;
}

      if (s && !String(item.producto).toLowerCase().includes(s)) {
return false;
}

      return true;
    });
  }, [items, bodega, productoFilter]);

  const totalSubtotal = useMemo(() => filtered.reduce((s, i) => s + (i.subtotal as number), 0), [filtered]);

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 1 }}>Reporte de Inventario</Typography>
      <BodegajeHeaderIndicator label="Valor Total Inventario" value={totalSubtotal} format="currency" />

      <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', alignItems: 'center', mb: 2 }}>
        <FormControl size="small" sx={{ minWidth: 160 }}>
          <InputLabel>Bodega</InputLabel>
          <Select value={bodega} label="Bodega" onChange={(e) => setBodega(e.target.value)}>
            <MenuItem value="">Todas</MenuItem>
            {bodegas.map((b) => <MenuItem key={b.id} value={b.id}>{b.nombre}</MenuItem>)}
          </Select>
        </FormControl>
        <TextField placeholder="Producto..." size="small" value={productoFilter}
          onChange={(e) => setProductoFilter(e.target.value)} sx={{ maxWidth: 200 }} />
        <Button variant="outlined" size="small" color="inherit"
          onClick={() => {
 setBodega(''); setProductoFilter(''); 
}}>
          Limpiar
        </Button>
        <Button variant="outlined" size="small" startIcon={<GetApp />} sx={{ ml: 'auto' }}>Exportar</Button>
      </Box>

      <BodegajeDataTable columns={columns} items={filtered} />
    </Box>
  );
}
