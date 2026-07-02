import { ChevronLeft, ChevronRight } from '@mui/icons-material';
import { Box, Typography, Paper, IconButton } from '@mui/material';
import {
  format, addMonths, subMonths, startOfMonth, endOfMonth,
  startOfWeek, endOfWeek, eachDayOfInterval, isSameDay,
} from 'date-fns';
import { es } from 'date-fns/locale';
import { useState, useMemo } from 'react';
import ActivityIcon from './ActivityIcon';
import type { Labor } from './types';

interface Props {
  labores: Labor[];
  onView: (labor: Labor) => void;
}

export default function VistaCalendario({ labores, onView }: Props) {
  const [currentMonth, setCurrentMonth] = useState(new Date());

  const days = useMemo(() => {
    const monthStart = startOfMonth(currentMonth);
    const monthEnd = endOfMonth(currentMonth);
    const calStart = startOfWeek(monthStart, { weekStartsOn: 1 });
    const calEnd = endOfWeek(monthEnd, { weekStartsOn: 1 });

    return eachDayOfInterval({ start: calStart, end: calEnd });
  }, [currentMonth]);

  const laboresByDay = useMemo(() => {
    const map: Record<string, Labor[]> = {};
    labores.forEach((l) => {
      if (l.fecha_programada) {
        if (!map[l.fecha_programada]) {
map[l.fecha_programada] = [];
}

        map[l.fecha_programada].push(l);
      }
    });

    return map;
  }, [labores]);

  const today = new Date();

  return (
    <Paper variant="outlined">
      <Box sx={{
        display: 'flex', alignItems: 'center', justifyContent: 'space-between',
        p: 1.5, borderBottom: 1, borderColor: 'divider',
      }}>
        <IconButton onClick={() => setCurrentMonth((m) => subMonths(m, 1))}>
          <ChevronLeft />
        </IconButton>
        <Typography variant="h6">
          {format(currentMonth, 'MMMM yyyy', { locale: es })}
        </Typography>
        <IconButton onClick={() => setCurrentMonth((m) => addMonths(m, 1))}>
          <ChevronRight />
        </IconButton>
      </Box>
      <Box sx={{ display: 'grid', gridTemplateColumns: 'repeat(7, 1fr)' }}>
        {['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'].map((d) => (
          <Box key={d} sx={{
            p: 1, textAlign: 'center', borderBottom: 1, borderColor: 'divider', bgcolor: 'grey.50',
          }}>
            <Typography variant="caption" sx={{ fontWeight: 600,color:'green' }}>{d}</Typography>
          </Box>
        ))}
        {days.map((day) => {
          const dateKey = format(day, 'yyyy-MM-dd');
          const dayLabores = laboresByDay[dateKey] ?? [];
          const isCurrentMonth = day.getMonth() === currentMonth.getMonth();
          const isToday = isSameDay(day, today);

          return (
            <Box
              key={dateKey}
              sx={{
                p: 0.5, minHeight: 80, borderBottom: 1, borderColor: 'divider',
                bgcolor: isToday ? 'action.selected' : 'transparent',
                opacity: isCurrentMonth ? 1 : 0.3,
              }}
            >
              <Typography
                variant="caption"
                color={isToday ? 'primary' : 'text.secondary'}
                sx={{ fontWeight: isToday ? 700 : 400 }}
              >
                {format(day, 'd')}
              </Typography>
              {dayLabores.slice(0, 3).map((labor) => (
                <Box
                  key={labor.id}
                  sx={{
                    display: 'flex', alignItems: 'center', gap: 0.5,
                    cursor: 'pointer', py: 0.25,
                  }}
                  onClick={() => onView(labor)}
                >
                  <Box sx={{ color: labor.color, display: 'flex', alignItems: 'center' }}>
                    <ActivityIcon name={labor.icono} fontSize="inherit" />
                  </Box>
                  <Box sx={{ minWidth: 0, flex: 1 }}>
                    <Typography variant="caption" noWrap sx={{ fontSize: '0.65rem', lineHeight: 1.2 }}>
                      {labor.actividad}
                    </Typography>
                    <Typography variant="caption" noWrap sx={{ fontSize: '0.6rem', color: 'text.secondary', display: 'block', lineHeight: 1.2 }}>
                      {labor.centro_costo}
                    </Typography>
                  </Box>
                  <Typography variant="caption" sx={{ fontSize: '0.6rem', fontWeight: 600, color: labor.avance === 100 ? 'success.main' : 'text.secondary' }}>
                    {labor.avance}%
                  </Typography>
                </Box>
              ))}
              {dayLabores.length > 3 && (
                <Typography variant="caption" color="text.secondary">
                  +{dayLabores.length - 3} más
                </Typography>
              )}
            </Box>
          );
        })}
      </Box>
    </Paper>
  );
}
