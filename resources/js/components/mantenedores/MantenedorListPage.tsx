import { router } from '@inertiajs/react';
import { CloudUpload } from '@mui/icons-material';
import { Box, Stack, Typography, Button } from '@mui/material';
import { useState, useMemo } from 'react';
import type { MantenedorConfig } from './mantenedor-types';
import MantenedorBar from './MantenedorBar';
import MantenedorBatchToolbar from './MantenedorBatchToolbar';
import MantenedorCard from './MantenedorCard';
import MantenedorFab from './MantenedorFab';
import MantenedorFilterBar from './MantenedorFilterBar';
import MantenedorFormModal from './MantenedorFormModal';
import MantenedorImportModal from './MantenedorImportModal';

interface Props {
    config: MantenedorConfig;
    items: Record<string, unknown>[];
    searchPlaceholder?: string;
}

export default function MantenedorListPage({ config, items, searchPlaceholder }: Props) {
    const [search, setSearch] = useState('');
    const [filter, setFilter] = useState<'activos' | 'todos'>('activos');
    const [modalOpen, setModalOpen] = useState(false);
    const [editingItem, setEditingItem] = useState<Record<string, unknown> | null>(null);
    const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());
    const [filters, setFilters] = useState<Record<string, unknown>>({});
    const [importOpen, setImportOpen] = useState(false);

    const filtered = useMemo(() => {
        let result = items;
        const q = search.toLowerCase().trim();

        if (q) {
            result = result.filter((item) =>
                Object.values(item).some(
                    (v) => typeof v === 'string' && v.toLowerCase().includes(q),
                ),
            );
        }

        if (filter === 'activos' && config.hasEstado !== false) {
            result = result.filter((item) => (item.activo ?? item.estado ?? true) !== false);
        }

        // Apply dynamic field filters
        for (const [key, value] of Object.entries(filters)) {
            if (value === '' || value === null || value === undefined) {
continue;
}

            result = result.filter((item) => {
                if (typeof value === 'object' && 'min' in (value as object)) {
                    const range = value as { min?: number | string; max?: number | string };
                    const itemVal = item[key];

                    if (range.min !== undefined) {
                        const min = typeof range.min === 'string' ? new Date(range.min).getTime() : Number(range.min);
                        const val = typeof itemVal === 'string' ? new Date(itemVal).getTime() : Number(itemVal);

                        if (val < min) {
return false;
}
                    }

                    if (range.max !== undefined) {
                        const max = typeof range.max === 'string' ? new Date(range.max).getTime() : Number(range.max);
                        const val = typeof itemVal === 'string' ? new Date(itemVal).getTime() : Number(itemVal);

                        if (val > max) {
return false;
}
                    }

                    return true;
                }

                if (typeof value === 'string') {
                    return String(item[key] ?? '').toLowerCase().includes(value.toLowerCase());
                }

                return item[key] === value;
            });

            if (result.length === 0) {
break;
}
        }

        return result;
    }, [items, search, filter, filters, config.hasEstado]);

    const handleEdit = (item: Record<string, unknown>) => {
        setEditingItem(item);
        setModalOpen(true);
    };

    const handleCreate = () => {
        setEditingItem(null);
        setModalOpen(true);
    };

    const handleDelete = (item: Record<string, unknown>) => {
        if (confirm(`¿Eliminar ${config.cardTitle(item)}?`)) {
            router.delete(`${config.endpoint}/${item.id}`, {
                preserveScroll: true,
                onSuccess: () => router.reload(),
            });
        }
    };

    const handleToggleStatus = (item: Record<string, unknown>) => {
        router.patch(`${config.endpoint}/${item.id}/toggle-status`, {}, {
            preserveScroll: true,
            onSuccess: () => router.reload(),
        });
    };

    const handleToggleSelect = (id: string) => {
        setSelectedIds((prev) => {
            const next = new Set(prev);

            if (next.has(id)) {
next.delete(id);
} else {
next.add(id);
}

            return next;
        });
    };

    const handleSelectAll = () => {
        setSelectedIds(new Set(filtered.map((item) => item.id as string)));
    };

    const handleClearSelection = () => setSelectedIds(new Set());

    const handleDeleteSelected = () => {
        if (selectedIds.size === 0) {
return;
}

        if (confirm(`¿Eliminar ${selectedIds.size} registro(s)?`)) {
            router.delete(`${config.endpoint}/batch`, {
                data: { ids: Array.from(selectedIds) },
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => setSelectedIds(new Set()),
            });
        }
    };

    const handleClose = () => {
        setModalOpen(false);
        setEditingItem(null);
    };

    return (
        <Box>
            <MantenedorBar
                title={config.title}
                description={config.description}
                searchValue={search}
                onSearchChange={setSearch}
                filterValue={filter}
                onFilterChange={setFilter}
                searchPlaceholder={searchPlaceholder}
                action={
                    <Button
                        size="small"
                        startIcon={<CloudUpload />}
                        onClick={() => setImportOpen(true)}
                    >
                        Importar
                    </Button>
                }
            />

            <MantenedorFilterBar
                fields={config.fields}
                items={items}
                filters={filters}
                onFiltersChange={setFilters}
            />

            {filtered.length === 0 ? (
                <Box
                    sx={{
                        textAlign: 'center',
                        py: 8,
                        color: 'text.secondary',
                    }}
                >
                    <Typography variant="body1" sx={{ mb: 1 }}>
                        {search || filter === 'activos'
                            ? 'No se encontraron elementos con los filtros actuales.'
                            : `No hay ${config.title.toLowerCase()} registrados.`}
                    </Typography>
                    {!search && filter === 'todos' && (
                        <Button variant="outlined" size="small" onClick={handleCreate}>
                            Crear primero
                        </Button>
                    )}
                </Box>
            ) : (
                <Stack spacing={1} sx={{ mt: 2 }}>
                    {filtered.map((item, i) => (
                        <MantenedorCard
                            key={String((item as any).id ?? i)}
                            item={item}
                            config={config}
                            selected={selectedIds.has((item as any).id as string)}
                            onToggleSelect={() => handleToggleSelect((item as any).id as string)}
                            onEdit={handleEdit}
                            onToggleStatus={handleToggleStatus}
                            onDelete={handleDelete}
                        />
                    ))}
                </Stack>
            )}

            {selectedIds.size > 0 && (
                <MantenedorBatchToolbar
                    selectedCount={selectedIds.size}
                    totalFilteredCount={filtered.length}
                    onSelectAll={handleSelectAll}
                    onDeleteSelected={handleDeleteSelected}
                    onClearSelection={handleClearSelection}
                />
            )}

            <MantenedorFab onClick={handleCreate} />

            <MantenedorFormModal
                open={modalOpen}
                onClose={handleClose}
                config={config}
                item={editingItem}
            />

            <MantenedorImportModal
                open={importOpen}
                onClose={() => setImportOpen(false)}
                entityEndpoint={config.endpoint}
                entityTitle={config.title}
            />
        </Box>
    );
}
