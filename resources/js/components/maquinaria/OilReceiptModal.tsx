import { router } from '@inertiajs/react';
import {
  Dialog, DialogTitle, DialogContent, DialogActions, Button, TextField, MenuItem,
  Box, FormControl, InputLabel, Select,
} from '@mui/material';
import { useState, useEffect } from 'react';

interface FaenaItem {
  id: string;
  tractor: { nombre: string } | null;
  fecha: string;
}

interface ProductoItem {
  id: string;
  nombre: string;
}

interface Props {
  open: boolean;
  onClose: () => void;
  faenas: FaenaItem[];
  productos: ProductoItem[];
  item?: Record<string, unknown> | null;
}

export default function OilReceiptModal({ open, onClose, faenas, productos, item }: Props) {
  const isEditing = !!item;

  const [formData, setFormData] = useState({
    uso_maquinaria_id: '',
    producto_id: '',
    cantidad_litros: '',
    costo_total_moneda_base: '',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    if (open) {
      if (item) {
        setFormData({
          uso_maquinaria_id: (item.uso_maquinaria_id as string) ?? '',
          producto_id: (item.producto_id as string) ?? '',
          cantidad_litros: String(item.cantidad_litros ?? ''),
          costo_total_moneda_base: String(item.costo_total_moneda_base ?? ''),
        });
      } else {
        setFormData({
          uso_maquinaria_id: '',
          producto_id: '',
          cantidad_litros: '',
          costo_total_moneda_base: '',
        });
      }

      setErrors({});
    }
  }, [open, item]);

  const handleChange = (name: string, value: string) => {
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = () => {
    setSaving(true);
    const method = isEditing ? 'put' as const : 'post' as const;
    const url = isEditing ? `/maquinaria/oil-receipts/${item?.id}` : '/maquinaria/oil-receipts';

    router[method](url, formData, {
      preserveScroll: true,
      onSuccess: () => {
        setSaving(false);
        onClose();
      },
      onError: (errs) => {
        setSaving(false);
        setErrors(errs as Record<string, string>);
      },
    });
  };

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle sx={{ fontWeight: 600 }}>
        {isEditing ? 'Editar salida de producto' : 'Nueva salida de producto'}
      </DialogTitle>
      <DialogContent dividers>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
          <FormControl size="small" error={!!errors.uso_maquinaria_id}>
            <InputLabel>Faena de maquinaria</InputLabel>
            <Select value={formData.uso_maquinaria_id} label="Faena de maquinaria"
              onChange={(e) => handleChange('uso_maquinaria_id', e.target.value)}
            >
              {faenas.map((f) => (
                <MenuItem key={f.id} value={f.id}>
                  {f.fecha} — {f.tractor?.nombre ?? 'Sin máquina'}
                </MenuItem>
              ))}
            </Select>
          </FormControl>

          <FormControl size="small" error={!!errors.producto_id}>
            <InputLabel>Producto</InputLabel>
            <Select value={formData.producto_id} label="Producto"
              onChange={(e) => handleChange('producto_id', e.target.value)}
            >
              {productos.map((p) => (
                <MenuItem key={p.id} value={p.id}>{p.nombre}</MenuItem>
              ))}
            </Select>
          </FormControl>

          <TextField label="Cantidad (litros)" type="number" size="small" value={formData.cantidad_litros}
            onChange={(e) => handleChange('cantidad_litros', e.target.value)}
            error={!!errors.cantidad_litros} helperText={errors.cantidad_litros} />

          <TextField label="Costo total ($)" type="number" size="small" value={formData.costo_total_moneda_base}
            onChange={(e) => handleChange('costo_total_moneda_base', e.target.value)}
            error={!!errors.costo_total_moneda_base} helperText={errors.costo_total_moneda_base} />
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose} color="inherit">Cancelar</Button>
        <Button onClick={handleSubmit} variant="contained" disabled={saving}>
          {saving ? 'Guardando...' : isEditing ? 'Actualizar' : 'Crear'}
        </Button>
      </DialogActions>
    </Dialog>
  );
}
