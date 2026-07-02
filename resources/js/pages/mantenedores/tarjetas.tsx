import { Head } from '@inertiajs/react';
import { useMemo, useCallback } from 'react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';
import { router } from '@inertiajs/react';

interface Props {
  items: Record<string, unknown>[];
  empleados: Array<{ id: string; nombre: string; apellido: string; rut: string }>;
}

export default function TarjetasPage({ items, empleados }: Props) {
  const fields: MantenedorField[] = useMemo(() => [
    { name: 'sigla', label: 'Sigla', type: 'text', required: true, xs: 12, sm: 4 },
    { name: 'codigo_qr', label: 'Código QR', type: 'text', disabled: true, xs: 12, sm: 8 },
    {
      name: 'empleado_id',
      label: 'Empleado',
      type: 'select',
      options: empleados.map((e) => ({
        value: e.id,
        label: `${e.nombre} ${e.apellido} (${e.rut})`,
      })),
    },
    { name: 'activo', label: 'Activa', type: 'switch' },
  ], [empleados]);

  const handleUnassign = useCallback((item: Record<string, unknown>) => {
    if (confirm('¿Desasignar esta tarjeta?')) {
      router.patch(`/mantenedores/tarjetas/${item.id}/unassign`);
    }
  }, []);

  const config: MantenedorConfig = {
    title: 'Tarjetas',
    description: 'Tarjetas con QR para identificación en faenas',
    endpoint: '/mantenedores/tarjetas',
    fields,
    cardTitle: (item: Record<string, unknown>) => item.codigo_qr as string,
    cardSubtitle: (item: Record<string, unknown>) => {
      const emp = (item as Record<string, unknown>).empleado as Record<string, unknown> | null;
      return emp ? `${emp.nombre} ${emp.apellido}` : 'Sin asignar';
    },
    cardMetadata: (item: Record<string, unknown>) => {
      const emp = (item as Record<string, unknown>).empleado as Record<string, unknown> | null;
      if (!emp) return '';
      const sigla = item.sigla as string;
      return `Sigla: ${sigla}`;
    },
    actions: [
      {
        label: 'Desasignar',
        icon: null,
        onClick: handleUnassign,
        color: 'warning',
      },
    ],
  };

  return (
    <>
      <Head title={config.title} />
      <MantenedorListPage config={config} items={items} />
    </>
  );
}

TarjetasPage.layout = {
  breadcrumbs: [
    { title: 'Mantenedores', href: '/mantenedores' },
    { title: 'Tarjetas', href: '/mantenedores/tarjetas' },
  ],
};
