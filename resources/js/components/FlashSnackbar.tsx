import { router } from '@inertiajs/react';
import { Snackbar, Alert } from '@mui/material';
import { useEffect, useState, useCallback } from 'react';

interface FlashData {
    type: 'success' | 'error' | 'info' | 'warning';
    message: string;
}

export default function FlashSnackbar() {
    const [flash, setFlash] = useState<FlashData | null>(null);
    const [open, setOpen] = useState(false);

    useEffect(() => {
        return router.on('flash', (event) => {
            const data = (event as CustomEvent).detail?.flash?.toast as FlashData | undefined;

            if (!data?.message) {
return;
}

            setFlash(data);
            setOpen(true);
        });
    }, []);

    const handleClose = useCallback((_?: React.SyntheticEvent | Event, reason?: string) => {
        if (reason === 'clickaway') {
return;
}

        setOpen(false);
    }, []);

    const severity = flash?.type === 'success' ? 'success'
        : flash?.type === 'error' ? 'error'
        : flash?.type === 'warning' ? 'warning'
        : 'info';

    return (
        <Snackbar
            open={open}
            autoHideDuration={5000}
            onClose={handleClose}
            anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
        >
            <Alert
                onClose={handleClose}
                severity={severity}
                variant="filled"
                sx={{ width: '100%', borderRadius: 2 }}
            >
                {flash?.message ?? ''}
            </Alert>
        </Snackbar>
    );
}
