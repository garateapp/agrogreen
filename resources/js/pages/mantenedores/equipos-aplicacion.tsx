import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

const fields: MantenedorField[] = [
  { name: 'nombre', label: 'Nombre', type: 'text', required: true },
  {
    name: 'tipo',
    label: 'Tipo',
    type: 'select',
    required: true,
    options: [
      { value: 'mochila', label: 'Mochila' },
      { value: 'nebulizadora', label: 'Nebulizadora' },
      { value: 'pulverizadora', label: 'Pulverizadora' },
      { value: 'dron', label: 'Dron' },
      { value: 'avion', label: 'Avión' },
      { value: 'otro', label: 'Otro' },
    ],
  },
  { name: 'ultima_calibracion', label: 'Última Calibración', type: 'date' },
  { name: 'proxima_calibracion', label: 'Próxima Calibración', type: 'date' },
  { name: 'ultima_mantencion', label: 'Última Mantención', type: 'date' },
  { name: 'proxima_mantencion', label: 'Próxima Mantención', type: 'date' },
  { name: 'activo', label: 'Activo', type: 'switch' },
];

const config: MantenedorConfig = {
  title: 'Equipos de Aplicación',
  description: 'Equipos para aplicación de agroquímicos',
  endpoint: '/mantenedores/equipos-aplicacion',
  fields,
  cardTitle: (item: Record<string, unknown>) => item.nombre as string,
  cardSubtitle: (item: Record<string, unknown>) => item.tipo as string,
};

interface Props {
  items: Record<string, unknown>[];
}

export default function EquiposAplicacionPage({ items }: Props) {
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

EquiposAplicacionPage.layout = {
  breadcrumbs: [
    { title: 'Mantenedores', href: '/mantenedores' },
    { title: 'Equipos de Aplicación', href: '/mantenedores/equipos-aplicacion' },
  ],
};
