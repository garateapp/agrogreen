import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

const fields: MantenedorField[] = [
  { name: 'nombre', label: 'Nombre', type: 'text', required: true },
  { name: 'codigo', label: 'Código', type: 'text', xs: 12, sm: 6 },
  {
    name: 'tipo_trato_id',
    label: 'Tipo de trato',
    type: 'select',
    options: [
      { value: '1', label: 'Por monto' },
      { value: '2', label: 'Por cajas' },
    ],
  },
  {
    name: 'unidad_id',
    label: 'Unidad',
    type: 'select',
    options: [
      { value: '1', label: 'Kilos' },
      { value: '2', label: 'Bandejas' },
    ],
  },
  { name: 'no_agrupar_actividad', label: 'No agrupar por actividad', type: 'switch' },
  { name: 'depende_jornada', label: 'Depende de la jornada', type: 'switch' },
  { name: 'sustraer_trato_base', label: 'Sustraer trato base', type: 'switch' },
  { name: 'bonificacion', label: 'Bonificación', type: 'switch' },
  { name: 'hora_extra', label: 'Hora Extra', type: 'switch' },
  { name: 'no_enviar_integraciones', label: 'No enviar a integraciones', type: 'switch' },
  { name: 'integracion', label: 'Códigos de integración', type: 'switch' },
];

const config: MantenedorConfig = {
  title: 'Tratos',
  description: 'Configuración de tarifas y bonos por rendimiento',
  endpoint: '/mantenedores/tratos',
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
    { title: 'Tratos', href: '/mantenedores/tratos' },
  ],
};
