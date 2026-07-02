import type { PageProps } from '@inertiajs/core';
import { router } from '@inertiajs/react';
import { History } from '@mui/icons-material';
import type { SelectChangeEvent } from '@mui/material';
import {
  Box, Button, Card, CardContent, Chip, MenuItem, Select,
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
  Paper, TextField, Typography, FormControl, InputLabel, Divider, Pagination,
  Dialog, DialogTitle, DialogContent, DialogActions, Grid,
} from '@mui/material';
import { useState } from 'react';

interface AuditRow {
  id: string;
  event: string;
  auditable_type: string;
  auditable_label: string;
  old_values: Record<string, any> | null;
  new_values: Record<string, any> | null;
  user_name: string;
  created_at: string;
}

interface ModelType {
  value: string;
  label: string;
}

interface Props extends PageProps {
  logs: { data: AuditRow[]; meta: { current_page: number; last_page: number; total: number; from: number; to: number } };
  filters: Record<string, string>;
  modelTypes: ModelType[];
}

const EVENT_COLORS: Record<string, 'success' | 'info' | 'error' | 'warning'> = {
  created: 'success',
  updated: 'info',
  deleted: 'error',
  restored: 'warning',
};

const EVENT_LABELS: Record<string, string> = {
  created: 'Creado',
  updated: 'Actualizado',
  deleted: 'Eliminado',
  restored: 'Restaurado',
};

