import { router } from '@inertiajs/react';
import { Add, Save, Delete } from '@mui/icons-material';
import {
  Box, Typography, Paper, IconButton, Button, TextField, MenuItem,
  Chip, Grid,
} from '@mui/material';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { format, parse } from 'date-fns';
import { es } from 'date-fns/locale';
import { useState } from 'react';
import EstadoBadge from '@/components/labores/EstadoBadge';
import LaborModal from '@/components/labores/LaborModal';
import type { Labor, SelectOption } from '@/components/labores/types';

interface Props {
  pageTitle: string;
  labores: Labor[];
  selectedDate: string;
  actividades: SelectOption[];
  centrosCosto: SelectOption[];
  cuarteles: SelectOption[];
  empleados: SelectOption[];
}

interface EmpleadoEntry {
  empleado_id: string;
  horas_trabajadas: string;
  cantidad_unidades_producidas: string;
  valor_trato_unitario: string;
  liquido_a_pagar: string;
  _existingId?: string;
}

export default function TarjaDiaria({
  pageTitle, labores: initialLabores, selectedDate,
  actividades, centrosCosto, cuarteles, empleados,
}: Props) {
  const [date, setDate] = useState<Date | null>(parse(selectedDate, 'yyyy-MM-dd', new Date()));
  const [laborModalOpen, setLaborModalOpen] = useState(false);
  const [tarjaData, setTarjaData] = useState<Record<string, EmpleadoEntry[]>>(() => {
    const init: Record<string, EmpleadoEntry[]> = {};
    initialLabores.forEach((l) => {
      init[l.id] = l.empleados.map((e) => ({
        empleado_id: e.empleado_id,
        horas_trabajadas: String(e.horas_trabajadas),
        cantidad_unidades_producidas: String(e.cantidad_unidades_producidas),
        valor_trato_unitario: String(e.valor_trato_unitario),
        liquido_a_pagar: String(e.liquido_a_pagar),
        _existingId: e.id,
      }));
    });

    return init;
  });

  const labores = initialLabores;

  const handleDateChange = (d: Date | null) => {
    if (d) {
      const dateStr = format(d, 'yyyy-MM-dd');
      setDate(d);
      router.get('/labores/tarja-diaria', { date: dateStr }, { preserveState: false, replace: true });
    }
  };

  const handleAddEmpleado = (laborId: string) => {
    const labor = labores.find((l) => l.id === laborId);
    const defaultValorTrato = labor?.valor_trato_unitario ? String(labor.valor_trato_unitario) : '0';
    setTarjaData((prev) => ({
      ...prev,
      [laborId]: [...(prev[laborId] ?? []), {
        empleado_id: '',
        horas_trabajadas: '0',
        cantidad_unidades_producidas: '0',
        valor_trato_unitario: defaultValorTrato,
        liquido_a_pagar: '0',
      }],
    }));
  };

  const handleRemoveEmpleado = (laborId: string, idx: number) => {
    setTarjaData((prev) => ({
      ...prev,
      [laborId]: prev[laborId].filter((_, i) => i !== idx),
    }));
  };

  const handleEmpleadoChange = (laborId: string, idx: number, field: keyof EmpleadoEntry, value: string) => {
    setTarjaData((prev) => {
      const updated = [...(prev[laborId] ?? [])];
      updated[idx] = { ...updated[idx], [field]: value };

      return { ...prev, [laborId]: updated };
    });
  };

  const handleSaveTarja = (laborId: string) => {
    const labor = labores.find((l) => l.id === laborId);
    const entries = tarjaData[laborId] ?? [];
    const payload = {
      empleados: entries.map((e) => ({
        empleado_id: e.empleado_id,
        horas_trabajadas: parseFloat(e.horas_trabajadas) || 0,
        cantidad_unidades_producidas: labor?.tipo_labor === 'trato' ? parseFloat(e.cantidad_unidades_producidas) || 0 : 0,
        valor_trato_unitario: labor?.tipo_labor === 'trato' ? parseFloat(e.valor_trato_unitario) || 0 : 0,
        liquido_a_pagar: parseFloat(e.liquido_a_pagar) || 0,
      })),
    };
    router.post(`/labores/tarja-diaria/${laborId}/empleados`, payload, {
      preserveScroll: true,
      onError: (errors) => {
        console.error('Error guardando tarja:', errors);
      },
    });
  };

  const handleNuevaLaborCreada = () => {
    if (date) {
      router.get('/labores/tarja-diaria', { date: format(date, 'yyyy-MM-dd') }, { preserveState: false, replace: true });
    }
  };

  const availableEmpleados = empleados;

  const fechaStr = date ? format(date, 'yyyy-MM-dd') : '';

  return (
    <LocalizationProvider dateAdapter={AdapterDateFns} adapterLocale={es}>
      <Box sx={{ p: 2 }}>
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
          <Typography variant="h5" sx={{ fontWeight: 700 }}>{pageTitle}</Typography>
        </Box>

        <Paper variant="outlined" sx={{ p: 2, mb: 2 }}>
          <Grid container spacing={2} sx={{ alignItems: 'center' }}>
            <Grid size={{ xs: 12, sm: 4 }}>
              <DatePicker
                label="Fecha"
                value={date}
                onChange={handleDateChange}
                slotProps={{ textField: { fullWidth: true, size: 'small' } }}
              />
            </Grid>
            <Grid size={{ xs: 12, sm: 8 }}>
              <Box sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
                <Typography variant="body2" color="text.secondary">
                  {labores.length} labor(es) para {fechaStr}
                </Typography>
                <Button
                  variant="outlined"
                  size="small"
                  startIcon={<Add />}
                  onClick={() => setLaborModalOpen(true)}
                >
                  Nueva Labor
                </Button>
              </Box>
            </Grid>
          </Grid>
        </Paper>

        {labores.length === 0 && (
          <Paper variant="outlined" sx={{ p: 4, textAlign: 'center' }}>
            <Typography color="text.secondary" sx={{ mb: 2 }}>
              No hay labores programadas para esta fecha
            </Typography>
            <Button variant="contained" startIcon={<Add />} onClick={() => setLaborModalOpen(true)}>
              Programar Nueva Labor
            </Button>
          </Paper>
        )}

        {labores.map((labor) => {
          const entries = tarjaData[labor.id] ?? [];
          const isTrato = labor.tipo_labor === 'trato';

          return (
            <Paper key={labor.id} variant="outlined" sx={{ p: 2, mb: 2 }}>
              <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', mb: 1 }}>
                <Box>
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 0.5 }}>
                    <EstadoBadge estado={labor.estado} />
                    <Typography variant="subtitle1" sx={{ fontWeight: 600 }}>
                      {labor.actividad}
                    </Typography>
                    {isTrato && (
                      <Chip label="Trato" size="small" color="warning" variant="outlined" />
                    )}
                  </Box>
                  <Typography variant="body2" color="text.secondary">
                    {labor.centro_costo}
                    {labor.cuarteles.length > 0 && (
                      <> &middot; {labor.cuarteles.map((c) => c.nombre).join(', ')}</>
                    )}
                  </Typography>
                  {labor.observaciones && (
                    <Typography variant="caption" color="text.secondary">
                      {labor.observaciones}
                    </Typography>
                  )}
                </Box>
              </Box>

              {labor.requiere_empleados && (
                <>
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 1 }}>
                    <Typography variant="subtitle2">Empleados</Typography>
                    <Button size="small" startIcon={<Add />} onClick={() => handleAddEmpleado(labor.id)}>
                      Agregar
                    </Button>
                  </Box>
                  {entries.map((entry, idx) => (
                    <Box key={idx} sx={{ display: 'flex', gap: 1, alignItems: 'center', mb: 1, flexWrap: 'wrap' }}>
                      <TextField
                        select
                        size="small"
                        label="Empleado"
                        value={entry.empleado_id}
                        onChange={(e) => handleEmpleadoChange(labor.id, idx, 'empleado_id', e.target.value)}
                        sx={{ minWidth: 180 }}
                      >
                        {availableEmpleados.map((emp) => (
                          <MenuItem key={emp.value} value={emp.value}>{emp.label}</MenuItem>
                        ))}
                      </TextField>
                      {!isTrato && (
                        <TextField
                          size="small"
                          label="Horas"
                          type="number"
                          value={entry.horas_trabajadas}
                          onChange={(e) => handleEmpleadoChange(labor.id, idx, 'horas_trabajadas', e.target.value)}
                          slotProps={{ htmlInput: { min: 0, step: 0.5 } }}
                          sx={{ width: 90 }}
                        />
                      )}
                      {isTrato && (
                        <>
                          <TextField
                            size="small"
                            label="Unidades Producidas"
                            type="number"
                            value={entry.cantidad_unidades_producidas}
                            onChange={(e) => handleEmpleadoChange(labor.id, idx, 'cantidad_unidades_producidas', e.target.value)}
                            slotProps={{ htmlInput: { min: 0, step: 1 } }}
                            sx={{ width: 120 }}
                          />
                          <TextField
                            size="small"
                            label="Valor Unit. ($)"
                            type="number"
                            value={entry.valor_trato_unitario}
                            onChange={(e) => handleEmpleadoChange(labor.id, idx, 'valor_trato_unitario', e.target.value)}
                            slotProps={{ htmlInput: { min: 0, step: 100 } }}
                            sx={{ width: 120 }}
                          />
                        </>
                      )}
                      <TextField
                        size="small"
                        label="Líquido $"
                        type="number"
                        value={entry.liquido_a_pagar}
                        onChange={(e) => handleEmpleadoChange(labor.id, idx, 'liquido_a_pagar', e.target.value)}
                        slotProps={{ htmlInput: { min: 0, step: 100 } }}
                        sx={{ width: 120 }}
                      />
                      <IconButton size="small" color="error" onClick={() => handleRemoveEmpleado(labor.id, idx)}>
                        <Delete fontSize="small" />
                      </IconButton>
                    </Box>
                  ))}
                  {entries.length === 0 && (
                    <Typography variant="caption" color="text.secondary" sx={{ display: 'block', mb: 1 }}>
                      Sin empleados registrados
                    </Typography>
                  )}
                  <Button
                    variant="contained"
                    size="small"
                    startIcon={<Save />}
                    onClick={() => handleSaveTarja(labor.id)}
                  >
                    Guardar Tarja
                  </Button>
                </>
              )}

              {!labor.requiere_empleados && (
                <Typography variant="caption" color="text.secondary">
                  Esta labor no requiere registro de empleados
                </Typography>
              )}
            </Paper>
          );
        })}

        <LaborModal
          open={laborModalOpen}
          onClose={() => {
 setLaborModalOpen(false); handleNuevaLaborCreada(); 
}}
          editingItem={{ fecha_programada: selectedDate }}
          actividades={actividades}
          centrosCosto={centrosCosto}
          cuarteles={cuarteles}
        />
      </Box>
    </LocalizationProvider>
  );
}
