import { Link } from '@inertiajs/react';
import { Box, Typography, Container } from '@mui/material';
import AppLogoIcon from '@/components/app-logo-icon';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <Box sx={{ display: 'flex', minHeight: '100dvh' }}>
            {/* Left panel — brand/imagery */}
            <Box
                sx={{
                    display: { xs: 'none', md: 'flex' },
                    flexDirection: 'column',
                    justifyContent: 'center',
                    alignItems: 'center',
                    width: '50%',
                    bgcolor: 'primary.dark',
                    backgroundImage: `linear-gradient(135deg, #1B5E20 0%, #2E7D32 40%, #388E3C 100%)`,
                    position: 'relative',
                    overflow: 'hidden',
                    p: 6,
                }}
            >
                {/* Decorative leaf pattern */}
                <Box
                    sx={{
                        position: 'absolute',
                        inset: 0,
                        opacity: 0.08,
                        backgroundImage: `url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 0C17.9 0 0 17.9 0 40c0 10.5 4 20 10.6 27.1L40 40l29.4 27.1C76 60 80 50.5 80 40 80 17.9 62.1 0 40 0z' fill='%23FFFFFF' fill-opacity='0.3'/%3E%3C/svg%3E")`,
                        backgroundSize: '120px 120px',
                    }}
                />

                <Box sx={{ position: 'relative', zIndex: 1, textAlign: 'center', maxWidth: 400 }}>
                    <Box sx={{ mb: 3, display: 'flex', justifyContent: 'center' }}>
                        <img src="/agrogreen-logo.png" alt="AgroGreen" style={{ height: 64, filter: 'brightness(0) invert(1)' }} />
                    </Box>
                    <Typography
                        variant="h3"
                        sx={{
                            color: 'white',
                            fontFamily: '"Lora", serif',
                            fontWeight: 600,
                            mb: 2,
                            fontSize: '2rem',
                        }}
                    >
                        AgroGreen
                    </Typography>
                    <Typography
                        sx={{
                            color: 'rgba(255,255,255,0.8)',
                            fontSize: '1rem',
                            lineHeight: 1.7,
                            fontFamily: '"Raleway", sans-serif',
                        }}
                    >
                        Plataforma inteligente para la gestión agrícola — controla tus cultivos, insumos y operaciones desde un solo lugar.
                    </Typography>
                </Box>

                <Box sx={{ position: 'absolute', bottom: 40, left: 0, right: 0, textAlign: 'center', zIndex: 1 }}>
                    <Typography sx={{ color: 'rgba(255,255,255,0.4)', fontSize: '0.75rem' }}>
                        &copy; {new Date().getFullYear()} AgroGreen. Todos los derechos reservados.
                    </Typography>
                </Box>
            </Box>

            {/* Right panel — form */}
            <Box
                sx={{
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'center',
                    alignItems: 'center',
                    width: { xs: '100%', md: '50%' },
                    bgcolor: 'background.default',
                    px: 3,
                    py: 6,
                }}
            >
                <Box sx={{ display: { xs: 'flex', md: 'none' }, alignItems: 'center', gap: 1.5, mb: 4 }}>
                    <img src="/agrogreen-logo.png" alt="AgroGreen" style={{ height: 36 }} />
                    <Typography sx={{ fontFamily: '"Lora", serif', fontWeight: 600, fontSize: '1.25rem', color: 'text.primary' }}>
                        AgroGreen
                    </Typography>
                </Box>

                <Box sx={{ width: '100%', maxWidth: 400 }}>
                    <Box sx={{ mb: 4, textAlign: { xs: 'center', md: 'left' } }}>
                        <Typography
                            variant="h5"
                            sx={{
                                fontFamily: '"Lora", serif',
                                fontWeight: 600,
                                color: 'text.primary',
                                mb: 1,
                            }}
                        >
                            {title}
                        </Typography>
                        <Typography
                            sx={{
                                color: 'text.secondary',
                                fontSize: '0.875rem',
                                lineHeight: 1.6,
                            }}
                        >
                            {description}
                        </Typography>
                    </Box>

                    {children}
                </Box>

                <Box sx={{ display: { xs: 'block', md: 'none' }, mt: 6 }}>
                    <Typography sx={{ color: 'text.secondary', fontSize: '0.75rem', textAlign: 'center' }}>
                        &copy; {new Date().getFullYear()} AgroGreen. Todos los derechos reservados.
                    </Typography>
                </Box>
            </Box>
        </Box>
    );
}
