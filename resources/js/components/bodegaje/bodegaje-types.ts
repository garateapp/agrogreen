export interface Column {
  key: string;
  label: string;
  align?: 'left' | 'right' | 'center';
  width?: number | string;
  render?: (value: unknown, row: Record<string, unknown>) => React.ReactNode;
  sortable?: boolean;
}

export interface FilterSelect {
  key: string;
  label: string;
  options: { value: string; label: string }[];
}

export interface LineItem {
  id: string;
  producto_id?: string;
  bodega_id?: string;
  producto: string;
  bodega: string;
  cantidad: number;
  precio: number;
  unidad: string;
  subtotal: number;
  centroCosto?: string;
  exentoIva?: boolean;
}

export interface VoucherFormData {
  numero: string;
  fecha: string;
  descripcion: string;
  tipo: 'productos' | 'servicios';
  distribuirCostos: boolean;
  descuentoLinea: boolean;
  vencimientoLote: boolean;
  items: LineItem[];
}
