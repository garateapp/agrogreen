import { router } from '@inertiajs/react';
import { Pagination as MuiPagination, Box, Typography } from '@mui/material';

interface Meta {
  current_page: number;
  last_page: number;
  total: number;
  from: number;
  to: number;
}

interface Props {
  meta: Meta;
  queryParams?: Record<string, string>;
  baseUrl?: string;
}

export default function Pagination({ meta, queryParams = {}, baseUrl }: Props) {
  if (meta.last_page <= 1) {
 return null; 
}

  const handleChange = (_: unknown, page: number) => {
    router.get(baseUrl ?? window.location.pathname, { ...queryParams, page }, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    });
  };

  return (
    <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mt: 2, flexWrap: 'wrap', gap: 1 }}>
      <Typography variant="body2" color="text.secondary">
        Mostrando {meta.from}–{meta.to} de {meta.total}
      </Typography>
      <MuiPagination
        count={meta.last_page}
        page={meta.current_page}
        onChange={handleChange}
        color="primary"
        size="small"
        shape="rounded"
      />
    </Box>
  );
}
