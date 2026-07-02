import { router } from '@inertiajs/react';
import { Delete } from '@mui/icons-material';
import {
  Dialog, DialogTitle, DialogContent, DialogActions, Button, TextField,
  Box, FormControl, InputLabel, Select, MenuItem, IconButton,
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
  Paper, Typography, Divider,
} from '@mui/material';
import { useEffect, useState } from 'react';

interface Linea {
  id: string;
  producto_id: string;
  producto: string;
  cantidad: number;
  precio_unitario: number;
  centro_costo_id: string;
  centro_costo: string;
}

interface Props {
  open: boolean;
  onClose: () => void;
  editingItem?: Record<string, unknown> | null;
  productos: { value: string; label: string }[];
  centrosCosto: { value: string; label: string }[];
  proveedores: { value: string; label: string }[];
}

function genId() {
 return Math.random().toString(36).slice(2, 9); 
}

export default function OrdenCompraModal({ open, onClose, editingItem, productos, centrosCosto, proveedores }: Props) {
  const [proveedorId, setProveedorId] = useState('');
  const [fechaEmision, setFechaEmision] = useState(new Date().toISOString().slice(0, 10));
  const [fechaEntrega, setFechaEntrega] = useState('');
  const [lineas, setLineas] = useState<Linea[]>([]);

  const [selProducto, setSelProducto] = useState('');
  const [selCantidad, setSelCantidad] = useState('');
  const [selPrecio, setSelPrecio] = useState('');
  const [selCC, setSelCC] = useState('');

  useEffect(() => {
    if (open && editingItem) {
      setProveedorId(String(editingItem.proveedor_id ?? ''));
      setFechaEmision(String(editingItem.fechaEmision ?? new Date().toISOString().slice(0, 10)));
      setFechaEntrega(String(editingItem.fechaRecepcion ?? ''));
      const raw = editingItem.detalles;
      const parsed = typeof raw === 'string' ? (() => {
 try {
 return JSON.parse(raw); 
} catch {
 return []; 
} 
})() : Array.isArray(raw) ? raw : [];
      setLineas(parsed.map((l: Record<string, unknown>) => ({ ...l, id: genId() })) as Linea[]);
    } else if (open) {
      resetForm();
    }
  }, [open, editingItem]);

  const resetForm = () => {
    setProveedorId(''); setFechaEmision(new Date().toISOString().slice(0, 10));
    setFechaEntrega(''); setLineas([]); setSelProducto(''); setSelCantidad(''); setSelPrecio(''); setSelCC('');
  };

  const agregarLinea = () => {
    if (!selProducto || !selCantidad || !selPrecio) {
return;
}

    const prod = productos.find((p) => p.value === selProducto);
    const cc = centrosCosto.find((c) => c.value === selCC);
    setLineas((prev) => [...prev, {
      id: genId(), producto_id: selProducto, producto: prod?.label ?? selProducto,
      cantidad: parseFloat(selCantidad), precio_unitario: parseFloat(selPrecio),
      centro_costo_id: selCC, centro_costo: cc?.label ?? '',
    }]);
    setSelProducto(''); setSelCantidad(''); setSelPrecio(''); setSelCC('');
  };

  const eliminarLinea = (id: string) => setLineas((prev) => prev.filter((l) => l.id !== id));

  const totalNeto = lineas.reduce((s, l) => s + l.cantidad * l.precio_unitario, 0);
  const iva = totalNeto * 0.19;
  const total = totalNeto + iva;

  const submit = () => {
    const payload = {
      proveedor_id: proveedorId,
      fecha_emision: fechaEmision,
      fecha_entrega: fechaEntrega || null,
      moneda: 'CLP',
      total_neto: totalNeto,
      iva,
      total,
      detalles: JSON.stringify(lineas.map(({ id, producto, centro_costo, ...rest }) => rest)),
    };

    if (editingItem) {
      router.put(`/compras/purchase-orders/${editingItem.id}`, payload, {
        preserveState: true, preserveScroll: true,
        onSuccess: () => {
 resetForm(); onClose(); 
},
      });
    } else {
      router.post('/compras/purchase-orders', payload, {
        preserveState: true, preserveScroll: true,
        onSuccess: () => {
 resetForm(); onClose(); 
},
      });
    }
  };

  return (
    <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth>
      <DialogTitle sx={{ fontWeight: 600 }}>{editingItem ? `Editar: ${editingItem.nroOC}` : 'Nueva Orden de Compra'}</DialogTitle>
      <DialogContent dividers>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
          <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap' }}>
            <FormControl size="small" sx={{ minWidth: 220, flex: 1 }}>
              <InputLabel>Proveedor</InputLabel>
              <Select value={proveedorId} label="Proveedor" onChange={(e) => setProveedorId(e.target.value)}>
                {proveedores.map((p) => <MenuItem key={p.value} value={p.value}>{p.label}</MenuItem>)}
              </Select>
            </FormControl>
            <TextField label="Fecha Emisión" type="date" size="small" value={fechaEmision}
              onChange={(e) => setFechaEmision(e.target.value)} slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
            <TextField label="Fecha Entrega" type="date" size="small" value={fechaEntrega}
              onChange={(e) => setFechaEntrega(e.target.value)} slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
          </Box>

          <Divider />
          <Typography variant="subtitle2" sx={{ fontWeight: 600 }}>Líneas de detalle</Typography>
          <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', alignItems: 'center' }}>
            <FormControl size="small" sx={{ minWidth: 160, flex: '1 1 140px' }}>
              <InputLabel>Producto</InputLabel>
              <Select value={selProducto} label="Producto" onChange={(e) => setSelProducto(e.target.value)}>
                {productos.map((p) => <MenuItem key={p.value} value={p.value}>{p.label}</MenuItem>)}
              </Select>
            </FormControl>
            <TextField label="Cantidad" type="number" size="small" value={selCantidad}
              onChange={(e) => setSelCantidad(e.target.value)} sx={{ maxWidth: 100 }} />
            <TextField label="Precio" type="number" size="small" value={selPrecio}
              onChange={(e) => setSelPrecio(e.target.value)} sx={{ maxWidth: 110 }} />
            <FormControl size="small" sx={{ minWidth: 140, flex: '1 1 120px' }}>
              <InputLabel>Centro costo</InputLabel>
              <Select value={selCC} label="Centro costo" onChange={(e) => setSelCC(e.target.value)}>
                {centrosCosto.map((c) => <MenuItem key={c.value} value={c.value}>{c.label}</MenuItem>)}
              </Select>
            </FormControl>
            <Button variant="contained" size="small" onClick={agregarLinea}>+</Button>
          </Box>

          {lineas.length > 0 && (
            <TableContainer component={Paper} variant="outlined">
              <Table size="small">
                <TableHead>
                  <TableRow>
                    <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }}>Producto</TableCell>
                    <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="right">Cantidad</TableCell>
                    <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="right">Precio</TableCell>
                    <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="right">Subtotal</TableCell>
                    <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }}>Centro Costo</TableCell>
                    <TableCell />
                  </TableRow>
                </TableHead>
                <TableBody>
                  {lineas.map((l) => (
                    <TableRow key={l.id}>
                      <TableCell sx={{ fontSize: '0.8125rem' }}>{l.producto}</TableCell>
                      <TableCell sx={{ fontSize: '0.8125rem' }} align="right">{l.cantidad}</TableCell>
                      <TableCell sx={{ fontSize: '0.8125rem' }} align="right">${l.precio_unitario.toLocaleString('es-CL')}</TableCell>
                      <TableCell sx={{ fontSize: '0.8125rem' }} align="right">${(l.cantidad * l.precio_unitario).toLocaleString('es-CL')}</TableCell>
                      <TableCell sx={{ fontSize: '0.8125rem' }}>{l.centro_costo}</TableCell>
                      <TableCell align="center">
                        <IconButton size="small" onClick={() => eliminarLinea(l.id)} color="error"><Delete fontSize="small" /></IconButton>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </TableContainer>
          )}

          {lineas.length > 0 && (
            <Box sx={{ display: 'flex', justifyContent: 'flex-end', gap: 3 }}>
              <Typography variant="body2">Neto: <strong>${totalNeto.toLocaleString('es-CL')}</strong></Typography>
              <Typography variant="body2">IVA: <strong>${iva.toLocaleString('es-CL')}</strong></Typography>
              <Typography variant="body2">Total: <strong>${total.toLocaleString('es-CL')}</strong></Typography>
            </Box>
          )}
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose} color="inherit">Cancelar</Button>
        <Button onClick={submit} variant="contained" disabled={!proveedorId || lineas.length === 0}>
          {editingItem ? 'Guardar cambios' : 'Aceptar'}
        </Button>
      </DialogActions>
    </Dialog>
  );
}
