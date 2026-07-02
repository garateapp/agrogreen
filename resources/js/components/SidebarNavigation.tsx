import { Link } from '@inertiajs/react';
import { Search, ExpandMore, Dashboard, ShoppingCart,
  Agriculture, History, Science, Grain, WaterDrop, AccountBalance, Dns, Warehouse, PrecisionManufacturing } from '@mui/icons-material';
import { Box, TextField, Accordion, AccordionSummary, AccordionDetails, Typography, InputAdornment } from '@mui/material';
import { useState, useMemo } from 'react';

interface NavModule {
  title: string;
  icon: React.ReactNode;
  items: { title: string; href: string }[];
}

const MODULES: NavModule[] = [
  { title: 'Dashboard', icon: <Dashboard fontSize="small" color="primary" />, items: [{ title: 'Resumen', href: '/dashboard' }] },
  { title: 'Compras y Tesorería', icon: <ShoppingCart fontSize="small" color="primary" />, items: [
    { title: 'Egresos y Recepciones', href: '/compras/invoices' },
    { title: 'Ingresos', href: '/compras/incomes' },
    { title: 'Órdenes de Compra', href: '/compras/purchase-orders' },
    { title: 'Reporte de Pagos', href: '/compras/payments' },
    { title: 'Reporte Aprobación OC', href: '/compras/purchase-orders-report' },
    { title: 'Flujo de Caja', href: '/compras/cash-flow-report' },
    { title: 'Solicitudes Cotización', href: '/compras/quotations' },
  ] },
   { title: 'Bodegaje', icon: <Warehouse fontSize="small" color="primary" />, items: [
    { title: 'Guías de Entrada', href: '/bodegaje/goods-receipts' },
    { title: 'Guías de Consumo', href: '/bodegaje/goods-issues' },
    { title: 'Reporte de Inventario', href: '/bodegaje/inventory-report' },
    { title: 'Traspaso entre Bodegas', href: '/bodegaje/warehouse-transfers' },
    { title: 'Reporte Consumo Productos', href: '/bodegaje/product-consumption-report' },
  ] },
//   { title: 'Stock', icon: <Inventory2 fontSize="small" color="primary" />, items: [{ title: 'Inventario', href: '/stock/inventario' }, { title: 'Movimientos', href: '/stock/movimientos' }] },
  { title: 'Control de Labores', icon: <Agriculture fontSize="small" color="primary" />, items: [
    { title: 'Planificador', href: '/labores/planificador' },
    { title: 'Tarja Diaria', href: '/labores/tarja-diaria' },
    // { title: 'Faenas', href: '/faenas/tasks' },
    // { title: 'Tarja Diaria', href: '/faenas/tasks-creation' },
    // { title: 'Tarja Diaria Móvil', href: '/faenas/tasks-creation-mobile' },
    { title: 'Reporte de Sueldos', href: '/faenas/salary-report' },
    { title: 'Reporte de Tratos', href: '/faenas/additional-salary-report' },
    { title: 'Rendimiento por Faenas', href: '/faenas/tasks-performance' },
  ] },
  { title: 'Maquinaria', icon: <PrecisionManufacturing fontSize="small" color="primary" />, items: [
    { title: 'Faenas de Maquinaria', href: '/maquinaria/machine-tasks' },
    { title: 'Salidas de Productos', href: '/maquinaria/oil-receipts' },
    { title: 'Reporte de Maquinaria', href: '/maquinaria/machine-report' },
  ] },
//   { title: 'Aplicaciones', icon: <Science fontSize="small" color="primary" />, items: [{ title: 'Órdenes de Aplicación', href: '/aplicaciones/ordenes' }, { title: 'Productos Fitosanitarios', href: '/aplicaciones/productos' }] },
  { title: 'Cosecha', icon: <Grain fontSize="small" color="primary" />, items: [{ title: 'Registro de Cosecha', href: '/cosecha/registro' }, { title: 'Contenedores', href: '/cosecha/contenedores' }] },
  { title: 'Riego', icon: <WaterDrop fontSize="small" color="primary" />, items: [{ title: 'Sectores de Riego', href: '/riego/sectores' }, { title: 'Registros', href: '/riego/registros' }] },
  { title: 'Presupuesto', icon: <AccountBalance fontSize="small" color="primary" />, items: [{ title: 'Presupuestos', href: '/presupuesto' }, { title: 'Estimaciones', href: '/presupuesto/estimaciones' }, { title: 'Temporadas', href: '/presupuesto/temporadas' }] },

  { title: 'Aplicaciones', icon: <Science fontSize="small" color="primary" />, items: [
    { title: 'Registro de Aplicaciones', href: '/agroquimicos' },
  ] },
  { title: 'Mantenedores', icon: <Dns fontSize="small" color="primary" />, items: [
    { title: 'Actividades', href: '/mantenedores/actividades' },
    { title: 'Bodegas', href: '/mantenedores/bodegas' },
    { title: 'Categorías', href: '/mantenedores/categorias' },
    { title: 'Centros de Costo', href: '/mantenedores/centros-costo' },
    { title: 'Clasif. Agroquímicos', href: '/mantenedores/clasificacion-agroquimicos' },
    { title: 'Clientes', href: '/mantenedores/clientes' },
    { title: 'Contratistas', href: '/mantenedores/contratistas' },
    { title: 'Contenedores Cosecha', href: '/mantenedores/contenedores-cosecha' },
    { title: 'Cuarteles', href: '/mantenedores/cuarteles' },
    { title: 'Direcciones de Envío', href: '/mantenedores/direcciones-envio' },
    { title: 'Especies', href: '/mantenedores/especies' },
    { title: 'Familias', href: '/mantenedores/familias' },
    { title: 'Aplicadores', href: '/mantenedores/aplicadores' },
    { title: 'Empleados', href: '/mantenedores/empleados' },
    { title: 'Equipos de Aplicación', href: '/mantenedores/equipos-aplicacion' },
    { title: 'Feriados', href: '/mantenedores/feriados' },
    { title: 'Implementos Seguridad', href: '/mantenedores/implementos-seguridad' },
    { title: 'Ingredientes Activos', href: '/mantenedores/ingredientes-activos' },
    { title: 'Ítems de Gasto', href: '/mantenedores/items-gasto' },
    { title: 'Jornadas', href: '/mantenedores/jornadas' },
    { title: 'Métodos de Pago', href: '/mantenedores/metodos-pago' },
    { title: 'Nebulizadoras', href: '/mantenedores/nebulizadoras' },
    { title: 'Productos', href: '/mantenedores/productos' },
    { title: 'Productos SAG', href: '/mantenedores/productos-sag' },
    { title: 'Proveedores', href: '/mantenedores/proveedores' },
    { title: 'Sectores de Riego', href: '/mantenedores/sectores-riego' },
    { title: 'Tipo Documentos', href: '/mantenedores/tipo-documentos' },
    { title: 'Tarjetas', href: '/mantenedores/tarjetas' },
    { title: 'Tractores', href: '/mantenedores/tractores' },
    { title: 'Tratos', href: '/mantenedores/tratos' },
    { title: 'Unidades', href: '/mantenedores/unidades' },
    { title: 'Usuarios', href: '/mantenedores/usuarios' },
    { title: 'Variedades', href: '/mantenedores/variedades' },
  ] },
  { title: 'Auditoría', icon: <History fontSize="small" color="primary" />, items: [{ title: 'Log de Cambios', href: '/audit/logs' }] },
];

