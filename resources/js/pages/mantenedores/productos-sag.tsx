import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import { useMemo } from 'react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

interface Props {
  items: Record<string, unknown>[];
  productos: Array<{ id: string; nombre: string }>;
}

export default function ProductosSagPage({ items, productos }: Props) {
  const fields: MantenedorField[] = useMemo(() => [
    {
      name: 'producto_id',
      label: 'Producto',
      type: 'select',
      required: true,
      options: productos.map((p) => ({ value: p.id, label: p.nombre })),
    },
    { name: 'nombre_comercial', label: 'Nombre Comercial', type: 'text', required: true },
    { name: 'nro_autorizacion_sag', label: 'N° Autorización SAG', type: 'text', required: true },
    { name: 'ingrediente_activo', label: 'Ingrediente Activo', type: 'text', required: true },
    { name: 'titular', label: 'Titular', type: 'text' },
    {
      name: 'estado_sag',
      label: 'Estado SAG',
      type: 'select',
      required: true,
      options: [
        { value: 'autorizado', label: 'Autorizado' },
        { value: 'restringido', label: 'Restringido' },
        { value: 'prohibido', label: 'Prohibido' },
        { value: 'cancelado', label: 'Cancelado' },
      ],
    },
    {
      name: 'toxicidad_abejas',
      label: 'Toxicidad Abejas',
      type: 'select',
      options: [
        { value: 'baja', label: 'Baja' },
        { value: 'moderada', label: 'Moderada' },
        { value: 'alta', label: 'Alta' },
        { value: 'sin_datos', label: 'Sin Datos' },
      ],
    },
    { name: 'url_etiqueta', label: 'URL Etiqueta', type: 'text' },
    { name: 'url_hds', label: 'URL HDS', type: 'text' },
    { name: 'ultima_actualizacion_sag', label: 'Última Actualización SAG', type: 'date' },
  ], [productos]);

  const config: MantenedorConfig = {
    title: 'Productos SAG',
    description: 'Registro SAG de productos agroquímicos autorizados',
    endpoint: '/mantenedores/productos-sag',
    fields,
    cardTitle: (item: Record<string, unknown>) => item.nombre_comercial as string,
    cardSubtitle: (item: Record<string, unknown>) => `SAG: ${item.nro_autorizacion_sag ?? ''}` as string,
    cardMetadata: (item: Record<string, unknown>) => item.estado_sag as string,
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

ProductosSagPage.layout = {
  breadcrumbs: [
    { title: 'Mantenedores', href: '/mantenedores' },
    { title: 'Productos SAG', href: '/mantenedores/productos-sag' },
  ],
};
