import type { SxProps, Theme } from '@mui/material';

export type FieldType =
  | 'text'
  | 'number'
  | 'select'
  | 'switch'
  | 'date'
  | 'rut'
  | 'map'
  | 'textarea'
  | 'email';

export interface SelectOption {
  value: string | number;
  label: string;
}

export interface MantenedorField {
  name: string;
  label: string;
  type: FieldType;
  required?: boolean;
  placeholder?: string;
  options?: SelectOption[];
  optionsEndpoint?: string;
  cascadeParent?: string;
  disabled?: boolean;
  xs?: number;
  sm?: number;
  group?: string;
  showIf?: Record<string, string | boolean | number>;
  tooltip?: string;
}

export interface MantenedorAction {
  label: string;
  icon?: React.ReactNode;
  onClick: (item: Record<string, unknown>) => void;
  color?: 'error' | 'warning' | 'info' | 'default';
}

export interface MantenedorConfig {
  title: string;
  description: string;
  icon?: React.ReactNode;
  fields: MantenedorField[];
  cardTitle: (item: Record<string, unknown>) => string;
  cardSubtitle?: (item: Record<string, unknown>) => string;
  cardMetadata?: (item: Record<string, unknown>) => string | React.ReactNode;
  cardAvatarColor?: (item: Record<string, unknown>) => string;
  columns?: number;
  endpoint: string;
  hasEstado?: boolean;
  mapField?: string;
  actions?: MantenedorAction[];
  renderFormExtra?: (props: {
    formData: Record<string, string | number | boolean>;
    onChange: (name: string, value: string | number | boolean) => void;
    errors: Record<string, string>;
  }) => React.ReactNode;
}

export interface MantenedorListHandlers {
  onToggleSelect: (id: string) => void;
  onSelectAll: () => void;
  onClearSelection: () => void;
  onFiltersChange: (filters: Record<string, unknown>) => void;
  onClearFilters: () => void;
  onImport: () => void;
}

export interface MantenedorPageProps {
  items: Record<string, unknown>[];
  config: MantenedorConfig;
}
