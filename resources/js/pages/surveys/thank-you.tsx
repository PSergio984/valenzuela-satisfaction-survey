import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { CheckCircle2, Home, ListTodo } from 'lucide-react';

interface Props {
    survey: {
        id: number;
        title: string;
        slug: string;
    };
}

export default function ThankYou({ survey }: Props) {
    return (
        <>
            <Head title="Thank You" />
            <div className="flex min-h-screen flex-col items-center justify-center bg-gradient-to-b from-green-50 to-white px-4 dark:from-gray-900 dark:to-gray-800">
                <Card className="w-full max-w-md text-center">
                    <CardContent className="pt-10 pb-8">
                        <div className="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                            <CheckCircle2 className="h-12 w-12 text-green-600 dark:text-green-400" />
                        </div>

                        <h1 className="mb-2 text-2xl font-bold text-gray-900 dark:text-white">
                            Thank You!
                        </h1>

                        <p className="mb-6 text-gray-600 dark:text-gray-400">
                            Your feedback for{' '}
                            <span className="font-medium">{survey.title}</span>{' '}
                            has been submitted successfully. We appreciate you
                            taking the time to share your thoughts with us.
                        </p>

                        <div className="flex flex-col gap-3 sm:flex-row sm:justify-center">
                            <Link href="/surveys">
                                <Button
                                    variant="outline"
                                    className="w-full sm:w-auto"
                                >
                                    <ListTodo className="mr-2 h-4 w-4" />
                                    More Surveys
                                </Button>
                            </Link>
                            <Link href="/">
                                <Button className="w-full sm:w-auto">
                                    <Home className="mr-2 h-4 w-4" />
                                    Back to Home
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <p className="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    Your responses are anonymous and confidential.
                </p>
            </div>
        </>
    );
}
