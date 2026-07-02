import { Box, Chip } from '@mui/material';
import type { HeaderIndicatorData } from './faenas-types';

const fmt = (val: string | number, format?: 'currency' | 'number' | 'decimal') => {
  const n = typeof val === 'string' ? parseFloat(val) : val;

  if (format === 'currency') {
return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 2 }).format(n);
}

  if (format === 'decimal') {
return n.toLocaleString('es-CL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

  return n.toLocaleString('es-CL');
};

export default function FaenasHeaderIndicator({ indicators }: { indicators: HeaderIndicatorData[] }) {
  return (
    <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', mb: 2 }}>
      {indicators.map((ind) => (
        <Chip
          key={ind.label}
          label={`${ind.label}: ${fmt(ind.value, ind.format)}`}
          variant="outlined"
          color="primary"
          size="small"
          sx={{ fontWeight: 600, fontSize: '0.8125rem' }}
        />
      ))}
    </Box>
  );
}
