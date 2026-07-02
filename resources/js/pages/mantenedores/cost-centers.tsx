import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

const fields: MantenedorField[] = [
  { name: 'codigo', label: 'Código', type: 'text', required: true, xs: 12, sm: 6 },
  { name: 'nombre', label: 'Nombre', type: 'text', required: true },
  { name: 'agrupador', label: 'Agrupador', type: 'text', xs: 12, sm: 6 },
];

const config: MantenedorConfig = {
  title: 'Centros de Costo',
  description: 'Unidades financieras para acumulación de egresos',
  endpoint: '/mantenedores/centros-costo',
  fields,
  cardTitle: (item: Record<string, unknown>) => item.nombre as string,
  cardSubtitle: (item: Record<string, unknown>) => item.codigo as string,
};

interface Props {
  items: Record<string, unknown>[];
}

export default function MantenedorPage({ items }: Props) {
  const handleDelete = (item: Record<string, unknown>) => {
    if (confirm(`¿Eliminar ${config.cardTitle(item)}?`)) {
      router.delete(`${config.endpoint}/${item.id}`, { preserveScroll: true, onSuccess: () => router.reload() });
    }
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
    { title: 'Centros de Costo', href: '/mantenedores/centros-costo' },
  ],
};
