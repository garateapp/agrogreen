import { Head } from '@inertiajs/react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

const TIPOS = [
  { value: 'tractor', label: 'Tractor' },
  { value: 'nebulizadora', label: 'Nebulizadora' },
  { value: 'rastra', label: 'Rastra' },
  { value: 'vehiculo_carga', label: 'Vehículo de carga' },
];

const fields: MantenedorField[] = [
  { name: 'nombre', label: 'Nombre', type: 'text', required: true },
  { name: 'patente_o_identificador', label: 'Patente / Identificador', type: 'text' },
  { name: 'tipo', label: 'Tipo', type: 'select', required: true, options: TIPOS },
  { name: 'horas_motor_iniciales', label: 'Horas motor iniciales', type: 'number' },
  { name: 'consumo_estimado_combustible_hora', label: 'Consumo est. combustible (L/h)', type: 'number' },
];

const config: MantenedorConfig = {
  title: 'Tractores',
  description: 'Tractores, maquinaria y vehículos agrícolas',
  endpoint: '/mantenedores/tractores',
  fields,
  cardTitle: (item: Record<string, unknown>) => item.nombre as string,
  cardSubtitle: (item: Record<string, unknown>) => item.patente_o_identificador as string,
  cardMetadata: (item: Record<string, unknown>) => item.tipo as string,
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
    { title: 'Tractores', href: '/mantenedores/tractores' },
  ],
};
