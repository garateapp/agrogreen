import { Head } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import GlobalAgriculturalFilter from '@/components/GlobalAgriculturalFilter';
import OnboardingWizard from '@/components/OnboardingWizard';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { dashboard } from '@/routes';
import type { CentroCostoRaw } from '@/types/agricultural';

export default function Dashboard() {
    const { auth } = usePage().props;
    const user = (auth as { user: Record<string, unknown> }).user;
    const isFirstLogin = (user.is_first_login as boolean) ?? false;

    const [showOnboarding, setShowOnboarding] = useState(false);
    const [centros, setCentros] = useState<CentroCostoRaw[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setShowOnboarding(isFirstLogin);
    }, [isFirstLogin]);

    useEffect(() => {
        fetch('/api/centros-costo')
            .then((r) => r.json())
            .then((data) => setCentros(data))
            .catch(() => {})
            .finally(() => setLoading(false));
    }, []);

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <GlobalAgriculturalFilter centrosCosto={centros} loading={loading} />
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                </div>
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>

            <OnboardingWizard
                open={showOnboarding}
                onClose={() => setShowOnboarding(false)}
                onFinish={() => setShowOnboarding(false)}
            />
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
