import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import FlashSnackbar from '@/components/FlashSnackbar';
import SuspensionBanner from '@/components/SuspensionBanner';
import { FilterProvider } from '@/contexts/filter-context';
import type { AppLayoutProps } from '@/types';

export default function AppSidebarLayout({
    children,
    breadcrumbs = [],
}: AppLayoutProps) {
    return (
        <FilterProvider>
            <AppShell variant="sidebar">
                <AppSidebar />
                <AppContent variant="sidebar" className="overflow-x-hidden">
                    <SuspensionBanner />
                    <AppSidebarHeader breadcrumbs={breadcrumbs} />
                    {children}
                    <FlashSnackbar />
                </AppContent>
            </AppShell>
        </FilterProvider>
    );
}
