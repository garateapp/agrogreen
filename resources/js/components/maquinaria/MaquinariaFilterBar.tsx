import { Clear } from '@mui/icons-material';
import { Box, TextField, Button, FormControl, InputLabel, Select, MenuItem, InputAdornment } from '@mui/material';
import type { FilterSelect } from './maquinaria-types';

interface Props {
  selects?: FilterSelect[];
  selectValues: Record<string, string>;
  onSelectChange: (key: string, value: string) => void;
  dateFrom: string;
  dateTo: string;
  onDateFromChange: (v: string) => void;
  onDateToChange: (v: string) => void;
  onSearch: () => void;
  onClear: () => void;
  dateLabelFrom?: string;
  dateLabelTo?: string;
  actions?: React.ReactNode;
}

export default function MaquinariaFilterBar({
  selects, selectValues, onSelectChange,
  dateFrom, dateTo, onDateFromChange, onDateToChange,
  onSearch, onClear, dateLabelFrom = 'Desde', dateLabelTo = 'Hasta',
  actions,
}: Props) {
  return (
    <Box sx={{ display: 'flex', gap: 1.5, flexWrap: 'wrap', alignItems: 'center' }}>
      <TextField label={dateLabelFrom} type="date" size="small" value={dateFrom}
        onChange={(e) => onDateFromChange(e.target.value)}
        slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
      <TextField label={dateLabelTo} type="date" size="small" value={dateTo}
        onChange={(e) => onDateToChange(e.target.value)}
        slotProps={{ inputLabel: { shrink: true } }} sx={{ maxWidth: 160 }} />
      {selects?.map((sel) => (
        <FormControl key={sel.key} size="small" sx={{ minWidth: 150 }}>
          <InputLabel>{sel.label}</InputLabel>
          <Select value={selectValues[sel.key] ?? ''} label={sel.label}
            onChange={(e) => onSelectChange(sel.key, e.target.value)}
          >
            <MenuItem value="">Todos</MenuItem>
            {sel.options.map((opt) => (
              <MenuItem key={opt.value} value={opt.value}>{opt.label}</MenuItem>
            ))}
          </Select>
        </FormControl>
      ))}
      <Button variant="contained" size="small" onClick={onSearch}>Buscar</Button>
      <Button variant="outlined" size="small" color="inherit" onClick={onClear} startIcon={<Clear fontSize="small" />}>Limpiar</Button>
      {actions && <Box sx={{ ml: 'auto' }}>{actions}</Box>}
    </Box>
  );
}
