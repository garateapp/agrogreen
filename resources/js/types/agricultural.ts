export interface CentroCosto {
  id: string;
  tenant_id: string;
  nombre: string;
  codigo: string;
  parent_id: string | null;
  activo: boolean;
  children: CentroCosto[];
}

export interface CentroCostoRaw {
  id: string;
  tenant_id: string;
  nombre: string;
  codigo: string;
  parent_id: string | null;
  activo: boolean;
  children?: CentroCostoRaw[];
}

export interface SlideData {
  title: string;
  description: string;
  icon: string;
}
