export interface ApplicationRecord {
  id: string;
  codigo: string;
  cuartel_id: string;
  variedad_id: string | null;
  temporada: string | null;
  superficie: number;
  fecha_aplicacion: string;
  hora_inicio: string | null;
  hora_termino: string | null;
  estado: string;
  objetivo_tipo: string;
  objetivo_nombre: string | null;
  responsable_id: string;
  aplicador_id: string | null;
  supervisor_id: string | null;
  equipo_id: string | null;
  observaciones: string | null;
  creado_por: string;
  aprobado_por: string | null;
  aprobado_en: string | null;
  anulado_por: string | null;
  motivo_anulacion: string | null;
  cuartel?: { id: string; nombre: string };
  variedad?: { id: string; nombre: string } | null;
  responsable?: { id: string; name: string };
  aplicadorRel?: { id: string; nombres: string; apellidos: string } | null;
  equipo?: { id: string; nombre: string } | null;
  productos?: ApplicationRecordProducto[];
  clima?: WeatherCondition | null;
  seguridad?: SafetyCheck | null;
  envases?: ContainerDisposal[];
  creadoPor?: { id: string; name: string };
  aprobadoPor?: { id: string; name: string };
  anuladoPor?: { id: string; name: string };
  created_at?: string;
  updated_at?: string;
}

export interface ApplicationRecordProducto {
  id: string;
  producto_sag_id: string;
  lote_id: string | null;
  dosis: number;
  unidad_dosis: string;
  cantidad_total: number;
  volumen_agua: number | null;
  label_snapshot: Record<string, unknown> | null;
  productoSAG?: { id: string; nombre_comercial: string };
  lote?: { id: string; codigo_lote: string } | null;
}

export interface WeatherCondition {
  temperatura: number | null;
  humedad: number | null;
  viento_velocidad: number | null;
  viento_direccion: string | null;
  estado_general: string | null;
  riesgo_deriva: string | null;
  fuente: string;
}

export interface SafetyCheck {
  epp_guantes: boolean;
  epp_mascarilla: boolean;
  epp_overol: boolean;
  epp_botas: boolean;
  epp_proteccion_ocular: boolean;
  senalizacion_instalada: boolean;
  agua_emergencia: boolean | null;
  observaciones: string | null;
}

export interface ContainerDisposal {
  id: string;
  producto_sag_id: string;
  envases_usados: number;
  capacidad_envase: number | null;
  triple_lavado: boolean;
  almacenamiento_temporal: string | null;
  metodo_disposicion: string | null;
  documento_respaldo_url: string | null;
}

export interface Cuartel {
  id: string;
  nombre: string;
}

export interface Variedad {
  id: string;
  nombre: string;
}

export interface ProductoSAG {
  id: string;
  producto_id?: string;
  nombre_comercial: string;
  nro_autorizacion_sag: string;
  ingrediente_activo: string;
  estado_sag: string;
}

export interface Aplicador {
  id: string;
  nombres: string;
  apellidos: string;
  rut: string;
}

export interface EquipoAplicacion {
  id: string;
  nombre: string;
  tipo: string;
}

export interface Lote {
  id: string;
  producto_id: string;
  codigo_lote: string;
  fecha_vencimiento: string | null;
  cantidad_inicial: number;
  cantidad_disponible: number;
}

export const ESTADOS: Record<string, { label: string; color: 'warning' | 'info' | 'success' | 'error' | 'default' }> = {
  borrador: { label: 'Borrador', color: 'default' },
  programada: { label: 'Programada', color: 'info' },
  ejecutada: { label: 'Ejecutada', color: 'warning' },
  en_revision: { label: 'En Revisión', color: 'warning' },
  aprobada: { label: 'Aprobada', color: 'success' },
  observada: { label: 'Observada', color: 'error' },
  anulada: { label: 'Anulada', color: 'error' },
};
