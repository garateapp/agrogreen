import type { SxProps, Theme } from '@mui/material';

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

export interface HeaderIndicatorData {
  label: string;
  value: string | number;
  format?: 'currency' | 'number' | 'decimal';
}

export interface TarjaRow {
  id: string;
  empleado: string;
  tipoFaena: string;
  centroCosto: string;
  jornada: string;
  descripcion: string;
  pagoTotal: number;
}

export interface MobileWorker {
  id: string;
  nombre: string;
  rut: string;
  inicial: string;
  horasAsignadas: number;
  horasMax: number;
}
