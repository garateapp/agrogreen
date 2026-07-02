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
  ordenesCompra: { value: string; label: string }[];
  metodosPago: { value: string; label: string }[];
}

export default function PagoModal({ open, onClose, editingItem, ordenesCompra, metodosPago }: Props) {
  const [egresoId, setEgresoId] = useState('');
  const [fechaPago, setFechaPago] = useState(new Date().toISOString().slice(0, 10));
  const [monto, setMonto] = useState('');
  const [metodoPago, setMetodoPago] = useState('transferencia');
  const [banco, setBanco] = useState('');

  useEffect(() => {
    if (open && editingItem) {
      setEgresoId(String(editingItem.egreso_id ?? ''));
      setFechaPago(String(editingItem.fechaPago ?? new Date().toISOString().slice(0, 10)));
      setMonto(String(editingItem.monto ?? ''));
      setMetodoPago(String(editingItem.medioPago ?? '').toLowerCase() || 'transferencia');
      setBanco(String(editingItem.banco ?? ''));
    } else if (open) {
      setEgresoId(''); setFechaPago(new Date().toISOString().slice(0, 10));
      setMonto(''); setMetodoPago('transferencia'); setBanco('');
    }
  }, [open, editingItem]);

  const submit = () => {
    const payload = {
      egreso_id: egresoId,
      fecha_pago: fechaPago,
      monto_moneda_base: parseFloat(monto),
      metodo_pago: metodoPago,
      cuenta_bancaria_origen: banco || null,
    };

    if (editingItem) {
      router.put(`/compras/payments/${editingItem.id}`, payload, {
        preserveState: true, preserveScroll: true,
        onSuccess: () => onClose(),
      });
    } else {
      router.post('/compras/payments', payload, {
        preserveState: true, preserveScroll: true,
        onSuccess: () => onClose(),
      });
    }
  };

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle sx={{ fontWeight: 600 }}>{editingItem ? 'Editar Pago' : 'Nuevo Pago'}</DialogTitle>
      <DialogContent dividers>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
          <FormControl size="small">
            <InputLabel>Egreso / Documento</InputLabel>
            <Select value={egresoId} label="Egreso / Documento" onChange={(e) => setEgresoId(e.target.value)}>
              {ordenesCompra.map((oc) => <MenuItem key={oc.value} value={oc.value}>{oc.label}</MenuItem>)}
            </Select>
          </FormControl>
          <TextField label="Fecha Pago" type="date" size="small" value={fechaPago}
            onChange={(e) => setFechaPago(e.target.value)} slotProps={{ inputLabel: { shrink: true } }} />
          <TextField label="Monto" type="number" size="small" value={monto}
            onChange={(e) => setMonto(e.target.value)} />
          <FormControl size="small">
            <InputLabel>Medio Pago</InputLabel>
            <Select value={metodoPago} label="Medio Pago" onChange={(e) => setMetodoPago(e.target.value)}>
              {metodosPago.map((m) => <MenuItem key={m.value} value={m.value}>{m.label}</MenuItem>)}
            </Select>
          </FormControl>
          <TextField label="Banco / Cuenta origen" size="small" value={banco}
            onChange={(e) => setBanco(e.target.value)} />
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose} color="inherit">Cancelar</Button>
        <Button onClick={submit} variant="contained" disabled={!egresoId || !monto}>Aceptar</Button>
      </DialogActions>
    </Dialog>
  );
}
