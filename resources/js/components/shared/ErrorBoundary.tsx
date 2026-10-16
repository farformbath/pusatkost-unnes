import React, { Component, ErrorInfo, ReactNode } from 'react';
import { Button } from '@/components/ui/Button';

interface Props {
    children: ReactNode;
    fallback?: ReactNode;
}

interface State {
    hasError: boolean;
}

export class ErrorBoundary extends Component<Props, State> {
    public state: State = {
        hasError: false,
    };

    public static getDerivedStateFromError(_: Error): State {
        return { hasError: true };
    }

    public componentDidCatch(error: Error, errorInfo: ErrorInfo): void {
        console.error('ErrorBoundary caught an interface error:', error, errorInfo);
    }

    private handleReset = (): void => {
        this.setState({ hasError: false });
        window.location.reload();
    };

    public render(): ReactNode {
        if (this.state.hasError) {
            if (this.props.fallback) {
                return this.props.fallback;
            }

            return (
                <div className="min-h-[400px] flex items-center justify-center p-6">
                    <div className="max-w-md w-full bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm text-center space-y-5">
                        <div className="w-14 h-14 bg-rose-50 text-[#FF6565] rounded-2xl flex items-center justify-center text-2xl mx-auto border border-rose-100 shadow-2xs">
                            ⚠️
                        </div>

                        <div className="space-y-1.5">
                            <h2 className="text-xl font-black text-slate-900 tracking-tight">
                                Terjadi Kendala Antarmuka
                            </h2>
                            <p className="text-xs text-slate-500 leading-relaxed">
                                Komponen ini mengalami kendala sementara saat memproses tampilan data. Silakan muat ulang halaman atau kembali ke katalog.
                            </p>
                        </div>

                        <div className="pt-2 flex justify-center gap-3">
                            <Button variant="primary" size="sm" onClick={this.handleReset} className="shadow-xs">
                                🔄 Muat Ulang Halaman
                            </Button>
                            <Button variant="outline" size="sm" onClick={() => { window.location.href = '/kos'; }}>
                                Ke Katalog Kos
                            </Button>
                        </div>
                    </div>
                </div>
            );
        }

        return this.props.children;
    }
}
