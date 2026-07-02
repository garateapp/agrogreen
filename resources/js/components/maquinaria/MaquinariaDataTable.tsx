import {
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
  TableSortLabel, Paper, Typography,
} from '@mui/material';
import { useState } from 'react';
import type { Column } from './maquinaria-types';

interface Props {
  columns: Column[];
  items: Record<string, unknown>[];
}

export default function MaquinariaDataTable({ columns, items }: Props) {
  const [sortKey, setSortKey] = useState('');
  const [sortDir, setSortDir] = useState<'asc' | 'desc'>('asc');

  const handleSort = (key: string) => {
    if (sortKey === key) {
setSortDir((d) => (d === 'asc' ? 'desc' : 'asc'));
} else {
 setSortKey(key); setSortDir('asc'); 
}
  };

  const sorted = [...items].sort((a, b) => {
    if (!sortKey) {
return 0;
}

    const aVal = a[sortKey]; const bVal = b[sortKey];

    if (aVal == null) {
return 1;
}

 if (bVal == null) {
return -1;
}

    const cmp = typeof aVal === 'number' ? aVal - (bVal as number) : String(aVal).localeCompare(String(bVal));

    return sortDir === 'asc' ? cmp : -cmp;
  });

  return (
    <TableContainer component={Paper} variant="outlined" sx={{ borderRadius: 2, mt: 2 }}>
      <Table size="small">
        <TableHead>
          <TableRow>
            {columns.map((col) => (
              <TableCell key={col.key} align={col.align ?? 'left'}
                sx={{ fontWeight: 600, fontSize: '0.8rem', whiteSpace: 'nowrap', width: col.width }}
              >
                {col.sortable !== false ? (
                  <TableSortLabel active={sortKey === col.key} direction={sortKey === col.key ? sortDir : 'asc'}
                    onClick={() => handleSort(col.key)}
                  >{col.label}</TableSortLabel>
                ) : col.label}
              </TableCell>
            ))}
          </TableRow>
        </TableHead>
        <TableBody>
          {sorted.length === 0 ? (
            <TableRow>
              <TableCell colSpan={columns.length} align="center" sx={{ py: 6 }}>
                <Typography variant="body2" sx={{ color: 'text.secondary' }}>No se encontraron registros</Typography>
              </TableCell>
            </TableRow>
          ) : (
            sorted.map((row, i) => (
              <TableRow key={String((row as any).id ?? i)} sx={{ '&:hover': { bgcolor: 'action.hover' } }}>
                {columns.map((col) => (
                  <TableCell key={col.key} align={col.align ?? 'left'} sx={{ fontSize: '0.8125rem', whiteSpace: 'nowrap' }}>
                    {col.render ? col.render(row[col.key], row) : String(row[col.key] ?? '')}
                  </TableCell>
                ))}
              </TableRow>
            ))
          )}
        </TableBody>
      </Table>
    </TableContainer>
  );
}
