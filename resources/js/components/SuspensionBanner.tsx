import { usePage } from '@inertiajs/react';
import { Close, Warning } from '@mui/icons-material';
import { Alert, AlertTitle, Collapse, IconButton } from '@mui/material';
import { useState } from 'react';

export default function SuspensionBanner() {
  const { auth } = usePage<{ auth: { user: { tenant?: { status?: string } } } }>().props;
  const [open, setOpen] = useState(true);

  const tenant = (auth as Record<string, unknown>)?.user
    ? ((auth as Record<string, unknown>).user as Record<string, unknown>)?.tenant as Record<string, unknown> | undefined
    : undefined;

  const isSuspended = (tenant?.status as string) === 'suspendido_pago';

  if (!isSuspended) {
return null;
}

  return (
    <Collapse in={open}>
      <Alert
        severity="error"
        icon={<Warning />}
        action={
          <IconButton color="inherit" size="small" onClick={() => setOpen(false)}>
            <Close fontSize="inherit" />
          </IconButton>
        }
        sx={{ borderRadius: 0 }}
      >
        <AlertTitle>Tenant suspendido</AlertTitle>
        El pago de su suscripción se encuentra pendiente. Algunas funcionalidades podrían estar restringidas.
      </Alert>
    </Collapse>
  );
}
