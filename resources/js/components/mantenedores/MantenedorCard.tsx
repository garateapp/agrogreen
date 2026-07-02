import { MoreVert, Edit, Delete, Block } from '@mui/icons-material';
import {
    Box,
    Card,
    CardContent,
    IconButton,
    Typography,
    Menu,
    MenuItem,
    ListItemIcon,
    ListItemText,
    Avatar,
    Chip,
    Checkbox,
} from '@mui/material';
import { useState } from 'react';
import type { MantenedorConfig, MantenedorAction } from './mantenedor-types';

interface Props {
    item: Record<string, unknown>;
    config: MantenedorConfig;
    selected?: boolean;
    onEdit?: (item: Record<string, unknown>) => void;
    onToggleSelect?: () => void;
    onToggleStatus?: (item: Record<string, unknown>) => void;
    onDelete?: (item: Record<string, unknown>) => void;
}

const AVATAR_COLORS = ['#2E7D32', '#1B5E20', '#4CAF50', '#388E3C', '#81C784', '#C62828'];

function getAvatarColor(name: string): string {
    if (!name) {
return AVATAR_COLORS[0];
}

    let hash = 0;

    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    return AVATAR_COLORS[Math.abs(hash) % AVATAR_COLORS.length];
}

export default function MantenedorCard({
    item,
    config,
    selected,
    onEdit,
    onToggleSelect,
    onToggleStatus,
    onDelete,
}: Props) {
    const [anchorEl, setAnchorEl] = useState<null | HTMLElement>(null);
    const open = Boolean(anchorEl);

    const title = config.cardTitle(item);
    const subtitle = config.cardSubtitle?.(item);
    const metadata = config.cardMetadata?.(item);
    const avatarColor = config.cardAvatarColor?.(item) ?? getAvatarColor(title);
    const isActive = (item.activo ?? item.estado ?? true) !== false;

    const actions: MantenedorAction[] = [
        ...(config.actions ?? []),
        ...(onEdit ? [{ label: 'Editar', icon: <Edit fontSize="small" />, onClick: () => onEdit(item), color: 'default' as const }] : []),
        ...(onToggleStatus ? [{ label: isActive ? 'Desactivar' : 'Activar', icon: <Block fontSize="small" />, onClick: () => onToggleStatus(item), color: 'warning' as const }] : []),
        ...(onDelete ? [{ label: 'Eliminar', icon: <Delete fontSize="small" />, onClick: () => onDelete(item), color: 'error' as const }] : []),
    ];

    return (
        <Card
            elevation={0}
            sx={{
                position: 'relative',
                display: 'flex',
                alignItems: 'center',
                gap: 2,
                p: 1.5,
                border: 1,
                borderColor: 'divider',
                borderRadius: 2,
                opacity: isActive ? 1 : 0.55,
                transition: 'all 0.15s',
                '&:hover': { borderColor: 'primary.light', bgcolor: 'action.hover' },
            }}
        >
            {onToggleSelect && (
                <Checkbox
                    size="small"
                    checked={selected ?? false}
                    onChange={onToggleSelect}
                    sx={{
                        position: 'absolute',
                        top: 4,
                        left: 4,
                        zIndex: 1,
                        opacity: selected ? 1 : 0,
                        '&:hover': { opacity: 1 },
                        '.MuiCard-root:hover &': { opacity: 1 },
                    }}
                />
            )}
            <Avatar
                sx={{
                    width: 44,
                    height: 44,
                    bgcolor: avatarColor,
                    fontSize: '1rem',
                    fontWeight: 700,
                    flexShrink: 0,
                }}
            >
                {(title ?? '?').charAt(0).toUpperCase()}
            </Avatar>

            <Box sx={{ flex: 1, minWidth: 0 }}>
                <Typography variant="body2" sx={{ fontWeight: 600, lineHeight: 1.3 }}>
                    {title}
                </Typography>
                {subtitle && (
                    <Typography variant="caption" sx={{ color: 'text.secondary', display: 'block' }}>
                        {subtitle}
                    </Typography>
                )}
                {metadata && (
                    <Box sx={{ mt: 0.5 }}>
                        {typeof metadata === 'string' ? (
                            <Chip
                                label={metadata}
                                size="small"
                                variant="outlined"
                                sx={{ height: 20, fontSize: '0.6875rem' }}
                            />
                        ) : (
                            metadata
                        )}
                    </Box>
                )}
            </Box>

            {actions.length > 0 && (
                <>
                    <IconButton
                        size="small"
                        onClick={(e) => setAnchorEl(e.currentTarget)}
                    >
                        <MoreVert fontSize="small" />
                    </IconButton>
                    <Menu
                        anchorEl={anchorEl}
                        open={open}
                        onClose={() => setAnchorEl(null)}
                        transformOrigin={{ horizontal: 'right', vertical: 'top' }}
                        anchorOrigin={{ horizontal: 'right', vertical: 'bottom' }}
                    >
                        {actions.map((action) => (
                            <MenuItem
                                key={action.label}
                                onClick={() => {
                                    setAnchorEl(null);
                                    action.onClick(item);
                                }}
                            >
                                {action.icon && (
                                    <ListItemIcon>{action.icon}</ListItemIcon>
                                )}
                                <ListItemText>{action.label}</ListItemText>
                            </MenuItem>
                        ))}
                    </Menu>
                </>
            )}
        </Card>
    );
}
