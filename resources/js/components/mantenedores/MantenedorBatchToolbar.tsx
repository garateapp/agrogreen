import CloseIcon from '@mui/icons-material/Close';
import DeleteIcon from '@mui/icons-material/Delete';
import Button from '@mui/material/Button';
import Paper from '@mui/material/Paper';
import Typography from '@mui/material/Typography';

interface BatchToolbarProps {
  selectedCount: number;
  totalFilteredCount: number;
  onSelectAll: () => void;
  onDeleteSelected: () => void;
  onClearSelection: () => void;
}

export default function MantenedorBatchToolbar({
  selectedCount,
  totalFilteredCount,
  onSelectAll,
  onDeleteSelected,
  onClearSelection,
}: BatchToolbarProps) {
  return (
    <Paper
      elevation={3}
      sx={{
        position: 'fixed',
        bottom: 16,
        left: '50%',
        transform: 'translateX(-50%)',
        zIndex: 1200,
        display: 'flex',
        alignItems: 'center',
        gap: 2,
        px: 3,
        py: 1.5,
        borderRadius: 2,
      }}
    >
      <Typography variant="body2" color="text.secondary">
        <strong>{selectedCount}</strong> seleccionado(s)
      </Typography>
      {selectedCount < totalFilteredCount && (
        <Button size="small" onClick={onSelectAll}>
          Seleccionar todos ({totalFilteredCount})
        </Button>
      )}
      <Button
        size="small"
        color="error"
        startIcon={<DeleteIcon />}
        onClick={onDeleteSelected}
      >
        Eliminar seleccionados
      </Button>
      <Button
        size="small"
        startIcon={<CloseIcon />}
        onClick={onClearSelection}
      >
        Cancelar
      </Button>
    </Paper>
  );
}
