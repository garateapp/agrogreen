import { Head } from '@inertiajs/react';
import { useMemo } from 'react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

interface Props {
    items: Record<string, unknown>[];
    unidades: Array<{ id: string; nombre: string }>;
    itemsGasto: Array<{ id: string; nombre: string }>;
}

export default function MantenedorPage({ items, unidades, itemsGasto }: Props) {
    const fields: MantenedorField[] = useMemo(() => [
        { name: 'nombre', label: 'Nombre', type: 'text', required: true },
        { name: 'codigo', label: 'Código', type: 'text', xs: 12, sm: 6 },
        {
            name: 'tipo_labor',
            label: 'Tipo de Labor',
            type: 'select',
            required: true,
            options: [
                { value: 'dia', label: 'Día' },
                { value: 'trato', label: 'Trato' },
            ],
        },
        {
            name: 'unidad_medida_id',
            label: 'Unidad de Medida',
            type: 'select',
            options: unidades.map((u) => ({ value: u.id, label: u.nombre })),
        },
        { name: 'valor', label: 'Valor', type: 'number', xs: 12, sm: 6 },
        {
            name: 'item_gasto_id',
            label: 'Item de Gasto',
            type: 'select',
            options: itemsGasto.map((ig) => ({ value: ig.id, label: ig.nombre })),
        },
        { name: 'requiere_maquinaria', label: 'Requiere Maquinaria', type: 'switch' },
        { name: 'presupuestable', label: 'Presupuestable', type: 'switch' },
    ], [unidades, itemsGasto]);

    const config: MantenedorConfig = {
        title: 'Actividades',
        description: 'Tareas realizables en faenas agrícolas',
        endpoint: '/mantenedores/actividades',
        fields,
        cardTitle: (item: Record<string, unknown>) => item.nombre as string,
        cardSubtitle: (item: Record<string, unknown>) => {
            const tipo = item.tipo_labor === 'dia' ? 'Día' : 'Trato';

            return `${tipo} - $${item.valor ?? '0'}`;
        },
    };

    return (
        <>
            <Head title={config.title} />
            <MantenedorListPage config={config} items={items} />
        </>
    );
}

MantenedorPage.layout = {
    breadcrumbs: [
        { title: 'Mantenedores', href: '/mantenedores' },
        { title: 'Actividades', href: '/mantenedores/actividades' },
    ],
};
