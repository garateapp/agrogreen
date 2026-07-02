import { router } from '@inertiajs/react';
import {
  Dialog, DialogTitle, DialogContent, DialogActions, Button, TextField,
  Box, FormControl, InputLabel, Select, MenuItem,
} from '@mui/material';
import { useState, useEffect } from 'react';

interface SelectItem {
  id: string;
  nombre: string;
  apellido?: string;
  patente_o_identificador?: string;
}

interface Props {
  open: boolean;
  onClose: () => void;
  tractores: SelectItem[];
  empleados: SelectItem[];
  centrosCosto: SelectItem[];
  item?: Record<string, unknown> | null;
}

export default function MachineTaskModal({ open, onClose, tractores, empleados, centrosCosto, item }: Props) {
  const isEditing = !!item;

  const [formData, setFormData] = useState({
    fecha: new Date().toISOString().slice(0, 10),
    tractor_id: '',
    operador_id: '',
    centro_costo_id: '',
    horas_inicio: '',
    horas_fin: '',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    if (open) {
      if (item) {
        setFormData({
          fecha: (item.fecha as string) ?? '',
          tractor_id: (item.tractor_id as string) ?? '',
          operador_id: (item.operador_id as string) ?? '',
          centro_costo_id: (item.centro_costo_id as string) ?? '',
          horas_inicio: String(item.horas_inicio ?? ''),
          horas_fin: String(item.horas_fin ?? ''),
        });
      } else {
        setFormData({
          fecha: new Date().toISOString().slice(0, 10),
          tractor_id: '',
          operador_id: '',
          centro_costo_id: '',
          horas_inicio: '',
          horas_fin: '',
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
    const url = isEditing ? `/maquinaria/machine-tasks/${item?.id}` : '/maquinaria/machine-tasks';

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

  const horasInicio = parseFloat(formData.horas_inicio) || 0;
  const horasFin = parseFloat(formData.horas_fin) || 0;
  const horasTotales = horasFin > horasInicio ? (horasFin - horasInicio).toFixed(2) : '0.00';

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle sx={{ fontWeight: 600 }}>
        {isEditing ? 'Editar faena de maquinaria' : 'Nueva faena de maquinaria'}
      </DialogTitle>
      <DialogContent dividers>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
          <TextField label="Fecha" type="date" size="small" value={formData.fecha}
            onChange={(e) => handleChange('fecha', e.target.value)}
            slotProps={{ inputLabel: { shrink: true } }}
            error={!!errors.fecha} helperText={errors.fecha} />

          <FormControl size="small" error={!!errors.tractor_id}>
            <InputLabel>Máquina</InputLabel>
            <Select value={formData.tractor_id} label="Máquina"
              onChange={(e) => handleChange('tractor_id', e.target.value)}
            >
              {tractores.map((t) => (
                <MenuItem key={t.id} value={t.id}>
                  {t.nombre}{t.patente_o_identificador ? ` (${t.patente_o_identificador})` : ''}
                </MenuItem>
              ))}
            </Select>
          </FormControl>

          <FormControl size="small" error={!!errors.operador_id}>
            <InputLabel>Operador</InputLabel>
            <Select value={formData.operador_id} label="Operador"
              onChange={(e) => handleChange('operador_id', e.target.value)}
            >
              {empleados.map((e) => (
                <MenuItem key={e.id} value={e.id}>{e.nombre} {e.apellido}</MenuItem>
              ))}
            </Select>
          </FormControl>

          <FormControl size="small" error={!!errors.centro_costo_id}>
            <InputLabel>Centro de costo</InputLabel>
            <Select value={formData.centro_costo_id} label="Centro de costo"
              onChange={(e) => handleChange('centro_costo_id', e.target.value)}
            >
              {centrosCosto.map((c) => (
                <MenuItem key={c.id} value={c.id}>{c.nombre}</MenuItem>
              ))}
            </Select>
          </FormControl>

          <Box sx={{ display: 'flex', gap: 2 }}>
            <TextField label="Horas inicio" type="number" size="small" value={formData.horas_inicio}
              onChange={(e) => handleChange('horas_inicio', e.target.value)}
              sx={{ flex: 1 }} error={!!errors.horas_inicio} helperText={errors.horas_inicio} />
            <TextField label="Horas fin" type="number" size="small" value={formData.horas_fin}
              onChange={(e) => handleChange('horas_fin', e.target.value)}
              sx={{ flex: 1 }} error={!!errors.horas_fin} helperText={errors.horas_fin} />
          </Box>

          <TextField label="Horas totales" type="text" size="small" value={horasTotales}
            slotProps={{ input: { readOnly: true } }} sx={{ maxWidth: 120 }} />
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
