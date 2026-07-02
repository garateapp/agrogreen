import { Box, Chip } from '@mui/material';

interface Props {
  label: string;
  value: number;
  format?: 'currency' | 'number';
}

const fmt = (val: number, f?: 'currency' | 'number') => {
  if (f === 'currency') {
return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 }).format(val);
}

  return val.toLocaleString('es-CL');
};

export default function BodegajeHeaderIndicator({ label, value, format }: Props) {
  return (
    <Chip
      label={`${label}: ${fmt(value, format)}`}
      color="primary"
      variant="outlined"
      size="small"
      sx={{ fontWeight: 600, fontSize: '0.8125rem', mb: 1 }}
    />
  );
}
