import { ExpandMore, ChevronRight, FilterAlt } from '@mui/icons-material';
import { TreeView, TreeItem } from '@mui/lab';
import { Box, TextField, Paper, CircularProgress, Typography } from '@mui/material';
import { useEffect } from 'react';
import { useFilter } from '@/contexts/filter-context';
import type { CentroCostoRaw } from '@/types/agricultural';

function renderTreeNodes(
  nodes: CentroCostoRaw[],
  onSelect: (id: string, name: string) => void,
  selectedId: string | null,
) {
  return nodes.map((node) => (
    <TreeItem
      key={node.id}
      nodeId={node.id}
      label={node.nombre}
      onClick={() => onSelect(node.id, node.nombre)}
      sx={{
        '& .MuiTreeItem-content.Mui-selected': {
          bgcolor: node.id === selectedId ? 'primary.light' : undefined,
        },
      }}
    >
      {node.children && node.children.length > 0 ? renderTreeNodes(node.children, onSelect, selectedId) : null}
    </TreeItem>
  ));
}

interface Props {
  centrosCosto: CentroCostoRaw[];
  loading?: boolean;
}

export default function GlobalAgriculturalFilter({ centrosCosto, loading }: Props) {
  const { filter, setFechaDesde, setFechaHasta, setCentroCosto, setCentrosData, treeData } = useFilter();

  useEffect(() => {
    if (centrosCosto.length > 0) {
      setCentrosData(centrosCosto);
    }
  }, [centrosCosto, setCentrosData]);

  return (
    <Paper elevation={0} variant="outlined" sx={{ p: 1.5, mb: 2, display: 'flex', gap: 2, alignItems: 'center', flexWrap: 'wrap' }}>
      <FilterAlt fontSize="small" color="action" />
      <TextField
        label="Desde"
        type="date"
        size="small"
        value={filter.fecha_desde ?? ''}
        onChange={(e) => setFechaDesde(e.target.value || null)}
        slotProps={{ inputLabel: { shrink: true } }}
        sx={{ width: 160 }}
      />
      <TextField
        label="Hasta"
        type="date"
        size="small"
        value={filter.fecha_hasta ?? ''}
        onChange={(e) => setFechaHasta(e.target.value || null)}
        slotProps={{ inputLabel: { shrink: true } }}
        sx={{ width: 160 }}
      />
      <Box sx={{ minWidth: 240, maxHeight: 260, overflow: 'auto', border: 1, borderColor: 'divider', borderRadius: 1, p: 0.5 }}>
        {loading ? (
          <Box sx={{ display: 'flex', justifyContent: 'center', p: 2 }}>
            <CircularProgress size={20} />
          </Box>
        ) : centrosCosto.length === 0 ? (
          <Typography variant="caption" color="text.disabled" sx={{ p: 1, display: 'block' }}>
            Sin centros de costo
          </Typography>
        ) : (
          <TreeView
            defaultCollapseIcon={<ExpandMore />}
            defaultExpandIcon={<ChevronRight />}
            selected={filter.centro_costo_id ?? ''}
            sx={{ flexGrow: 1 }}
          >
            {renderTreeNodes(treeData, (id, name) => setCentroCosto(id, name), filter.centro_costo_id)}
          </TreeView>
        )}
      </Box>
      {filter.centro_costo_nombre && (
        <Typography variant="caption" color="primary.main" sx={{ fontWeight: 500 }}>
          {filter.centro_costo_nombre}
        </Typography>
      )}
    </Paper>
  );
}