export default function AuditIndex({ logs, filters, modelTypes }: Props) {
  const [searchEvent, setSearchEvent] = useState(filters.event ?? '');
  const [searchType, setSearchType] = useState(filters.auditable_type ?? '');
  const [searchText, setSearchText] = useState(filters.search ?? '');
  const [searchFrom, setSearchFrom] = useState(filters.from ?? '');
  const [searchTo, setSearchTo] = useState(filters.to ?? '');
  const [detailLog, setDetailLog] = useState<AuditRow | null>(null);

  const handleSearch = () => {
    const params: Record<string, string> = {};

    if (searchEvent) {
params.event = searchEvent;
}

    if (searchType) {
params.auditable_type = searchType;
}

    if (searchText) {
params.search = searchText;
}

    if (searchFrom) {
params.from = searchFrom;
}

    if (searchTo) {
params.to = searchTo;
}

    router.get('/audit/logs', params);
  };

  const renderDiff = (oldV: Record<string, any> | null, newV: Record<string, any> | null) => {
    const keys = new Set<string>();

    if (oldV) {
Object.keys(oldV).forEach(k => keys.add(k));
}

    if (newV) {
Object.keys(newV).forEach(k => keys.add(k));
}

    const changed = Array.from(keys).filter(k =>
      JSON.stringify(oldV?.[k] ?? null) !== JSON.stringify(newV?.[k] ?? null)
    );

    if (changed.length === 0) {
      return <Typography variant="body2" color="text.secondary">Sin cambios registrados</Typography>;
    }

    return (
      <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1, mt: 1 }}>
        {changed.map(k => (
          <Box key={k} sx={{ display: 'flex', gap: 2, alignItems: 'baseline' }}>
            <Typography variant="body2" sx={{ fontWeight: 600, minWidth: 180 }}>{k}:</Typography>
            <Typography variant="body2" color="error.main" sx={{ textDecoration: 'line-through', mr: 1 }}>
              {oldV?.[k] !== undefined ? String(oldV[k]) : '—'}
            </Typography>
            <Typography variant="body2" color="success.main" sx={{ fontWeight: 600 }}>
              {newV?.[k] !== undefined ? String(newV[k]) : '—'}
            </Typography>
          </Box>
        ))}
      </Box>
    );
  };

  return (
    <Box sx={{ p: 3 }}>
      <Box sx={{ display: 'flex', alignItems: 'center', gap: 2, mb: 3 }}>
        <History color="primary" />
        <Typography variant="h5" sx={{ fontWeight: 700 }}>Auditoría</Typography>
      </Box>

      <Card sx={{ mb: 3 }}>
        <CardContent sx={{ display: 'flex', gap: 2, flexWrap: 'wrap', alignItems: 'end' }}>
          <FormControl size="small" sx={{ minWidth: 130 }}>
            <InputLabel>Evento</InputLabel>
            <Select value={searchEvent} label="Evento" onChange={(e: SelectChangeEvent) => setSearchEvent(e.target.value)}>
              <MenuItem value="">Todos</MenuItem>
              <MenuItem value="created">Creado</MenuItem>
              <MenuItem value="updated">Actualizado</MenuItem>
              <MenuItem value="deleted">Eliminado</MenuItem>
            </Select>
          </FormControl>
          <FormControl size="small" sx={{ minWidth: 180 }}>
            <InputLabel>Entidad</InputLabel>
            <Select value={searchType} label="Entidad" onChange={(e: SelectChangeEvent) => setSearchType(e.target.value)}>
              <MenuItem value="">Todas</MenuItem>
              {modelTypes.map(m => <MenuItem key={m.value} value={m.value}>{m.label}</MenuItem>)}
            </Select>
          </FormControl>
          <TextField size="small" label="Buscar" value={searchText}
            onChange={e => setSearchText(e.target.value)} sx={{ width: 200 }} />
          <TextField size="small" label="Desde" type="date" value={searchFrom}
            onChange={e => setSearchFrom(e.target.value)}
            slotProps={{ htmlInput: { placeholder: 'yyyy-mm-dd' } }}
            sx={{ width: 150 }} />
          <TextField size="small" label="Hasta" type="date" value={searchTo}
            onChange={e => setSearchTo(e.target.value)}
            slotProps={{ htmlInput: { placeholder: 'yyyy-mm-dd' } }}
            sx={{ width: 150 }} />
          <Button variant="contained" onClick={handleSearch}>Filtrar</Button>
        </CardContent>
      </Card>

      {logs.data.length === 0 ? (
        <Card sx={{ py: 6, textAlign: 'center' }}>
          <Typography color="text.secondary">No hay registros de auditoría</Typography>
        </Card>
      ) : (
        <>
          <TableContainer component={Paper} sx={{ mb: 2, maxHeight: 600 }}>
            <Table size="small" stickyHeader>
              <TableHead>
                <TableRow>
                  <TableCell sx={{ fontWeight: 700 }}>Fecha</TableCell>
                  <TableCell sx={{ fontWeight: 700 }}>Usuario</TableCell>
                  <TableCell sx={{ fontWeight: 700 }}>Evento</TableCell>
                  <TableCell sx={{ fontWeight: 700 }}>Entidad</TableCell>
                  <TableCell sx={{ fontWeight: 700 }}>Descripción</TableCell>
                  <TableCell sx={{ fontWeight: 700 }} align="center">Detalle</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {logs.data.map(row => (
                  <TableRow key={row.id} hover>
                    <TableCell sx={{ whiteSpace: 'nowrap' }}>{row.created_at}</TableCell>
                    <TableCell>{row.user_name}</TableCell>
                    <TableCell>
                      <Chip label={EVENT_LABELS[row.event] ?? row.event}
                        color={EVENT_COLORS[row.event] ?? 'default'} size="small" />
                    </TableCell>
                    <TableCell>{row.auditable_type}</TableCell>
                    <TableCell>{row.auditable_label}</TableCell>
                    <TableCell align="center">
                      <Button size="small" variant="outlined" onClick={() => setDetailLog(row)}>
                        Ver cambios
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>

          {logs.meta.last_page > 1 && (
            <Box sx={{ display: 'flex', justifyContent: 'center', mb: 3 }}>
              <Pagination
                count={logs.meta.last_page}
                page={logs.meta.current_page}
                onChange={(_, page) => {
                  const params = new URLSearchParams();

                  if (filters) {
Object.entries(filters).forEach(([k, v]) => v && params.set(k, v));
}

                  params.set('page', page.toString());
                  router.get(`/audit/logs?${params.toString()}`);
                }}
                color="primary"
                showFirstButton
                showLastButton
              />
            </Box>
          )}
        </>
      )}

      <Dialog open={detailLog !== null} onClose={() => setDetailLog(null)} maxWidth="md" fullWidth>
        <DialogTitle>Detalle del Cambio</DialogTitle>
        <DialogContent>
          {detailLog && (
            <Box sx={{ pt: 1 }}>
              <Grid container spacing={2} sx={{ mb: 2 }}>
                <Grid size={{ xs: 6 }}>
                  <Typography variant="caption" color="text.secondary">Entidad</Typography>
                  <Typography variant="body2" sx={{ fontWeight: 600 }}>{detailLog.auditable_type}</Typography>
                </Grid>
                <Grid size={{ xs: 6 }}>
                  <Typography variant="caption" color="text.secondary">Descripción</Typography>
                  <Typography variant="body2" sx={{ fontWeight: 600 }}>{detailLog.auditable_label}</Typography>
                </Grid>
                <Grid size={{ xs: 4 }}>
                  <Typography variant="caption" color="text.secondary">Evento</Typography>
                  <Chip label={EVENT_LABELS[detailLog.event] ?? detailLog.event}
                    color={EVENT_COLORS[detailLog.event] ?? 'default'} size="small" />
                </Grid>
                <Grid size={{ xs: 4 }}>
                  <Typography variant="caption" color="text.secondary">Usuario</Typography>
                  <Typography variant="body2" sx={{ fontWeight: 600 }}>{detailLog.user_name}</Typography>
                </Grid>
                <Grid size={{ xs: 4 }}>
                  <Typography variant="caption" color="text.secondary">Fecha</Typography>
                  <Typography variant="body2" sx={{ fontWeight: 600 }}>{detailLog.created_at}</Typography>
                </Grid>
              </Grid>

              <Divider sx={{ mb: 2 }} />
              <Typography variant="subtitle2" sx={{ mb: 1 }}>
                {detailLog.event === 'created' ? 'Valores creados' :
                 detailLog.event === 'deleted' ? 'Valores eliminados' :
                 'Valores modificados'}
              </Typography>
              {renderDiff(detailLog.old_values, detailLog.new_values)}
            </Box>
          )}
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDetailLog(null)}>Cerrar</Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}
