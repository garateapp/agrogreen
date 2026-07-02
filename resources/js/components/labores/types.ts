export interface LaborCuartel {
  id: string;
  nombre: string;
}

export interface LaborEmpleado {
  id: string;
  empleado_id: string;
  nombre: string;
  horas_trabajadas: number;
  cantidad_unidades_producidas: number;
  valor_trato_unitario: number;
  liquido_a_pagar: number;
}

export interface Labor {
  id: string;
  plantilla_id: string | null;
  actividad_id: string;
  actividad: string;
  icono: string;
  color: string;
  tipo_labor: string;
  centro_costo_id: string;
  centro_costo: string;
  supervisor_id: string | null;
  supervisor: string;
  estado: LaborEstado;
  fecha_programada: string;
  fecha_ejecucion: string | null;
  fecha_fin_estimada: string | null;
  observaciones: string | null;
  avance: number;
  valor_trato_unitario: number | null;
  requiere_empleados: boolean;
  es_ciclica: boolean;
  frecuencia: string | null;
  fecha_fin_ciclo: string | null;
  inicio_real: string | null;
  fin_real: string | null;
  cuarteles: LaborCuartel[];
  empleados_count: number;
  empleados: LaborEmpleado[];
}

export type LaborEstado = 'programada' | 'en_curso' | 'en_pausa' | 'completada' | 'realizada' | 'cancelada';

export type ViewType = 'tabla' | 'gantt' | 'kanban' | 'calendario';

export interface SelectOption {
  value: string;
  label: string;
  icono?: string;
  color?: string;
  tipo_labor?: string;
  unidad_medida_id?: string;
  valor?: number | null;
  centro_costo_id?: string;
}
