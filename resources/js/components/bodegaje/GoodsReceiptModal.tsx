import { router } from '@inertiajs/react';
import { Delete } from '@mui/icons-material';
import {
  Dialog, DialogTitle, DialogContent, DialogActions, Button, TextField,
  Box, Radio, RadioGroup, FormControlLabel, FormControl, FormLabel,
  Switch, Checkbox, Select, MenuItem, InputLabel, Table, TableBody,
  TableCell, TableContainer, TableHead, TableRow, Paper, IconButton,
  Typography, Divider,
} from '@mui/material';
import { useState, useCallback, useEffect } from 'react';
import type { LineItem } from './bodegaje-types';

interface Props {
  open: boolean;
  onClose: () => void;
  title?: string;
  showVencimientoLote?: boolean;
  proveedores: Array<{ id: string; razon_social: string; rut: string }>;
  productos: Array<{ id: string; nombre: string }>;
  bodegas: Array<{ id: string; nombre: string }>;
  centroCostos: Array<{ id: string; nombre: string }>;
  editingItem?: Record<string, unknown> | null;
}

function generarId() {
 return Math.random().toString(36).slice(2, 9); 
}

const defaultLineas: LineItem[] = [];

export default function GoodsReceiptModal({
  open, onClose, title = 'Nueva guía de entrada', showVencimientoLote = true,
  proveedores, productos, bodegas, centroCostos, editingItem,
}: Props) {
  const [numero, setNumero] = useState('');
  const [fecha, setFecha] = useState(new Date().toISOString().slice(0, 10));
  const [descripcion, setDescripcion] = useState('');
  const [tipo, setTipo] = useState<'productos' | 'servicios'>('productos');
  const [proveedorId, setProveedorId] = useState('');
  const [distribuirCostos, setDistribuirCostos] = useState(false);
  const [descuentoLinea, setDescuentoLinea] = useState(false);
  const [vencimientoLote, setVencimientoLote] = useState(false);

  const [producto, setProducto] = useState('');
  const [bodega, setBodega] = useState('');
  const [cantidad, setCantidad] = useState('');
  const [precio, setPrecio] = useState('');
  const [exentoIva, setExentoIva] = useState(false);
  const [centroCosto, setCentroCosto] = useState('');

  const [items, setItems] = useState<LineItem[]>(defaultLineas);

  const [motivoOpen, setMotivoOpen] = useState(false);
  const [motivo, setMotivo] = useState('');

  // Populate form when editing
  useEffect(() => {
    if (open && editingItem) {
      setNumero(String(editingItem.numero ?? ''));
      setFecha(String(editingItem.fecha ?? new Date().toISOString().slice(0, 10)));
      setDescripcion(String(editingItem.descripcion ?? ''));
      setTipo((editingItem.tipo as 'productos' | 'servicios') ?? 'productos');
      setProveedorId(String(editingItem.proveedor_id ?? ''));
      setDistribuirCostos(Boolean(editingItem.distribuir_costos));
      setDescuentoLinea(Boolean(editingItem.descuento_linea));
      setVencimientoLote(Boolean(editingItem.vencimiento_lote));
      const lineasRaw = editingItem.lineas;
      const parsed = typeof lineasRaw === 'string' ? (() => {
 try {
 return JSON.parse(lineasRaw); 
} catch {
 return []; 
} 
})() : lineasRaw;
      setItems(Array.isArray(parsed) ? parsed.map((l: Record<string, unknown>) => ({
        ...l,
        id: generarId(),
      })) as LineItem[] : []);
    } else if (open) {
      resetForm();
    }
  }, [open, editingItem]);

  const handleAgregarItem = useCallback(() => {
    if (!producto || !bodega || !cantidad || !precio) {
return;
}

    const cant = parseFloat(cantidad);
    const prec = parseFloat(precio);
    const item: LineItem = {
      id: generarId(),
      producto: productos.find((p) => p.id === producto)?.nombre ?? producto,
      producto_id: producto,
      bodega: bodegas.find((b) => b.id === bodega)?.nombre ?? bodega,
      bodega_id: bodega,
      cantidad: cant,
      precio: prec,
      unidad: 'unidad',
      subtotal: cant * prec,
      centroCosto: centroCostos.find((c) => c.id === centroCosto)?.nombre,
      exentoIva,
    };
    setItems((prev) => [...prev, item]);
    setProducto(''); setBodega(''); setCantidad(''); setPrecio('');
    setExentoIva(false); setCentroCosto('');
  }, [producto, bodega, cantidad, precio, exentoIva, centroCosto, productos, bodegas, centroCostos]);

  const handleEliminarItem = (id: string) => {
    setItems((prev) => prev.filter((i) => i.id !== id));
  };

  const resetForm = () => {
    setNumero(''); setFecha(new Date().toISOString().slice(0, 10));
    setDescripcion(''); setTipo('productos'); setProveedorId('');
    setDistribuirCostos(false); setDescuentoLinea(false); setVencimientoLote(false);
    setProducto(''); setBodega(''); setCantidad(''); setPrecio('');
    setExentoIva(false); setCentroCosto(''); setItems([]);
  };

  const handleAceptar = () => {
    if (editingItem) {
      setMotivoOpen(true);
    } else {
      submit();
    }
  };

  const submit = () => {
    const payload = {
      numero,
      fecha_emision: fecha,
      descripcion,
      tipo,
      proveedor_id: proveedorId || null,
      distribuir_costos: distribuirCostos,
      descuento_linea: descuentoLinea,
      vencimiento_lote: vencimientoLote,
      lineas: JSON.stringify(items.map(({ id, ...rest }) => rest)),
    };

    if (editingItem) {
      router.put(`/bodegaje/goods-receipts/${editingItem.id}`, { ...payload, motivo }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
 setMotivoOpen(false); setMotivo(''); resetForm(); onClose(); 
},
      });
    } else {
      router.post('/bodegaje/goods-receipts', payload, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
 resetForm(); onClose(); 
},
      });
    }
  };

  return (
    <>
      <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth>
        <DialogTitle sx={{ fontWeight: 600 }}>{editingItem ? `Editar: ${editingItem.numero}` : title}</DialogTitle>
        <DialogContent dividers>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            {/* Cabecera */}
            <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap' }}>
              <TextField label="Número de documento" size="small" value={numero}
                onChange={(e) => setNumero(e.target.value)} sx={{ flex: '1 1 180px' }} />
              <TextField label="Fecha emisión" type="date" size="small" value={fecha}
                onChange={(e) => setFecha(e.target.value)}
                slotProps={{ inputLabel: { shrink: true } }} sx={{ flex: '0 0 160px' }} />
            </Box>
            <TextField label="Descripción" size="small" value={descripcion}
              onChange={(e) => setDescripcion(e.target.value)} multiline rows={2} />

            {/* Proveedor */}
            <FormControl size="small" sx={{ minWidth: 200 }}>
              <InputLabel>Proveedor</InputLabel>
              <Select value={proveedorId} label="Proveedor" onChange={(e) => setProveedorId(e.target.value)}>
                <MenuItem value=""><em>Seleccionar...</em></MenuItem>
                {proveedores.map((p) => (
                  <MenuItem key={p.id} value={p.id}>{p.razon_social} ({p.rut})</MenuItem>
                ))}
              </Select>
            </FormControl>

            {/* Tipo selector */}
            <FormControl>
              <FormLabel sx={{ fontSize: '0.8125rem' }}>Tipo</FormLabel>
              <RadioGroup row value={tipo} onChange={(e) => setTipo(e.target.value as 'productos' | 'servicios')}>
                <FormControlLabel value="productos" control={<Radio size="small" />} label="Productos" />
                <FormControlLabel value="servicios" control={<Radio size="small" />} label="Servicios" />
              </RadioGroup>
            </FormControl>

            {/* Switches */}
            <Box sx={{ display: 'flex', gap: 3, flexWrap: 'wrap', alignItems: 'center' }}>
              <FormControlLabel control={<Switch size="small" checked={distribuirCostos}
                onChange={(e) => setDistribuirCostos(e.target.checked)} />} label="Distribuir costos" />
              <FormControlLabel control={<Switch size="small" checked={descuentoLinea}
                onChange={(e) => setDescuentoLinea(e.target.checked)} />} label="Descuento por línea" />
              {showVencimientoLote && (
                <FormControlLabel control={<Checkbox size="small" checked={vencimientoLote}
                  onChange={(e) => setVencimientoLote(e.target.checked)} />} label="Vencimiento por lote" />
              )}
            </Box>

            <Divider />

            {/* Línea de carga */}
            <Typography variant="subtitle2" sx={{ fontSize: '0.8125rem', fontWeight: 600 }}>Agregar línea</Typography>
            <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', alignItems: 'center' }}>
              <FormControl size="small" sx={{ minWidth: 160, flex: '1 1 140px' }}>
                <InputLabel>Producto</InputLabel>
                <Select value={producto} label="Producto" onChange={(e) => setProducto(e.target.value)}>
                  {productos.map((p) => <MenuItem key={p.id} value={p.id}>{p.nombre}</MenuItem>)}
                </Select>
              </FormControl>
              <FormControl size="small" sx={{ minWidth: 140, flex: '1 1 120px' }}>
                <InputLabel>Bodega</InputLabel>
                <Select value={bodega} label="Bodega" onChange={(e) => setBodega(e.target.value)}>
                  {bodegas.map((b) => <MenuItem key={b.id} value={b.id}>{b.nombre}</MenuItem>)}
                </Select>
              </FormControl>
              <TextField label="Cantidad" type="number" size="small" value={cantidad}
                onChange={(e) => setCantidad(e.target.value)} sx={{ maxWidth: 100 }} />
              <TextField label="Precio" type="number" size="small" value={precio}
                onChange={(e) => setPrecio(e.target.value)} sx={{ maxWidth: 110 }} />
              <FormControlLabel control={<Checkbox size="small" checked={exentoIva}
                onChange={(e) => setExentoIva(e.target.checked)} />} label="Exento IVA" />
              <FormControl size="small" sx={{ minWidth: 140, flex: '1 1 120px' }}>
                <InputLabel>Centro costo</InputLabel>
                <Select value={centroCosto} label="Centro costo" onChange={(e) => setCentroCosto(e.target.value)}>
                  {centroCostos.map((c) => <MenuItem key={c.id} value={c.id}>{c.nombre}</MenuItem>)}
                </Select>
              </FormControl>
              <Button variant="contained" size="small" onClick={handleAgregarItem}>Aceptar</Button>
            </Box>

            {/* Tabla de líneas */}
            {items.length > 0 && (
              <TableContainer component={Paper} variant="outlined" sx={{ mt: 1 }}>
                <Table size="small">
                  <TableHead>
                    <TableRow>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }}>Bodega</TableCell>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }}>Producto/servicio</TableCell>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="right">Precio</TableCell>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="right">Cantidad</TableCell>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="center">Unidad</TableCell>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="right">Subtotal</TableCell>
                      <TableCell sx={{ fontWeight: 600, fontSize: '0.8rem' }} align="center"></TableCell>
                    </TableRow>
                  </TableHead>
                  <TableBody>
                    {items.map((item) => (
                      <TableRow key={item.id}>
                        <TableCell sx={{ fontSize: '0.8125rem' }}>{item.bodega}</TableCell>
                        <TableCell sx={{ fontSize: '0.8125rem' }}>{item.producto}</TableCell>
                        <TableCell sx={{ fontSize: '0.8125rem' }} align="right">${item.precio.toLocaleString('es-CL')}</TableCell>
                        <TableCell sx={{ fontSize: '0.8125rem' }} align="right">{item.cantidad}</TableCell>
                        <TableCell sx={{ fontSize: '0.8125rem' }} align="center">{item.unidad}</TableCell>
                        <TableCell sx={{ fontSize: '0.8125rem' }} align="right">${item.subtotal.toLocaleString('es-CL')}</TableCell>
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
          <Button onClick={handleAceptar} variant="contained" disabled={items.length === 0}>
            {editingItem ? 'Guardar cambios' : 'Aceptar'}
          </Button>
        </DialogActions>
      </Dialog>

      {/* Motivo dialog for edits */}
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
