import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { CheckCircle2, Home, ListTodo, ShieldCheck } from 'lucide-react';

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
            <Head title="Feedback Received" />
            <div className="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-6 font-sans text-slate-900 selection:bg-primary selection:text-primary-foreground dark:bg-slate-950 dark:text-slate-100">
                <Card className="w-full max-w-lg overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white text-center shadow-2xl shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
                    <div className="h-2 w-full bg-emerald-500" />
                    <CardContent className="px-8 pb-12 pt-16">
                        <div className="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-3xl bg-emerald-50 dark:bg-emerald-900/20">
                            <CheckCircle2 className="h-12 w-12 text-emerald-500" />
                        </div>

                        <h1 className="font-heading text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Feedback Received
                        </h1>

                        <p className="mx-auto mt-6 max-w-xs text-lg leading-relaxed text-slate-600 dark:text-slate-400">
                            Your contribution to <span className="font-bold text-slate-900 dark:text-white">{survey.title}</span> has been securely logged.
                        </p>

                        <div className="mt-12 flex flex-col gap-4 sm:flex-row sm:justify-center">
                            <Link href="/surveys" className="flex-1">
                                <Button
                                    variant="outline"
                                    className="h-14 w-full rounded-2xl border-slate-200 font-bold hover:bg-slate-50 dark:border-slate-700"
                                >
                                    <ListTodo className="mr-2 h-5 w-5 text-primary" />
                                    Active Initiatives
                                </Button>
                            </Link>
                            <Link href="/" className="flex-1">
                                <Button className="h-14 w-full rounded-2xl bg-slate-900 font-bold text-white transition-all hover:bg-primary hover:text-primary-foreground dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-primary dark:hover:text-primary-foreground">
                                    <Home className="mr-2 h-5 w-5" />
                                    Portal Home
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-12 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                    <ShieldCheck className="h-3 w-3" />
                    Verified Submission &bull; Encrypted
                </div>
            </div>
        </>
    );
}
