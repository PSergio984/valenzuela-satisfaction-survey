import { index as surveysIndex } from '@/actions/App/Http/Controllers/SurveyController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/pagination';
import { type Survey } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { ArrowRight, ClipboardCheck, Search, Sparkles, LayoutGrid } from 'lucide-react';
import * as React from 'react';
import AppearanceToggleDropdown from '@/components/appearance-dropdown';

interface Props {
    surveys: {
        data: Survey[];
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
        meta: {
            current_page: number;
            from: number | null;
            last_page: number;
            path: string;
            per_page: number;
            to: number | null;
            total: number;
        };
    };
    filters: {
        search: string;
    };
}

export default function SurveyIndex({ surveys, filters }: Props) {
    const [search, setSearch] = React.useState(filters.search || '');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get(
            surveysIndex.url(),
            { search },
            { preserveState: true, replace: true }
        );
    };

    const isNew = (createdAt: string) => {
        const created = new Date(createdAt);
        const now = new Date();
        const diffInHours = (now.getTime() - created.getTime()) / (1000 * 60 * 60);
        return diffInHours <= 48;
    };

    return (
        <>
            <Head title="Available Surveys" />
            <div className="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 selection:bg-primary selection:text-primary-foreground dark:bg-slate-950 dark:text-slate-100">
                {/* Header */}
                <header className="sticky top-0 z-50 w-full border-b border-slate-200/60 bg-white/70 backdrop-blur-xl dark:border-slate-800/60 dark:bg-slate-950/70">
                    <div className="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                        <div className="flex items-center gap-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-primary shadow-lg shadow-primary/20">
                                <ClipboardCheck className="h-5 w-5 text-white" />
                            </div>
                            <span className="font-heading text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                                Survey System
                            </span>
                        </div>
                        <div className="flex items-center gap-4">
                            <AppearanceToggleDropdown />
                            <Link
                                href="/"
                                className="group flex items-center gap-1.5 text-sm font-bold text-slate-500 transition-colors hover:text-primary dark:text-slate-400"
                            >
                                Return Home
                            </Link>
                        </div>
                    </div>
                </header>

                {/* Main Content */}
                <main className="flex-1 px-6 py-16">
                    <div className="mx-auto max-w-5xl">
                        <div className="mb-16 text-center">
                            <h1 className="font-heading text-4xl font-extrabold tracking-tight sm:text-5xl">
                                Active Surveys
                            </h1>
                            <p className="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-slate-600 dark:text-slate-400">
                                Participate in our professional feedback initiatives. Your perspective 
                                contributes to organizational excellence and service refinement.
                            </p>

                            <form
                                onSubmit={handleSearch}
                                className="mx-auto mt-10 flex max-w-lg items-center gap-3"
                            >
                                <div className="relative flex-1">
                                    <Search className="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                    <Input
                                        type="text"
                                        placeholder="Search by title or category..."
                                        className="h-12 rounded-xl border-slate-200 bg-white pl-11 text-base shadow-sm focus:ring-primary dark:border-slate-800 dark:bg-slate-900"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                    />
                                </div>
                                <Button 
                                    type="submit"
                                    className="h-12 rounded-xl bg-primary px-6 font-bold text-primary-foreground transition-all hover:bg-primary/90"
                                >
                                    Filter
                                </Button>
                            </form>
                        </div>

                        {surveys.data.length === 0 ? (
                            <Card className="rounded-3xl border-dashed border-slate-200 bg-transparent text-center shadow-none dark:border-slate-800">
                                <CardContent className="py-20">
                                    <div className="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-900">
                                        <LayoutGrid className="h-8 w-8 text-slate-300" />
                                    </div>
                                    <h3 className="font-heading text-2xl font-bold text-slate-900 dark:text-white">
                                        {filters.search ? 'Search Term Unmatched' : 'Vault Currently Empty'}
                                    </h3>
                                    <p className="mt-2 font-medium text-slate-500 dark:text-slate-400">
                                        {filters.search
                                            ? `No professional surveys match your query: "${filters.search}"`
                                            : 'No survey initiatives are currently active.'}
                                    </p>
                                    {filters.search && (
                                        <Button
                                            variant="outline"
                                            className="mt-8 rounded-full border-slate-200 font-bold"
                                            onClick={() => {
                                                setSearch('');
                                                router.get(surveysIndex.url());
                                            }}
                                        >
                                            Reset Filters
                                        </Button>
                                    )}
                                </CardContent>
                            </Card>
                        ) : (
                            <div className="space-y-12">
                                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-2">
                                    {surveys.data.map((survey) => (
                                        <Card
                                            key={survey.id}
                                            className="group relative overflow-hidden rounded-3xl border border-slate-200 bg-white transition-all hover:border-primary/20 hover:shadow-2xl hover:shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-primary/30 dark:hover:shadow-none"
                                        >
                                            {isNew(survey.created_at) && (
                                                <div className="absolute right-4 top-4">
                                                    <Badge className="bg-primary hover:bg-primary border-none text-[10px] font-bold uppercase tracking-widest px-2.5 py-1">
                                                        <Sparkles className="mr-1 h-3 w-3" />
                                                        Fresh
                                                    </Badge>
                                                </div>
                                            )}
                                            <CardHeader className="p-8">
                                                <CardTitle className="pr-12">
                                                    <span className="font-heading text-2xl font-extrabold tracking-tight">
                                                        {survey.title}
                                                    </span>
                                                </CardTitle>
                                                {survey.description && (
                                                    <CardDescription className="mt-3 text-base leading-relaxed text-slate-500 line-clamp-2">
                                                        {survey.description}
                                                    </CardDescription>
                                                )}
                                            </CardHeader>
                                            <CardContent className="p-8 pt-0">
                                                <Link
                                                    href={`/surveys/${survey.slug}`}
                                                >
                                                    <Button className="h-12 w-full rounded-xl bg-slate-900 font-bold text-white transition-all group-hover:bg-primary group-hover:text-primary-foreground dark:bg-slate-800 dark:text-slate-100 dark:group-hover:bg-primary dark:group-hover:text-primary-foreground">
                                                        Access Survey
                                                        <ArrowRight className="ml-2 h-4 w-4" />
                                                    </Button>
                                                </Link>
                                            </CardContent>
                                        </Card>
                                    ))}
                                </div>

                                <Pagination links={surveys.links} />
                            </div>
                        )}
                    </div>
                </main>

                {/* Footer */}
                <footer className="border-t border-slate-200 bg-white py-12 dark:border-slate-800 dark:bg-slate-950">
                    <div className="mx-auto max-w-5xl px-6 text-center">
                        <p className="text-sm font-bold tracking-widest text-slate-400 uppercase">
                            SURVEY SYSTEM — CUSTOMER SATISFACTION
                        </p>
                    </div>
                </footer>
            </div>
        </>
    );
}
