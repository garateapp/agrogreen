import {
    TextField,
    MenuItem,
    Switch,
    FormControlLabel,
    Grid,
    FormHelperText,
    FormControl,
} from '@mui/material';
import { LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import MantenedorRutField from './fields/MantenedorRutField';
import type { MantenedorField } from './mantenedor-types';

interface Props {
    fields: MantenedorField[];
    values: Record<string, string | number | boolean>;
    errors: Record<string, string>;
    onChange: (name: string, value: string | number | boolean) => void;
}

function renderField(field: MantenedorField, value: string | number | boolean, error: string | undefined, onChange: Props['onChange'], allValues: Record<string, string | number | boolean> = {}) {
    const commonProps = {
        fullWidth: true,
        size: 'small' as const,
        error: !!error,
        helperText: error,
        disabled: field.disabled,
    };

    switch (field.type) {
        case 'text':
        case 'email':
            return (
                <TextField
                    {...commonProps}
                    label={field.label}
                    placeholder={field.placeholder}
                    required={field.required}
                    value={String(value ?? '')}
                    onChange={(e) => onChange(field.name, e.target.value)}
                    type={field.type === 'email' ? 'email' : 'text'}
                />
            );

        case 'number':
            return (
                <TextField
                    {...commonProps}
                    label={field.label}
                    placeholder={field.placeholder}
                    required={field.required}
                    value={value === '' ? '' : Number(value)}
                    onChange={(e) => onChange(field.name, e.target.value === '' ? '' : Number(e.target.value))}
                    type="number"
                />
            );

        case 'textarea':
            return (
                <TextField
                    {...commonProps}
                    label={field.label}
                    placeholder={field.placeholder}
                    required={field.required}
                    value={String(value ?? '')}
                    onChange={(e) => onChange(field.name, e.target.value)}
                    multiline
                    minRows={3}
                    maxRows={6}
                />
            );

        case 'select': {
            let filteredOptions = field.options ?? [];

            if (field.cascadeParent && allValues[field.cascadeParent]) {
                filteredOptions = filteredOptions.filter(
                    (opt) => (opt as any).especie_id === allValues[field.cascadeParent]
                );
            }

            return (
                <TextField
                    {...commonProps}
                    label={field.label}
                    required={field.required}
                    value={String(value ?? '')}
                    onChange={(e) => onChange(field.name, e.target.value)}
                    select
                >
                    <MenuItem value="">
                        <em>Seleccionar...</em>
                    </MenuItem>
                    {filteredOptions.map((opt) => (
                        <MenuItem key={opt.value} value={opt.value}>
                            {opt.label}
                        </MenuItem>
                    ))}
                </TextField>
            );
        }

        case 'switch':
            return (
                <FormControl error={!!error} component="fieldset">
                    <FormControlLabel
                        control={
                            <Switch
                                checked={Boolean(value)}
                                onChange={(e) => onChange(field.name, e.target.checked)}
                            />
                        }
                        label={field.label}
                    />
                    {error && <FormHelperText>{error}</FormHelperText>}
                </FormControl>
            );

        case 'date':
            return (
                <LocalizationProvider dateAdapter={AdapterDateFns}>
                    <DatePicker
                        label={field.label}
                        value={value ? new Date(String(value)) : null}
                        onChange={(date) =>
                            onChange(field.name, date ? date.toISOString().split('T')[0] : '')
                        }
                        slotProps={{
                            textField: {
                                ...commonProps,
                                required: field.required,
                            },
                        }}
                    />
                </LocalizationProvider>
            );

        case 'rut':
            return (
                <MantenedorRutField
                    {...commonProps}
                    label={field.label}
                    value={String(value ?? '')}
                    onChange={(val) => onChange(field.name, val)}
                />
            );

        default:
            return (
                <TextField
                    {...commonProps}
                    label={field.label}
                    value={String(value ?? '')}
                    onChange={(e) => onChange(field.name, e.target.value)}
                />
            );
    }
}

export default function MantenedorFieldFactory({ fields, values, errors, onChange }: Props) {
    return (
        <Grid container spacing={2.5}>
            {fields.map((field) => {
                const fieldValue = values[field.name] ?? '';
                const fieldError = errors[field.name];

                if (field.showIf) {
                    const show = Object.entries(field.showIf).every(
                        ([key, expected]) => values[key] === expected
                    );

                    if (!show) {
return null;
}
                }

                if (field.type === 'switch') {
                    return (
                        <Grid key={field.name} size={{ xs: 12 }}>
                            {renderField(field, fieldValue, fieldError, onChange, values)}
                        </Grid>
                    );
                }

                return (
                    <Grid key={field.name} size={{ xs: field.xs ?? 12, sm: field.sm ?? 12 }}>
                        {renderField(field, fieldValue, fieldError, onChange, values)}
                    </Grid>
                );
            })}
        </Grid>
    );
}
