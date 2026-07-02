import { Head, usePage } from '@inertiajs/react';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

export default function SimpleMantenedorPage() {
  const { items, pageTitle, pageDescription, entityName, endpoint, entityFields } = usePage().props as any;

  const config = {
    title: pageTitle,
    description: pageDescription,
    endpoint: endpoint,
    fields: entityFields && entityFields.length > 0
      ? entityFields
      : [
          { name: 'codigo', label: 'Código', type: 'text' as const, xs: 12, sm: 6 },
          { name: 'nombre', label: 'Nombre', type: 'text' as const, required: true },
        ],
    cardTitle: (item: any) => item.nombre || item.razon_social || item.codigo,
    cardSubtitle: (item: any) => item.codigo,
    hasEstado: true,
  };

  return (
    <>
      <Head title={pageTitle} />
      <MantenedorListPage config={config} items={items} />
    </>
  );
}

SimpleMantenedorPage.layout = {
  breadcrumbs: [
    { title: 'Mantenedores', href: '/mantenedores' },
    { title: '...', href: '#' },
  ],
};
