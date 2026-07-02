import { AccessTime, Assignment, RemoveDone, AttachMoney } from '@mui/icons-material';
import {
  Box, Typography, TextField, FormControl, InputLabel, Select, MenuItem,
  Button, Avatar, Chip, Paper, LinearProgress,
} from '@mui/material';
import { useState } from 'react';

interface Worker {
  id: string;
  nombre: string;
  rut: string;
  inicial: string;
  horas: number;
  asignado: boolean;
}

const MOCK_WORKERS: Worker[] = [
  { id: '1', nombre: 'Juan Pérez', rut: '12.345.678-9', inicial: 'JP', horas: 0, asignado: false },
  { id: '2', nombre: 'María Soto', rut: '23.456.789-0', inicial: 'MS', horas: 8, asignado: true },
  { id: '3', nombre: 'Pedro Ramírez', rut: '34.567.890-1', inicial: 'PR', horas: 0, asignado: false },
  { id: '4', nombre: 'Carlos Muñoz', rut: '45.678.901-2', inicial: 'CM', horas: 6, asignado: true },
  { id: '5', nombre: 'Ana López Contratista', rut: '56.789.012-3', inicial: 'AL', horas: 0, asignado: false },
];

export default function TasksCreationMobile() {
  const [fecha, setFecha] = useState(new Date().toISOString().slice(0, 10));
  const [jefe, setJefe] = useState('');
  const [workers, setWorkers] = useState(MOCK_WORKERS);

  const asignados = workers.filter((w) => w.asignado).length;

  return (
    <Box>
      <Typography variant="h5" sx={{ mb: 2 }}>Tarja Diaria Móvil</Typography>

      {/* Header */}
      <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap', alignItems: 'center', mb: 2 }}>
        <TextField label="Fecha" type="date" size="small" value={fecha}
          onChange={(e) => setFecha(e.target.value)}
          slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
        <FormControl size="small" sx={{ minWidth: 180 }}>
          <InputLabel>Jefe</InputLabel>
          <Select value={jefe} label="Jefe" onChange={(e) => setJefe(e.target.value)}>
            <MenuItem value="j1">Pedro Ramírez</MenuItem>
            <MenuItem value="j2">María González</MenuItem>
          </Select>
        </FormControl>
      </Box>

      {/* Mass actions */}
      <Box sx={{ display: 'flex', gap: 1, flexWrap: 'wrap', mb: 1 }}>
        <Button variant="contained" size="small" startIcon={<Assignment />}>Asignar</Button>
        <Button variant="outlined" size="small" color="inherit" startIcon={<RemoveDone />}>Desasignar</Button>
        <Button variant="outlined" size="small" color="inherit" startIcon={<AttachMoney />}>Editar Tratos</Button>
      </Box>

      {/* Progress indicator */}
      <Paper variant="outlined" sx={{ p: 1.5, mb: 2, borderRadius: 2 }}>
        <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 0.5 }}>
          <Typography variant="body2" sx={{ fontWeight: 500 }}>Empleados con faenas asignadas</Typography>
          <Typography variant="body2" sx={{ fontWeight: 600 }}>{asignados}/{workers.length}</Typography>
        </Box>
        <LinearProgress variant="determinate" value={(asignados / workers.length) * 100} />
      </Paper>

      {/* Worker list */}
      <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
        {workers.map((w) => (
          <Paper key={w.id} variant="outlined" sx={{ p: 1.5, borderRadius: 2, display: 'flex', alignItems: 'center', gap: 1.5 }}>
            <Avatar sx={{ width: 36, height: 36, bgcolor: w.asignado ? 'primary.main' : 'grey.400', fontSize: '0.875rem', fontWeight: 600 }}>
              {w.inicial}
            </Avatar>
            <Box sx={{ flex: 1 }}>
              <Typography variant="body2" sx={{ fontWeight: 600 }}>{w.nombre}</Typography>
              <Typography variant="caption" sx={{ color: 'text.secondary' }}>{w.rut}</Typography>
              <Box sx={{ mt: 0.5 }}>
                <Chip label={`${w.horas}/16 horas asignadas`} size="small" variant="outlined"
                  color={w.horas > 0 ? 'primary' : 'default'}
                  sx={{ fontSize: '0.7rem' }} />
              </Box>
            </Box>
            <Button variant="text" size="small" sx={{ minWidth: 32, p: 0.5 }}>
              <AccessTime fontSize="small" color="action" />
            </Button>
          </Paper>
        ))}
      </Box>

      {/* Accept button */}
      <Box sx={{ mt: 3, display: 'flex', justifyContent: 'flex-end' }}>
        <Button variant="contained" size="large" sx={{ px: 6 }}>Aceptar</Button>
      </Box>
    </Box>
  );
}
