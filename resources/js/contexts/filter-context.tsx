import { createContext, useContext, useState, useCallback  } from 'react';
import type {ReactNode} from 'react';
import type { CentroCosto, CentroCostoRaw } from '@/types/agricultural';
import { DEFAULT_FILTER, buildTree } from './FilterContext';

export interface FilterState {
  fecha_desde: string | null;
  fecha_hasta: string | null;
  centro_costo_id: string | null;
  centro_costo_nombre: string | null;
}

interface FilterContextValue {
  filter: FilterState;
  setFechaDesde: (val: string | null) => void;
  setFechaHasta: (val: string | null) => void;
  setCentroCosto: (id: string | null, nombre: string | null) => void;
  resetFilter: () => void;
  treeData: CentroCosto[];
  setCentrosData: (items: CentroCostoRaw[]) => void;
}

const FilterContext = createContext<FilterContextValue>(null!);

export function FilterProvider({ children }: { children: ReactNode }) {
  const [filter, setFilter] = useState<FilterState>(DEFAULT_FILTER);
  const [treeData, setTreeData] = useState<CentroCosto[]>([]);

  const setCentrosData = useCallback((items: CentroCostoRaw[]) => {
    setTreeData(buildTree(items));
  }, []);

  const setFechaDesde = useCallback((val: string | null) => {
    setFilter((prev) => ({ ...prev, fecha_desde: val }));
  }, []);

  const setFechaHasta = useCallback((val: string | null) => {
    setFilter((prev) => ({ ...prev, fecha_hasta: val }));
  }, []);

  const setCentroCosto = useCallback((id: string | null, nombre: string | null) => {
    setFilter((prev) => ({ ...prev, centro_costo_id: id, centro_costo_nombre: nombre }));
  }, []);

  const resetFilter = useCallback(() => {
    setFilter(DEFAULT_FILTER);
  }, []);

  return (
    <FilterContext.Provider
      value={{ filter, setFechaDesde, setFechaHasta, setCentroCosto, resetFilter, treeData, setCentrosData }}
    >
      {children}
    </FilterContext.Provider>
  );
}

export const useFilter = () => useContext(FilterContext);
