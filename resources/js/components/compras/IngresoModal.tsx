import { router } from '@inertiajs/react';
import {
  Dialog, DialogTitle, DialogContent, DialogActions, Button, TextField,
  Box, FormControl, InputLabel, Select, MenuItem,
} from '@mui/material';
import { useEffect, useState } from 'react';

interface Props {
  open: boolean;
  onClose: () => void;
  editingItem?: Record<string, unknown> | null;
  clientes: { value: string; label: string }[];
}

export default function IngresoModal({ open, onClose, editingItem, clientes }: Props) {
  const [clienteId, setClienteId] = useState('');
  const [tipoDoc, setTipoDoc] = useState('factura');
  const [folio, setFolio] = useState('');
  const [fecha, setFecha] = useState(new Date().toISOString().slice(0, 10));
  const [neto, setNeto] = useState('');
  const [iva, setIva] = useState('');
  const [total, setTotal] = useState('');
  const [estado, setEstado] = useState('pendiente');

  useEffect(() => {
    if (open && editingItem) {
      setClienteId(String(editingItem.cliente_id ?? ''));
      setTipoDoc(String(editingItem.tipoDoc ?? 'factura'));
      setFolio(String(editingItem.folio ?? ''));
      setFecha(String(editingItem.fecha ?? new Date().toISOString().slice(0, 10)));
      setNeto(String(editingItem.neto ?? ''));
      setIva(String(editingItem.iva ?? ''));
      setTotal(String(editingItem.total ?? ''));
      setEstado(matchEstado(editingItem.estado as string));
    } else if (open) {
      setClienteId(''); setTipoDoc('factura'); setFolio('');
      setFecha(new Date().toISOString().slice(0, 10)); setNeto(''); setIva(''); setTotal(''); setEstado('pendiente');
    }
  }, [open, editingItem]);

  function matchEstado(v: string): string {
    if (v === 'Pagado') {
return 'pagado';
}

    if (v === 'Anulado') {
return 'anulado';
}

    return 'pendiente';
  }

  const submit = () => {
    const payload = {
      cliente_id: clienteId,
      tipo_documento: tipoDoc,
      folio_documento: folio || null,
      fecha_emision: fecha,
      monto_neto: parseFloat(neto),
      iva: parseFloat(iva),
      monto_total: parseFloat(total),
      estado,
    };

    if (editingItem) {
      router.put(`/compras/incomes/${editingItem.id}`, payload, {
        preserveState: true, preserveScroll: true,
        onSuccess: () => onClose(),
      });
    } else {
      router.post('/compras/incomes', payload, {
        preserveState: true, preserveScroll: true,
        onSuccess: () => onClose(),
      });
    }
  };

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle sx={{ fontWeight: 600 }}>{editingItem ? `Editar: ${editingItem.folio}` : 'Nuevo Ingreso'}</DialogTitle>
      <DialogContent dividers>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
          <FormControl size="small">
            <InputLabel>Cliente</InputLabel>
            <Select value={clienteId} label="Cliente" onChange={(e) => setClienteId(e.target.value)}>
              {clientes.map((c) => <MenuItem key={c.value} value={c.value}>{c.label}</MenuItem>)}
            </Select>
          </FormControl>
          <FormControl size="small">
            <InputLabel>Tipo Documento</InputLabel>
            <Select value={tipoDoc} label="Tipo Documento" onChange={(e) => setTipoDoc(e.target.value)}>
              <MenuItem value="factura">Factura</MenuItem>
              <MenuItem value="boleta">Boleta</MenuItem>
            </Select>
          </FormControl>
          <TextField label="Folio" size="small" value={folio} onChange={(e) => setFolio(e.target.value)} />
          <TextField label="Fecha Emisión" type="date" size="small" value={fecha}
            onChange={(e) => setFecha(e.target.value)} slotProps={{ inputLabel: { shrink: true } }} />
          <TextField label="Neto" type="number" size="small" value={neto}
            onChange={(e) => setNeto(e.target.value)} />
          <TextField label="IVA" type="number" size="small" value={iva}
            onChange={(e) => setIva(e.target.value)} />
          <TextField label="Total" type="number" size="small" value={total}
            onChange={(e) => setTotal(e.target.value)} />
          <FormControl size="small">
            <InputLabel>Estado</InputLabel>
            <Select value={estado} label="Estado" onChange={(e) => setEstado(e.target.value)}>
              <MenuItem value="pendiente">Pendiente</MenuItem>
              <MenuItem value="pagado">Pagado</MenuItem>
              <MenuItem value="anulado">Anulado</MenuItem>
            </Select>
          </FormControl>
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose} color="inherit">Cancelar</Button>
        <Button onClick={submit} variant="contained" disabled={!clienteId || !neto || !total}>Aceptar</Button>
      </DialogActions>
    </Dialog>
  );
}
