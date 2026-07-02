import { Head } from '@inertiajs/react';
import { useMemo } from 'react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

interface EspecieItem { id: string; nombre: string }

interface Props {
  items: Record<string, unknown>[];
  especies: EspecieItem[];
}

export default function MantenedorPage({ items, especies }: Props) {
  const fields: MantenedorField[] = useMemo(() => [
    { name: 'nombre', label: 'Nombre', type: 'text', required: true },
    {
      name: 'especie_id',
      label: 'Especie',
      type: 'select',
      required: true,
      options: especies.map((e) => ({ value: e.id, label: e.nombre })),
    },
    { name: 'descripcion', label: 'Descripción', type: 'textarea' },
  ], [especies]);

  const config: MantenedorConfig = useMemo(() => ({
    title: 'Variedades',
    description: 'Variedades de cultivo asociadas a una especie',
    endpoint: '/mantenedores/variedades',
    fields,
    cardTitle: (item: Record<string, unknown>) => item.nombre as string,
    cardSubtitle: (item: Record<string, unknown>) => {
      const especie = (item as any).especie as EspecieItem | null;

      return especie?.nombre ?? (item.descripcion as string) ?? '';
    },
  }), [fields]);

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
    { title: 'Variedades', href: '/mantenedores/variedades' },
  ],
};
