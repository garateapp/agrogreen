import { Dialog, DialogTitle, DialogContent, DialogActions, Button, TextField } from '@mui/material';
import { useState } from 'react';

interface Props {
  open: boolean;
  title?: string;
  onClose: () => void;
  onConfirm: (motivo: string) => void;
}

export default function MotivoDialog({ open, title = 'Confirmar acción', onClose, onConfirm }: Props) {
  const [motivo, setMotivo] = useState('');

  const handleConfirm = () => {
    onConfirm(motivo);
    setMotivo('');
  };

  const handleClose = () => {
    setMotivo('');
    onClose();
  };

  return (
    <Dialog open={open} onClose={handleClose} maxWidth="sm" fullWidth>
      <DialogTitle sx={{ fontWeight: 600 }}>{title}</DialogTitle>
      <DialogContent>
        <TextField label="Motivo (mín. 10 caracteres)" size="small" fullWidth multiline rows={3}
          value={motivo} onChange={(e) => setMotivo(e.target.value)} sx={{ mt: 1 }} />
      </DialogContent>
      <DialogActions>
        <Button onClick={handleClose} color="inherit">Cancelar</Button>
        <Button onClick={handleConfirm} variant="contained" color="error" disabled={motivo.length < 10}>
          Confirmar
        </Button>
      </DialogActions>
    </Dialog>
  );
}
