import { createTheme  } from '@mui/material/styles';
import type {ThemeOptions} from '@mui/material/styles';

declare module '@mui/material/Button' {
  interface ButtonPropsColorOverrides {
    cherry: true;
  }
}

declare module '@mui/material/styles' {
  interface Palette {
    cherry: Palette['primary'];
  }
  interface PaletteOptions {
    cherry?: PaletteOptions['primary'];
  }
}

const baseTheme: ThemeOptions = {
  typography: {
    fontFamily: '"Raleway", "Helvetica", "Arial", sans-serif',
    h1: {
      fontFamily: '"Lora", "Georgia", serif',
      fontWeight: 700,
    },
    h2: {
      fontFamily: '"Lora", "Georgia", serif',
      fontWeight: 600,
    },
    h3: {
      fontFamily: '"Lora", "Georgia", serif',
      fontWeight: 600,
    },
    h4: {
      fontFamily: '"Lora", "Georgia", serif',
      fontWeight: 600,
    },
    h5: {
      fontFamily: '"Lora", "Georgia", serif',
      fontWeight: 500,
    },
    h6: {
      fontFamily: '"Lora", "Georgia", serif',
      fontWeight: 500,
    },
    button: {
      textTransform: 'none',
      fontWeight: 600,
    },
  },
  shape: {
    borderRadius: 8,
  },
};

export const lightTheme = createTheme({
  ...baseTheme,
  palette: {
    mode: 'light',
    primary: {
      main: '#2E7D32',
      light: '#4CAF50',
      dark: '#1B5E20',
      contrastText: '#FFFFFF',
    },
    secondary: {
      main: '#F9A825',
      light: '#FFD54F',
      dark: '#F57F17',
      contrastText: '#1B1B18',
    },
    error: {
      main: '#C62828',
      light: '#EF5350',
      dark: '#B71C1C',
      contrastText: '#FFFFFF',
    },
    warning: {
      main: '#ED6C02',
      light: '#FF9800',
      dark: '#E65100',
    },
    info: {
      main: '#0288D1',
      light: '#03A9F4',
      dark: '#01579B',
    },
    success: {
      main: '#2E7D32',
      light: '#4CAF50',
      dark: '#1B5E20',
    },
    background: {
      default: '#FAFAF5',
      paper: '#FFFFFF',
    },
    text: {
      primary: '#1B1B18',
      secondary: '#5F5F5A',
    },
    divider: '#E6E3DD',
    cherry: {
      main: '#C62828',
      light: '#EF5350',
      dark: '#8E0000',
      contrastText: '#FFFFFF',
    },
  },
});

export const darkTheme = createTheme({
  ...baseTheme,
  palette: {
    mode: 'dark',
    primary: {
      main: '#4CAF50',
      light: '#81C784',
      dark: '#2E7D32',
      contrastText: '#000000',
    },
    secondary: {
      main: '#FFD54F',
      light: '#FFE082',
      dark: '#F9A825',
      contrastText: '#1B1B18',
    },
    error: {
      main: '#EF5350',
      light: '#E57373',
      dark: '#C62828',
      contrastText: '#000000',
    },
    warning: {
      main: '#FF9800',
      light: '#FFB74D',
      dark: '#ED6C02',
    },
    info: {
      main: '#03A9F4',
      light: '#29B6F6',
      dark: '#0288D1',
    },
    success: {
      main: '#4CAF50',
      light: '#81C784',
      dark: '#2E7D32',
    },
    background: {
      default: '#121212',
      paper: '#1E1E1E',
    },
    text: {
      primary: '#EDEDEC',
      secondary: '#A1A09A',
    },
    divider: '#3E3E3A',
    cherry: {
      main: '#EF5350',
      light: '#E57373',
      dark: '#C62828',
      contrastText: '#000000',
    },
  },
});
