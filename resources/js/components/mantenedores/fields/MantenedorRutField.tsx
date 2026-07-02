import { TextField  } from '@mui/material';
import type {TextFieldProps} from '@mui/material';
import { useState, useCallback } from 'react';

type Props = Omit<TextFieldProps, 'onChange' | 'value'> & {
    value: string;
    onChange: (value: string) => void;
};

function formatRut(value: string): string {
    const clean = value.replace(/[^0-9kK]/g, '');

    if (clean.length <= 1) {
return clean.toUpperCase();
}

    const body = clean.slice(0, -1);
    const dv = clean.slice(-1).toUpperCase();
    const formattedBody = Number(body).toLocaleString('es-CL');

    return `${formattedBody}-${dv}`;
}

export default function MantenedorRutField({ value, onChange, ...props }: Props) {
    const [displayValue, setDisplayValue] = useState(() => formatRut(value));

    const handleChange = useCallback(
        (e: React.ChangeEvent<HTMLInputElement>) => {
            const raw = e.target.value.replace(/[^0-9kK]/g, '');
            const formatted = formatRut(raw);
            setDisplayValue(formatted);
            onChange(raw);
        },
        [onChange],
    );

    return (
        <TextField
            {...props}
            value={displayValue}
            onChange={handleChange}
            placeholder="12.345.678-9"
        />
    );
}
