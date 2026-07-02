import { Search, FilterList } from '@mui/icons-material';
import { Box, TextField, Typography, ToggleButtonGroup, ToggleButton, InputAdornment } from '@mui/material';

interface Props {
    title: string;
    description?: string;
    searchValue: string;
    onSearchChange: (value: string) => void;
    filterValue: 'activos' | 'todos';
    onFilterChange: (value: 'activos' | 'todos') => void;
    searchPlaceholder?: string;
    action?: React.ReactNode;
}

export default function MantenedorBar({
    title,
    description,
    searchValue,
    onSearchChange,
    filterValue,
    onFilterChange,
    searchPlaceholder = 'Buscar por nombre, código o RUT...',
    action,
}: Props) {
    return (
        <Box>
            <Box sx={{ mb: 3 }}>
                <Typography variant="h5" sx={{ fontWeight: 600, fontFamily: '"Lora", serif' }}>
                    {title}
                </Typography>
                {description && (
                    <Typography variant="body2" sx={{ color: 'text.secondary', mt: 0.5 }}>
                        {description}
                    </Typography>
                )}
            </Box>

            <Box sx={{ display: 'flex', gap: 2, alignItems: 'center', flexWrap: 'wrap' }}>
                <TextField
                    placeholder={searchPlaceholder}
                    size="small"
                    value={searchValue}
                    onChange={(e) => onSearchChange(e.target.value)}
                    slotProps={{
                        input: {
                            startAdornment: (
                                <InputAdornment position="start">
                                    <Search fontSize="small" color="action" />
                                </InputAdornment>
                            ),
                        },
                    }}
                    sx={{ flex: { xs: '1 1 100%', sm: '1 1 260px' }, maxWidth: 360 }}
                />

                <ToggleButtonGroup
                    value={filterValue}
                    exclusive
                    onChange={(_, val) => val && onFilterChange(val)}
                    size="small"
                >
                    <ToggleButton value="activos" sx={{ textTransform: 'none', fontSize: '0.8rem' }}>
                        <FilterList fontSize="small" sx={{ mr: 0.5 }} />
                        Solo activos
                    </ToggleButton>
                    <ToggleButton value="todos" sx={{ textTransform: 'none', fontSize: '0.8rem' }}>
                        Todos
                    </ToggleButton>
                </ToggleButtonGroup>

                {action && (
                    <Box sx={{ ml: 'auto' }}>{action}</Box>
                )}
            </Box>
        </Box>
    );
}
