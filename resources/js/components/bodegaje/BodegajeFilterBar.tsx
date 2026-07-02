import { Search, Clear, FilterList } from '@mui/icons-material';
import {
  Box, TextField, Button, FormControl, InputLabel, Select, MenuItem,
  InputAdornment,
} from '@mui/material';
import { useState } from 'react';

interface FilterSelect {
  key: string;
  label: string;
  options: { value: string; label: string }[];
}

interface Props {
  searchPlaceholder?: string;
  searchValue: string;
  onSearchChange: (v: string) => void;
  onSearch: () => void;
  onClear: () => void;
  selects?: FilterSelect[];
  selectValues?: Record<string, string>;
  onSelectChange?: (key: string, value: string) => void;
  showDateRange?: boolean;
  dateFrom?: string;
  dateTo?: string;
  onDateFromChange?: (v: string) => void;
  onDateToChange?: (v: string) => void;
  dateLabel?: string;
  extraFilters?: React.ReactNode;
  actions?: React.ReactNode;
  variant?: 'list' | 'report';
}

export default function BodegajeFilterBar({
  searchPlaceholder = 'Buscar...',
  searchValue,
  onSearchChange,
  onSearch,
  onClear,
  selects,
  selectValues = {},
  onSelectChange,
  showDateRange,
  dateFrom = '',
  dateTo = '',
  onDateFromChange,
  onDateToChange,
  dateLabel = 'Fecha',
  extraFilters,
  actions,
  variant = 'list',
}: Props) {
  const [showFilters, setShowFilters] = useState(variant === 'report');

  return (
    <Box>
      <Box sx={{ display: 'flex', gap: 1.5, alignItems: 'center', flexWrap: 'wrap' }}>
        {variant === 'list' && (
          <TextField
            placeholder={searchPlaceholder}
            size="small"
            value={searchValue}
            onChange={(e) => onSearchChange(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && onSearch()}
            slotProps={{
              input: {
                startAdornment: <InputAdornment position="start"><Search fontSize="small" color="primary" /></InputAdornment>,
              },
            }}
            sx={{ flex: '1 1 200px', maxWidth: 320 }}
          />
        )}

        {showDateRange && (
          <>
            <TextField label={dateLabel} type="date" size="small" value={dateFrom}
              onChange={(e) => onDateFromChange?.(e.target.value)}
              slotProps={{ inputLabel: { shrink: true } }}
              sx={{ maxWidth: 160 }}
            />
            <TextField label="Hasta" type="date" size="small" value={dateTo}
              onChange={(e) => onDateToChange?.(e.target.value)}
              slotProps={{ inputLabel: { shrink: true } }}
              sx={{ maxWidth: 160 }}
            />
          </>
        )}

        <Button variant="contained" size="small" onClick={onSearch}>Buscar</Button>
        <Button variant="outlined" size="small" color="inherit" onClick={onClear} startIcon={<Clear fontSize="small" />}>Limpiar</Button>

        {variant === 'list' && (
          <Button variant="text" size="small" onClick={() => setShowFilters(!showFilters)} startIcon={<FilterList fontSize="small" />}>
            Filtros
          </Button>
        )}

        {actions && <Box sx={{ ml: 'auto' }}>{actions}</Box>}
      </Box>

      {(showFilters || variant === 'report') && (selects || extraFilters) && (
        <Box sx={{ mt: 2, display: 'flex', gap: 2, flexWrap: 'wrap', alignItems: 'center' }}>
          {selects?.map((sel) => (
            <FormControl key={sel.key} size="small" sx={{ minWidth: 160 }}>
              <InputLabel>{sel.label}</InputLabel>
              <Select value={selectValues[sel.key] ?? ''} label={sel.label}
                onChange={(e) => onSelectChange?.(sel.key, e.target.value)}
              >
                <MenuItem value="">Todos</MenuItem>
                {sel.options.map((opt) => (
                  <MenuItem key={opt.value} value={opt.value}>{opt.label}</MenuItem>
                ))}
              </Select>
            </FormControl>
          ))}
          {extraFilters}
        </Box>
      )}
    </Box>
  );
}
