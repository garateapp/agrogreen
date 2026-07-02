import { Add } from '@mui/icons-material';
import { Box, Button, Typography } from '@mui/material';
import { useMemo, useState } from 'react';
import type { Column } from '@/components/bodegaje/bodegaje-types';
import BodegajeDataTable from '@/components/bodegaje/BodegajeDataTable';
import BodegajeFilterBar from '@/components/bodegaje/BodegajeFilterBar';
import TransferModal from '@/components/bodegaje/TransferModal';

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

interface TransferItem extends Record<string, unknown> {
    id: string;
    folio: string;
    origen: string;
    origen_id: string | null;
    destino: string;
    destino_id: string | null;
    fecha: string;
}

interface Props {
    items: TransferItem[];
    bodegas: BodegaOption[];
    productos: ProductoOption[];
}

const columns: Column[] = [
    { key: 'folio', label: '#', width: 80 },
    { key: 'origen', label: 'Origen' },
    { key: 'destino', label: 'Destino' },
    { key: 'fecha', label: 'Fecha', width: 120 },
];

export default function WarehouseTransfers({
    items,
    bodegas,
    productos,
}: Props) {
    const [search, setSearch] = useState('');
    const [selectValues, setSelectValues] = useState<Record<string, string>>(
        {},
    );
    const [dateFrom, setDateFrom] = useState('');
    const [dateTo, setDateTo] = useState('');
    const [modalOpen, setModalOpen] = useState(false);

    const selectFilters = useMemo(
        () => [
            {
                key: 'bodega',
                label: 'Bodega',
                options: bodegas.map((bodega) => ({
                    value: bodega.id,
                    label: bodega.nombre,
                })),
            },
        ],
        [bodegas],
    );

    const filteredItems = useMemo(() => {
        const s = search.toLowerCase();
        const selectedBodega = selectValues.bodega ?? '';

        return items.filter((item) => {
            if (
                selectedBodega &&
                item.origen_id !== selectedBodega &&
                item.destino_id !== selectedBodega
            ) {
                return false;
            }

            if (dateFrom && item.fecha < dateFrom) {
                return false;
            }

            if (dateTo && item.fecha > dateTo) {
                return false;
            }

            if (
                s &&
                !item.folio.toLowerCase().includes(s) &&
                !item.origen.toLowerCase().includes(s) &&
                !item.destino.toLowerCase().includes(s)
            ) {
                return false;
            }

            return true;
        });
    }, [items, search, selectValues, dateFrom, dateTo]);

    return (
        <Box>
            <Typography variant="h5" sx={{ mb: 1 }}>
                Traspaso entre Bodegas
            </Typography>
            <BodegajeFilterBar
                searchValue={search}
                onSearchChange={setSearch}
                onSearch={() => {}}
                onClear={() => {
                    setSearch('');
                    setSelectValues({});
                    setDateFrom('');
                    setDateTo('');
                }}
                selects={selectFilters}
                selectValues={selectValues}
                onSelectChange={(k, v) =>
                    setSelectValues((prev) => ({ ...prev, [k]: v }))
                }
                showDateRange
                dateFrom={dateFrom}
                dateTo={dateTo}
                onDateFromChange={setDateFrom}
                onDateToChange={setDateTo}
                actions={
                    <Button
                        variant="contained"
                        size="small"
                        startIcon={<Add />}
                        onClick={() => setModalOpen(true)}
                    >
                        Nuevo traspaso
                    </Button>
                }
            />
            <BodegajeDataTable columns={columns} items={filteredItems} />
            <TransferModal
                open={modalOpen}
                onClose={() => setModalOpen(false)}
                bodegas={bodegas}
                productos={productos}
            />
        </Box>
    );
}
