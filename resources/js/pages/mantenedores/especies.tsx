import { Head } from '@inertiajs/react';
import { useMemo } from 'react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

interface Props {
  items: Record<string, unknown>[];
  familias: Array<{ id: string; nombre: string }>;
}

export default function MantenedorPage({ items, familias }: Props) {
  const fields: MantenedorField[] = useMemo(() => [
    { name: 'nombre', label: 'Nombre', type: 'text', required: true },
    {
      name: 'familia_id',
      label: 'Familia',
      type: 'select',
      options: familias.map((f) => ({ value: f.id, label: f.nombre })),
    },
    { name: 'descripcion', label: 'Descripción', type: 'textarea' },
  ], [familias]);

  const config: MantenedorConfig = {
    title: 'Especies',
    description: 'Especies botánicas asociadas a una familia',
    endpoint: '/mantenedores/especies',
    fields,
    cardTitle: (item: Record<string, unknown>) => item.nombre as string,
    cardSubtitle: (item: Record<string, unknown>) => item.descripcion as string,
    cardMetadata: (item: Record<string, unknown>) => {
      const familia = familias.find((f) => f.id === item.familia_id);

      return familia?.nombre ?? '';
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
    { title: 'Especies', href: '/mantenedores/especies' },
  ],
};
