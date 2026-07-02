import type { PageProps } from '@inertiajs/core';
import { router } from '@inertiajs/react';
import { Add, Edit, Delete } from '@mui/icons-material';
import type { SelectChangeEvent} from '@mui/material';
import {
  Box, Button, Dialog, DialogTitle, DialogContent, DialogActions,
  TextField, Table, TableBody, TableCell, TableContainer, TableHead,
  TableRow, Paper, IconButton, Chip, Typography, Grid,
  MenuItem, Select, FormControl, InputLabel
} from '@mui/material';
import { useState } from 'react';

interface CuartelOption {
  value: string;
  label: string;
  especie: string;
  hectareas: number;
  centro_costo_id: string;
}

interface Estimacion {
  id: string;
  cuartel_id: string;
  cuartel_nombre: string;
  especie: string;
  centro_costo: string;
  anho: number;
  nombre: string;
  kilos_estimados: number;
  fecha_estimacion: string;
  estado: string;
  observaciones: string | null;
}

interface Props extends PageProps {
  estimaciones: Estimacion[];
  cuarteles: CuartelOption[];
  filters: Record<string, string>;
}

export default function EstimacionesPage({ estimaciones, cuarteles, filters }: Props) {
  const [modalOpen, setModalOpen] = useState(false);
  const [editing, setEditing] = useState<Estimacion | null>(null);
  const [form, setForm] = useState({
    cuartel_id: '',
    anho: new Date().getFullYear().toString(),
    nombre: '',
    kilos_estimados: '',
    fecha_estimacion: new Date().toISOString().split('T')[0],
    estado: 'borrador',
    observaciones: '',
  });
  const [searchAnho, setSearchAnho] = useState(filters.anho ?? '');
  const [searchCuartel, setSearchCuartel] = useState(filters.cuartel_id ?? '');
  const [searchEstado, setSearchEstado] = useState(filters.estado ?? '');

  const openCreate = () => {
    setEditing(null);
    setForm({ cuartel_id: '', anho: new Date().getFullYear().toString(), nombre: '', kilos_estimados: '', fecha_estimacion: new Date().toISOString().split('T')[0], estado: 'borrador', observaciones: '' });
    setModalOpen(true);
  };

  const openEdit = (e: Estimacion) => {
    setEditing(e);
    setForm({
      cuartel_id: e.cuartel_id,
      anho: e.anho.toString(),
      nombre: e.nombre,
      kilos_estimados: e.kilos_estimados.toString(),
      fecha_estimacion: e.fecha_estimacion,
      estado: e.estado,
      observaciones: e.observaciones ?? '',
    });
    setModalOpen(true);
  };

  const handleSubmit = () => {
    const payload = {
      cuartel_id: form.cuartel_id,
      anho: parseInt(form.anho),
      nombre: form.nombre,
      kilos_estimados: parseFloat(form.kilos_estimados),
      fecha_estimacion: form.fecha_estimacion,
      estado: form.estado,
      observaciones: form.observaciones || null,
    };

    if (editing) {
      router.put(`/presupuesto/estimaciones/${editing.id}`, payload);
    } else {
      router.post('/presupuesto/estimaciones', payload);
    }

    setModalOpen(false);
  };

  const handleDelete = (id: string) => {
    if (confirm('¿Eliminar esta estimación?')) {
      router.delete(`/presupuesto/estimaciones/${id}`);
    }
  };

  const handleSearch = () => {
    const params: Record<string, string> = {};

    if (searchAnho) {
params.anho = searchAnho;
}

    if (searchCuartel) {
params.cuartel_id = searchCuartel;
}

    if (searchEstado) {
params.estado = searchEstado;
}

    router.get('/presupuesto/estimaciones', params);
  };

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h5" sx={{ mb: 3, fontWeight: 600 }}>Estimaciones de Cosecha</Typography>

      <Box sx={{ display: 'flex', gap: 2, mb: 2, alignItems: 'end', flexWrap: 'wrap' }}>
        <TextField label="Año" type="number" size="small" value={searchAnho} onChange={e => setSearchAnho(e.target.value)} sx={{ width: 100 }} />
        <FormControl size="small" sx={{ minWidth: 200 }}>
          <InputLabel>Cuartel</InputLabel>
          <Select value={searchCuartel} label="Cuartel" onChange={(e: SelectChangeEvent) => setSearchCuartel(e.target.value)}>
            <MenuItem value="">Todos</MenuItem>
            {cuarteles.map(c => <MenuItem key={c.value} value={c.value}>{c.label}</MenuItem>)}
          </Select>
        </FormControl>
        <FormControl size="small" sx={{ minWidth: 120 }}>
          <InputLabel>Estado</InputLabel>
          <Select value={searchEstado} label="Estado" onChange={(e: SelectChangeEvent) => setSearchEstado(e.target.value)}>
            <MenuItem value="">Todos</MenuItem>
            <MenuItem value="borrador">Borrador</MenuItem>
            <MenuItem value="confirmado">Confirmado</MenuItem>
          </Select>
        </FormControl>
        <Button variant="outlined" onClick={handleSearch}>Buscar</Button>
        <Button variant="contained" startIcon={<Add />} onClick={openCreate} sx={{ ml: 'auto' }}>Nueva Estimación</Button>
      </Box>

      {estimaciones.length === 0 ? (
        <Typography color="text.secondary" sx={{ py: 4, textAlign: 'center' }}>No hay estimaciones registradas</Typography>
      ) : (
        <TableContainer component={Paper}>
          <Table size="small">
            <TableHead>
              <TableRow>
                <TableCell>Cuartel</TableCell>
                <TableCell>Especie</TableCell>
                <TableCell>CC</TableCell>
                <TableCell>Año</TableCell>
                <TableCell>Nombre</TableCell>
                <TableCell align="right">Kilos Est.</TableCell>
                <TableCell>Fecha</TableCell>
                <TableCell>Estado</TableCell>
                <TableCell align="center">Acciones</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {estimaciones.map(e => (
                <TableRow key={e.id}>
                  <TableCell>{e.cuartel_nombre}</TableCell>
                  <TableCell>{e.especie}</TableCell>
                  <TableCell>{e.centro_costo}</TableCell>
                  <TableCell>{e.anho}</TableCell>
                  <TableCell>{e.nombre}</TableCell>
                  <TableCell align="right">{(e.kilos_estimados ?? 0).toLocaleString('es-CL')}</TableCell>
                  <TableCell>{e.fecha_estimacion}</TableCell>
                  <TableCell>
                    <Chip label={e.estado} color={e.estado === 'confirmado' ? 'success' : 'warning'} size="small" />
                  </TableCell>
                  <TableCell align="center">
                    <IconButton size="small" onClick={() => openEdit(e)}><Edit fontSize="small" /></IconButton>
                    <IconButton size="small" onClick={() => handleDelete(e.id)} color="error"><Delete fontSize="small" /></IconButton>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      )}

      <Dialog open={modalOpen} onClose={() => setModalOpen(false)} maxWidth="md" fullWidth>
        <DialogTitle>{editing ? 'Editar Estimación' : 'Nueva Estimación'}</DialogTitle>
        <DialogContent>
          <Grid container spacing={2} sx={{ mt: 0.5 }}>
            <Grid size={{ xs: 12, md: 6 }}>
              <FormControl fullWidth size="small">
                <InputLabel>Cuartel</InputLabel>
                <Select value={form.cuartel_id} label="Cuartel" onChange={e => setForm(f => ({ ...f, cuartel_id: e.target.value }))}>
                  {cuarteles.map(c => <MenuItem key={c.value} value={c.value}>{c.label}</MenuItem>)}
                </Select>
              </FormControl>
            </Grid>
            <Grid size={{ xs: 12, md: 6 }}>
              <TextField fullWidth label="Nombre de la Estimación" size="small" value={form.nombre} onChange={e => setForm(f => ({ ...f, nombre: e.target.value }))} placeholder="Ej: Post poda 2026" />
            </Grid>
            <Grid size={{ xs: 6, md: 3 }}>
              <TextField fullWidth label="Año" type="number" size="small" value={form.anho} onChange={e => setForm(f => ({ ...f, anho: e.target.value }))} />
            </Grid>
            <Grid size={{ xs: 6, md: 3 }}>
              <TextField fullWidth label="Kilos Estimados" type="number" size="small" value={form.kilos_estimados} onChange={e => setForm(f => ({ ...f, kilos_estimados: e.target.value }))} slotProps={{ htmlInput: { min: 0, step: 0.01 } }} />
            </Grid>
            <Grid size={{ xs: 6, md: 3 }}>
              <TextField fullWidth label="Fecha Estimación" type="date" size="small" value={form.fecha_estimacion} onChange={e => setForm(f => ({ ...f, fecha_estimacion: e.target.value }))} slotProps={{ inputLabel: { shrink: true } }} />
            </Grid>
            <Grid size={{ xs: 6, md: 3 }}>
              <FormControl fullWidth size="small">
                <InputLabel>Estado</InputLabel>
                <Select value={form.estado} label="Estado" onChange={e => setForm(f => ({ ...f, estado: e.target.value }))}>
                  <MenuItem value="borrador">Borrador</MenuItem>
                  <MenuItem value="confirmado">Confirmado</MenuItem>
                </Select>
              </FormControl>
            </Grid>
            <Grid size={{ xs: 12 }}>
              <TextField fullWidth label="Observaciones" size="small" multiline rows={2} value={form.observaciones} onChange={e => setForm(f => ({ ...f, observaciones: e.target.value }))} />
            </Grid>
          </Grid>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setModalOpen(false)}>Cancelar</Button>
          <Button variant="contained" onClick={handleSubmit}>{editing ? 'Guardar Cambios' : 'Crear'}</Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}
