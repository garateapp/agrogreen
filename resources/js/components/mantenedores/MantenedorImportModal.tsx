import { router } from '@inertiajs/react';
import CloudUploadIcon from '@mui/icons-material/CloudUpload';
import DownloadIcon from '@mui/icons-material/Download';
import Alert from '@mui/material/Alert';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogTitle from '@mui/material/DialogTitle';
import LinearProgress from '@mui/material/LinearProgress';
import Typography from '@mui/material/Typography';
import { useState, useRef } from 'react';

function parsePreview(file: File): Promise<{ headers: string[]; rows: string[][] }> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      const text = e.target?.result as string;
      const lines = text.split('\n').filter(Boolean);

      if (lines.length === 0) {
 resolve({ headers: [], rows: [] });

 return; 
}

      const headers = lines[0].split(',').map((h) => h.trim());
      const rows = lines.slice(1, 6).map((line) => line.split(',').map((c) => c.trim()));
      resolve({ headers, rows });
    };
    reader.onerror = () => reject(new Error('Error reading file'));
    reader.readAsText(file.slice(0, 1024 * 50));
  });
}

interface ImportModalProps {
  open: boolean;
  onClose: () => void;
  entityEndpoint: string;
  entityTitle: string;
}

export default function MantenedorImportModal({
  open,
  onClose,
  entityEndpoint,
  entityTitle,
}: ImportModalProps) {
  const [file, setFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
  const [result, setResult] = useState<'success' | 'warning' | null>(null);
  const [resultMessage, setResultMessage] = useState('');
  const [preview, setPreview] = useState<{ headers: string[]; rows: string[][] } | null>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  const handleDownloadTemplate = () => {
    window.open(`${entityEndpoint}/template`, '_blank');
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const f = e.target.files?.[0];

    if (f) {
      setFile(f);
      setResult(null);
      setPreview(null);

      if (f.name.endsWith('.csv')) {
        parsePreview(f).then(setPreview).catch(() => {});
      }
    }
  };

  const handleUpload = () => {
    if (!file) {
return;
}

    setUploading(true);
    setResult(null);

    const formData = new FormData();
    formData.append('file', file);

    router.post(`${entityEndpoint}/import`, formData, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (page: any) => {
        setUploading(false);
        const flash = page.props.flash as Record<string, string> | undefined;

        if (flash?.success) {
          setResult('success');
          setResultMessage(flash.success);
        } else if (flash?.warning) {
          setResult('warning');
          setResultMessage(flash.warning);
        }

        setFile(null);
        setPreview(null);
      },
      onError: () => {
        setUploading(false);
        setResult('warning');
        setResultMessage('Error al importar el archivo.');
      },
    });
  };

  const handleClose = () => {
    if (!uploading) {
      setFile(null);
      setResult(null);
      setResultMessage('');
      setPreview(null);
      onClose();
    }
  };

  return (
    <Dialog open={open} onClose={handleClose} maxWidth="sm" fullWidth>
      <DialogTitle>Importar {entityTitle}</DialogTitle>
      <DialogContent>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2, mt: 1 }}>
          <Button
            variant="outlined"
            startIcon={<DownloadIcon />}
            onClick={handleDownloadTemplate}
          >
            Descargar plantilla Excel
          </Button>

          <Box
            sx={{
              border: '2px dashed',
              borderColor: file ? 'primary.main' : 'grey.300',
              borderRadius: 1,
              p: 3,
              textAlign: 'center',
              cursor: 'pointer',
            }}
            onClick={() => inputRef.current?.click()}
          >
            <input
              ref={inputRef}
              type="file"
              accept=".xlsx,.xls,.csv"
              hidden
              onChange={handleFileChange}
            />
            <CloudUploadIcon sx={{ fontSize: 40, color: 'grey.400', mb: 1 }} />
            <Typography variant="body2" color="text.secondary">
              {file ? file.name : 'Haga clic para seleccionar un archivo Excel o CSV'}
            </Typography>
          </Box>

          {preview && (
            <Box sx={{ mt: 1 }}>
              <Typography variant="caption" color="text.secondary">
                Vista previa (primeras filas):
              </Typography>
              <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 12 }}>
                <thead>
                  <tr>
                    {preview.headers.map((h, i) => (
                      <th key={i} style={{ border: '1px solid #ddd', padding: 4, textAlign: 'left' }}>{h}</th>
                    ))}
                  </tr>
                </thead>
                <tbody>
                  {preview.rows.map((row, ri) => (
                    <tr key={ri}>
                      {row.map((cell, ci) => (
                        <td key={ci} style={{ border: '1px solid #ddd', padding: 4 }}>{cell}</td>
                      ))}
                    </tr>
                  ))}
                </tbody>
              </table>
            </Box>
          )}

          {uploading && <LinearProgress />}

          {result && (
            <Alert severity={result === 'success' ? 'success' : 'warning'}>
              {resultMessage}
            </Alert>
          )}
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={handleClose} disabled={uploading}>Cancelar</Button>
        <Button
          variant="contained"
          disabled={!file || uploading}
          onClick={handleUpload}
        >
          {uploading ? 'Importando...' : 'Importar'}
        </Button>
      </DialogActions>
    </Dialog>
  );
}
