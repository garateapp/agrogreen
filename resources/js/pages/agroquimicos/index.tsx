import { Head, Link, router } from '@inertiajs/react';
import { Add, Visibility, CheckCircle, Cancel } from '@mui/icons-material';
import { Box, Typography, Button, Chip, TextField, MenuItem, Stack } from '@mui/material';
import type { GridColDef } from '@mui/x-data-grid';
import { DataGrid } from '@mui/x-data-grid';
import { useState } from 'react';
import { ESTADOS } from '@/components/agroquimicos/types';
import type { ApplicationRecord } from '@/components/agroquimicos/types';

interface Props {
  records: { data: ApplicationRecord[]; meta: any };
  filters: Record<string, string>;
}

export default function AgroquimicosIndex({ records, filters }: Props) {
  const [estado, setEstado] = useState(filters.estado ?? '');

  const columns: GridColDef[] = [
    { field: 'codigo', headerName: 'Código', width: 180 },
    { field: 'fecha_aplicacion', headerName: 'Fecha', width: 120 },
    {
      field: 'cuartel',
      headerName: 'Cuartel',
      width: 150,
      valueGetter: (_value, row: ApplicationRecord) => row.cuartel?.nombre ?? '-',
    },
    {
      field: 'objetivo_tipo',
      headerName: 'Objetivo',
      width: 120,
    },
    {
      field: 'productos',
      headerName: 'Producto',
      width: 180,
      valueGetter: (_value, row: ApplicationRecord) =>
        row.productos?.map((p) => p.productoSAG?.nombre_comercial).join(', ') ?? '-',
    },
    {
      field: 'aplicadorRel',
      headerName: 'Aplicador',
      width: 160,
      valueGetter: (_value, row: ApplicationRecord) => {
        const a = row.aplicadorRel;

        return a ? `${a.nombres} ${a.apellidos}` : '-';
      },
    },
    {
      field: 'estado',
      headerName: 'Estado',
      width: 130,
      renderCell: (params) => {
        const cfg = ESTADOS[params.value as string] ?? { label: params.value, color: 'default' as const };

        return <Chip label={cfg.label} color={cfg.color} size="small" />;
      },
    },
    {
      field: 'acciones',
      headerName: 'Acciones',
      width: 200,
      sortable: false,
      renderCell: (params) => {
        const row = params.row as ApplicationRecord;

        return (
          <Stack direction="row" spacing={0.5}>
            <Button size="small" component={Link} href={`/agroquimicos/${row.id}`} startIcon={<Visibility />}>
              Ver
            </Button>
            {(row.estado === 'ejecutada' || row.estado === 'en_revision') && (
              <Button
                size="small"
                color="success"
                startIcon={<CheckCircle />}
                onClick={() => {
                  if (confirm('¿Aprobar esta aplicación? Se descontará el stock.')) {
                    router.patch(`/agroquimicos/${row.id}/approve`, undefined, { preserveScroll: true });
                  }
                }}
              >
                Aprobar
              </Button>
            )}
            {row.estado !== 'anulada' && (
              <Button
                size="small"
                color="error"
                startIcon={<Cancel />}
                onClick={() => router.post(`/agroquimicos/${row.id}/cancel`, { motivo_anulacion: prompt('Motivo de anulación:') ?? '' }, { preserveScroll: true })}
              >
                Anular
              </Button>
            )}
          </Stack>
        );
      },
    },
  ];

  return (
    <>
      <Head title="Registro de Aplicaciones" />
      <Box>
        <Stack direction="row" justifyContent="space-between" alignItems="center" mb={2}>
          <Typography variant="h5">Registro de Aplicaciones de Agroquímicos</Typography>
          <Button variant="contained" component={Link} href="/agroquimicos/create" startIcon={<Add />}>
            Nueva Aplicación
          </Button>
        </Stack>

        <Stack direction="row" spacing={2} mb={2}>
          <TextField
            select
            size="small"
            label="Estado"
            value={estado}
            onChange={(e) => {
              setEstado(e.target.value);
              router.get('/agroquimicos', { estado: e.target.value }, { preserveState: true });
            }}
            sx={{ minWidth: 160 }}
          >
            <MenuItem value="">Todos</MenuItem>
            {Object.entries(ESTADOS).map(([key, cfg]) => (
              <MenuItem key={key} value={key}>{cfg.label}</MenuItem>
            ))}
          </TextField>
        </Stack>

        <DataGrid
          rows={records.data}
          columns={columns}
          autoHeight
          disableRowSelectionOnClick
          pageSizeOptions={[20]}
          paginationModel={{ page: 0, pageSize: 20 }}
          getRowId={(row) => row.id}
        />
      </Box>
    </>
  );
}

AgroquimicosIndex.layout = {
  breadcrumbs: [
    { title: 'Agroquímicos', href: '/agroquimicos' },
    { title: 'Registro de Aplicaciones', href: '/agroquimicos' },
  ],
};
