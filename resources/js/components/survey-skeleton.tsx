import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';

export function SurveyCardSkeleton() {
    return (
        <Card className="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <CardHeader className="p-8">
                <Skeleton className="h-8 w-3/4 rounded-lg" />
                <Skeleton className="mt-4 h-4 w-full rounded-md" />
                <Skeleton className="mt-2 h-4 w-5/6 rounded-md" />
            </CardHeader>
            <CardContent className="flex items-center justify-between border-t border-slate-100 p-8 dark:border-slate-800">
                <div className="flex gap-4">
                    <Skeleton className="h-4 w-20 rounded-md" />
                    <Skeleton className="h-4 w-24 rounded-md" />
                </div>
                <Skeleton className="h-10 w-32 rounded-full" />
            </CardContent>
        </Card>
    );
}

export function SurveyListSkeleton({ count = 4 }: { count?: number }) {
    return (
        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-2">
            {Array.from({ length: count }).map((_, i) => (
                <SurveyCardSkeleton key={i} />
            ))}
        </div>
    );
}

export function QuestionSkeleton() {
    return (
        <div className="space-y-6 rounded-2xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900">
            <Skeleton className="h-6 w-1/2 rounded-md" />
            <Skeleton className="h-12 w-full rounded-xl" />
        </div>
    );
}

export function SurveyShowSkeleton({ count = 3 }: { count?: number }) {
    return (
        <div className="space-y-12">
            <div className="space-y-8">
                <Skeleton className="mx-auto h-12 w-3/4 rounded-xl" />
                <Skeleton className="mx-auto h-4 w-1/2 rounded-md" />
            </div>
            <div className="space-y-8">
                {Array.from({ length: count }).map((_, i) => (
                    <QuestionSkeleton key={i} />
                ))}
            </div>
        </div>
    );
}
