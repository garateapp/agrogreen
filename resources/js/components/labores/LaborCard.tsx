import { CalendarMonth, People } from '@mui/icons-material';
import { Card, CardContent, Typography, Box, LinearProgress } from '@mui/material';
import ActivityIcon from './ActivityIcon';
import EstadoBadge from './EstadoBadge';
import type { Labor } from './types';

interface Props {
  labor: Labor;
  onClick?: () => void;
  dragHandleProps?: Record<string, unknown>;
}

export default function LaborCard({ labor, onClick, dragHandleProps }: Props) {
  return (
    <Card
      sx={{ mb: 1, cursor: 'pointer', '&:hover': { boxShadow: 3 } }}
      onClick={onClick}
      {...(dragHandleProps ?? {})}
    >
      <CardContent sx={{ p: 1.5, '&:last-child': { pb: 1.5 } }}>
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 0.5 }}>
          <EstadoBadge estado={labor.estado} />
        </Box>
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
          <Box sx={{ color: labor.color, display: 'flex', alignItems: 'center' }}>
            <ActivityIcon name={labor.icono} />
          </Box>
          <Typography variant="body2" sx={{ fontWeight: 600 }}>
            {labor.actividad}
          </Typography>
        </Box>
        <Typography variant="caption" color="text.secondary">
          {labor.centro_costo}
        </Typography>
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mt: 0.5 }}>
          <LinearProgress
            variant="determinate"
            value={labor.avance}
            sx={{ flex: 1, height: 5, borderRadius: 3 }}
          />
          <Typography variant="caption" sx={{ fontWeight: 600, minWidth: 24, textAlign: 'right' }}>
            {labor.avance}%
          </Typography>
        </Box>
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mt: 0.5 }}>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
            <CalendarMonth fontSize="inherit" />
            <Typography variant="caption">{labor.fecha_programada}</Typography>
          </Box>
          {labor.cuarteles.length > 0 && (
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
              <People fontSize="inherit" />
              <Typography variant="caption">{labor.cuarteles.length} cuartel(es)</Typography>
            </Box>
          )}
        </Box>
      </CardContent>
    </Card>
  );
}
