import { Head, router, usePage } from '@inertiajs/react';
import {
    Agriculture,
    WaterDrop,
    Inventory,
    BarChart,
    WbSunny,
    Group,
    Forest,
    ArrowForward,
} from '@mui/icons-material';
import {
    AppBar,
    Box,
    Button,
    Container,
    Grid,
    Toolbar,
    Typography,
    useTheme,
} from '@mui/material';
import { dashboard, login, register } from '@/routes';

const features = [
    {
        title: 'Manejo de Cultivos',
        description: 'Controla ciclos de siembra, cosecha y sanidad vegetal de todos tus campos con precisión digital.',
        icon: <Agriculture sx={{ fontSize: 32 }} />,
        color: '#2E7D32',
    },
    {
        title: 'Riego Inteligente',
        description: 'Monitorea humedad del suelo, programa riegos automatizados y optimiza el uso del agua en tiempo real.',
        icon: <WaterDrop sx={{ fontSize: 32 }} />,
        color: '#1565C0',
    },
    {
        title: 'Inventario e Insumos',
        description: 'Administra fertilizantes, plaguicidas y equipamiento con alertas automáticas de reabastecimiento.',
        icon: <Inventory sx={{ fontSize: 32 }} />,
        color: '#F9A825',
    },
    {
        title: 'Analítica Financiera',
        description: 'Control de costos integral, presupuestos por cultivo y reportes de rentabilidad en tiempo real.',
        icon: <BarChart sx={{ fontSize: 32 }} />,
        color: '#6A1B9A',
    },
    {
        title: 'Clima Inteligente',
        description: 'Pronósticos hiperlocales y datos históricos integrados para la toma de decisiones informadas.',
        icon: <WbSunny sx={{ fontSize: 32 }} />,
        color: '#E65100',
    },
    {
        title: 'Gestión de Equipos',
        description: 'Asigna tareas, controla horas de trabajo y administra la nómina de temporeros y permanentes.',
        icon: <Group sx={{ fontSize: 32 }} />,
        color: '#2E7D32',
    },
];

const stats = [
    { value: '15K+', label: 'Predios gestionados' },
    { value: '98%', label: 'Disponibilidad' },
    { value: '45K', label: 'Campos monitoreados' },
    { value: '4.9', label: 'Valoración usuarios' },
];

