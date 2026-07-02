import {
  DndContext, DragOverlay, closestCorners, PointerSensor, useSensor, useSensors
   
} from '@dnd-kit/core';
import type {DragEndEvent, UniqueIdentifier} from '@dnd-kit/core';
import {
  SortableContext, verticalListSortingStrategy, useSortable,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { router } from '@inertiajs/react';
import { Box, Typography, Paper } from '@mui/material';
import { useState, useMemo } from 'react';
import LaborCard from './LaborCard';
import type { Labor, LaborEstado } from './types';

interface Props {
  labores: Labor[];
  onView: (labor: Labor) => void;
}

const COLUMNAS: { estado: LaborEstado; title: string; color: string }[] = [
  { estado: 'programada', title: 'Programadas', color: '#1976d2' },
  { estado: 'en_curso', title: 'En Curso', color: '#f57c00' },
  { estado: 'completada', title: 'Completadas / Realizadas', color: '#388e3c' },
];

function SortableLaborCard({ labor, onClick }: { labor: Labor; onClick: () => void }) {
  const { attributes, listeners, setNodeRef, transform, transition, isDragging } = useSortable({
    id: labor.id,
  });
  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
    opacity: isDragging ? 0.5 : 1,
  };

  return (
    <div ref={setNodeRef} style={style} {...attributes} {...listeners}>
      <LaborCard labor={labor} onClick={onClick} />
    </div>
  );
}

export default function VistaKanban({ labores, onView }: Props) {
  const [activeId, setActiveId] = useState<UniqueIdentifier | null>(null);

  const sensors = useSensors(
    useSensor(PointerSensor, { activationConstraint: { distance: 8 } }),
  );

  const grouped = useMemo(() => {
    const map: Record<string, Labor[]> = { programada: [], en_curso: [], completada: [], cancelada: [] };
    labores.forEach((l) => {
      if (l.estado === 'realizada') {
        map['completada'].push(l);
      } else if (map[l.estado]) {
        map[l.estado].push(l);
      }
    });

    return map;
  }, [labores]);

  const handleDragEnd = (event: DragEndEvent) => {
    const { active, over } = event;
    setActiveId(null);

    if (!over) {
return;
}

    const laborId = String(active.id);
    const overId = String(over.id);
    const isColumn = ['programada', 'en_curso', 'completada'].includes(overId);
    const targetEstado = isColumn ? overId : null;

    if (targetEstado) {
      router.patch(`/labores/planificador/${laborId}/estado`, { estado: targetEstado }, { preserveScroll: true });
    }
  };

  const activeLabor = activeId ? labores.find((l) => l.id === activeId) : null;

  return (
    <DndContext
      sensors={sensors}
      collisionDetection={closestCorners}
      onDragStart={(event) => setActiveId(event.active.id)}
      onDragEnd={handleDragEnd}
    >
      <Box sx={{ display: 'flex', gap: 2, overflow: 'auto', pb: 2, minHeight: 400 }}>
        {COLUMNAS.map((col) => {
          const items = grouped[col.estado] ?? [];

          return (
            <Box key={col.estado} sx={{ flex: '1 1 0', minWidth: 280, maxWidth: 400 }}>
              <Paper
                variant="outlined"
                sx={{
                  p: 1.5,
                  bgcolor: 'grey.50',
                  height: '100%',
                  display: 'flex',
                  flexDirection: 'column',
                }}
              >
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 1.5 }}>
                  <Box sx={{ width: 10, height: 10, borderRadius: '50%', bgcolor: col.color }} />
                  <Typography variant="subtitle2" sx={{ fontWeight: 600,color: col.color}}>
                    {col.title} ({items.length})
                  </Typography>
                </Box>
                <SortableContext items={items.map((l) => l.id)} strategy={verticalListSortingStrategy}>
                  <Box sx={{ flex: 1, overflow: 'auto' }} id={col.estado}>
                    {items.map((labor) => (
                      <SortableLaborCard
                        key={labor.id}
                        labor={labor}
                        onClick={() => onView(labor)}
                      />
                    ))}
                    {items.length === 0 && (
                      <Typography variant="caption" color="text.secondary" sx={{ py: 2, textAlign: 'center', display: 'block' }}>
                        Sin labores
                      </Typography>
                    )}
                  </Box>
                </SortableContext>
              </Paper>
            </Box>
          );
        })}
      </Box>
      <DragOverlay>
        {activeLabor ? <LaborCard labor={activeLabor} onClick={() => {}} /> : null}
      </DragOverlay>
    </DndContext>
  );
}
