import { Box, Typography, Paper, Tooltip } from '@mui/material';
import {
  parse, startOfWeek, endOfWeek, eachWeekOfInterval, isWithinInterval, format,
} from 'date-fns';
import { es } from 'date-fns/locale';
import { useMemo } from 'react';
import ActivityIcon from './ActivityIcon';
import type { Labor } from './types';

interface Props {
  labores: Labor[];
  onView: (labor: Labor) => void;
}

export default function VistaGantt({ labores, onView }: Props) {
  const weeks = useMemo(() => {
    const dates = labores
      .map((l) => {
        try {
 return parse(l.fecha_programada, 'yyyy-MM-dd', new Date()); 
} catch {
 return null; 
}
      })
      .filter((d): d is Date => d !== null);

    if (dates.length === 0) {
return [];
}

    const minDate = startOfWeek(dates.reduce((a, b) => (a < b ? a : b)), { weekStartsOn: 1 });
    const maxDate = endOfWeek(dates.reduce((a, b) => (a > b ? a : b)), { weekStartsOn: 1 });

    return eachWeekOfInterval({ start: minDate, end: maxDate }, { weekStartsOn: 1 });
  }, [labores]);

  if (weeks.length === 0) {
    return <Typography color="text.secondary" sx={{ py: 4, textAlign: 'center' }}>No hay labores para mostrar</Typography>;
  }

  return (
    <Paper variant="outlined" sx={{ overflow: 'auto' }}>
      <Box sx={{
        display: 'grid',
        gridTemplateColumns: `250px repeat(${weeks.length}, minmax(120px, 1fr))`,
        minWidth: 600,
      }}>
        <Box sx={{
          p: 1, borderBottom: 1, borderColor: 'divider',
          fontWeight: 600, bgcolor: 'grey.50',
          position: 'sticky', left: 0, zIndex: 1,color:'green',
        }}>
          Labor
        </Box>
        {weeks.map((week) => (
          <Box key={week.toISOString()} sx={{
            p: 1, borderBottom: 1, borderColor: 'divider',
            textAlign: 'center', bgcolor: 'grey.50',
          }}>
            <Typography variant="caption" sx={{ fontWeight: 600,color:'green' }}>
              {format(week, 'd MMM', { locale: es })}
            </Typography>
          </Box>
        ))}
        {labores.map((labor) => {
          const laborDate = parse(labor.fecha_programada, 'yyyy-MM-dd', new Date());

          return (
            <>
              <Box
                key={`${labor.id}-name`}
                sx={{
                  p: 1, borderBottom: 1, borderColor: 'divider',
                  cursor: 'pointer', '&:hover': { bgcolor: 'action.hover' },
                  position: 'sticky', left: 0, bgcolor: 'background.paper', zIndex: 1,
                }}
                onClick={() => onView(labor)}
              >
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
                  <Box sx={{ color: labor.color, display: 'flex', alignItems: 'center' }}>
                    <ActivityIcon name={labor.icono} />
                  </Box>
                  <Typography variant="body2" noWrap sx={{ flex: 1 }}>{labor.actividad}</Typography>
                  <Typography variant="caption" sx={{ fontWeight: 600, color: labor.avance === 100 ? 'success.main' : 'text.secondary' }}>
                    {labor.avance}%
                  </Typography>
                </Box>
                <Typography variant="caption" color="text.secondary">{labor.centro_costo}</Typography>
              </Box>
              {weeks.map((week) => {
                const weekStart = startOfWeek(week, { weekStartsOn: 1 });
                const weekEnd = endOfWeek(week, { weekStartsOn: 1 });
                const isInWeek = isWithinInterval(laborDate, { start: weekStart, end: weekEnd });

                return (
                  <Box
                    key={`${labor.id}-${week.toISOString()}`}
                    sx={{
                      p: 0.5, borderBottom: 1, borderColor: 'divider',
                      display: 'flex', alignItems: 'center', justifyContent: 'center',
                    }}
                  >
                    {isInWeek && (
                      <Tooltip title={`${labor.actividad} — ${labor.estado} (${labor.avance}%)`}>
                        <Box
                          sx={{
                            width: '100%', height: 24, borderRadius: 1,
                            bgcolor: 'grey.200',
                            cursor: 'pointer', position: 'relative', overflow: 'hidden',
                          }}
                          onClick={() => onView(labor)}
                        >
                          <Box
                            sx={{
                              position: 'absolute', inset: 0,
                              bgcolor: labor.color, opacity: 0.7,
                              width: `${labor.avance}%`,
                              transition: 'width 0.3s',
                            }}
                          />
                        </Box>
                      </Tooltip>
                    )}
                  </Box>
                );
              })}
            </>
          );
        })}
      </Box>
    </Paper>
  );
}
