import {
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Button,
    Box,
    Typography,
    Stack,
    Chip,
    Divider,
} from '@mui/material';
import { QRCodeSVG } from 'qrcode.react';
import { useRef } from 'react';

interface CuartelData {
    id: string;
    nombre: string;
    superficie_hectareas?: number;
    variedades?: Array<{
        id: string;
        nombre: string;
        pivot: { cantidad_plantas: number };
    }>;
}

interface QrCodePrintProps {
    open: boolean;
    cuartel: CuartelData | null;
    onClose: () => void;
}

export default function QrCodePrint({ open, cuartel, onClose }: QrCodePrintProps) {
    const contentRef = useRef<HTMLDivElement>(null);

    const handlePrint = () => {
        window.print();
    };

    if (!cuartel) {
return null;
}

    return (
        <>
            <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
                <DialogTitle>Código QR — {cuartel.nombre}</DialogTitle>
                <DialogContent>
                    <Box ref={contentRef} className="qr-print-content" sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 3, py: 3 }}>
                        <Box sx={{ p: 3, border: 2, borderColor: 'grey.300', borderRadius: 2, bgcolor: 'white', display: 'inline-flex' }}>
                            <QRCodeSVG value={cuartel.id} size={220} level="M" includeMargin />
                        </Box>
                        <Stack spacing={0.5} alignItems="center" sx={{ textAlign: 'center' }}>
                            <Typography variant="h6" fontWeight={700}>{cuartel.nombre}</Typography>
                            <Typography variant="body2" color="text.secondary">{cuartel.superficie_hectareas ?? '—'} ha</Typography>
                        </Stack>
                        {Array.isArray(cuartel.variedades) && cuartel.variedades.length > 0 && (
                            <>
                                <Divider sx={{ width: '100%' }} />
                                <Box sx={{ width: '100%' }}>
                                    <Typography variant="subtitle2" fontWeight={600} sx={{ mb: 1 }}>Variedades</Typography>
                                    <Stack spacing={1}>
                                        {cuartel.variedades.map((v) => (
                                            <Chip key={v.id} label={`${v.nombre} (${v.pivot?.cantidad_plantas ?? '?'} plantas)`} variant="outlined" size="medium" sx={{ borderRadius: 1, justifyContent: 'flex-start', width: '100%' }} />
                                        ))}
                                    </Stack>
                                </Box>
                            </>
                        )}
                    </Box>
                </DialogContent>
                <DialogActions>
                    <Button onClick={onClose}>Cerrar</Button>
                    <Button variant="contained" onClick={handlePrint}>Imprimir</Button>
                </DialogActions>
            </Dialog>
            <style>{`
                @media print {
                    body > :not(.MuiDialog-root) { display: none !important; }
                    .MuiDialog-root { position: fixed !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; }
                    .MuiDialog-container { align-items: center !important; justify-content: center !important; min-height: 100vh !important; }
                    .MuiDialog-paper { box-shadow: none !important; border: none !important; margin: 0 !important; }
                    .MuiDialogActions-root, .MuiDialogTitle-root { display: none !important; }
                    .qr-print-content { padding: 0 !important; }
                }
            `}</style>
        </>
    );
}
