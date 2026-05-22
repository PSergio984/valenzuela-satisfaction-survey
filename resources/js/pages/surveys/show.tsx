import { Progress } from '@/components/ui/progress';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type Question, type Survey } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Loader2, Star, CheckCircle2, ClipboardCheck, Lock } from 'lucide-react';
import { FormEvent, useEffect, useState } from 'react';
import AppearanceToggleDropdown from '@/components/appearance-dropdown';

interface Props {
    survey: Survey;
}

interface FormData {
    respondent_name: string;
    respondent_email: string;
    respondent_phone: string;
    started_at: string;
    answers: Record<number, string | string[]>;
}

export default function SurveyShow({ survey }: Props) {
    const { data, setData, post, processing, errors } = useForm<FormData>({
        respondent_name: '',
        respondent_phone: '',
        respondent_email: '',
        started_at: '',
        answers: {},
    });

    useEffect(() => {
        setData('started_at', new Date().toISOString());
    }, []);

    const [progress, setProgress] = useState(0);

    useEffect(() => {
        if (!survey.questions) return;
        
        const requiredQuestions = survey.questions.filter(q => q.is_required);
        if (requiredQuestions.length === 0) {
            setProgress(100);
            return;
        }

        const answeredRequired = requiredQuestions.filter(q => {
            const answer = data.answers[q.id];
            if (Array.isArray(answer)) return answer.length > 0;
            return answer !== undefined && answer !== '';
        });

        setProgress(Math.round((answeredRequired.length / requiredQuestions.length) * 100));
    }, [data.answers, survey.questions]);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(`/surveys/${survey.slug}`);
    };

    const handleAnswerChange = (
        questionId: number,
        value: string | string[],
    ) => {
        setData('answers', {
            ...data.answers,
            [questionId]: value,
        });
    };

    const handleCheckboxChange = (
        questionId: number,
        optionValue: string,
        checked: boolean,
    ) => {
        const currentValues = (data.answers[questionId] as string[]) || [];
        let newValues: string[];

        if (checked) {
            newValues = [...currentValues, optionValue];
        } else {
            newValues = currentValues.filter((v) => v !== optionValue);
        }

        handleAnswerChange(questionId, newValues);
    };

    const renderQuestion = (question: Question) => {
        const error = errors[`answers.${question.id}` as keyof typeof errors];
        const isAnswered = data.answers[question.id] !== undefined && 
                          (Array.isArray(data.answers[question.id]) 
                            ? (data.answers[question.id] as string[]).length > 0 
                            : data.answers[question.id] !== '');

        return (
            <div key={question.id} className="group space-y-6 rounded-2xl border border-slate-200 bg-white p-8 transition-all hover:border-primary/20 hover:shadow-xl hover:shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-primary/30 dark:hover:shadow-none">
                <div className="flex items-start justify-between gap-4">
                    <Label className="font-heading text-xl font-bold leading-tight text-slate-900 dark:text-white">
                        {question.question}
                        {question.is_required && (
                            <span className="ml-1 text-primary">*</span>
                        )}
                    </Label>
                    {isAnswered && (
                        <div className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                            <CheckCircle2 className="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    )}
                </div>

                {question.type === 'text' && (
                    <Input
                        type="text"
                        value={(data.answers[question.id] as string) || ''}
                        onChange={(e) =>
                            handleAnswerChange(question.id, e.target.value)
                        }
                        placeholder="Type your answer here..."
                        className={`h-12 border-slate-200 bg-slate-50/50 px-4 text-base transition-all focus:bg-white dark:border-slate-700 dark:bg-slate-800/50 ${error ? 'border-red-500 ring-red-500' : 'focus:ring-primary'}`}
                    />
                )}

                {question.type === 'textarea' && (
                    <textarea
                        value={(data.answers[question.id] as string) || ''}
                        onChange={(e) =>
                            handleAnswerChange(question.id, e.target.value)
                        }
                        placeholder="Type your detailed answer here..."
                        rows={4}
                        className={`w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-base transition-all focus:bg-white focus:outline-none focus:ring-2 dark:border-slate-700 dark:bg-slate-800/50 dark:focus:bg-slate-800 ${
                            error
                                ? 'border-red-500 focus:ring-red-500'
                                : 'focus:border-primary focus:ring-primary'
                        }`}
                    />
                )}

                {question.type === 'radio' && question.options && (
                    <div className="grid gap-3 sm:grid-cols-2">
                        {question.options.map((option) => (
                            <label
                                key={option.id}
                                className={`flex cursor-pointer items-center space-x-3 rounded-xl border p-4 transition-all ${
                                    data.answers[question.id] === option.value
                                        ? 'border-primary bg-primary/5 ring-1 ring-primary dark:bg-primary/10'
                                        : 'border-slate-100 bg-slate-50/50 hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/50 dark:hover:bg-slate-800'
                                }`}
                            >
                                <input
                                    type="radio"
                                    name={`question-${question.id}`}
                                    value={option.value}
                                    checked={data.answers[question.id] === option.value}
                                    onChange={(e) => handleAnswerChange(question.id, e.target.value)}
                                    className="h-4 w-4 border-slate-300 text-primary focus:ring-primary"
                                />
                                <span className="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    {option.label}
                                </span>
                            </label>
                        ))}
                    </div>
                )}

                {question.type === 'checkbox' && question.options && (
                    <div className="grid gap-3 sm:grid-cols-2">
                        {question.options.map((option) => {
                            const currentValues = (data.answers[question.id] as string[]) || [];
                            const isChecked = currentValues.includes(option.value);
                            return (
                                <label
                                    key={option.id}
                                    className={`flex cursor-pointer items-center space-x-3 rounded-xl border p-4 transition-all ${
                                        isChecked
                                            ? 'border-primary bg-primary/5 ring-1 ring-primary dark:bg-primary/10'
                                            : 'border-slate-100 bg-slate-50/50 hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/50 dark:hover:bg-slate-800'
                                    }`}
                                >
                                    <Checkbox
                                        checked={isChecked}
                                        onCheckedChange={(checked) =>
                                            handleCheckboxChange(question.id, option.value, checked as boolean)
                                        }
                                        className="border-slate-300 data-[state=checked]:bg-primary data-[state=checked]:border-primary"
                                    />
                                    <span className="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        {option.label}
                                    </span>
                                </label>
                            );
                        })}
                    </div>
                )}

                {question.type === 'select' && question.options && (
                    <select
                        value={(data.answers[question.id] as string) || ''}
                        onChange={(e) => handleAnswerChange(question.id, e.target.value)}
                        className={`h-12 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 text-sm transition-all focus:bg-white focus:outline-none focus:ring-2 dark:border-slate-700 dark:bg-slate-800/50 ${
                            error
                                ? 'border-red-500 focus:ring-red-500'
                                : 'focus:border-primary focus:ring-primary'
                        }`}
                    >
                        <option value="">Choose an option...</option>
                        {question.options.map((option) => (
                            <option key={option.id} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </select>
                )}

                {question.type === 'rating' && (
                    <div className="flex flex-col gap-4">
                        <div className="flex flex-wrap items-center gap-3">
                            {[1, 2, 3, 4, 5].map((rating) => (
                                <button
                                    key={rating}
                                    type="button"
                                    onClick={() => handleAnswerChange(question.id, rating.toString())}
                                    className={`flex h-14 w-14 items-center justify-center rounded-2xl border-2 transition-all ${
                                        data.answers[question.id] === rating.toString()
                                            ? 'border-primary bg-primary text-white shadow-xl shadow-primary/20 scale-110'
                                            : 'border-slate-100 bg-slate-50 hover:border-slate-300 dark:border-slate-800 dark:bg-slate-800 dark:hover:border-slate-700'
                                    }`}
                                    aria-label={`Rate ${rating} out of 5`}
                                >
                                    <Star
                                        className={`h-7 w-7 ${
                                            data.answers[question.id] === rating.toString()
                                                ? 'fill-current'
                                                : 'text-slate-300 dark:text-slate-600'
                                        }`}
                                    />
                                </button>
                            ))}
                        </div>
                        <p className="text-sm font-bold tracking-tight text-slate-500">
                            {data.answers[question.id]
                                ? `${data.answers[question.id]} out of 5 Selected`
                                : 'Please provide a rating'}
                        </p>
                    </div>
                )}

                {question.helper_text && !error && (
                    <p className="text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                        {question.helper_text}
                    </p>
                )}

                {error && <p className="flex items-center gap-1.5 text-sm font-bold text-red-500">
                    <span className="h-1.5 w-1.5 rounded-full bg-red-500" />
                    {error}
                </p>}
            </div>
        );
    };

    return (
        <>
            <Head title={survey.title} />
            <div className="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 selection:bg-primary selection:text-white dark:bg-slate-950 dark:text-slate-100">
                {/* Sticky Progress Header */}
                <div className="sticky top-0 z-50 w-full border-b border-slate-200/60 bg-white/80 backdrop-blur-xl dark:border-slate-800/60 dark:bg-slate-950/80">
                    <div className="mx-auto max-w-3xl px-6 py-4">
                        <div className="mb-2.5 flex items-center justify-between">
                            <span className="text-[10px] font-bold uppercase tracking-[0.2em] text-primary">
                                Response Progress
                            </span>
                            <span className="text-xs font-bold tabular-nums text-slate-600 dark:text-slate-400">
                                {progress}% Complete
                            </span>
                        </div>
                        <div className="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div 
                                className="h-full bg-primary transition-all duration-500 ease-out"
                                style={{ width: `${progress}%` }}
                            />
                        </div>
                    </div>
                </div>

                {/* Header */}
                <header className="px-6 py-10">
                    <div className="mx-auto flex max-w-3xl items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-primary shadow-lg shadow-primary/20">
                                <ClipboardCheck className="h-6 w-6 text-white" />
                            </div>
                            <div className="flex flex-col">
                                <span className="font-heading text-lg font-bold leading-none">
                                    Survey System
                                </span>
                                <span className="mt-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                    Verification System
                                </span>
                            </div>
                        </div>
                        <div className="flex items-center gap-4">
                            <AppearanceToggleDropdown />
                            <Link
                                href="/surveys"
                                className="group flex items-center gap-1.5 text-sm font-bold text-slate-500 transition-colors hover:text-primary dark:text-slate-400"
                            >
                                <ArrowLeft className="h-4 w-4 transition-transform group-hover:-translate-x-0.5" />
                                Return
                            </Link>
                        </div>
                    </div>
                </header>

                {/* Main Content */}
                <main className="flex-1 px-6 pb-32 pt-4">
                    <div className="mx-auto max-w-3xl">
                        <div className="mb-16 text-center">
                            <h1 className="font-heading text-4xl font-extrabold tracking-tight sm:text-5xl">
                                {survey.title}
                            </h1>
                            {survey.description && (
                                <p className="mt-6 text-xl leading-relaxed text-slate-600 dark:text-slate-400">
                                    {survey.description}
                                </p>
                            )}
                        </div>

                        <form onSubmit={handleSubmit} className="space-y-12">
                            {/* Optional Respondent Info */}
                            {survey.collect_respondent_info && (
                                <div className="rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900">
                                    <div className="mb-8 flex items-center gap-3">
                                        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10">
                                            <Lock className="h-4 w-4 text-primary" />
                                        </div>
                                        <div>
                                            <h2 className="font-heading text-xl font-bold">Your Identity</h2>
                                            <p className="text-sm font-medium text-slate-500">Optional: Helps us provide a personalized response.</p>
                                        </div>
                                    </div>
                                    
                                    <div className="space-y-6">
                                        <div className="grid gap-6 sm:grid-cols-2">
                                            <div className="space-y-2">
                                                <Label htmlFor="respondent_name" className="text-xs font-bold uppercase tracking-wider text-slate-500">Full Name</Label>
                                                <Input
                                                    id="respondent_name"
                                                    value={data.respondent_name}
                                                    onChange={(e) => setData('respondent_name', e.target.value)}
                                                    placeholder="John Doe"
                                                    className="h-12 border-slate-200 bg-slate-50/50 focus:bg-white dark:border-slate-700 dark:bg-slate-800/50"
                                                />
                                            </div>
                                            <div className="space-y-2">
                                                <Label htmlFor="respondent_email" className="text-xs font-bold uppercase tracking-wider text-slate-500">Email Address</Label>
                                                <Input
                                                    id="respondent_email"
                                                    type="email"
                                                    value={data.respondent_email}
                                                    onChange={(e) => setData('respondent_email', e.target.value)}
                                                    placeholder="john@example.com"
                                                    className="h-12 border-slate-200 bg-slate-50/50 focus:bg-white dark:border-slate-700 dark:bg-slate-800/50"
                                                />
                                            </div>
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="respondent_phone" className="text-xs font-bold uppercase tracking-wider text-slate-500">Phone Number</Label>
                                            <Input
                                                id="respondent_phone"
                                                type="tel"
                                                value={data.respondent_phone}
                                                onChange={(e) => setData('respondent_phone', e.target.value)}
                                                placeholder="+1 (555) 000-0000"
                                                className="h-12 border-slate-200 bg-slate-50/50 focus:bg-white dark:border-slate-700 dark:bg-slate-800/50"
                                            />
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Questions */}
                            <div className="space-y-8">
                                {survey.questions?.map((question) => renderQuestion(question))}
                            </div>

                            {/* Submit Button */}
                            <div className="pt-12 text-center">
                                <Button
                                    type="submit"
                                    className="h-16 w-full max-w-md rounded-2xl bg-primary text-xl font-bold text-white shadow-2xl shadow-primary/20 transition-all hover:scale-[1.02] hover:bg-primary/90 active:scale-[0.98]"
                                    disabled={processing}
                                >
                                    {processing ? (
                                        <div className="flex items-center gap-3">
                                            <Loader2 className="h-6 w-6 animate-spin" />
                                            Encrypting & Submitting...
                                        </div>
                                    ) : (
                                        'Complete Your Feedback'
                                    )}
                                </Button>
                                <div className="mt-8 flex items-center justify-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                                    <Lock className="h-3 w-3" />
                                    Secure Submission Portal
                                </div>
                            </div>
                        </form>
                    </div>
                </main>

                {/* Footer */}
                <footer className="border-t border-slate-200 bg-white py-12 dark:border-slate-800 dark:bg-slate-950">
                    <div className="mx-auto max-w-3xl px-6 text-center">
                        <p className="text-sm font-bold text-slate-400">
                            SURVEY SYSTEM &copy; {new Date().getFullYear()} — CONFIDENTIAL DATA HANDLING
                        </p>
                    </div>
                </footer>
            </div>
        </>
    );
}