interface Props {
  defaultExpanded?: string[];
}

export default function SidebarNavigation({ defaultExpanded }: Props) {
  const [search, setSearch] = useState('');
  const [expanded, setExpanded] = useState<string[]>(defaultExpanded ?? []);

  const filteredModules = useMemo(() => {
    if (!search.trim()) {
return MODULES;
}

    const q = search.toLowerCase();

    return MODULES
      .map((mod) => ({
        ...mod,
        items: mod.items.filter((item) => item.title.toLowerCase().includes(q)),
      }))
      .filter((mod) => mod.title.toLowerCase().includes(q) || mod.items.length > 0);
  }, [search]);

  const handleAccordionChange = (panel: string) => (_: React.SyntheticEvent, isExpanded: boolean) => {
    setExpanded((prev) =>
      isExpanded ? [...prev, panel] : prev.filter((p) => p !== panel),
    );
  };

  return (
    <Box sx={{ px: 1, py: 1 }}>
      <TextField
        placeholder="Buscar módulo..."
        size="small"
        value={search}
        onChange={(e) => setSearch(e.target.value)}
        slotProps={{
          input: {
            startAdornment: (
              <InputAdornment position="start">
                <Search fontSize="small" color="action" />
              </InputAdornment>
            ),
          },
        }}
        sx={{ mb: 1, '& .MuiInputBase-root': { fontSize: '0.875rem' } }}
      />

      {filteredModules.map((mod) => (
        <Accordion
          key={mod.title}
          expanded={expanded.includes(mod.title)}
          onChange={handleAccordionChange(mod.title)}
          disableGutters
          elevation={0}
          sx={{
            '&:before': { display: 'none' },
            borderBottom: '1px solid',
            borderColor: 'divider',
          }}
        >
          <AccordionSummary expandIcon={<ExpandMore fontSize="small" />} sx={{ minHeight: 40, '& .MuiAccordionSummary-content': { gap: 1, alignItems: 'center' } }}>
            {mod.icon}
            <Typography variant="body2" sx={{ fontWeight: 500 }}>{mod.title}</Typography>
          </AccordionSummary>
          <AccordionDetails sx={{ p: 0, pb: 0.5 }}>
            {mod.items.map((item) => (
              <Link
                key={item.href}
                href={item.href}
                style={{
                  display: 'block',
                  padding: '6px 16px 6px 36px',
                  fontSize: '0.8125rem',
                  color: 'inherit',
                  textDecoration: 'none',
                  borderRadius: 4,
                }}
              >
                {item.title}
              </Link>
            ))}
          </AccordionDetails>
        </Accordion>
      ))}
    </Box>
  );
}
