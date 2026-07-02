import { Head } from '@inertiajs/react';
import { useMemo } from 'react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

interface Props {
    items: Record<string, unknown>[];
    especies: Array<{ id: string; nombre: string }>;
}

export default function MantenedorPage({ items, especies }: Props) {
    const fields: MantenedorField[] = useMemo(() => [
        {
            name: 'especie_id',
            label: 'Especie',
            type: 'select',
            options: especies.map((e) => ({ value: e.id, label: e.nombre })),
        },
        { name: 'nombre', label: 'Nombre del contenedor', type: 'text', required: true },
        { name: 'unidades_por_bin', label: 'Unidades por Bin', type: 'number' },
        { name: 'peso_bin_kg', label: 'Peso Bin (Kg)', type: 'number' },
    ], [especies]);

    const config: MantenedorConfig = {
        title: 'Contenedores de Cosecha',
        description: 'Bins, cajas de embalaje y bandejas',
        endpoint: '/mantenedores/contenedores-cosecha',
        fields,
        cardTitle: (item: Record<string, unknown>) => item.nombre as string,
        cardSubtitle: (item: Record<string, unknown>) => {
            const especie = especies.find((e) => e.id === item.especie_id);

            return especie?.nombre ?? '';
        },
        cardMetadata: (item: Record<string, unknown>) => {
            const unidades = (item as any).unidades_por_bin;
            const pesoBin = (item as any).peso_bin_kg;

            if (unidades && pesoBin) {
                const unitario = (Number(pesoBin) / Number(unidades)).toFixed(3);

                return `${unitario} kg/unidad`;
            }

            return pesoBin ? `${pesoBin} kg` : '';
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
        { title: 'Contenedores de Cosecha', href: '/mantenedores/contenedores-cosecha' },
    ],
};
