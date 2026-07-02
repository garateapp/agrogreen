import {
  Map,
  ShoppingCart,
  Inventory2,
  Agriculture,
  Science,
  Grain,
  WaterDrop,
  AccountBalance,
} from '@mui/icons-material';
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  Stepper,
  Step,
  StepLabel,
  Typography,
  Box,
} from '@mui/material';
import { useState } from 'react';
import type { SlideData } from '@/types/agricultural';

const SLIDES: SlideData[] = [
  { title: 'Configuración de Cuarteles', description: 'Registra tus campos, sectores y cuarteles con superficies, especies y variedades. Define la geometría GeoJSON para mapas.', icon: 'Map' },
  { title: 'Compras', description: 'Gestiona órdenes de compra a proveedores con control de moneda, tipo de cambio y aprobaciones por usuario.', icon: 'ShoppingCart' },
  { title: 'Stock', description: 'Administra el inventario valorizado con Costo Promedio Ponderado (CPP). Trazabilidad completa de entradas y salidas.', icon: 'Inventory2' },
  { title: 'Faenas', description: 'Registra faenas diarias con asignación de mano de obra. Soporte para trabajadores de planta, contratistas y temporeros.', icon: 'Agriculture' },
  { title: 'Aplicaciones', description: 'Planifica aplicaciones químicas con control de carencia fitosanitaria. Cruce automático contra cosechas planificadas.', icon: 'Science' },
  { title: 'Cosecha', description: 'Registra cosecha por cuartel con pesaje, códigos QR por tarjeta y sincronización offline con app móvil.', icon: 'Grain' },
  { title: 'Riego', description: 'Administra sectores de riego con caudal disponible. Histórico de metros cúbicos aplicados por evento.', icon: 'WaterDrop' },
  { title: 'Presupuesto', description: 'Define presupuestos mensuales por centro de costo. Control de periodos fiscales para evitar modificaciones retroactivas.', icon: 'AccountBalance' },
];

const ICON_MAP: Record<string, React.ReactNode> = {
  Map: <Map sx={{ fontSize: 48 }} color="primary" />,
  ShoppingCart: <ShoppingCart sx={{ fontSize: 48 }} color="primary" />,
  Inventory2: <Inventory2 sx={{ fontSize: 48 }} color="primary" />,
  Agriculture: <Agriculture sx={{ fontSize: 48 }} color="primary" />,
  Science: <Science sx={{ fontSize: 48 }} color="primary" />,
  Grain: <Grain sx={{ fontSize: 48 }} color="primary" />,
  WaterDrop: <WaterDrop sx={{ fontSize: 48 }} color="primary" />,
  AccountBalance: <AccountBalance sx={{ fontSize: 48 }} color="primary" />,
};

interface Props {
  open: boolean;
  onClose: () => void;
  onFinish: () => void;
}

export default function OnboardingWizard({ open, onClose, onFinish }: Props) {
  const [activeStep, setActiveStep] = useState(0);

  const handleNext = () => {
    if (activeStep === SLIDES.length - 1) {
      onFinish();
      onClose();
    } else {
      setActiveStep((prev) => prev + 1);
    }
  };

  const handleBack = () => {
    setActiveStep((prev) => prev - 1);
  };

  const slide = SLIDES[activeStep];

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle sx={{ textAlign: 'center', pb: 0 }}>Bienvenido a AgroGreen</DialogTitle>
      <DialogContent sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 3, py: 3 }}>
        <Stepper activeStep={activeStep} alternativeLabel sx={{ width: '100%', mb: 2 }}>
          {SLIDES.map((s, i) => (
            <Step key={i}>
              <StepLabel />
            </Step>
          ))}
        </Stepper>

        <Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 1, textAlign: 'center', px: 2 }}>
          {ICON_MAP[slide.icon]}
          <Typography variant="h6" sx={{ fontWeight: 600 }}>{slide.title}</Typography>
          <Typography variant="body2" color="text.secondary">{slide.description}</Typography>
        </Box>
      </DialogContent>
      <DialogActions sx={{ justifyContent: 'space-between', px: 3, pb: 2 }}>
        <Button disabled={activeStep === 0} onClick={handleBack}>
          Atrás
        </Button>
        <Box>
          <Button variant="text" color="inherit" onClick={onClose} sx={{ mr: 1 }}>
            Omitir
          </Button>
          <Button variant="contained" onClick={handleNext}>
            {activeStep === SLIDES.length - 1 ? 'Comenzar' : 'Siguiente'}
          </Button>
        </Box>
      </DialogActions>
    </Dialog>
  );
}
