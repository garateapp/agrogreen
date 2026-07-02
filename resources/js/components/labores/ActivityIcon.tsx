import Agriculture from '@mui/icons-material/Agriculture';
import BugReport from '@mui/icons-material/BugReport';
import Build from '@mui/icons-material/Build';
import Cable from '@mui/icons-material/Cable';
import CleaningServices from '@mui/icons-material/CleaningServices';
import Construction from '@mui/icons-material/Construction';
import ContentCut from '@mui/icons-material/ContentCut';
import Deck from '@mui/icons-material/Deck';
import Engineering from '@mui/icons-material/Engineering';
import FormatPaint from '@mui/icons-material/FormatPaint';
import Handyman from '@mui/icons-material/Handyman';
import Roofing from '@mui/icons-material/Roofing';
import Shield from '@mui/icons-material/Shield';
import WaterDrop from '@mui/icons-material/WaterDrop';
import Yard from '@mui/icons-material/Yard';

const ICON_MAP: Record<string, React.ComponentType<{ fontSize?: 'small' | 'inherit' | 'large' | 'medium' }>> = {
  Agriculture,
  Build,
  BugReport,
  Cable,
  CleaningServices,
  Construction,
  ContentCut,
  Deck,
  Engineering,
  FormatPaint,
  Handyman,
  Roofing,
  Shield,
  WaterDrop,
  Yard,
};

interface Props {
  name: string;
  fontSize?: 'small' | 'inherit' | 'large' | 'medium';
}

export default function ActivityIcon({ name, fontSize = 'small' }: Props) {
  const Icon = ICON_MAP[name];

  if (!Icon) {
return null;
}

  return <Icon fontSize={fontSize} />;
}
