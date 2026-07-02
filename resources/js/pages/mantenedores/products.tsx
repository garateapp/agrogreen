import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import { useMemo } from 'react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

interface Props {
  items: Record<string, unknown>[];
  unidades: Array<{ id: string; nombre: string }>;
}

export default function MantenedorPage({ items, unidades }: Props) {
  const fields: MantenedorField[] = useMemo(() => [
    { name: 'nombre', label: 'Nombre', type: 'text', required: true },
    { name: 'codigo_barras', label: 'Código', type: 'text', xs: 12, sm: 6 },
    {
      name: 'categoria',
      label: 'Categoría',
      type: 'select',
      required: true,
      options: [
        { value: 'agroquimico', label: 'Agroquímico' },
        { value: 'fertilizante', label: 'Fertilizante' },
        { value: 'maquinaria_repuesto', label: 'Maquinaria / Repuesto' },
        { value: 'combustible', label: 'Combustible' },
        { value: 'EPP', label: 'EPP' },
        { value: 'otros', label: 'Otros' },
      ],
    },
    {
      name: 'unidad_medida_id',
      label: 'Unidad de Medida',
      type: 'select',
      required: true,
      options: unidades.map((u) => ({ value: u.id, label: u.nombre })),
    },
    { name: 'ingrediente_activo', label: 'Ingrediente Activo', type: 'text' },
    { name: 'dosis_recomendada_por_ha', label: 'Dosis Recomendada (ha)', type: 'number' },
    { name: 'dias_carencia', label: 'Días Carencia', type: 'number' },
  ], [unidades]);

  const config: MantenedorConfig = {
    title: 'Productos',
    description: 'Fichas de insumos, herramientas y agroquímicos',
    endpoint: '/mantenedores/productos',
    fields,
    cardTitle: (item: Record<string, unknown>) => item.nombre as string,
    cardSubtitle: (item: Record<string, unknown>) => item.codigo_barras as string,
  };

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
    { title: 'Productos', href: '/mantenedores/productos' },
  ],
};
