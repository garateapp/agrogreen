import { router } from '@inertiajs/react';
import { TableChart, CalendarMonth, ViewKanban, Timeline, Add } from '@mui/icons-material';
import {
  Box, Typography, Fab, ToggleButton, ToggleButtonGroup, Tooltip,
  Dialog, DialogTitle, DialogContent, DialogActions, Button,
} from '@mui/material';
import { useState, useCallback } from 'react';
import LaborDetail from '@/components/labores/LaborDetail';
import LaborModal from '@/components/labores/LaborModal';
import type { Labor, ViewType, SelectOption } from '@/components/labores/types';
import VistaCalendario from '@/components/labores/VistaCalendario';
import VistaGantt from '@/components/labores/VistaGantt';
import VistaKanban from '@/components/labores/VistaKanban';
import VistaTabla from '@/components/labores/VistaTabla';

interface Props {
  pageTitle: string;
  labores: Labor[];
  actividades: SelectOption[];
  centrosCosto: SelectOption[];
  cuarteles: SelectOption[];
  filters: Record<string, string>;
}

export default function Planificador({ pageTitle, labores, actividades, centrosCosto, cuarteles }: Props) {
  const [view, setView] = useState<ViewType>('tabla');
  const [modalOpen, setModalOpen] = useState(false);
  const [editingItem, setEditingItem] = useState<Partial<Labor> | null>(null);
  const [detailItem, setDetailItem] = useState<Labor | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<Labor | null>(null);

  const handleViewChange = (_: unknown, newView: ViewType | null) => {
    if (newView) {
setView(newView);
}
  };

  const handleOpenCreate = () => {
    setEditingItem(null);
    setModalOpen(true);
  };

  const handleEdit = useCallback((labor: Labor) => {
    setEditingItem({
      id: labor.id,
      actividad_id: labor.actividad_id,
      actividad: labor.actividad,
      tipo_labor: labor.tipo_labor,
      centro_costo_id: labor.centro_costo_id,
      centro_costo: labor.centro_costo,
      fecha_programada: labor.fecha_programada,
      fecha_fin_estimada: labor.fecha_fin_estimada,
      observaciones: labor.observaciones,
      avance: labor.avance,
      valor_trato_unitario: labor.valor_trato_unitario,
      requiere_empleados: labor.requiere_empleados,
      es_ciclica: labor.es_ciclica,
      frecuencia: labor.frecuencia,
      fecha_fin_ciclo: labor.fecha_fin_ciclo,
      cuarteles: labor.cuarteles,
    });
    setModalOpen(true);
  }, []);

  const handleView = useCallback((labor: Labor) => {
    setDetailItem(labor);
  }, []);

  const handleCambiarEstado = useCallback((labor: Labor) => {
    const nextEstado = labor.estado === 'programada' ? 'en_curso' : 'completada';
    router.patch(`/labores/planificador/${labor.id}/estado`, { estado: nextEstado }, { preserveScroll: true });
  }, []);

  const handleDelete = useCallback((labor: Labor) => {
    setDeleteTarget(labor);
  }, []);

  const confirmDelete = () => {
    if (deleteTarget) {
      router.delete(`/labores/planificador/${deleteTarget.id}`, {
        preserveScroll: true,
        onSuccess: () => setDeleteTarget(null),
      });
    }
  };

  return (
    <Box sx={{ p: 2 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
        <Typography variant="h5" sx={{ fontWeight: 700 }}>{pageTitle}</Typography>
        <ToggleButtonGroup value={view} exclusive onChange={handleViewChange} size="small">
          <ToggleButton value="tabla">
            <Tooltip title="Tabla"><TableChart /></Tooltip>
          </ToggleButton>
          <ToggleButton value="kanban">
            <Tooltip title="Kanban"><ViewKanban /></Tooltip>
          </ToggleButton>
          <ToggleButton value="gantt">
            <Tooltip title="Gantt"><Timeline /></Tooltip>
          </ToggleButton>
          <ToggleButton value="calendario">
            <Tooltip title="Calendario"><CalendarMonth /></Tooltip>
          </ToggleButton>
        </ToggleButtonGroup>
      </Box>

      <Box sx={{ mb: 2 }}>
        <Typography variant="body2" color="text.secondary">
          {labores.length} labor(es)
        </Typography>
      </Box>

      {view === 'tabla' && (
        <VistaTabla
          labores={labores}
          onView={handleView}
          onEdit={handleEdit}
          onDelete={handleDelete}
          onCambiarEstado={handleCambiarEstado}
        />
      )}
      {view === 'kanban' && (
        <VistaKanban
          labores={labores}
          onView={handleView}
          onEdit={handleEdit}
          onDelete={handleDelete}
          onCambiarEstado={handleCambiarEstado}
        />
      )}
      {view === 'gantt' && (
        <VistaGantt labores={labores} onView={handleView} />
      )}
      {view === 'calendario' && (
        <VistaCalendario labores={labores} onView={handleView} />
      )}

      <Fab
        color="primary"
        sx={{ position: 'fixed', bottom: 24, right: 24 }}
        onClick={handleOpenCreate}
      >
        <Add />
      </Fab>

      <LaborModal
        open={modalOpen}
        onClose={() => {
 setModalOpen(false); setEditingItem(null); 
}}
        editingItem={editingItem}
        actividades={actividades}
        centrosCosto={centrosCosto}
        cuarteles={cuarteles}
      />

      <LaborDetail
        labor={detailItem}
        open={!!detailItem}
        onClose={() => setDetailItem(null)}
      />

      <Dialog
        open={!!deleteTarget}
        onClose={() => setDeleteTarget(null)}
      >
        <DialogTitle>Confirmar eliminación</DialogTitle>
        <DialogContent>
          <Typography>
            ¿Está seguro de eliminar la labor &quot;{deleteTarget?.actividad}&quot;?
          </Typography>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDeleteTarget(null)}>Cancelar</Button>
          <Button onClick={confirmDelete} color="error" variant="contained">
            Eliminar
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}
