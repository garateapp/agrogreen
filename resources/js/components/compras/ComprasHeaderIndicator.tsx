import { Box, Typography, Chip } from '@mui/material';

interface Props {
  countLabel: string;
  countValue: number;
  totalLabel?: string;
  totalValue?: number;
}

const formatCurrency = (val: number) =>
  new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 }).format(val);

export default function ComprasHeaderIndicator({ countLabel, countValue, totalLabel, totalValue }: Props) {
  return (
    <Box sx={{ display: 'flex', gap: 2, alignItems: 'center', flexWrap: 'wrap', mb: 2 }}>
      <Chip
        label={`${countLabel}: ${countValue}`}
        variant="outlined"
        size="small"
        sx={{ fontWeight: 500, fontSize: '0.8125rem' }}
      />
      {totalLabel != null && totalValue != null && (
        <Chip
          label={`${totalLabel}: ${formatCurrency(totalValue)}`}
          color="primary"
          variant="outlined"
          size="small"
          sx={{ fontWeight: 600, fontSize: '0.8125rem' }}
        />
      )}
    </Box>
  );
}
