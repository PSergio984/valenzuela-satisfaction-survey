import { index as surveysIndex } from '@/actions/App/Http/Controllers/SurveyController';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, BarChart3, ClipboardCheck, ShieldCheck, Zap } from 'lucide-react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Insights & Feedback" />
            <div className="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 selection:bg-primary selection:text-white dark:bg-slate-950 dark:text-slate-100">
                {/* Navigation */}
                <header className="sticky top-0 z-50 w-full border-b border-slate-200/60 bg-white/70 backdrop-blur-xl dark:border-slate-800/60 dark:bg-slate-950/70">
                    <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                        <div className="flex items-center gap-2.5">
                            <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-primary shadow-lg shadow-primary/20">
                                <ClipboardCheck className="h-5 w-5 text-white" />
                            </div>
                            <span className="font-heading text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                                Survey <span className="text-primary">System</span>
                            </span>
                        </div>
                        <nav className="flex items-center gap-6">
                            {auth.user ? (
                                <Link
                                    href="/admin"
                                    className="group inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white transition-all hover:bg-primary/90 hover:shadow-xl hover:shadow-primary/10"
                                >
                                    Admin Console
                                    <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                                </Link>
                            ) : (
                                <Link
                                    href="/admin/login"
                                    className="text-sm font-semibold text-slate-600 transition-colors hover:text-primary dark:text-slate-400 dark:hover:text-white"
                                >
                                    Sign In
                                </Link>
                            )}
                        </nav>
                    </div>
                </header>

                <main className="flex-1">
                    {/* Hero Section */}
                    <section className="relative px-6 py-24 sm:py-32 lg:px-8">
                        <div className="mx-auto max-w-3xl text-center">
                            <div className="mb-8 inline-flex items-center gap-2 rounded-full border border-primary/10 bg-primary/5 px-3 py-1 text-xs font-bold tracking-wider text-primary uppercase dark:bg-primary/20">
                                <ShieldCheck className="h-3.5 w-3.5" />
                                Secure & Anonymous Feedback
                            </div>
                            <h1 className="font-heading text-5xl font-extrabold tracking-tight text-slate-900 sm:text-7xl dark:text-white">
                                Modern Feedback <br />
                                <span className="text-primary">Management.</span>
                            </h1>
                            <p className="mt-8 text-lg leading-8 text-slate-600 dark:text-slate-400">
                                High-integrity survey systems built for professional organizations. 
                                Collect, analyze, and act on meaningful insights with confidence.
                            </p>
                            <div className="mt-12 flex items-center justify-center gap-x-6">
                                <Link
                                    href={surveysIndex.url()}
                                    className="rounded-full bg-primary px-8 py-4 text-lg font-bold text-white shadow-2xl shadow-primary/20 transition-all hover:scale-[1.02] hover:bg-primary/90 active:scale-[0.98]"
                                >
                                    Get Started
                                </Link>
                                <Link
                                    href="/admin/login"
                                    className="group text-sm font-bold leading-6 text-slate-900 dark:text-white"
                                >
                                    Admin Login <span className="transition-transform group-hover:inline-block group-hover:translate-x-1" aria-hidden="true">→</span>
                                </Link>
                            </div>
                        </div>
                    </section>

                    {/* Bento Grid Features */}
                    <section className="mx-auto max-w-7xl px-6 pb-32">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div className="col-span-1 flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-8 transition-colors hover:border-primary/20 dark:border-slate-800 dark:bg-slate-900">
                                <div>
                                    <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800">
                                        <Zap className="h-5 w-5 text-primary" />
                                    </div>
                                    <h3 className="font-heading text-xl font-bold">Instant Response</h3>
                                    <p className="mt-3 text-slate-600 dark:text-slate-400">
                                        Frictionless completion experience designed to maximize response rates across all devices.
                                    </p>
                                </div>
                            </div>
                            <div className="col-span-1 flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-8 transition-colors hover:border-primary/20 dark:border-slate-800 dark:bg-slate-900">
                                <div>
                                    <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800">
                                        <ShieldCheck className="h-5 w-5 text-primary" />
                                    </div>
                                    <h3 className="font-heading text-xl font-bold">Privacy First</h3>
                                    <p className="mt-3 text-slate-600 dark:text-slate-400">
                                        Enterprise-grade security ensuring your feedback data remains private and protected at all times.
                                    </p>
                                </div>
                            </div>
                            <div className="col-span-1 flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-8 transition-colors hover:border-primary/20 dark:border-slate-800 dark:bg-slate-900">
                                <div>
                                    <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800">
                                        <BarChart3 className="h-5 w-5 text-primary" />
                                    </div>
                                    <h3 className="font-heading text-xl font-bold">Deep Analytics</h3>
                                    <p className="mt-3 text-slate-600 dark:text-slate-400">
                                        Transform raw data into actionable intelligence with our advanced reporting dashboard.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                </main>

                {/* Footer */}
                <footer className="border-t border-slate-200 bg-white py-12 dark:border-slate-800 dark:bg-slate-950">
                    <div className="mx-auto max-w-7xl px-6">
                        <div className="flex flex-col items-center justify-between gap-6 sm:flex-row">
                            <div className="flex items-center gap-2">
                                <ClipboardCheck className="h-5 w-5 text-primary/50" />
                                <span className="text-sm font-bold tracking-tight text-slate-500">
                                    Valenzuela<span className="text-slate-400">Insights</span>
                                </span>
                            </div>
                            <p className="text-sm text-slate-500 dark:text-slate-400">
                                &copy; {new Date().getFullYear()} Survey System. Built for Excellence.
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
