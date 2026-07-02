import { router } from '@inertiajs/react';
import { Delete } from '@mui/icons-material';
import {
  Dialog, DialogTitle, DialogContent, DialogActions, Button, TextField,
  Box, Select, MenuItem, InputLabel, FormControl, Table, TableBody,
  TableCell, TableContainer, TableHead, TableRow, Paper, IconButton,
  Typography,
} from '@mui/material';
import { useState, useCallback, useMemo, useEffect } from 'react';

interface ProductoOption {
  id: string;
  nombre: string;
  unidad: string;
  stockTotal: number;
  stockPorBodega: Record<string, number>;
}

interface LineItem {
  id: string;
  producto_id: string;
  producto: string;
  unidad: string;
  cantidad: number;
}

interface Props {
  open: boolean;
  onClose: () => void;
  productos: ProductoOption[];
  bodegas: Array<{ id: string; nombre: string }>;
  editingItem?: Record<string, unknown> | null;
}

function generarId() {
  return Math.random().toString(36).slice(2, 9);
}

export default function GoodsIssueModal({ open, onClose, productos, bodegas, editingItem }: Props) {
  const [fecha, setFecha] = useState(new Date().toISOString().slice(0, 10));
  const [descripcion, setDescripcion] = useState('');
  const [bodegaId, setBodegaId] = useState('');
  const [productoId, setProductoId] = useState('');
  const [cantidad, setCantidad] = useState('');
  const [items, setItems] = useState<LineItem[]>([]);
  const [motivo, setMotivo] = useState('');
  const [motivoOpen, setMotivoOpen] = useState(false);

  const isEditing = !!editingItem;

  useEffect(() => {
    if (!open) {
return;
}

    if (editingItem) {
      setFecha(String(editingItem.fecha ?? new Date().toISOString().slice(0, 10)));
      setDescripcion(String(editingItem.descripcion ?? ''));
      setBodegaId(String(editingItem.bodega_origen_id ?? ''));
      setProductoId('');
      setCantidad('');
      const lineas = (editingItem.lineas as Array<{ producto_id: string; producto: string; unidad?: string; cantidad: number }>) ?? [];
      setItems(lineas.map((l) => ({
        id: generarId(),
        producto_id: l.producto_id,
        producto: l.producto,
        unidad: l.unidad ?? '',
        cantidad: l.cantidad,
      })));
    } else {
      setFecha(new Date().toISOString().slice(0, 10));
      setDescripcion('');
      setBodegaId('');
      setProductoId('');
      setCantidad('');
      setItems([]);
    }

    setMotivo('');
    setMotivoOpen(false);
  }, [open, editingItem]);

  const hayStockEnBodega = useMemo(() => {
    if (!bodegaId) {
return false;
}

    return productos.some((p) => (p.stockPorBodega[bodegaId] ?? 0) > 0);
  }, [productos, bodegaId]);

  const productosFiltrados = useMemo(() => {
    if (!bodegaId) {
return [];
}

    if (hayStockEnBodega) {
      return productos.filter((p) => (p.stockPorBodega[bodegaId] ?? 0) > 0);
    }

    return productos;
  }, [productos, bodegaId, hayStockEnBodega]);

  const productoSeleccionado = productos.find((p) => p.id === productoId);
  const stockDisponible = bodegaId && productoSeleccionado
    ? (productoSeleccionado.stockPorBodega[bodegaId] ?? 0)
    : 0;

  const errorStock = cantidad && productoSeleccionado && bodegaId
    ? parseFloat(cantidad) > stockDisponible
    : false;

  const handleAgregarItem = useCallback(() => {
    if (!productoId || !cantidad) {
return;
}

    const p = productos.find((pr) => pr.id === productoId);

    if (!p) {
return;
}

    const cant = parseFloat(cantidad);

    if (errorStock) {
return;
}

    const item: LineItem = {
      id: generarId(),
      producto_id: p.id,
      producto: p.nombre,
      unidad: p.unidad,
      cantidad: cant,
    };
    setItems((prev) => [...prev, item]);
    setProductoId('');
    setCantidad('');
  }, [productoId, cantidad, productos, errorStock]);

  const handleEliminarItem = (id: string) => {
    setItems((prev) => prev.filter((i) => i.id !== id));
  };

  const handleAceptar = () => {
    if (isEditing) {
      setMotivoOpen(true);
    } else {
      submit();
    }
  };

  const submit = () => {
    const lineasPayload = items.map(({ id, producto, unidad, ...rest }) => rest);

    if (isEditing) {
      router.put(`/bodegaje/goods-issues/${editingItem.id}`, {
        fecha_emision: fecha,
        descripcion,
        bodega_origen_id: bodegaId,
        lineas: lineasPayload,
        motivo,
      }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
 setMotivoOpen(false); setMotivo(''); onClose(); 
},
      });
    } else {
      router.post('/bodegaje/goods-issues', {
        fecha_emision: fecha,
        descripcion,
        bodega_origen_id: bodegaId,
        lineas: lineasPayload,
      }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
 onClose(); 
},
      });
    }
  };

  return (
    <>
      <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth disablePortal>
        <DialogTitle sx={{ fontWeight: 600 }}>
          {isEditing ? 'Editar guía de consumo' : 'Nueva guía de consumo'}
        </DialogTitle>
        <DialogContent dividers>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap', alignItems: 'center' }}>
              <TextField label="Fecha emisión" type="date" size="small" value={fecha}
                onChange={(e) => setFecha(e.target.value)}
                slotProps={{ inputLabel: { shrink: true } }} sx={{ flex: '0 0 160px' }} />
            </Box>
            <TextField label="Descripción" size="small" value={descripcion}
              onChange={(e) => setDescripcion(e.target.value)} multiline rows={2} />

            <FormControl size="small" sx={{ minWidth: 200 }}>
              <InputLabel>Bodega origen</InputLabel>
              <Select value={bodegaId} label="Bodega origen" disabled={isEditing}
                onChange={(e) => {
 setBodegaId(e.target.value); setProductoId(''); 
}}>
                <MenuItem value=""><em>Seleccionar...</em></MenuItem>
                {bodegas.map((b) => <MenuItem key={b.id} value={b.id}>{b.nombre}</MenuItem>)}
              </Select>
            </FormControl>

            <Typography variant="subtitle2" sx={{ fontSize: '0.8125rem', fontWeight: 600 }}>Agregar línea</Typography>
            <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', alignItems: 'center' }}>
              <FormControl size="small" sx={{ minWidth: 200, flex: '1 1 160px' }} disabled={!bodegaId}>
                <InputLabel>Producto</InputLabel>
                <Select value={productoId} label="Producto" onChange={(e) => setProductoId(e.target.value)}>
                  {productosFiltrados.map((p) => {
                    const stock = p.stockPorBodega[bodegaId] ?? 0;
                    const sinStock = stock <= 0;

                    return (
                      <MenuItem key={p.id} value={p.id} disabled={sinStock}>
                        {p.nombre} ({p.unidad}) — Stock: {stock}
                      </MenuItem>
                    );
                  })}
                  {!hayStockEnBodega && bodegaId && (
                    <MenuItem disabled value="">
                      <em>Sin datos de stock para esta bodega</em>
                    </MenuItem>
                  )}
                </Select>
              </FormControl>
              <TextField label="Cantidad" type="number" size="small" value={cantidad}
                onChange={(e) => setCantidad(e.target.value)}
                error={errorStock}
                helperText={errorStock ? `Máx: ${stockDisponible}` : undefined}
                sx={{ maxWidth: 120 }} />
              <Button variant="contained" size="small" onClick={handleAgregarItem}
                disabled={!productoId || !cantidad || errorStock}>
                Agregar
              </Button>
            </Box>

            {items.length > 0 && (
              <TableContainer component={Paper} variant="outlined" sx={{ mt: 1 }}>
                <Table size="small">
                  <TableHead>
                    <TableRow>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }}>Producto</TableCell>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="right">Cantidad</TableCell>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="center">Unidad</TableCell>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="center" />
                    </TableRow>
                  </TableHead>
                  <TableBody>
                    {items.map((item) => (
                      <TableRow key={item.id}>
                        <TableCell sx={{ fontSize: '0.8125rem' }}>{item.producto}</TableCell>
                        <TableCell sx={{ fontSize: '0.8125rem' }} align="right">{item.cantidad}</TableCell>
                        <TableCell sx={{ fontSize: '0.8125rem' }} align="center">{item.unidad}</TableCell>
                        <TableCell align="center">
                          <IconButton size="small" onClick={() => handleEliminarItem(item.id)} color="error">
                            <Delete fontSize="small" />
                          </IconButton>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </TableContainer>
            )}
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={onClose} color="inherit">Cancelar</Button>
          <Button onClick={handleAceptar} variant="contained"
            disabled={items.length === 0 || !bodegaId}>
            {isEditing ? 'Guardar cambios' : 'Aceptar'}
          </Button>
        </DialogActions>
      </Dialog>

      <Dialog open={motivoOpen} onClose={() => setMotivoOpen(false)} maxWidth="sm" fullWidth>
        <DialogTitle sx={{ fontWeight: 600 }}>Motivo de la edición</DialogTitle>
        <DialogContent>
          <TextField label="Describa el motivo (mín. 10 caracteres)" size="small" fullWidth multiline rows={3}
            value={motivo} onChange={(e) => setMotivo(e.target.value)} sx={{ mt: 1 }} />
        </DialogContent>
        <DialogActions>
          <Button onClick={() => {
 setMotivoOpen(false); setMotivo(''); 
}} color="inherit">Cancelar</Button>
          <Button onClick={submit} variant="contained" disabled={motivo.length < 10}>Confirmar</Button>
        </DialogActions>
      </Dialog>
    </>
  );
}
