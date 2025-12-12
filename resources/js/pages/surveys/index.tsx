import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { type Survey } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowRight, ClipboardList } from 'lucide-react';

interface Props {
    surveys: Survey[];
}

export default function SurveyIndex({ surveys }: Props) {
    return (
        <>
            <Head title="Surveys" />
            <div className="flex min-h-screen flex-col bg-gradient-to-b from-blue-50 to-white dark:from-gray-900 dark:to-gray-800">
                {/* Header */}
                <header className="border-b border-gray-200 bg-white/80 backdrop-blur-sm dark:border-gray-700 dark:bg-gray-900/80">
                    <div className="mx-auto flex max-w-4xl items-center justify-between px-4 py-4">
                        <div className="flex items-center gap-2">
                            <img
                                src="/images/logo.png"
                                alt="Logo"
                                className="h-8 w-8"
                            />
                            <span className="text-lg font-semibold text-gray-900 dark:text-white">
                                Customer Satisfaction Surveys
                            </span>
                        </div>
                        <Link
                            href="/"
                            className="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                        >
                            ← Back to Home
                        </Link>
                    </div>
                </header>

                {/* Main Content */}
                <main className="flex-1 px-4 py-12">
                    <div className="mx-auto max-w-4xl">
                        <div className="mb-8 text-center">
                            <h1 className="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                                Available Surveys
                            </h1>
                            <p className="mt-3 text-lg text-gray-600 dark:text-gray-400">
                                Your feedback helps us improve our services.
                                Please take a moment to complete one of our
                                surveys.
                            </p>
                        </div>

                        {surveys.length === 0 ? (
                            <Card className="text-center">
                                <CardContent className="py-12">
                                    <ClipboardList className="mx-auto h-12 w-12 text-gray-400" />
                                    <h3 className="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                                        No surveys available
                                    </h3>
                                    <p className="mt-2 text-gray-500 dark:text-gray-400">
                                        There are currently no active surveys.
                                        Please check back later.
                                    </p>
                                </CardContent>
                            </Card>
                        ) : (
                            <div className="grid gap-6 md:grid-cols-2">
                                {surveys.map((survey) => (
                                    <Card
                                        key={survey.id}
                                        className="transition-shadow hover:shadow-lg dark:hover:shadow-gray-900/50"
                                    >
                                        <CardHeader>
                                            <CardTitle className="flex items-start justify-between">
                                                <span className="text-xl">
                                                    {survey.title}
                                                </span>
                                            </CardTitle>
                                            {survey.description && (
                                                <CardDescription className="line-clamp-2">
                                                    {survey.description}
                                                </CardDescription>
                                            )}
                                        </CardHeader>
                                        <CardContent>
                                            <Link
                                                href={`/surveys/${survey.slug}`}
                                            >
                                                <Button className="w-full hover:bg-blue-600">
                                                    Take Survey
                                                    <ArrowRight className="ml-2 h-4 w-4" />
                                                </Button>
                                            </Link>
                                        </CardContent>
                                    </Card>
                                ))}
                            </div>
                        )}
                    </div>
                </main>

                {/* Footer */}
                <footer className="border-t border-gray-200 bg-white py-6 dark:border-gray-700 dark:bg-gray-900">
                    <div className="mx-auto max-w-4xl px-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        <p>Your responses are anonymous and confidential.</p>
                    </div>
                </footer>
            </div>
        </>
    );
}
