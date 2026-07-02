import { Dialog, DialogTitle, DialogContent, DialogActions, Button, Box, Typography, Table, TableBody, TableCell, TableHead, TableRow, Chip, LinearProgress } from '@mui/material';
import EstadoBadge from './EstadoBadge';
import type { Labor } from './types';

interface Props {
  labor: Labor | null;
  open: boolean;
  onClose: () => void;
}

export default function LaborDetail({ labor, open, onClose }: Props) {
  if (!labor) {
return null;
}

  const isTrato = labor.tipo_labor === 'trato';

  return (
    <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth>
      <DialogTitle>
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
          <EstadoBadge estado={labor.estado} />
          <Typography variant="h6">{labor.actividad}</Typography>
          {isTrato && <Chip label="Trato" size="small" color="warning" variant="outlined" />}
        </Box>
      </DialogTitle>
      <DialogContent dividers>
        <Box sx={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 2, mb: 2 }}>
          <Box>
            <Typography variant="caption" color="text.secondary">Centro de Costo</Typography>
            <Typography variant="body2">{labor.centro_costo}</Typography>
          </Box>
          <Box>
            <Typography variant="caption" color="text.secondary">Supervisor</Typography>
            <Typography variant="body2">{labor.supervisor || '—'}</Typography>
          </Box>
          <Box>
            <Typography variant="caption" color="text.secondary">Fecha Programada</Typography>
            <Typography variant="body2">{labor.fecha_programada}</Typography>
          </Box>
          <Box>
            <Typography variant="caption" color="text.secondary">Fecha Fin Estimada</Typography>
            <Typography variant="body2">{labor.fecha_fin_estimada || '—'}</Typography>
          </Box>
          <Box>
            <Typography variant="caption" color="text.secondary">Fecha Ejecución</Typography>
            <Typography variant="body2">{labor.fecha_ejecucion || '—'}</Typography>
          </Box>
          <Box>
            <Typography variant="caption" color="text.secondary">Inicio Real</Typography>
            <Typography variant="body2">{labor.inicio_real || '—'}</Typography>
          </Box>
          <Box>
            <Typography variant="caption" color="text.secondary">Fin Real</Typography>
            <Typography variant="body2">{labor.fin_real || '—'}</Typography>
          </Box>
          <Box>
            <Typography variant="caption" color="text.secondary">Requiere Empleados</Typography>
            <Typography variant="body2">{labor.requiere_empleados ? 'Sí' : 'No'}</Typography>
          </Box>
          <Box>
            <Typography variant="caption" color="text.secondary">Cíclica</Typography>
            <Typography variant="body2">{labor.es_ciclica ? `Sí (${labor.frecuencia})` : 'No'}</Typography>
          </Box>
          {isTrato && (
            <Box>
              <Typography variant="caption" color="text.secondary">Valor Trato Unitario</Typography>
              <Typography variant="body2">${Number(labor.valor_trato_unitario ?? 0).toLocaleString()}</Typography>
            </Box>
          )}
        </Box>
        <Box sx={{ mb: 2 }}>
          <Typography variant="caption" color="text.secondary">Avance</Typography>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mt: 0.5 }}>
            <LinearProgress
              variant="determinate"
              value={labor.avance}
              sx={{ flex: 1, height: 8, borderRadius: 4 }}
            />
            <Typography variant="body2" sx={{ fontWeight: 600, minWidth: 40, textAlign: 'right' }}>
              {labor.avance}%
            </Typography>
          </Box>
        </Box>
        {labor.observaciones && (
          <Box sx={{ mb: 2 }}>
            <Typography variant="caption" color="text.secondary">Observaciones</Typography>
            <Typography variant="body2">{labor.observaciones}</Typography>
          </Box>
        )}
        <Box sx={{ mb: 2 }}>
          <Typography variant="caption" color="text.secondary">Cuarteles</Typography>
          <Box sx={{ display: 'flex', gap: 0.5, flexWrap: 'wrap', mt: 0.5 }}>
            {labor.cuarteles.map((c) => <Chip key={c.id} label={c.nombre} size="small" />)}
            {labor.cuarteles.length === 0 && <Typography variant="body2">—</Typography>}
          </Box>
        </Box>
        {labor.empleados.length > 0 && (
          <Box>
            <Typography variant="caption" color="text.secondary">Empleados ({labor.empleados.length})</Typography>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>Nombre</TableCell>
                  {isTrato && <TableCell align="right">Unidades</TableCell>}
                  {isTrato && <TableCell align="right">Valor Unit.</TableCell>}
                  <TableCell align="right">{isTrato ? 'Horas / Días' : 'Horas'}</TableCell>
                  <TableCell align="right">Líquido a Pagar</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {labor.empleados.map((emp) => (
                  <TableRow key={emp.id}>
                    <TableCell>{emp.nombre}</TableCell>
                    {isTrato && <TableCell align="right">{emp.cantidad_unidades_producidas}</TableCell>}
                    {isTrato && <TableCell align="right">${Number(emp.valor_trato_unitario).toLocaleString()}</TableCell>}
                    <TableCell align="right">{emp.horas_trabajadas}</TableCell>
                    <TableCell align="right">${Number(emp.liquido_a_pagar).toLocaleString()}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Box>
        )}
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose}>Cerrar</Button>
      </DialogActions>
    </Dialog>
  );
}
