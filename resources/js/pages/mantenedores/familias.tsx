import { Head } from '@inertiajs/react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

const fields: MantenedorField[] = [
  { name: 'nombre', label: 'Nombre', type: 'text', required: true },
  { name: 'descripcion', label: 'Descripción', type: 'textarea' },
];

const config: MantenedorConfig = {
  title: 'Familias',
  description: 'Clasificación botánica de cultivos',
  endpoint: '/mantenedores/familias',
  fields,
  cardTitle: (item: Record<string, unknown>) => item.nombre as string,
  cardSubtitle: (item: Record<string, unknown>) => item.descripcion as string,
};

interface Props {
  items: Record<string, unknown>[];
}

export default function MantenedorPage({ items }: Props) {
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
    { title: 'Familias', href: '/mantenedores/familias' },
  ],
};
