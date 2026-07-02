import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

const fields: MantenedorField[] = [
  { name: 'nombres', label: 'Nombres', type: 'text', required: true },
  { name: 'apellidos', label: 'Apellidos', type: 'text', required: true },
  { name: 'rut', label: 'RUT', type: 'rut', required: true },
  { name: 'fecha_nacimiento', label: 'Fecha Nacimiento', type: 'date' },
  { name: 'capacitado', label: 'Capacitado', type: 'switch' },
  { name: 'certificado_url', label: 'URL Certificado', type: 'text' },
  { name: 'activo', label: 'Activo', type: 'switch' },
];

const config: MantenedorConfig = {
  title: 'Aplicadores',
  description: 'Operadores capacitados para aplicar agroquímicos',
  endpoint: '/mantenedores/aplicadores',
  fields,
  cardTitle: (item: Record<string, unknown>) => `${item.nombres ?? ''} ${item.apellidos ?? ''}`,
  cardSubtitle: (item: Record<string, unknown>) => item.rut as string,
  cardMetadata: (item: Record<string, unknown>) => item.capacitado ? '✓ Capacitado' : '✗ Sin capacitación',
};

interface Props {
  items: Record<string, unknown>[];
}

export default function AplicadoresPage({ items }: Props) {
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

AplicadoresPage.layout = {
  breadcrumbs: [
    { title: 'Mantenedores', href: '/mantenedores' },
    { title: 'Aplicadores', href: '/mantenedores/aplicadores' },
  ],
};
