import { Chip } from '@mui/material';
import type { LaborEstado } from './types';

const ESTADO_CONFIG: Record<LaborEstado, { label: string; color: 'info' | 'warning' | 'default' | 'success' | 'error' }> = {
  programada: { label: 'Programada', color: 'info' },
  en_curso: { label: 'En Curso', color: 'warning' },
  en_pausa: { label: 'En Pausa', color: 'default' },
  completada: { label: 'Completada', color: 'success' },
  realizada: { label: 'Realizada', color: 'success' },
  cancelada: { label: 'Cancelada', color: 'error' },
};

interface Props {
  estado: LaborEstado;
}

export default function EstadoBadge({ estado }: Props) {
  const config = ESTADO_CONFIG[estado];

  return <Chip label={config.label} color={config.color} size="small" variant="outlined" />;
}
