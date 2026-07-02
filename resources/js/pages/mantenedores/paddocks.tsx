import { Head } from '@inertiajs/react';
import { Add, Delete, QrCode } from '@mui/icons-material';
import {
    Box,
    Stack,
    IconButton,
    Button,
    Typography,
    Select,
    MenuItem,
    TextField,
    FormHelperText,
    FormControl,
    InputLabel,
} from '@mui/material';
import { useMemo, useState, useCallback } from 'react';
import type {
    MantenedorConfig,
    MantenedorField,
} from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';
import QrCodePrint from '@/components/QrCodePrint';

interface VariedadRow {
    variedad_id: string;
    cantidad_plantas: number | string;
}

interface Props {
    items: Record<string, unknown>[];
    centroCostos: Array<{ id: string; nombre: string }>;
    especies: Array<{ id: string; nombre: string }>;
    variedades: Array<{ id: string; nombre: string; especie_id: string }>;
}

function VariedadesForm({
    formData,
    onChange,
    variedades,
}: {
    formData: Record<string, string | number | boolean>;
    onChange: (name: string, value: string | number | boolean) => void;
    variedades: Array<{ id: string; nombre: string; especie_id: string }>;
}) {
    const list: VariedadRow[] = useMemo(() => {
        try {
            const raw = formData._variedades;

            if (typeof raw === 'string') {
                return JSON.parse(raw);
            }

            return [];
        } catch {
            return [];
        }
    }, [formData._variedades]);

    const especieId = formData.especie_id as string;

    const filteredVariedades = variedades.filter(
        (v) => v.especie_id === especieId,
    );

    const update = (rows: VariedadRow[]) => {
        onChange('_variedades', JSON.stringify(rows));
    };

    const addRow = () => {
        update([...list, { variedad_id: '', cantidad_plantas: '' }]);
    };

    const removeRow = (index: number) => {
        update(list.filter((_, i) => i !== index));
    };

    const changeRow = (
        index: number,
        field: keyof VariedadRow,
        value: string,
    ) => {
        const rows = [...list];
        (rows as any)[index][field] =
            field === 'cantidad_plantas'
                ? value === ''
                    ? ''
                    : Number(value)
                : value;
        update(rows);
    };

    return (
        <Box sx={{ mt: 2 }}>
            <Typography variant="subtitle2" sx={{ mb: 1, fontWeight: 600 }}>
                Variedades
            </Typography>
            {!especieId && (
                <FormHelperText error>
                    Selecciona una especie primero para ver las variedades
                    disponibles.
                </FormHelperText>
            )}
            <Stack spacing={1.5}>
                {list.map((row, i) => (
                    <Stack
                        key={i}
                        direction="row"
                        spacing={1}
                        sx={{ alignItems: 'center' }}
                    >
                        <FormControl size="small" sx={{ minWidth: 200 }}>
                            <InputLabel>Variedad</InputLabel>
                            <Select
                                value={row.variedad_id}
                                label="Variedad"
                                onChange={(e) =>
                                    changeRow(i, 'variedad_id', e.target.value)
                                }
                            >
                                <MenuItem value="">
                                    <em>Seleccionar...</em>
                                </MenuItem>
                                {filteredVariedades.map((v) => (
                                    <MenuItem key={v.id} value={v.id}>
                                        {v.nombre}
                                    </MenuItem>
                                ))}
                            </Select>
                        </FormControl>
                        <TextField
                            size="small"
                            label="Cant. Plantas"
                            type="number"
                            value={row.cantidad_plantas}
                            onChange={(e) =>
                                changeRow(i, 'cantidad_plantas', e.target.value)
                            }
                            sx={{ width: 140 }}
                        />
                        <IconButton
                            size="small"
                            color="error"
                            onClick={() => removeRow(i)}
                        >
                            <Delete fontSize="small" />
                        </IconButton>
                    </Stack>
                ))}
            </Stack>
            <Button
                size="small"
                startIcon={<Add />}
                onClick={addRow}
                sx={{ mt: 1 }}
                disabled={!especieId}
            >
                Agregar variedad
            </Button>
        </Box>
    );
}

export default function MantenedorPage({
    items,
    centroCostos,
    especies,
    variedades,
}: Props) {
    const [qrCuartel, setQrCuartel] = useState<Record<string, unknown> | null>(
        null,
    );

    const fields: MantenedorField[] = useMemo(
        () => [
            { name: 'nombre', label: 'Nombre', type: 'text', required: true },
            {
                name: 'centro_costo_id',
                label: 'Centro de Costo',
                type: 'select',
                required: true,
                options: centroCostos.map((cc) => ({
                    value: cc.id,
                    label: cc.nombre,
                })),
            },
            {
                name: 'especie_id',
                label: 'Especie',
                type: 'select',
                options: especies.map((e) => ({
                    value: e.id,
                    label: e.nombre,
                })),
            },
            {
                name: 'superficie_hectareas',
                label: 'Superficie (ha)',
                type: 'number',
                required: true,
            },
            {
                name: 'ano_plantacion',
                label: 'Año Plantación',
                type: 'number',
                required: true,
            },
            {
                name: 'distancia_sobre_hilera',
                label: 'Dist. Sobre Hilera (m)',
                type: 'number',
                required: true,
            },
            {
                name: 'distancia_intra_hilera',
                label: 'Dist. Intra Hilera (m)',
                type: 'number',
                required: true,
            },
        ],
        [centroCostos, especies],
    );

    const renderFormExtra = useCallback(
        ({ formData, onChange }: any) => (
            <VariedadesForm
                formData={formData}
                onChange={onChange}
                variedades={variedades}
            />
        ),
        [variedades],
    );

    const config: MantenedorConfig = {
        title: 'Cuarteles',
        description: 'Administración de parcelas y bloques productivos',
        endpoint: '/mantenedores/cuarteles',
        fields,
        renderFormExtra,
        cardTitle: (item: Record<string, unknown>) => item.nombre as string,
        cardSubtitle: (item: Record<string, unknown>) => {
            const especie = especies.find((e) => e.id === item.especie_id);

            return especie?.nombre ?? '';
        },
        cardMetadata: (item: Record<string, unknown>) => {
            const v = (item as any).variedades;

            if (Array.isArray(v) && v.length > 0) {
                const total = v.reduce(
                    (s: number, r: any) => s + (r.pivot?.cantidad_plantas ?? 0),
                    0,
                );

                return `${total} plantas`;
            }

            return `${item.superficie_hectareas ?? '?'} ha`;
        },
        actions: [
            {
                label: 'Código QR',
                icon: <QrCode fontSize="small" />,
                onClick: (item: Record<string, unknown>) => setQrCuartel(item),
            },
        ],
    };

    return (
        <>
            <Head title={config.title} />
            <MantenedorListPage config={config} items={items} />
            <QrCodePrint
                open={!!qrCuartel}
                cuartel={
                    qrCuartel
                        ? {
                              id: qrCuartel.id as string,
                              nombre: qrCuartel.nombre as string,
                              superficie_hectareas:
                                  qrCuartel.superficie_hectareas as
                                      | number
                                      | undefined,
                              variedades: (qrCuartel as any).variedades as
                                  | any[]
                                  | undefined,
                          }
                        : null
                }
                onClose={() => setQrCuartel(null)}
            />
        </>
    );
}

MantenedorPage.layout = {
    breadcrumbs: [
        { title: 'Mantenedores', href: '/mantenedores' },
        { title: 'Cuarteles', href: '/mantenedores/cuarteles' },
    ],
};
