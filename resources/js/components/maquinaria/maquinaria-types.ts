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

export interface HeaderIndicator {
  label: string;
  value: string | number;
  format?: 'currency' | 'number';
}
