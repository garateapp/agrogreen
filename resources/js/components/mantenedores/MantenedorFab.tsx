import { Add } from '@mui/icons-material';
import {
    Box,
    Fab,
    Tooltip,
} from '@mui/material';

interface Props {
    onClick: () => void;
    label?: string;
}

export default function MantenedorFab({ onClick, label = 'Crear' }: Props) {
    return (
        <Tooltip title={label} placement="left">
            <Fab
                color="primary"
                onClick={onClick}
                sx={{ position: 'fixed', bottom: 24, right: 24 }}
            >
                <Add />
            </Fab>
        </Tooltip>
    );
}
