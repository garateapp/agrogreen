import { router } from '@inertiajs/react';
import { Close } from '@mui/icons-material';
import {
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Button,
    Box,
    Typography,
} from '@mui/material';
import { useState, useEffect } from 'react';
import type { MantenedorConfig } from './mantenedor-types';
import MantenedorFieldFactory from './MantenedorFieldFactory';

interface Props {
    open: boolean;
    onClose: () => void;
    config: MantenedorConfig;
    item?: Record<string, unknown> | null;
    title?: string;
}

export default function MantenedorFormModal({ open, onClose, config, item, title }: Props) {
    const isEditing = !!item;

    const [formData, setFormData] = useState<Record<string, string | number | boolean>>({});
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        if (open) {
            const data = { ...item } as Record<string, string | number | boolean>;

            for (const f of config.fields) {
                if (!(f.name in data)) {
                    data[f.name] = f.type === 'switch' ? false : '';
                }
            }

            setFormData(data);
            setErrors({});
        }
    }, [open, item]);

    const handleChange = (name: string, value: string | number | boolean) => {
        setFormData((prev) => ({ ...prev, [name]: value }));
        setErrors((prev) => {
            const next = { ...prev };
            delete next[name];

            return next;
        });
    };

    const handleSubmit = () => {
        setSaving(true);
        const method = isEditing ? 'put' as const : 'post' as const;
        const url = isEditing
            ? `${config.endpoint}/${item?.id}`
            : config.endpoint;

        router[method](url, formData as Record<string, string>, {
            preserveScroll: true,
            onSuccess: () => {
                setSaving(false);
                onClose();
                router.reload();
            },
            onError: (errs) => {
                setSaving(false);
                setErrors(errs as Record<string, string>);
            },
        });
    };

    return (
        <Dialog
            open={open}
            onClose={onClose}
            maxWidth="sm"
            fullWidth
            slotProps={{
                paper: {
                    sx: { borderRadius: 2.5 },
                },
            }}
        >
            <DialogTitle sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', fontWeight: 600, fontFamily: '"Lora", serif', fontSize: '1.125rem' }}>
                    {title ?? (isEditing ? `Editar ${config.title}` : `Nuevo ${config.title}`)}
                <Button
                    size="small"
                    onClick={onClose}
                    sx={{ minWidth: 32, p: 0.5 }}
                >
                    <Close fontSize="small" />
                </Button>
            </DialogTitle>

            <DialogContent>
                <Box sx={{ pt: 1 }}>
                    <MantenedorFieldFactory
                        fields={config.fields}
                        values={formData}
                        errors={errors}
                        onChange={handleChange}
                    />
                    {config.renderFormExtra?.({
                        formData,
                        onChange: handleChange,
                        errors,
                    })}
                </Box>
            </DialogContent>

            <DialogActions sx={{ p: 2.5, pt: 0 }}>
                <Button onClick={onClose} variant="outlined" color="inherit">
                    Cancelar
                </Button>
                <Button
                    onClick={handleSubmit}
                    variant="contained"
                    disabled={saving}
                >
                    {saving ? 'Guardando...' : isEditing ? 'Actualizar' : 'Crear'}
                </Button>
            </DialogActions>
        </Dialog>
    );
}
