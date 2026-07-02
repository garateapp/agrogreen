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
  proveedores: { value: string; label: string }[];
}

export default function CotizacionModal({ open, onClose, editingItem, proveedores }: Props) {
  const [proveedorId, setProveedorId] = useState('');
  const [nroSolicitud, setNroSolicitud] = useState('');
  const [fecha, setFecha] = useState(new Date().toISOString().slice(0, 10));
  const [descripcion, setDescripcion] = useState('');
  const [monto, setMonto] = useState('');
  const [estado, setEstado] = useState('pendiente');

  useEffect(() => {
    if (open && editingItem) {
      setProveedorId(String(editingItem.proveedor_id ?? ''));
      setNroSolicitud(String(editingItem.nroSolicitud ?? ''));
      setFecha(String(editingItem.fecha ?? new Date().toISOString().slice(0, 10)));
      setDescripcion(String(editingItem.producto ?? ''));
      setMonto(String(editingItem.montoEstimado ?? ''));
      setEstado(matchEstado(editingItem.estado as string));
    } else if (open) {
      setProveedorId(''); setNroSolicitud(''); setFecha(new Date().toISOString().slice(0, 10));
      setDescripcion(''); setMonto(''); setEstado('pendiente');
    }
  }, [open, editingItem]);

  function matchEstado(v: string): string {
    if (v === 'Aprobado') {
return 'aprobado';
}

    if (v === 'Rechazado') {
return 'rechazado';
}

    if (v === 'Anulado') {
return 'anulado';
}

    return 'pendiente';
  }

  const submit = () => {
    const payload = {
      proveedor_id: proveedorId,
      numero_solicitud: nroSolicitud || null,
      fecha_solicitud: fecha,
      descripcion,
      monto_estimado: parseFloat(monto),
      estado,
    };

    if (editingItem) {
      router.put(`/compras/quotations/${editingItem.id}`, payload, {
        preserveState: true, preserveScroll: true,
        onSuccess: () => onClose(),
      });
    } else {
      router.post('/compras/quotations', payload, {
        preserveState: true, preserveScroll: true,
        onSuccess: () => onClose(),
      });
    }
  };

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle sx={{ fontWeight: 600 }}>{editingItem ? `Editar: ${editingItem.nroSolicitud}` : 'Nueva Solicitud de Cotización'}</DialogTitle>
      <DialogContent dividers>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
          <FormControl size="small">
            <InputLabel>Proveedor</InputLabel>
            <Select value={proveedorId} label="Proveedor" onChange={(e) => setProveedorId(e.target.value)}>
              {proveedores.map((p) => <MenuItem key={p.value} value={p.value}>{p.label}</MenuItem>)}
            </Select>
          </FormControl>
          <TextField label="Nº Solicitud" size="small" value={nroSolicitud}
            onChange={(e) => setNroSolicitud(e.target.value)} />
          <TextField label="Fecha" type="date" size="small" value={fecha}
            onChange={(e) => setFecha(e.target.value)} slotProps={{ inputLabel: { shrink: true } }} />
          <TextField label="Producto / Servicio" size="small" value={descripcion} multiline rows={2}
            onChange={(e) => setDescripcion(e.target.value)} />
          <TextField label="Monto Estimado" type="number" size="small" value={monto}
            onChange={(e) => setMonto(e.target.value)} />
          <FormControl size="small">
            <InputLabel>Estado</InputLabel>
            <Select value={estado} label="Estado" onChange={(e) => setEstado(e.target.value)}>
              <MenuItem value="pendiente">Pendiente</MenuItem>
              <MenuItem value="aprobado">Aprobado</MenuItem>
              <MenuItem value="rechazado">Rechazado</MenuItem>
              <MenuItem value="anulado">Anulado</MenuItem>
            </Select>
          </FormControl>
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose} color="inherit">Cancelar</Button>
        <Button onClick={submit} variant="contained" disabled={!proveedorId || !monto}>Aceptar</Button>
      </DialogActions>
    </Dialog>
  );
}