export default function Welcome() {
    const { auth } = usePage().props;
    const theme = useTheme();

    return (
        <>
            <Head title="AgroGreen — Gestión Agrícola Inteligente" />

            {/* Navbar */}
            <AppBar
                position="sticky"
                elevation={0}
                sx={{
                    bgcolor: 'rgba(250, 250, 245, 0.9)',
                    backdropFilter: 'blur(12px)',
                    borderBottom: 1,
                    borderColor: 'divider',
                }}
            >
                <Container maxWidth="lg">
                    <Toolbar disableGutters sx={{ justifyContent: 'space-between' }}>
                        <Box sx={{ display: 'flex', alignItems: 'center', gap: 1.5 }}>
                            <img src="/agrogreen-logo.png" alt="AgroGreen" style={{ height: 32 }} />
                            <Typography
                                variant="h6"
                                sx={{
                                    fontFamily: '"Lora", serif',
                                    fontWeight: 600,
                                    color: 'text.primary',
                                }}
                            >
                                AgroGreen
                            </Typography>
                        </Box>

                        <Box sx={{ display: 'flex', gap: 1.5 }}>
                            {auth.user ? (
                                <Button
                                    variant="contained"
                                    onClick={() => router.visit(dashboard().url)}
                                >
                                    Dashboard
                                </Button>
                            ) : (
                                <>
                                    <Button
                                        variant="text"
                                        onClick={() => router.visit(login().url)}
                                        sx={{ color: 'text.secondary' }}
                                    >
                                        Iniciar sesión
                                    </Button>
                                    <Button
                                        variant="contained"
                                        onClick={() => router.visit(register().url)}
                                        sx={{
                                            bgcolor: 'primary.main',
                                            '&:hover': { bgcolor: 'primary.dark' },
                                        }}
                                    >
                                        Comenzar gratis
                                    </Button>
                                </>
                            )}
                        </Box>
                    </Toolbar>
                </Container>
            </AppBar>

            <Box sx={{ bgcolor: 'background.default', minHeight: '100vh' }}>
                {/* Hero Section */}
                <Container maxWidth="lg" sx={{ py: { xs: 8, md: 14 } }}>
                    <Grid container spacing={6} sx={{ alignItems: 'center' }}>
                        <Grid size={{ xs: 12, md: 6 }}>
                            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 2 }}>
                                <Forest sx={{ color: 'primary.main', fontSize: 20 }} />
                                <Typography
                                    sx={{
                                        color: 'primary.main',
                                        fontSize: '0.8125rem',
                                        fontWeight: 600,
                                        letterSpacing: '0.05em',
                                        textTransform: 'uppercase',
                                    }}
                                >
                                    Plataforma Agrícola Integral
                                </Typography>
                            </Box>
                            <Typography
                                variant="h1"
                                sx={{
                                    fontSize: { xs: '2.25rem', md: '3.75rem' },
                                    lineHeight: 1.1,
                                    color: 'text.primary',
                                    mb: 2.5,
                                    letterSpacing: '-0.02em',
                                }}
                            >
                                Gestión inteligente{' '}
                                <Box
                                    component="span"
                                    sx={{ color: 'primary.main' }}
                                >
                                    para el campo
                                </Box>
                            </Typography>
                            <Typography
                                sx={{
                                    fontSize: { xs: '1rem', md: '1.125rem' },
                                    color: 'text.secondary',
                                    lineHeight: 1.7,
                                    mb: 4,
                                    maxWidth: 520,
                                }}
                            >
                                AgroGreen potencia tu operación agrícola con monitoreo en tiempo real, planificación inteligente de recursos y gestión de cultivos basada en datos.
                            </Typography>
                            <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap' }}>
                                <Button
                                    variant="contained"
                                    size="large"
                                    onClick={() => router.visit(register().url)}
                                    sx={{
                                        px: 4,
                                        py: 1.5,
                                        fontSize: '1rem',
                                        borderRadius: 2,
                                    }}
                                    endIcon={<ArrowForward />}
                                >
                                    Prueba gratuita
                                </Button>
                                <Button
                                    variant="outlined"
                                    size="large"
                                    onClick={() => router.visit(login().url)}
                                    sx={{
                                        px: 4,
                                        py: 1.5,
                                        fontSize: '1rem',
                                        borderColor: 'divider',
                                        color: 'text.primary',
                                        borderRadius: 2,
                                    }}
                                >
                                    Iniciar sesión
                                </Button>
                            </Box>
                        </Grid>
                        <Grid size={{ xs: 12, md: 6 }}>
                            <Box
                                sx={{
                                    position: 'relative',
                                    width: '100%',
                                    aspectRatio: '4/3',
                                    borderRadius: 3,
                                    overflow: 'hidden',
                                    bgcolor: 'primary.dark',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    boxShadow: `0 24px 64px -12px rgba(30, 125, 50, 0.25)`,
                                }}
                            >
                                <Box
                                    sx={{
                                        position: 'absolute',
                                        inset: 0,
                                        opacity: 0.1,
                                        backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
                                    }}
                                />
                                <Box sx={{ textAlign: 'center', zIndex: 1, px: 4 }}>
                                    <Forest sx={{ fontSize: 56, color: 'rgba(255,255,255,0.9)', mb: 2 }} />
                                    <Typography
                                        variant="h2"
                                        sx={{
                                            color: 'white',
                                            fontSize: { xs: '1.75rem', md: '2.5rem' },
                                            fontWeight: 600,
                                            fontFamily: '"Lora", serif',
                                            mb: 2,
                                        }}
                                    >
                                        Cultiva de forma inteligente
                                    </Typography>
                                    <Typography
                                        sx={{
                                            color: 'rgba(255,255,255,0.75)',
                                            fontSize: '1.125rem',
                                            fontFamily: '"Raleway", sans-serif',
                                        }}
                                    >
                                        Datos precisos para cada etapa de tu ciclo productivo
                                    </Typography>
                                </Box>
                            </Box>
                        </Grid>
                    </Grid>
                </Container>

                {/* Stats Bar */}
                <Box sx={{ bgcolor: 'primary.main', py: { xs: 5, md: 6 } }}>
                    <Container maxWidth="lg">
                        <Grid container spacing={4} sx={{ justifyContent: 'center' }}>
                            {stats.map((stat) => (
                                <Grid key={stat.label} size={{ xs: 6, md: 3 }}>
                                    <Box sx={{ textAlign: 'center' }}>
                                        <Typography
                                            variant="h3"
                                            sx={{
                                                color: 'white',
                                                fontWeight: 700,
                                                fontSize: { xs: '2rem', md: '2.5rem' },
                                                fontFamily: '"Lora", serif',
                                            }}
                                        >
                                            {stat.value}
                                        </Typography>
                                        <Typography
                                            sx={{
                                                color: 'rgba(255,255,255,0.8)',
                                                fontSize: '0.875rem',
                                                mt: 0.5,
                                            }}
                                        >
                                            {stat.label}
                                        </Typography>
                                    </Box>
                                </Grid>
                            ))}
                        </Grid>
                    </Container>
                </Box>

                {/* Features — Bento-style grid */}
                <Container maxWidth="lg" sx={{ py: { xs: 8, md: 14 } }}>
                    <Box sx={{ mb: 10 }}>
                        <Typography
                            sx={{
                                color: 'primary.main',
                                fontSize: '0.8125rem',
                                fontWeight: 600,
                                letterSpacing: '0.05em',
                                textTransform: 'uppercase',
                                mb: 1.5,
                            }}
                        >
                            Funcionalidades
                        </Typography>
                        <Typography
                            variant="h2"
                            sx={{
                                fontSize: { xs: '1.75rem', md: '2.5rem' },
                                color: 'text.primary',
                                mb: 2,
                                fontFamily: '"Lora", serif',
                                fontWeight: 600,
                                maxWidth: 600,
                            }}
                        >
                            Todo lo que necesitas para{' '}
                            <Box component="span" sx={{ color: 'primary.main' }}>
                                administrar tu campo
                            </Box>
                        </Typography>
                        <Typography
                            sx={{
                                color: 'text.secondary',
                                fontSize: '1.0625rem',
                                maxWidth: 520,
                                lineHeight: 1.7,
                            }}
                        >
                            Desde la siembra hasta la comercialización, AgroGreen te entrega las herramientas para optimizar cada aspecto de tu operación agrícola.
                        </Typography>
                    </Box>

                    {/* Bento grid: 2+2+2 asymmetric */}
                    <Grid container spacing={2.5}>
                        {features.slice(0, 2).map((feature) => (
                            <Grid key={feature.title} size={{ xs: 12, md: 6 }}>
                                <Box
                                    sx={{
                                        p: 4,
                                        borderRadius: 3,
                                        border: 1,
                                        borderColor: 'divider',
                                        bgcolor: 'background.paper',
                                        transition: 'all 0.25s ease',
                                        height: '100%',
                                        '&:hover': {
                                            borderColor: 'primary.main',
                                            boxShadow: `0 8px 32px ${theme.palette.primary.main}12`,
                                            transform: 'translateY(-2px)',
                                        },
                                    }}
                                >
                                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 2, mb: 2 }}>
                                        <Box
                                            sx={{
                                                width: 48,
                                                height: 48,
                                                borderRadius: 2,
                                                bgcolor: `${feature.color}12`,
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center',
                                                color: feature.color,
                                            }}
                                        >
                                            {feature.icon}
                                        </Box>
                                        <Typography
                                            variant="h6"
                                            sx={{
                                                fontWeight: 600,
                                                fontFamily: '"Lora", serif',
                                                color: 'text.primary',
                                            }}
                                        >
                                            {feature.title}
                                        </Typography>
                                    </Box>
                                    <Typography
                                        sx={{
                                            color: 'text.secondary',
                                            lineHeight: 1.7,
                                            fontSize: '0.9375rem',
                                        }}
                                    >
                                        {feature.description}
                                    </Typography>
                                </Box>
                            </Grid>
                        ))}
                        {features.slice(2, 5).map((feature) => (
                            <Grid key={feature.title} size={{ xs: 12, sm: 6, md: 4 }}>
                                <Box
                                    sx={{
                                        p: 3.5,
                                        borderRadius: 3,
                                        border: 1,
                                        borderColor: 'divider',
                                        bgcolor: 'background.paper',
                                        transition: 'all 0.25s ease',
                                        height: '100%',
                                        '&:hover': {
                                            borderColor: 'primary.main',
                                            boxShadow: `0 8px 32px ${theme.palette.primary.main}12`,
                                            transform: 'translateY(-2px)',
                                        },
                                    }}
                                >
                                    <Box
                                        sx={{
                                            width: 44,
                                            height: 44,
                                            borderRadius: 2,
                                            bgcolor: `${feature.color}12`,
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            color: feature.color,
                                            mb: 2,
                                        }}
                                    >
                                        {feature.icon}
                                    </Box>
                                    <Typography
                                        variant="subtitle1"
                                        sx={{
                                            fontWeight: 600,
                                            fontFamily: '"Lora", serif',
                                            mb: 1,
                                            color: 'text.primary',
                                        }}
                                    >
                                        {feature.title}
                                    </Typography>
                                    <Typography
                                        sx={{
                                            color: 'text.secondary',
                                            lineHeight: 1.7,
                                            fontSize: '0.875rem',
                                        }}
                                    >
                                        {feature.description}
                                    </Typography>
                                </Box>
                            </Grid>
                        ))}
                        <Grid size={{ xs: 12, md: 6 }}>
                            <Box
                                sx={{
                                    p: 4,
                                    borderRadius: 3,
                                    border: 1,
                                    borderColor: 'divider',
                                    bgcolor: 'primary.main',
                                    transition: 'all 0.25s ease',
                                    height: '100%',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    justifyContent: 'center',
                                    '&:hover': {
                                        transform: 'translateY(-2px)',
                                        boxShadow: `0 8px 32px rgba(46, 125, 50, 0.25)`,
                                    },
                                }}
                            >
                                <Box
                                    sx={{
                                        width: 44,
                                        height: 44,
                                        borderRadius: 2,
                                        bgcolor: 'rgba(255,255,255,0.15)',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        color: 'white',
                                        mb: 2,
                                    }}
                                >
                                    {features[5].icon}
                                </Box>
                                <Typography
                                    variant="h6"
                                    sx={{
                                        fontWeight: 600,
                                        fontFamily: '"Lora", serif',
                                        color: 'white',
                                        mb: 1,
                                    }}
                                >
                                    {features[5].title}
                                </Typography>
                                <Typography
                                    sx={{
                                        color: 'rgba(255,255,255,0.85)',
                                        lineHeight: 1.7,
                                        fontSize: '0.9375rem',
                                    }}
                                >
                                    {features[5].description}
                                </Typography>
                            </Box>
                        </Grid>
                    </Grid>
                </Container>

                {/* CTA Section */}
                <Box
                    sx={{
                        backgroundImage: `linear-gradient(135deg, #1B5E20 0%, #2E7D32 50%, #388E3C 100%)`,
                        py: { xs: 8, md: 12 },
                        position: 'relative',
                        overflow: 'hidden',
                    }}
                >
                    <Box
                        sx={{
                            position: 'absolute',
                            inset: 0,
                            opacity: 0.06,
                            backgroundImage: `url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 0C17.9 0 0 17.9 0 40c0 10.5 4 20 10.6 27.1L40 40l29.4 27.1C76 60 80 50.5 80 40 80 17.9 62.1 0 40 0z' fill='%23FFFFFF' fill-opacity='0.3'/%3E%3C/svg%3E")`,
                            backgroundSize: '140px 140px',
                        }}
                    />
                    <Container maxWidth="sm" sx={{ position: 'relative', zIndex: 1, textAlign: 'center' }}>
                        <Typography
                            variant="h2"
                            sx={{
                                color: 'white',
                                fontSize: { xs: '1.75rem', md: '2.25rem' },
                                fontWeight: 600,
                                fontFamily: '"Lora", serif',
                                mb: 2,
                            }}
                        >
                            ¿Listo para transformar tu operación agrícola?
                        </Typography>
                        <Typography
                            sx={{
                                color: 'rgba(255,255,255,0.85)',
                                fontSize: '1.125rem',
                                mb: 4,
                                lineHeight: 1.7,
                            }}
                        >
                            Únete a los miles de productores que ya usan AgroGreen para aumentar rendimientos, reducir costos y tomar decisiones más inteligentes.
                        </Typography>
                        <Button
                            variant="contained"
                            size="large"
                            onClick={() => router.visit(register().url)}
                            sx={{
                                px: 5,
                                py: 1.5,
                                fontSize: '1rem',
                                bgcolor: 'white',
                                color: 'primary.dark',
                                fontWeight: 600,
                                borderRadius: 2,
                                '&:hover': {
                                    bgcolor: 'rgba(255,255,255,0.9)',
                                },
                            }}
                            endIcon={<ArrowForward />}
                        >
                            Comenzar gratis
                        </Button>
                    </Container>
                </Box>

                {/* Footer */}
                <Box
                    component="footer"
                    sx={{
                        borderTop: 1,
                        borderColor: 'divider',
                        py: 4,
                        bgcolor: 'background.paper',
                    }}
                >
                    <Container maxWidth="lg">
                        <Box
                            sx={{
                                display: 'flex',
                                flexDirection: { xs: 'column', sm: 'row' },
                                justifyContent: 'space-between',
                                alignItems: 'center',
                                gap: 2,
                            }}
                        >
                            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1.5 }}>
                                <img src="/agrogreen-logo.png" alt="AgroGreen" style={{ height: 24 }} />
                                <Typography
                                    variant="body2"
                                    sx={{
                                        fontWeight: 600,
                                        fontFamily: '"Lora", serif',
                                        color: 'text.primary',
                                    }}
                                >
                                    AgroGreen
                                </Typography>
                            </Box>
                            <Typography
                                variant="body2"
                                sx={{ color: 'text.secondary' }}
                            >
                                &copy; {new Date().getFullYear()} AgroGreen. Todos los derechos reservados.
                            </Typography>
                        </Box>
                    </Container>
                </Box>
            </Box>
        </>
    );
}
