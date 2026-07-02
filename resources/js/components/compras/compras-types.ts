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
  multiple?: boolean;
}

export interface ComprasTableProps {
  title: string;
  columns: Column[];
  items: Record<string, unknown>[];
  totalLabel?: string;
  totalValue?: number;
  countLabel?: string;
  countValue?: number;
  filters?: {
    selects?: FilterSelect[];
    showDateRange?: boolean;
    dateRangeLabel?: string;
    showEstadoPago?: boolean;
    showEstadoContable?: boolean;
    showEstadoRecepcion?: boolean;
    showEstadoAprobacion?: boolean;
    searchPlaceholder?: string;
  };
  actions?: React.ReactNode;
  onRowAction?: (action: string, row: Record<string, unknown>) => void;
  detailField?: string;
}
