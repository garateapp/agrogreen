import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import { useMemo } from 'react';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';

interface Props {
  items: Record<string, unknown>[];
  contratistas: Array<{ id: string; nombre: string; rut: string }>;
}

export default function MantenedorPage({ items, contratistas }: Props) {
  const fields: MantenedorField[] = useMemo(() => [
    { name: 'nombre', label: 'Nombre', type: 'text', required: true },
    { name: 'apellido', label: 'Apellido', type: 'text', required: true },
    { name: 'rut', label: 'RUT', type: 'rut', required: true },
    { name: 'sueldo_base', label: 'Sueldo base', type: 'number' },
    { name: 'valor_dia_base' , label: 'Valor día base', type: 'number' },
    { name: 'valor_hora_extra' , label: 'Valor hora extra', type: 'number' },
    { name: 'fecha_nacimiento', label: 'Fecha de nacimiento', type: 'date' },
    { name: 'fecha_inicio_contrato', label: 'Fecha inicio contrato', type: 'date' },
    { name: 'fecha_termino_contrato', label: 'Fecha término contrato', type: 'date' },
    {
      name: 'trato_id',
      label: 'Trato',
      type: 'select',
      options: [
        { value: '1', label: 'Por monto' },
        { value: '2', label: 'Por cajas' },
      ],
    },
    { name: 'monto_trato', label: 'Monto de trato', type: 'number' },
    {
      name: 'jefe_id',
      label: 'Jefe',
      type: 'select',
      options: [{ value: '1', label: 'Admin' }],
    },
    {
      name: 'usuario_asociado_id',
      label: 'Usuario asociado',
      type: 'select',
      options: [{ value: '1', label: 'usuario@mail.com' }],
    },
    { name: 'es_contratista', label: 'Es contratista', type: 'switch' },
    {
      name: 'contratista_id',
      label: 'Contratista',
      type: 'select',
      options: contratistas.map((c) => ({ value: c.id, label: `${c.nombre} (${c.rut})` })),
      showIf: { es_contratista: true },
    },
    { name: 'semana_corrida', label: 'Semana corrida', type: 'switch' },
    { name: 'trabaja_sueldo_liquido', label: 'Trabaja con sueldo líquido', type: 'switch' },
    { name: 'trabajador_agricola', label: 'Trabajador agrícola', type: 'switch' },
    { name: 'costos_sensibles', label: 'Costos sensibles', type: 'switch' },
  ], [contratistas]);

  const config: MantenedorConfig = {
    title: 'Empleados',
    description: 'Ficha de personal interno y subcontratado',
    endpoint: '/mantenedores/empleados',
    fields,
    cardTitle: (item: Record<string, unknown>) => `${item.nombre} ${item.apellido}` as string,
    cardSubtitle: (item: Record<string, unknown>) => item.rut as string,
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
    { title: 'Empleados', href: '/mantenedores/empleados' },
  ],
};
