import { router } from '@inertiajs/react';
import { Delete } from '@mui/icons-material';
import {
    DialogActions,
    Box,
    Button,
    Dialog,
    DialogContent,
    DialogTitle,
    FormControl,
    IconButton,
    InputLabel,
    MenuItem,
    Paper,
    Select,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    TextField,
    Typography,
} from '@mui/material';
import { useCallback, useMemo, useState } from 'react';

interface BodegaOption {
    id: string;
    nombre: string;
}

interface ProductoOption {
    id: string;
    nombre: string;
    unidad: string;
    stockPorBodega: Record<string, number>;
}

interface TransferItem {
    id: string;
    producto_id: string;
    producto: string;
    unidad: string;
    cantidad: number;
}

function generarId() {
    return Math.random().toString(36).slice(2, 9);
}

interface Props {
    open: boolean;
    onClose: () => void;
    bodegas: BodegaOption[];
    productos: ProductoOption[];
}

export default function TransferModal({
    open,
    onClose,
    bodegas,
    productos,
}: Props) {
    const [origen, setOrigen] = useState('');
    const [destino, setDestino] = useState('');
    const [fecha, setFecha] = useState(new Date().toISOString().slice(0, 10));
    const [descripcion, setDescripcion] = useState('');

    const [producto, setProducto] = useState('');
    const [cantidad, setCantidad] = useState('');

    const [items, setItems] = useState<TransferItem[]>([]);

    const resetForm = useCallback(() => {
        setOrigen('');
        setDestino('');
        setFecha(new Date().toISOString().slice(0, 10));
        setDescripcion('');
        setProducto('');
        setCantidad('');
        setItems([]);
    }, []);

    const handleOrigenChange = (value: string) => {
        setOrigen(value);
        setProducto('');
        setCantidad('');
        setItems([]);

        if (destino === value) {
            setDestino('');
        }
    };

    const handleClose = useCallback(() => {
        resetForm();
        onClose();
    }, [resetForm, onClose]);

    const productosFiltrados = useMemo(() => {
        if (!origen) {
            return [];
        }

        return productos.filter((p) => (p.stockPorBodega[origen] ?? 0) > 0);
    }, [productos, origen]);

    const productoSeleccionado = productos.find((p) => p.id === producto);
    const stockDisponible =
        origen && productoSeleccionado
            ? (productoSeleccionado.stockPorBodega[origen] ?? 0)
            : 0;

    const errorStock =
        cantidad && productoSeleccionado
            ? parseFloat(cantidad) > stockDisponible
            : false;

    const handleAgregar = useCallback(() => {
        if (!producto || !cantidad || errorStock) {
            return;
        }

        const selectedProduct = productos.find((p) => p.id === producto);

        if (!selectedProduct) {
            return;
        }

        const item: TransferItem = {
            id: generarId(),
            producto_id: selectedProduct.id,
            producto: selectedProduct.nombre,
            unidad: selectedProduct.unidad,
            cantidad: parseFloat(cantidad),
        };
        setItems((prev) => [...prev, item]);
        setProducto('');
        setCantidad('');
    }, [producto, cantidad, productos, errorStock]);

    const handleEliminar = (id: string) => {
        setItems((prev) => prev.filter((i) => i.id !== id));
    };

    const handleAceptar = () => {
        const lineas = items.map((item) => ({
            producto_id: item.producto_id,
            cantidad: item.cantidad,
        }));

        router.post(
            '/bodegaje/warehouse-transfers',
            {
                bodega_origen_id: origen,
                bodega_destino_id: destino,
                fecha_emision: fecha,
                descripcion,
                lineas,
            },
            {
                preserveScroll: true,
                onSuccess: () => handleClose(),
            },
        );
    };

    return (
        <Dialog open={open} onClose={handleClose} maxWidth="sm" fullWidth>
            <DialogTitle sx={{ fontWeight: 600 }}>
                Nuevo traspaso entre bodegas
            </DialogTitle>
            <DialogContent dividers>
                <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                    <TextField
                        label="Número de traspaso"
                        size="small"
                        value="Asignado automáticamente"
                        disabled
                    />
                    <FormControl size="small">
                        <InputLabel>Bodega de origen</InputLabel>
                        <Select
                            value={origen}
                            label="Bodega de origen"
                            onChange={(e) => handleOrigenChange(e.target.value)}
                        >
                            <MenuItem value="">
                                <em>Seleccionar...</em>
                            </MenuItem>
                            {bodegas.map((b) => (
                                <MenuItem key={b.id} value={b.id}>
                                    {b.nombre}
                                </MenuItem>
                            ))}
                        </Select>
                    </FormControl>
                    <FormControl size="small">
                        <InputLabel>Bodega de destino</InputLabel>
                        <Select
                            value={destino}
                            label="Bodega de destino"
                            onChange={(e) => setDestino(e.target.value)}
                        >
                            <MenuItem value="">
                                <em>Seleccionar...</em>
                            </MenuItem>
                            {bodegas.map((b) => (
                                <MenuItem
                                    key={b.id}
                                    value={b.id}
                                    disabled={b.id === origen}
                                >
                                    {b.nombre}
                                </MenuItem>
                            ))}
                        </Select>
                    </FormControl>
                    <TextField
                        label="Fecha"
                        type="date"
                        size="small"
                        value={fecha}
                        onChange={(e) => setFecha(e.target.value)}
                        slotProps={{ inputLabel: { shrink: true } }}
                    />
                    <TextField
                        label="Descripción"
                        size="small"
                        value={descripcion}
                        onChange={(e) => setDescripcion(e.target.value)}
                        multiline
                        rows={2}
                    />

                    <Typography
                        variant="subtitle2"
                        sx={{ fontSize: '0.8125rem', fontWeight: 600 }}
                    >
                        Agregar producto
                    </Typography>
                    <Box
                        sx={{
                            display: 'flex',
                            gap: 1.5,
                            flexWrap: 'wrap',
                            alignItems: 'center',
                        }}
                    >
                        <FormControl
                            size="small"
                            sx={{ minWidth: 200, flex: 1 }}
                            disabled={!origen}
                        >
                            <InputLabel>Producto</InputLabel>
                            <Select
                                value={producto}
                                label="Producto"
                                onChange={(e) => setProducto(e.target.value)}
                            >
                                {productosFiltrados.map((p) => (
                                    <MenuItem key={p.id} value={p.id}>
                                        {p.nombre} ({p.unidad}) — Stock:{' '}
                                        {p.stockPorBodega[origen] ?? 0}
                                    </MenuItem>
                                ))}
                                {origen && productosFiltrados.length === 0 && (
                                    <MenuItem disabled value="">
                                        <em>
                                            Sin productos con stock en la bodega
                                            origen
                                        </em>
                                    </MenuItem>
                                )}
                            </Select>
                        </FormControl>
                        <TextField
                            label="Cantidad"
                            type="number"
                            size="small"
                            value={cantidad}
                            onChange={(e) => setCantidad(e.target.value)}
                            error={errorStock}
                            helperText={
                                errorStock
                                    ? `Máx: ${stockDisponible}`
                                    : undefined
                            }
                            sx={{ maxWidth: 120 }}
                        />
                        <Button
                            variant="contained"
                            size="small"
                            onClick={handleAgregar}
                            disabled={!producto || !cantidad || errorStock}
                        >
                            Agregar
                        </Button>
                    </Box>

                    {items.length > 0 && (
                        <TableContainer component={Paper} variant="outlined">
                            <Table size="small">
                                <TableHead>
                                    <TableRow>
                                        <TableCell
                                            sx={{
                                                fontWeight: 600,
                                                fontSize: '0.8rem',
                                            }}
                                        >
                                            Producto
                                        </TableCell>
                                        <TableCell
                                            sx={{
                                                fontWeight: 600,
                                                fontSize: '0.8rem',
                                            }}
                                            align="right"
                                        >
                                            Cantidad
                                        </TableCell>
                                        <TableCell
                                            sx={{
                                                fontWeight: 600,
                                                fontSize: '0.8rem',
                                            }}
                                            align="center"
                                        >
                                            Unidad
                                        </TableCell>
                                        <TableCell
                                            sx={{
                                                fontWeight: 600,
                                                fontSize: '0.8rem',
                                            }}
                                            align="center"
                                        ></TableCell>
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {items.map((item) => (
                                        <TableRow key={item.id}>
                                            <TableCell
                                                sx={{ fontSize: '0.8125rem' }}
                                            >
                                                {item.producto}
                                            </TableCell>
                                            <TableCell
                                                sx={{ fontSize: '0.8125rem' }}
                                                align="right"
                                            >
                                                {item.cantidad}
                                            </TableCell>
                                            <TableCell
                                                sx={{ fontSize: '0.8125rem' }}
                                                align="center"
                                            >
                                                {item.unidad}
                                            </TableCell>
                                            <TableCell align="center">
                                                <IconButton
                                                    size="small"
                                                    onClick={() =>
                                                        handleEliminar(item.id)
                                                    }
                                                    color="error"
                                                >
                                                    <Delete fontSize="small" />
                                                </IconButton>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </TableContainer>
                    )}
                </Box>
            </DialogContent>
            <DialogActions>
                <Button onClick={handleClose} color="inherit">
                    Cancelar
                </Button>
                <Button
                    onClick={handleAceptar}
                    variant="contained"
                    disabled={items.length === 0 || !origen || !destino}
                >
                    Aceptar
                </Button>
            </DialogActions>
        </Dialog>
    );
}
