import { Head, router } from '@inertiajs/react';
import { Mail, Lock, Login as LoginIcon } from '@mui/icons-material';
import {
    Box,
    Button,
    Checkbox,
    FormControlLabel,
    TextField,
    Typography,
    InputAdornment,
} from '@mui/material';
import {  useState } from 'react';
import type {FormEvent} from 'react';
import PasskeyVerify from '@/components/passkey-verify';
import { register as registerRoute } from '@/routes';
import { request } from '@/routes/password';

type Props = {
    status?: string;
    canResetPassword: boolean;
};

export default function Login({ status, canResetPassword }: Props) {
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        const form = e.target as HTMLFormElement;
        const data = new FormData(form);

        router.post('/login', Object.fromEntries(data), {
            onFinish: () => setProcessing(false),
            onError: (errs) => setErrors(errs),
        });
    };

    return (
        <>
            <Head title="Iniciar sesión" />

            <PasskeyVerify />

            <Box
                component="form"
                onSubmit={handleSubmit}
                sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}
                noValidate
            >
                <TextField
                    id="email"
                    label="Correo electrónico"
                    type="email"
                    name="email"
                    required
                    autoFocus
                    slotProps={{
                        htmlInput: { tabIndex: 1, autoComplete: 'email' },
                        input: {
                            startAdornment: (
                                <InputAdornment position="start">
                                    <Mail sx={{ color: 'text.secondary', fontSize: 20 }} />
                                </InputAdornment>
                            ),
                        },
                    }}
                    fullWidth
                    error={!!errors.email}
                    helperText={errors.email}
                />

                <Box>
                    <Box sx={{ display: 'flex', alignItems: 'center', mb: 0.5 }}>
                        <Typography
                            component="label"
                            htmlFor="password"
                            variant="body2"
                            sx={{ fontWeight: 500, color: 'text.primary' }}
                        >
                            Contraseña
                        </Typography>
                        {canResetPassword && (
                            <Typography
                                component="a"
                                href={request().url}
                                variant="body2"
                                sx={{
                                    ml: 'auto',
                                    color: 'primary.main',
                                    textDecoration: 'none',
                                    '&:hover': { textDecoration: 'underline' },
                                    fontSize: '0.8125rem',
                                }}
                                tabIndex={5}
                            >
                                ¿Olvidaste tu contraseña?
                            </Typography>
                        )}
                    </Box>
                    <TextField
                        id="password"
                        type="password"
                        name="password"
                        required
                        slotProps={{
                            htmlInput: { tabIndex: 2, autoComplete: 'current-password' },
                            input: {
                                startAdornment: (
                                    <InputAdornment position="start">
                                        <Lock sx={{ color: 'text.secondary', fontSize: 20 }} />
                                    </InputAdornment>
                                ),
                            },
                        }}
                        fullWidth
                        error={!!errors.password}
                        helperText={errors.password}
                        placeholder="Tu contraseña"
                    />
                </Box>

                <FormControlLabel
                    control={
                        <Checkbox
                            id="remember"
                            name="remember"
                            tabIndex={3}
                            size="small"
                            sx={{ color: 'text.secondary' }}
                        />
                    }
                    label={
                        <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                            Recordarme
                        </Typography>
                    }
                />

                <Button
                    type="submit"
                    variant="contained"
                    color="primary"
                    fullWidth
                    tabIndex={4}
                    disabled={processing}
                    data-test="login-button"
                    sx={{
                        py: 1.5,
                        fontSize: '0.9375rem',
                        fontWeight: 600,
                        borderRadius: 2,
                    }}
                    startIcon={<LoginIcon />}
                >
                    {processing ? 'Ingresando...' : 'Iniciar sesión'}
                </Button>

                <Typography
                    variant="body2"
                    sx={{ textAlign: 'center', color: 'text.secondary' }}
                >
                    ¿No tienes cuenta?{' '}
                    <Typography
                        component="a"
                        href={registerRoute().url}
                        variant="body2"
                        sx={{
                            color: 'primary.main',
                            fontWeight: 600,
                            textDecoration: 'none',
                            '&:hover': { textDecoration: 'underline' },
                        }}
                        tabIndex={5}
                    >
                        Registrarse
                    </Typography>
                </Typography>
            </Box>

            {status && (
                <Typography
                    variant="body2"
                    sx={{
                        mt: 2,
                        textAlign: 'center',
                        fontWeight: 500,
                        color: 'success.main',
                    }}
                >
                    {status}
                </Typography>
            )}
        </>
    );
}

Login.layout = {
    title: 'Iniciar sesión',
    description: 'Ingresa tu correo y contraseña para acceder a la plataforma',
};
