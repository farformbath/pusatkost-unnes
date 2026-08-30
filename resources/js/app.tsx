import '../css/app.css';
import { createInertiaApp } from '@inertiajs/react';
import type { ResolvedComponent } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

const appName = (import.meta.env.VITE_APP_NAME as string) || 'Pusat Kost UNNES';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: async (name): Promise<ResolvedComponent> => {
        const pages = import.meta.glob<{ default: ResolvedComponent }>('./Pages/**/*.tsx');
        const pagePath = `./Pages/${name}.tsx`;
        const importPage = pages[pagePath];
        if (!importPage) {
            throw new Error(`Page not found: ${pagePath}`);
        }
        const module = await importPage();
        return module.default;
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#FF6565',
    },
});
