import { Box, Chip } from '@mui/material';

interface Props {
  indicators: { label: string; value: string | number; format?: 'currency' | 'number' }[];
}

const fmt = (val: string | number, format?: 'currency' | 'number') => {
  const n = typeof val === 'string' ? parseFloat(val) : val;

  if (format === 'currency') {
return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 }).format(n);
}

  return n.toLocaleString('es-CL');
};

export default function MaquinariaHeaderIndicator({ indicators }: Props) {
  return (
    <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', mb: 2 }}>
      {indicators.map((ind) => (
        <Chip key={ind.label} label={`${ind.label}: ${fmt(ind.value, ind.format)}`}
          variant="outlined" color="primary" size="small"
          sx={{ fontWeight: 600, fontSize: '0.8125rem' }} />
      ))}
    </Box>
  );
}
