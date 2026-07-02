import { router } from '@inertiajs/react';
import {
  Dialog, DialogTitle, DialogContent, DialogActions, Button, TextField,
  Box, FormControl, InputLabel, Select, MenuItem, ToggleButtonGroup, ToggleButton,
} from '@mui/material';
import { useState } from 'react';

interface SelectOption {
  value: string;
  label: string;
}

interface Props {
  open: boolean;
  onClose: () => void;
  editingItem?: Record<string, unknown> | null;
  ordenesCompra: SelectOption[];
  proveedores?: SelectOption[];
  centrosCosto?: SelectOption[];
  itemsGasto?: SelectOption[];
}

function matchEstado(v: string): string {
  if (v === 'Pagado') {
 return 'pagado'; 
}

  if (v === 'Parcial') {
 return 'abono_parcial'; 
}

  return 'pendiente';
}

function initFrom(item: Record<string, unknown> | null | undefined, key: string, fallback: string): string {
  return item ? String(item[key] ?? fallback) : fallback;
}

export default function EgresoModal({ open, onClose, editingItem, ordenesCompra, proveedores, centrosCosto, itemsGasto }: Props) {
  const [tipoOrigen, setTipoOrigen] = useState(() => initFrom(editingItem, 'tipo_origen', 'oc'));
  const [ordenCompraId, setOrdenCompraId] = useState(() => initFrom(editingItem, 'orden_compra_id', ''));
  const [proveedorId, setProveedorId] = useState(() => initFrom(editingItem, 'proveedor_id', ''));
  const [centroCostoId, setCentroCostoId] = useState(() => initFrom(editingItem, 'centro_costo_id', ''));
  const [itemGastoId, setItemGastoId] = useState(() => initFrom(editingItem, 'item_gasto_id', ''));
  const [tipoDocumento, setTipoDocumento] = useState(() => initFrom(editingItem, 'tipoDoc', 'factura'));
  const [folio, setFolio] = useState(() => initFrom(editingItem, 'folio', ''));
  const [fecha, setFecha] = useState(() => editingItem
    ? String(editingItem.fechaRecepcion ?? new Date().toISOString().slice(0, 10))
    : new Date().toISOString().slice(0, 10));
  const [monto, setMonto] = useState(() => initFrom(editingItem, 'total', ''));
  const [estadoPago, setEstadoPago] = useState(() => editingItem
    ? matchEstado(String(editingItem.estadoPago ?? ''))
    : 'pendiente');

  const submit = () => {
    const payload: Record<string, string | number | null> = {
      tipo_origen: tipoOrigen,
      tipo_documento: tipoDocumento,
      folio_documento: folio || null,
      fecha_registro: fecha,
      monto_total_moneda_base: parseFloat(monto),
      estado_pago: estadoPago,
    };

    if (tipoOrigen === 'oc') {
      payload.orden_compra_id = ordenCompraId;
    } else {
      payload.proveedor_id = proveedorId;
      payload.centro_costo_id = centroCostoId;
      payload.item_gasto_id = itemGastoId;
    }

    if (editingItem) {
      router.put(`/compras/invoices/${editingItem.id}`, payload, {
        preserveState: true, preserveScroll: true,
        onSuccess: () => onClose(),
      });
    } else {
      router.post('/compras/invoices', payload, {
        preserveState: true, preserveScroll: true,
        onSuccess: () => onClose(),
      });
    }
  };

  const canSubmit = tipoOrigen === 'oc'
    ? !!ordenCompraId && !!monto
    : !!proveedorId && !!centroCostoId && !!itemGastoId && !!monto;

  return (
    <Dialog key={String(editingItem?.id ?? 'new')} open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle sx={{ fontWeight: 600 }}>{editingItem ? `Editar: ${editingItem.folio}` : 'Nuevo Egreso'}</DialogTitle>
      <DialogContent dividers>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
          <ToggleButtonGroup
            value={tipoOrigen}
            exclusive
            fullWidth
            size="small"
            onChange={(_, v) => {
 if (v) {
 setTipoOrigen(v); 
} 
}}
          >
            <ToggleButton value="oc">Asociado a OC</ToggleButton>
            <ToggleButton value="directo">Gasto Directo</ToggleButton>
          </ToggleButtonGroup>

          {tipoOrigen === 'oc' ? (
            <FormControl size="small">
              <InputLabel>Orden de Compra</InputLabel>
              <Select value={ordenCompraId} label="Orden de Compra" onChange={(e) => setOrdenCompraId(e.target.value)}>
                {ordenesCompra.map((oc) => <MenuItem key={oc.value} value={oc.value}>{oc.label}</MenuItem>)}
              </Select>
            </FormControl>
          ) : (
            <>
              <FormControl size="small">
                <InputLabel>Proveedor</InputLabel>
                <Select value={proveedorId} label="Proveedor" onChange={(e) => setProveedorId(e.target.value)}>
                  {proveedores?.map((p) => <MenuItem key={p.value} value={p.value}>{p.label}</MenuItem>)}
                </Select>
              </FormControl>
              <FormControl size="small">
                <InputLabel>Centro de Costo</InputLabel>
                <Select value={centroCostoId} label="Centro de Costo" onChange={(e) => setCentroCostoId(e.target.value)}>
                  {centrosCosto?.map((cc) => <MenuItem key={cc.value} value={cc.value}>{cc.label}</MenuItem>)}
                </Select>
              </FormControl>
              <FormControl size="small">
                <InputLabel>Item de Gasto</InputLabel>
                <Select value={itemGastoId} label="Item de Gasto" onChange={(e) => setItemGastoId(e.target.value)}>
                  {itemsGasto?.map((ig) => <MenuItem key={ig.value} value={ig.value}>{ig.label}</MenuItem>)}
                </Select>
              </FormControl>
            </>
          )}

          <FormControl size="small">
            <InputLabel>Tipo Documento</InputLabel>
            <Select value={tipoDocumento} label="Tipo Documento" onChange={(e) => setTipoDocumento(e.target.value)}>
              <MenuItem value="factura">Factura</MenuItem>
              <MenuItem value="boleta">Boleta</MenuItem>
            </Select>
          </FormControl>
          <TextField label="Folio" size="small" value={folio} onChange={(e) => setFolio(e.target.value)} />
          <TextField label="Fecha Registro" type="date" size="small" value={fecha}
            onChange={(e) => setFecha(e.target.value)} slotProps={{ inputLabel: { shrink: true } }} />
          <TextField label="Monto Total" type="number" size="small" value={monto}
            onChange={(e) => setMonto(e.target.value)} />
          <FormControl size="small">
            <InputLabel>Estado Pago</InputLabel>
            <Select value={estadoPago} label="Estado Pago" onChange={(e) => setEstadoPago(e.target.value)}>
              <MenuItem value="pendiente">Pendiente</MenuItem>
              <MenuItem value="pagado">Pagado</MenuItem>
              <MenuItem value="abono_parcial">Parcial</MenuItem>
            </Select>
          </FormControl>
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose} color="inherit">Cancelar</Button>
        <Button onClick={submit} variant="contained" disabled={!canSubmit}>Aceptar</Button>
      </DialogActions>
    </Dialog>
  );
}
