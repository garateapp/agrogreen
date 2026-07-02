import type { CentroCosto, CentroCostoRaw } from '@/types/agricultural';

export interface FilterState {
  fecha_desde: string | null;
  fecha_hasta: string | null;
  centro_costo_id: string | null;
  centro_costo_nombre: string | null;
}

export const DEFAULT_FILTER: FilterState = {
  fecha_desde: null,
  fecha_hasta: null,
  centro_costo_id: null,
  centro_costo_nombre: null,
};

export function buildTree(items: CentroCostoRaw[]): CentroCosto[] {
  const map = new Map<string, CentroCosto>();
  const roots: CentroCosto[] = [];

  for (const item of items) {
    map.set(item.id, { ...item, children: [] });
  }

  for (const item of items) {
    const node = map.get(item.id)!;

    if (item.parent_id && map.has(item.parent_id)) {
      map.get(item.parent_id)!.children.push(node);
    } else {
      roots.push(node);
    }
  }

  return roots;
}
