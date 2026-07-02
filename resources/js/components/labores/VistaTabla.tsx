import { Edit, Delete, PlayArrow, CheckCircle } from '@mui/icons-material';
import {
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
  Paper, IconButton, Box, Typography, TableSortLabel, LinearProgress,
} from '@mui/material';
import { useState, useMemo } from 'react';
import ActivityIcon from './ActivityIcon';
import EstadoBadge from './EstadoBadge';
import type { Labor } from './types';

interface Props {
  labores: Labor[];
  onView: (labor: Labor) => void;
  onEdit: (labor: Labor) => void;
  onDelete: (labor: Labor) => void;
  onCambiarEstado: (labor: Labor) => void;
}

type SortKey = 'actividad' | 'centro_costo' | 'fecha_programada' | 'estado' | 'avance';
type SortDir = 'asc' | 'desc';

export default function VistaTabla({ labores, onView, onEdit, onDelete, onCambiarEstado }: Props) {
  const [sortKey, setSortKey] = useState<SortKey>('fecha_programada');
  const [sortDir, setSortDir] = useState<SortDir>('desc');

  const handleSort = (key: SortKey) => {
    if (sortKey === key) {
      setSortDir((prev) => (prev === 'asc' ? 'desc' : 'asc'));
    } else {
      setSortKey(key);
      setSortDir('asc');
    }
  };

  const sorted = useMemo(() => {
    return [...labores].sort((a, b) => {
      const getVal = (l: Labor, k: SortKey): string => {
        if (k === 'actividad') {
return l.actividad;
}

        if (k === 'centro_costo') {
return l.centro_costo;
}

        if (k === 'fecha_programada') {
return l.fecha_programada;
}

        if (k === 'avance') {
          return String(l.avance).padStart(3, '0');
        }

        return l.estado;
      };
      const aVal = getVal(a, sortKey);
      const bVal = getVal(b, sortKey);

      return sortDir === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
    });
  }, [labores, sortKey, sortDir]);

  if (labores.length === 0) {
    return (
      <Box sx={{ py: 4, textAlign: 'center' }}>
        <Typography color="text.secondary">No se encontraron labores</Typography>
      </Box>
    );
  }

  return (
    <TableContainer component={Paper} variant="outlined">
      <Table size="small">
        <TableHead>
          <TableRow>
            <TableCell>
              <TableSortLabel
                active={sortKey === 'actividad'}
                direction={sortKey === 'actividad' ? sortDir : 'asc'}
                onClick={() => handleSort('actividad')}
              >
                Actividad
              </TableSortLabel>
            </TableCell>
            <TableCell>
              <TableSortLabel
                active={sortKey === 'centro_costo'}
                direction={sortKey === 'centro_costo' ? sortDir : 'asc'}
                onClick={() => handleSort('centro_costo')}
              >
                Centro Costo
              </TableSortLabel>
            </TableCell>
            <TableCell>Cuarteles</TableCell>
            <TableCell>
              <TableSortLabel
                active={sortKey === 'fecha_programada'}
                direction={sortKey === 'fecha_programada' ? sortDir : 'asc'}
                onClick={() => handleSort('fecha_programada')}
              >
                Fecha Prog.
              </TableSortLabel>
            </TableCell>
            <TableCell>
              <TableSortLabel
                active={sortKey === 'avance'}
                direction={sortKey === 'avance' ? sortDir : 'asc'}
                onClick={() => handleSort('avance')}
              >
                Avance
              </TableSortLabel>
            </TableCell>
            <TableCell>
              <TableSortLabel
                active={sortKey === 'estado'}
                direction={sortKey === 'estado' ? sortDir : 'asc'}
                onClick={() => handleSort('estado')}
              >
                Estado
              </TableSortLabel>
            </TableCell>
            <TableCell align="right">Acciones</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {sorted.map((labor) => (
            <TableRow key={labor.id} hover sx={{ cursor: 'pointer' }} onClick={() => onView(labor)}>
              <TableCell>
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                  <Box sx={{ color: labor.color, display: 'flex', alignItems: 'center' }}>
                    <ActivityIcon name={labor.icono} />
                  </Box>
                  {labor.actividad}
                </Box>
              </TableCell>
              <TableCell>{labor.centro_costo}</TableCell>
              <TableCell>
                <Typography variant="caption">
                  {labor.cuarteles.map((c) => c.nombre).join(', ') || '—'}
                </Typography>
              </TableCell>
              <TableCell>{labor.fecha_programada}</TableCell>
              <TableCell sx={{ minWidth: 120 }}>
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                  <LinearProgress
                    variant="determinate"
                    value={labor.avance}
                    sx={{ flex: 1, height: 6, borderRadius: 3 }}
                  />
                  <Typography variant="caption" sx={{ fontWeight: 600, minWidth: 28, textAlign: 'right' }}>
                    {labor.avance}%
                  </Typography>
                </Box>
              </TableCell>
              <TableCell>
                <EstadoBadge estado={labor.estado} />
              </TableCell>
              <TableCell align="right">
                <Box sx={{ display: 'flex', gap: 0.5, justifyContent: 'flex-end' }}>
                  {(labor.estado === 'programada' || labor.estado === 'en_curso') && (
                    <IconButton
                      size="small"
                      color="success"
                      onClick={(e) => {
 e.stopPropagation(); onCambiarEstado(labor); 
}}
                      title={labor.estado === 'programada' ? 'Iniciar' : 'Completar'}
                    >
                      {labor.estado === 'programada' ? <PlayArrow fontSize="small" /> : <CheckCircle fontSize="small" />}
                    </IconButton>
                  )}
                  <IconButton size="small" onClick={(e) => {
 e.stopPropagation(); onEdit(labor); 
}}>
                    <Edit fontSize="small" />
                  </IconButton>
                  <IconButton size="small" color="error" onClick={(e) => {
 e.stopPropagation(); onDelete(labor); 
}}>
                    <Delete fontSize="small" />
                  </IconButton>
                </Box>
              </TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </TableContainer>
  );
}
