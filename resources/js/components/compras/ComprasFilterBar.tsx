import { Search, Clear, FilterList } from '@mui/icons-material';
import {
    Box,
    TextField,
    Button,
    FormControl,
    InputLabel,
    Select,
    MenuItem,
    ToggleButtonGroup,
    ToggleButton,
    InputAdornment,
    Chip,
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
    dateRangeLabel?: string;
    dateFrom?: string;
    dateTo?: string;
    onDateFromChange?: (v: string) => void;
    onDateToChange?: (v: string) => void;
    estadoPagoValue?: string;
    onEstadoPagoChange?: (v: string) => void;
    estadoContableValue?: string;
    onEstadoContableChange?: (v: string) => void;
    estadoRecepcionValue?: string;
    onEstadoRecepcionChange?: (v: string) => void;
    estadoAprobacionValue?: string;
    onEstadoAprobacionChange?: (v: string) => void;
    actions?: React.ReactNode;
}

export default function ComprasFilterBar({
    searchPlaceholder = 'Buscar...',
    searchValue,
    onSearchChange,
    onSearch,
    onClear,
    selects,
    selectValues = {},
    onSelectChange,
    showDateRange,
    dateRangeLabel = 'Fecha',
    dateFrom = '',
    dateTo = '',
    onDateFromChange,
    onDateToChange,
    estadoPagoValue,
    onEstadoPagoChange,
    estadoContableValue,
    onEstadoContableChange,
    estadoRecepcionValue,
    onEstadoRecepcionChange,
    estadoAprobacionValue,
    onEstadoAprobacionChange,
    actions,
}: Props) {
    const [showFilters, setShowFilters] = useState(false);

    return (
        <Box>
            <Box sx={{ display: 'flex', gap: 1.5, alignItems: 'center', flexWrap: 'wrap' }}>
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
                    sx={{ flex: '1 1 240px', maxWidth: 360 }}
                />

                <Button variant="contained" size="small" onClick={onSearch}>
                    Buscar
                </Button>
                <Button variant="outlined" size="small" color="inherit" onClick={onClear} startIcon={<Clear fontSize="small" />}>
                    Limpiar
                </Button>

                <Button
                    variant="text"
                    size="small"
                    onClick={() => setShowFilters(!showFilters)}
                    startIcon={<FilterList fontSize="small" />}
                >
                    Filtros
                </Button>

                {actions && <Box sx={{ ml: 'auto' }}>{actions}</Box>}
            </Box>

            {showFilters && (
                <Box sx={{ mt: 2, display: 'flex', gap: 2, flexWrap: 'wrap', alignItems: 'center' }}>
                    {showDateRange && (
                        <>
                            <TextField
                                label={dateRangeLabel}
                                type="date"
                                size="small"
                                value={dateFrom}
                                onChange={(e) => onDateFromChange?.(e.target.value)}
                                slotProps={{ inputLabel: { shrink: true } }}
                                sx={{ maxWidth: 160 }}
                            />
                            <TextField
                                label="Hasta"
                                type="date"
                                size="small"
                                value={dateTo}
                                onChange={(e) => onDateToChange?.(e.target.value)}
                                slotProps={{ inputLabel: { shrink: true } }}
                                sx={{ maxWidth: 160 }}
                            />
                        </>
                    )}

                    {estadoPagoValue != null && onEstadoPagoChange && (
                        <ToggleButtonGroup
                            value={estadoPagoValue}
                            exclusive
                            onChange={(_, v) => v && onEstadoPagoChange(v)}
                            size="small"
                        >
                            <ToggleButton value="todos">Todos</ToggleButton>
                            <ToggleButton value="pagados">Pagados</ToggleButton>
                            <ToggleButton value="pendientes">Pendientes</ToggleButton>
                        </ToggleButtonGroup>
                    )}

                    {estadoContableValue != null && onEstadoContableChange && (
                        <ToggleButtonGroup
                            value={estadoContableValue}
                            exclusive
                            onChange={(_, v) => v && onEstadoContableChange(v)}
                            size="small"
                        >
                            <ToggleButton value="todos">Todos</ToggleButton>
                            <ToggleButton value="contabilizados">Contabilizados</ToggleButton>
                            <ToggleButton value="sin_contabilizar">Sin contabilizar</ToggleButton>
                        </ToggleButtonGroup>
                    )}

                    {estadoRecepcionValue != null && onEstadoRecepcionChange && (
                        <ToggleButtonGroup
                            value={estadoRecepcionValue}
                            exclusive
                            onChange={(_, v) => v && onEstadoRecepcionChange(v)}
                            size="small"
                        >
                            <ToggleButton value="todos">Todos</ToggleButton>
                            <ToggleButton value="recibido_parcial">Recibido parcial</ToggleButton>
                            <ToggleButton value="recibido_total">Recibido total</ToggleButton>
                        </ToggleButtonGroup>
                    )}

                    {estadoAprobacionValue != null && onEstadoAprobacionChange && (
                        <ToggleButtonGroup
                            value={estadoAprobacionValue}
                            exclusive
                            onChange={(_, v) => v && onEstadoAprobacionChange(v)}
                            size="small"
                        >
                            <ToggleButton value="todos">Todos</ToggleButton>
                            <ToggleButton value="pendiente">Pendiente</ToggleButton>
                            <ToggleButton value="aprobado">Aprobado</ToggleButton>
                            <ToggleButton value="rechazado">Rechazado</ToggleButton>
                        </ToggleButtonGroup>
                    )}

                    {selects?.map((sel) => (
                        <FormControl key={sel.key} size="small" sx={{ minWidth: 150 }}>
                            <InputLabel>{sel.label}</InputLabel>
                            <Select
                                value={selectValues[sel.key] ?? ''}
                                label={sel.label}
                                onChange={(e) => onSelectChange?.(sel.key, e.target.value)}
                            >
                                <MenuItem value="">Todos</MenuItem>
                                {sel.options.map((opt) => (
                                    <MenuItem key={opt.value} value={opt.value}>{opt.label}</MenuItem>
                                ))}
                            </Select>
                        </FormControl>
                    ))}
                </Box>
            )}
        </Box>
    );
}
