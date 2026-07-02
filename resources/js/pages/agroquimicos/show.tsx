import { Head, router } from '@inertiajs/react';
import { ArrowBack, CheckCircle, Cancel } from '@mui/icons-material';
import {
  Box, Typography, Button, Chip, Tabs, Tab, Paper, Stack, Table,
  TableBody, TableCell, TableContainer, TableHead, TableRow, Alert, Dialog,
  DialogTitle, DialogContent, DialogActions, TextField,
} from '@mui/material';
import { useState } from 'react';
import { ESTADOS } from '@/components/agroquimicos/types';
import type { ApplicationRecord } from '@/components/agroquimicos/types';

interface Props {
  record: ApplicationRecord;
}

export default function AgroquimicosShow({ record }: Props) {
  const [tab, setTab] = useState(0);
  const [cancelDialog, setCancelDialog] = useState(false);
  const [motivo, setMotivo] = useState('');

  const estadoCfg = ESTADOS[record.estado] ?? { label: record.estado, color: 'default' as const };

  const handleApprove = () => {
    if (confirm('¿Aprobar esta aplicación? Se descontará el stock de los lotes seleccionados.')) {
      router.patch(`/agroquimicos/${record.id}/approve`, undefined, { preserveScroll: true });
    }
  };

  const handleCancel = () => {
    router.post(`/agroquimicos/${record.id}/cancel`, { motivo_anulacion: motivo }, { preserveScroll: true, onSuccess: () => setCancelDialog(false) });
  };

  return (
    <>
      <Head title={`Aplicación ${record.codigo}`} />
      <Box>
        <Stack direction="row" alignItems="center" spacing={2} mb={2}>
          <Button startIcon={<ArrowBack />} onClick={() => router.visit('/agroquimicos')}>Volver</Button>
          <Typography variant="h5">Aplicación {record.codigo}</Typography>
          <Chip label={estadoCfg.label} color={estadoCfg.color} />
        </Stack>

        <Stack direction="row" spacing={1} mb={3}>
          {(record.estado === 'ejecutada' || record.estado === 'en_revision') && (
            <Button variant="contained" color="success" startIcon={<CheckCircle />} onClick={handleApprove}>
              Aprobar y Descontar Stock
            </Button>
          )}
          {record.estado !== 'anulada' && (
            <Button variant="outlined" color="error" startIcon={<Cancel />} onClick={() => setCancelDialog(true)}>
              Anular
            </Button>
          )}
        </Stack>

        <Tabs value={tab} onChange={(_, v) => setTab(v)} sx={{ mb: 2 }}>
          <Tab label="Datos Generales" />
          <Tab label="Productos" />
          <Tab label="Clima y Seguridad" />
          <Tab label="Envases" />
          <Tab label="Auditoría" />
        </Tabs>

        {tab === 0 && (
          <Stack spacing={2}>
            <Paper sx={{ p: 2 }}>
              <Typography variant="subtitle2" mb={1}>Ubicación</Typography>
              <Typography variant="body2">Cuartel: {record.cuartel?.nombre ?? '-'}</Typography>
              <Typography variant="body2">Variedad: {record.variedad?.nombre ?? '-'}</Typography>
              <Typography variant="body2">Temporada: {record.temporada ?? '-'}</Typography>
              <Typography variant="body2">Superficie: {record.superficie} ha</Typography>
            </Paper>
            <Paper sx={{ p: 2 }}>
              <Typography variant="subtitle2" mb={1}>Aplicación</Typography>
              <Typography variant="body2">Fecha: {record.fecha_aplicacion}</Typography>
              <Typography variant="body2">Hora: {record.hora_inicio ?? '-'} {record.hora_termino ? `— ${record.hora_termino}` : ''}</Typography>
              <Typography variant="body2">Objetivo: {record.objetivo_tipo} {record.objetivo_nombre ? `— ${record.objetivo_nombre}` : ''}</Typography>
            </Paper>
            <Paper sx={{ p: 2 }}>
              <Typography variant="subtitle2" mb={1}>Personas y Equipo</Typography>
              <Typography variant="body2">Responsable: {record.responsable?.name ?? '-'}</Typography>
              <Typography variant="body2">Aplicador: {record.aplicadorRel ? `${record.aplicadorRel.nombres} ${record.aplicadorRel.apellidos}` : '-'}</Typography>
              <Typography variant="body2">Equipo: {record.equipo?.nombre ?? '-'}</Typography>
            </Paper>
            {record.observaciones && (
              <Paper sx={{ p: 2 }}>
                <Typography variant="subtitle2">Observaciones</Typography>
                <Typography variant="body2">{record.observaciones}</Typography>
              </Paper>
            )}
          </Stack>
        )}

        {tab === 1 && (
          <TableContainer component={Paper}>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>Producto</TableCell>
                  <TableCell>Lote</TableCell>
                  <TableCell>Dosis</TableCell>
                  <TableCell>Cantidad Total</TableCell>
                  <TableCell>Vol. Agua</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {record.productos?.map((p) => (
                  <TableRow key={p.id}>
                    <TableCell>{p.productoSAG?.nombre_comercial ?? '-'}</TableCell>
                    <TableCell>{p.lote?.codigo_lote ?? 'Sin lote'}</TableCell>
                    <TableCell>{p.dosis} {p.unidad_dosis}</TableCell>
                    <TableCell>{p.cantidad_total}</TableCell>
                    <TableCell>{p.volumen_agua ?? '-'}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        )}

        {tab === 2 && (
          <Stack spacing={2}>
            <Paper sx={{ p: 2 }}>
              <Typography variant="subtitle2" mb={1}>Condiciones Climáticas</Typography>
              {record.clima ? (
                <>
                  <Typography variant="body2">Temperatura: {record.clima.temperatura ?? '-'} °C</Typography>
                  <Typography variant="body2">Humedad: {record.clima.humedad ?? '-'} %</Typography>
                  <Typography variant="body2">Viento: {record.clima.viento_velocidad ?? '-'} km/h ({record.clima.viento_direccion ?? '-'})</Typography>
                  <Typography variant="body2">Estado: {record.clima.estado_general ?? '-'}</Typography>
                  <Typography variant="body2">Riesgo Deriva: {record.clima.riesgo_deriva ?? '-'}</Typography>
                  <Typography variant="body2">Fuente: {record.clima.fuente}</Typography>
                  {Number(record.clima.viento_velocidad) > 20 && <Alert severity="warning">Viento alto durante la aplicación</Alert>}
                </>
              ) : (
                <Typography variant="body2" color="text.secondary">Sin datos climáticos</Typography>
              )}
            </Paper>
            <Paper sx={{ p: 2 }}>
              <Typography variant="subtitle2" mb={1}>Seguridad y EPP</Typography>
              {record.seguridad ? (
                <>
                  <Typography variant="body2">Guantes: {record.seguridad.epp_guantes ? '✓' : '✗'}</Typography>
                  <Typography variant="body2">Mascarilla: {record.seguridad.epp_mascarilla ? '✓' : '✗'}</Typography>
                  <Typography variant="body2">Overol: {record.seguridad.epp_overol ? '✓' : '✗'}</Typography>
                  <Typography variant="body2">Botas: {record.seguridad.epp_botas ? '✓' : '✗'}</Typography>
                  <Typography variant="body2">Protección Ocular: {record.seguridad.epp_proteccion_ocular ? '✓' : '✗'}</Typography>
                  <Typography variant="body2">Señalización: {record.seguridad.senalizacion_instalada ? '✓' : '✗'}</Typography>
                  {record.seguridad.observaciones && <Typography variant="body2">Obs: {record.seguridad.observaciones}</Typography>}
                </>
              ) : (
                <Typography variant="body2" color="text.secondary">Sin datos de seguridad</Typography>
              )}
            </Paper>
          </Stack>
        )}

        {tab === 3 && (
          <TableContainer component={Paper}>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>Producto</TableCell>
                  <TableCell>Envases</TableCell>
                  <TableCell>Capacidad</TableCell>
                  <TableCell>Triple Lavado</TableCell>
                  <TableCell>Almacenamiento</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {record.envases?.map((e) => (
                  <TableRow key={e.id}>
                    <TableCell>{e.producto_sag_id}</TableCell>
                    <TableCell>{e.envases_usados}</TableCell>
                    <TableCell>{e.capacidad_envase ?? '-'}</TableCell>
                    <TableCell>{e.triple_lavado ? '✓' : '✗'}</TableCell>
                    <TableCell>{e.almacenamiento_temporal ?? '-'}</TableCell>
                  </TableRow>
                ))}
                {(!record.envases || record.envases.length === 0) && (
                  <TableRow><TableCell colSpan={5} align="center">Sin registros de envases</TableCell></TableRow>
                )}
              </TableBody>
            </Table>
          </TableContainer>
        )}

        {tab === 4 && (
          <Paper sx={{ p: 2 }}>
            <Typography variant="subtitle2" mb={1}>Auditoría</Typography>
            <Typography variant="body2">Creado por: {record.creadoPor?.name ?? '-'}</Typography>
            <Typography variant="body2">Creado en: {record.created_at}</Typography>
            {record.aprobadoPor && (
              <>
                <Typography variant="body2">Aprobado por: {record.aprobadoPor.name}</Typography>
                <Typography variant="body2">Aprobado en: {record.aprobado_en}</Typography>
              </>
            )}
            {record.anuladoPor && (
              <>
                <Typography variant="body2">Anulado por: {record.anuladoPor.name}</Typography>
                <Typography variant="body2">Motivo: {record.motivo_anulacion}</Typography>
              </>
            )}
            <Typography variant="body2">Última actualización: {record.updated_at}</Typography>
          </Paper>
        )}

        <Dialog open={cancelDialog} onClose={() => setCancelDialog(false)}>
          <DialogTitle>Anular Aplicación</DialogTitle>
          <DialogContent>
            <TextField
              autoFocus
              margin="dense"
              label="Motivo de anulación"
              fullWidth
              multiline
              rows={3}
              value={motivo}
              onChange={(e) => setMotivo(e.target.value)}
            />
          </DialogContent>
          <DialogActions>
            <Button onClick={() => setCancelDialog(false)}>Cancelar</Button>
            <Button onClick={handleCancel} color="error" disabled={!motivo.trim()}>Anular</Button>
          </DialogActions>
        </Dialog>
      </Box>
    </>
  );
}

AgroquimicosShow.layout = {
  breadcrumbs: [
    { title: 'Agroquímicos', href: '/agroquimicos' },
    { title: 'Detalle Aplicación', href: '' },
  ],
};
