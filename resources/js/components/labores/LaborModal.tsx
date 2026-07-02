import { router } from '@inertiajs/react';
import {
  Dialog, DialogTitle, DialogContent, DialogActions, Button, TextField, MenuItem,
  FormControlLabel, Switch, Grid,
} from '@mui/material';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { parse, format } from 'date-fns';
import { es } from 'date-fns/locale';
import { useState } from 'react';
import type { Labor, SelectOption } from './types';

const FRECUENCIA_OPTIONS = [
  { value: 'diaria', label: 'Diaria' },
  { value: 'semanal', label: 'Semanal' },
  { value: 'quincenal', label: 'Quincenal' },
  { value: 'mensual', label: 'Mensual' },
];

interface LaborFormProps {
  editingItem: Partial<Labor> | null;
  actividades: SelectOption[];
  centrosCosto: SelectOption[];
  cuarteles: SelectOption[];
  onClose: () => void;
}

function LaborForm({ editingItem, actividades, centrosCosto, cuarteles, onClose }: LaborFormProps) {
  const [actividad_id, setActividadId] = useState(editingItem?.actividad_id ?? '');
  const [centro_costo_id, setCentroCostoId] = useState(editingItem?.centro_costo_id ?? '');
  const [fecha_programada, setFechaProgramada] = useState<Date | null>(
    editingItem?.fecha_programada ? parse(editingItem.fecha_programada, 'yyyy-MM-dd', new Date()) : new Date()
  );
  const [fecha_fin_estimada, setFechaFinEstimada] = useState<Date | null>(
    editingItem?.fecha_fin_estimada ? parse(editingItem.fecha_fin_estimada, 'yyyy-MM-dd', new Date()) : null
  );
  const [avance, setAvance] = useState(editingItem?.avance ?? 0);
  const [observaciones, setObservaciones] = useState(editingItem?.observaciones ?? '');
  const [valor_trato_unitario, setValorTratoUnitario] = useState(
    editingItem?.valor_trato_unitario ? String(editingItem.valor_trato_unitario) : ''
  );
  const [requiere_empleados, setRequiereEmpleados] = useState(editingItem?.requiere_empleados ?? true);
  const [es_ciclica, setEsCiclica] = useState(editingItem?.es_ciclica ?? false);
  const [frecuencia, setFrecuencia] = useState(editingItem?.frecuencia ?? '');
  const [fecha_fin_ciclo, setFechaFinCiclo] = useState<Date | null>(
    editingItem?.fecha_fin_ciclo ? parse(editingItem.fecha_fin_ciclo, 'yyyy-MM-dd', new Date()) : null
  );
  const [cuarteles_selected, setCuartelesSelected] = useState<string[]>(
    editingItem?.cuarteles?.map((c) => c.id) ?? []
  );

  const isEdit = !!editingItem;
  const selectedActividad = actividades.find((a) => a.value === actividad_id);
  const isTrato = selectedActividad?.tipo_labor === 'trato';

  const handleCentroCostoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setCentroCostoId(e.target.value);
    setCuartelesSelected([]);
  };

  const handleActividadChange = (value: string) => {
    setActividadId(value);
    const act = actividades.find((a) => a.value === value);

    if (act?.tipo_labor === 'trato') {
      setValorTratoUnitario(act.valor ? String(act.valor) : '');
    }
  };

  const handleSubmit = () => {
    const payload = {
      actividad_id,
      centro_costo_id,
      fecha_programada: fecha_programada ? format(fecha_programada, 'yyyy-MM-dd') : '',
      fecha_fin_estimada: fecha_fin_estimada ? format(fecha_fin_estimada, 'yyyy-MM-dd') : null,
      avance,
      observaciones,
      valor_trato_unitario: isTrato && valor_trato_unitario ? parseFloat(valor_trato_unitario) : null,
      requiere_empleados,
      es_ciclica,
      frecuencia: es_ciclica ? frecuencia : '',
      fecha_fin_ciclo: es_ciclica && fecha_fin_ciclo ? format(fecha_fin_ciclo, 'yyyy-MM-dd') : null,
      cuarteles: cuarteles_selected,
    };

    if (isEdit) {
      router.put(`/labores/planificador/${editingItem.id}`, payload, {
        preserveScroll: true,
        onSuccess: onClose,
      });
    } else {
      router.post('/labores/planificador', payload, {
        preserveScroll: true,
        onSuccess: onClose,
      });
    }
  };

  const filteredCuarteles = cuarteles.filter(
    (c) => !centro_costo_id || c.centro_costo_id === centro_costo_id
  );

  return (
    <>
      <DialogTitle>{isEdit ? 'Editar Labor' : 'Nueva Labor'}</DialogTitle>
      <DialogContent>
        <Grid container spacing={2} sx={{ mt: 0.5 }}>
          <Grid size={{ xs: 12, sm: 6 }}>
            <TextField
              select
              label="Actividad"
              value={actividad_id}
              onChange={(e) => handleActividadChange(e.target.value)}
              fullWidth
              required
            >
              {actividades.map((a) => (
                <MenuItem key={a.value} value={a.value}>{a.label}</MenuItem>
              ))}
            </TextField>
          </Grid>
          <Grid size={{ xs: 12, sm: 6 }}>
            <TextField
              select
              label="Centro de Costo"
              value={centro_costo_id}
              onChange={handleCentroCostoChange}
              fullWidth
              required
            >
              {centrosCosto.map((cc) => (
                <MenuItem key={cc.value} value={cc.value}>{cc.label}</MenuItem>
              ))}
            </TextField>
          </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <DatePicker
                label="Fecha Programada"
                value={fecha_programada}
                onChange={(d) => setFechaProgramada(d)}
                slotProps={{ textField: { fullWidth: true, required: true } }}
              />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <DatePicker
                label="Fecha Fin Estimada"
                value={fecha_fin_estimada}
                onChange={(d) => setFechaFinEstimada(d)}
                slotProps={{ textField: { fullWidth: true } }}
              />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField
                select
                label="Cuarteles"
              value={cuarteles_selected}
              onChange={(e) => setCuartelesSelected(e.target.value as unknown as string[])}
              fullWidth
              slotProps={{ select: { multiple: true } }}
            >
              {filteredCuarteles.map((c) => (
                <MenuItem key={c.value} value={c.value}>{c.label}</MenuItem>
              ))}
            </TextField>
          </Grid>
            {isTrato && (
              <Grid size={{ xs: 12, sm: 6 }}>
                <TextField
                  label="Valor Trato Unitario ($)"
                  type="number"
                  value={valor_trato_unitario}
                  onChange={(e) => setValorTratoUnitario(e.target.value)}
                  fullWidth
                  slotProps={{ htmlInput: { min: 0, step: 100 } }}
                />
              </Grid>
            )}
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField
                label="Avance (%)"
                type="number"
                value={avance}
                onChange={(e) => setAvance(Math.min(100, Math.max(0, Number(e.target.value))))}
                fullWidth
                slotProps={{ htmlInput: { min: 0, max: 100 } }}
                helperText={`${avance}% completado`}
              />
            </Grid>
            <Grid size={{ xs: 12 }}>
            <TextField
              label="Observaciones"
              value={observaciones}
              onChange={(e) => setObservaciones(e.target.value)}
              fullWidth
              multiline
              rows={2}
            />
          </Grid>
          <Grid size={{ xs: 6 }}>
            <FormControlLabel
              control={<Switch checked={requiere_empleados} onChange={(e) => setRequiereEmpleados(e.target.checked)} />}
              label="Requiere empleados"
            />
          </Grid>
          <Grid size={{ xs: 6 }}>
            <FormControlLabel
              control={<Switch checked={es_ciclica} onChange={(e) => setEsCiclica(e.target.checked)} />}
              label="Es cíclica (plantilla)"
            />
          </Grid>
          {es_ciclica && (
            <>
              <Grid size={{ xs: 12, sm: 6 }}>
                <TextField
                  select
                  label="Frecuencia"
                  value={frecuencia}
                  onChange={(e) => setFrecuencia(e.target.value)}
                  fullWidth
                  required
                >
                  {FRECUENCIA_OPTIONS.map((f) => (
                    <MenuItem key={f.value} value={f.value}>{f.label}</MenuItem>
                  ))}
                </TextField>
              </Grid>
              <Grid size={{ xs: 12, sm: 6 }}>
                <DatePicker
                  label="Fecha Fin Ciclo"
                  value={fecha_fin_ciclo}
                  onChange={(d) => setFechaFinCiclo(d)}
                  slotProps={{ textField: { fullWidth: true } }}
                />
              </Grid>
            </>
          )}
        </Grid>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose}>Cancelar</Button>
        <Button onClick={handleSubmit} variant="contained">{isEdit ? 'Actualizar' : 'Crear'}</Button>
      </DialogActions>
    </>
  );
}

interface Props {
  open: boolean;
  onClose: () => void;
  editingItem: Partial<Labor> | null;
  actividades: SelectOption[];
  centrosCosto: SelectOption[];
  cuarteles: SelectOption[];
}

export default function LaborModal({ open, onClose, editingItem, actividades, centrosCosto, cuarteles }: Props) {
  return (
    <LocalizationProvider dateAdapter={AdapterDateFns} adapterLocale={es}>
      <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth>
        <LaborForm
          key={editingItem?.id ?? 'new'}
          editingItem={editingItem}
          actividades={actividades}
          centrosCosto={centrosCosto}
          cuarteles={cuarteles}
          onClose={onClose}
        />
      </Dialog>
    </LocalizationProvider>
  );
}
