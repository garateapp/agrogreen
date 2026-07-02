import FilterListIcon from '@mui/icons-material/FilterList';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import Chip from '@mui/material/Chip';
import Collapse from '@mui/material/Collapse';
import FormControl from '@mui/material/FormControl';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';
import Select from '@mui/material/Select';
import TextField from '@mui/material/TextField';
import { useState, useMemo } from 'react';
import type { MantenedorField } from './mantenedor-types';

interface FilterBarProps {
  fields: MantenedorField[];
  items: Record<string, unknown>[];
  filters: Record<string, unknown>;
  onFiltersChange: (filters: Record<string, unknown>) => void;
}

export default function MantenedorFilterBar({
  fields,
  items,
  filters,
  onFiltersChange,
}: FilterBarProps) {
  const [open, setOpen] = useState(false);

  const activeCount = useMemo(
    () => Object.values(filters).filter((v) => v !== '' && v !== null && v !== undefined && v !== 'all').length,
    [filters],
  );

  const filterableFields = fields.filter(
    (f) => ['select', 'text', 'number', 'date', 'boolean', 'switch', 'email', 'rut'].includes(f.type),
  );

  const handleChange = (name: string, value: unknown) => {
    onFiltersChange({ ...filters, [name]: value });
  };

  const clearAll = () => onFiltersChange({});

  const getOptions = (field: MantenedorField) => {
    if (field.options) {
return field.options;
}

    const values = [...new Set(items.map((item) => item[field.name] as string).filter(Boolean))];

    return values.map((v) => ({ value: v, label: v }));
  };

  return (
    <Box sx={{ mb: 2 }}>
      <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
        <Button
          size="small"
          startIcon={<FilterListIcon />}
          onClick={() => setOpen(!open)}
          color={activeCount > 0 ? 'primary' : 'inherit'}
        >
          Filtros
          {activeCount > 0 && (
            <Chip
              size="small"
              label={activeCount}
              color="primary"
              sx={{ ml: 0.5, height: 20, minWidth: 20 }}
            />
          )}
        </Button>
        {activeCount > 0 && (
          <Button size="small" onClick={clearAll}>
            Limpiar filtros
          </Button>
        )}
      </Box>

      <Collapse in={open}>
        <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 2, mt: 1 }}>
          {filterableFields.map((field) => {
            if (field.type === 'select' || field.type === 'boolean' || field.type === 'switch') {
              const options = field.type === 'select'
                ? getOptions(field)
                : [{ value: 'all', label: 'Todos' }, { value: '1', label: 'Sí' }, { value: '0', label: 'No' }];
              const currentValue = filters[field.name] ?? 'all';

              return (
                <FormControl key={field.name} size="small" sx={{ minWidth: 160 }}>
                  <InputLabel>{field.label}</InputLabel>
                  <Select
                    value={currentValue === '' || currentValue === null || currentValue === undefined ? 'all' : String(currentValue)}
                    label={field.label}
                    onChange={(e) => handleChange(field.name, e.target.value === 'all' ? '' : e.target.value)}
                  >
                    <MenuItem value="all">Todos</MenuItem>
                    {options.map((opt) => (
                      <MenuItem key={String(opt.value)} value={String(opt.value)}>
                        {opt.label}
                      </MenuItem>
                    ))}
                  </Select>
                </FormControl>
              );
            }

            if (field.type === 'date') {
              const range = (filters[field.name] as { min?: string; max?: string }) ?? {};

              return (
                <Box key={field.name} sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
                  <TextField
                    size="small"
                    label={`${field.label} (desde)`}
                    type="date"
                    InputLabelProps={{ shrink: true }}
                    value={range.min ?? ''}
                    onChange={(e) => handleChange(field.name, { ...range, min: e.target.value || undefined })}
                    sx={{ width: 160 }}
                  />
                  <TextField
                    size="small"
                    label={`${field.label} (hasta)`}
                    type="date"
                    InputLabelProps={{ shrink: true }}
                    value={range.max ?? ''}
                    onChange={(e) => handleChange(field.name, { ...range, max: e.target.value || undefined })}
                    sx={{ width: 160 }}
                  />
                </Box>
              );
            }

            if (field.type === 'number') {
              const range = (filters[field.name] as { min?: number; max?: number }) ?? {};

              return (
                <Box key={field.name} sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
                  <TextField
                    size="small"
                    label={`${field.label} (min)`}
                    type="number"
                    value={range.min ?? ''}
                    onChange={(e) => handleChange(field.name, { ...range, min: e.target.value ? Number(e.target.value) : undefined })}
                    sx={{ width: 120 }}
                  />
                  <TextField
                    size="small"
                    label={`${field.label} (max)`}
                    type="number"
                    value={range.max ?? ''}
                    onChange={(e) => handleChange(field.name, { ...range, max: e.target.value ? Number(e.target.value) : undefined })}
                    sx={{ width: 120 }}
                  />
                </Box>
              );
            }

            return (
              <TextField
                key={field.name}
                size="small"
                label={field.label}
                value={filters[field.name] ?? ''}
                onChange={(e) => handleChange(field.name, e.target.value)}
                sx={{ minWidth: 160 }}
              />
            );
          })}
        </Box>
      </Collapse>
    </Box>
  );
}
