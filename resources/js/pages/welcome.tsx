import { index as surveysIndex } from '@/actions/App/Http/Controllers/SurveyController';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, BarChart3, ClipboardList, Users } from 'lucide-react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Welcome" />
            <div className="flex min-h-screen flex-col bg-gradient-to-b from-blue-50 to-white dark:from-gray-900 dark:to-gray-800">
                {/* Header */}
                <header className="w-full border-b border-gray-200 bg-white/80 backdrop-blur-sm dark:border-gray-700 dark:bg-gray-900/80">
                    <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                        <div className="flex items-center gap-3">
                            <span className="text-xl font-bold text-gray-900 dark:text-white">
                                Survey System
                            </span>
                        </div>
                        <nav className="flex items-center gap-4">
                            {auth.user ? (
                                <a
                                    href="/admin"
                                    className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                                >
                                    Dashboard
                                    <ArrowRight className="h-4 w-4" />
                                </a>
                            ) : (
                                <a
                                    href="/admin/login"
                                    className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                                >
                                    Log in
                                </a>
                            )}
                        </nav>
                    </div>
                </header>

                {/* Hero Section */}
                <main className="flex flex-1 flex-col items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-4xl text-center">
                            <h1 className="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white">
                                Feedback & Survey
                                <span className="block text-blue-600">
                                    Management System
                                </span>
                            </h1>
                        <p className="mx-auto mt-6 max-w-2xl text-lg text-gray-600 dark:text-gray-300">
                            Help us improve our services by sharing your
                            feedback. Your responses are valuable and will help
                            us serve you better.
                        </p>
                        <div className="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                            <Link
                                href={surveysIndex.url()}
                                className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-lg font-medium text-white shadow-lg transition hover:bg-blue-700 hover:shadow-xl"
                            >
                                View Surveys
                                <ArrowRight className="h-5 w-5" />
                            </Link>
                        </div>
                    </div>

                    {/* Features */}
                    <div className="mx-auto mt-20 grid max-w-5xl gap-8 sm:grid-cols-3">
                        <div className="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                                <ClipboardList className="h-6 w-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                Easy to Use
                            </h3>
                            <p className="mt-2 text-gray-600 dark:text-gray-400">
                                Simple and intuitive surveys that take just a
                                few minutes to complete.
                            </p>
                        </div>
                        <div className="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                                <Users className="h-6 w-6 text-green-600 dark:text-green-400" />
                            </div>
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                Your Voice Matters
                            </h3>
                            <p className="mt-2 text-gray-600 dark:text-gray-400">
                                Every response helps shape better services for
                                our community.
                            </p>
                        </div>
                        <div className="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900">
                                <BarChart3 className="h-6 w-6 text-purple-600 dark:text-purple-400" />
                            </div>
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                Data-Driven
                            </h3>
                            <p className="mt-2 text-gray-600 dark:text-gray-400">
                                Your feedback is analyzed to improve city
                                services continuously.
                            </p>
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <footer className="border-t border-gray-200 bg-white py-8 dark:border-gray-700 dark:bg-gray-900">
                    <div className="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            © {new Date().getFullYear()} Survey System. All
                            rights reserved.
                        </p>
                    </div>
                </footer>
            </div>
        </>
    );
}
