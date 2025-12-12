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
import { ArrowLeft, ClipboardList, Loader2, Star } from 'lucide-react';
import { FormEvent } from 'react';

interface Props {
    survey: Survey;
}

interface FormData {
    respondent_name: string;
    respondent_email: string;
    answers: Record<number, string | string[]>;
}

export default function SurveyShow({ survey }: Props) {
    const { data, setData, post, processing, errors } = useForm<FormData>({
        respondent_name: '',
        respondent_email: '',
        answers: {},
    });

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

        return (
            <div key={question.id} className="space-y-3">
                <div>
                    <Label className="text-base font-medium">
                        {question.question}
                        {question.is_required && (
                            <span className="ml-1 text-red-500">*</span>
                        )}
                    </Label>
                    {question.helper_text && (
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {question.helper_text}
                        </p>
                    )}
                </div>

                {question.type === 'text' && (
                    <Input
                        type="text"
                        value={(data.answers[question.id] as string) || ''}
                        onChange={(e) =>
                            handleAnswerChange(question.id, e.target.value)
                        }
                        placeholder="Enter your answer"
                        className={error ? 'border-red-500' : ''}
                    />
                )}

                {question.type === 'textarea' && (
                    <textarea
                        value={(data.answers[question.id] as string) || ''}
                        onChange={(e) =>
                            handleAnswerChange(question.id, e.target.value)
                        }
                        placeholder="Enter your answer"
                        rows={4}
                        className={`w-full rounded-md border px-3 py-2 text-sm ${
                            error
                                ? 'border-red-500'
                                : 'border-gray-300 dark:border-gray-600 dark:bg-gray-800'
                        } focus:ring-2 focus:ring-blue-500 focus:outline-none`}
                    />
                )}

                {question.type === 'radio' && question.options && (
                    <div className="space-y-2">
                        {question.options.map((option) => (
                            <label
                                key={option.id}
                                className="flex cursor-pointer items-center space-x-3 rounded-lg border border-gray-200 p-3 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                            >
                                <input
                                    type="radio"
                                    name={`question-${question.id}`}
                                    value={option.value}
                                    checked={
                                        data.answers[question.id] ===
                                        option.value
                                    }
                                    onChange={(e) =>
                                        handleAnswerChange(
                                            question.id,
                                            e.target.value,
                                        )
                                    }
                                    className="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <span className="text-sm text-gray-700 dark:text-gray-300">
                                    {option.label}
                                </span>
                            </label>
                        ))}
                    </div>
                )}

                {question.type === 'checkbox' && question.options && (
                    <div className="space-y-2">
                        {question.options.map((option) => {
                            const currentValues =
                                (data.answers[question.id] as string[]) || [];
                            return (
                                <label
                                    key={option.id}
                                    className="flex cursor-pointer items-center space-x-3 rounded-lg border border-gray-200 p-3 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                                >
                                    <Checkbox
                                        checked={currentValues.includes(
                                            option.value,
                                        )}
                                        onCheckedChange={(checked) =>
                                            handleCheckboxChange(
                                                question.id,
                                                option.value,
                                                checked as boolean,
                                            )
                                        }
                                    />
                                    <span className="text-sm text-gray-700 dark:text-gray-300">
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
                        onChange={(e) =>
                            handleAnswerChange(question.id, e.target.value)
                        }
                        className={`w-full rounded-md border px-3 py-2 text-sm ${
                            error
                                ? 'border-red-500'
                                : 'border-gray-300 dark:border-gray-600 dark:bg-gray-800'
                        } focus:ring-2 focus:ring-blue-500 focus:outline-none`}
                    >
                        <option value="">Select an option</option>
                        {question.options.map((option) => (
                            <option key={option.id} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </select>
                )}

                {question.type === 'rating' && (
                    <div className="flex items-center gap-2">
                        {[1, 2, 3, 4, 5].map((rating) => (
                            <button
                                key={rating}
                                type="button"
                                onClick={() =>
                                    handleAnswerChange(
                                        question.id,
                                        rating.toString(),
                                    )
                                }
                                className={`flex h-12 w-12 items-center justify-center rounded-full border-2 transition-all ${
                                    data.answers[question.id] ===
                                    rating.toString()
                                        ? 'border-yellow-400 bg-yellow-400 text-white'
                                        : 'border-gray-300 hover:border-yellow-400 dark:border-gray-600'
                                }`}
                                aria-label={`Rate ${rating} out of 5`}
                            >
                                <Star
                                    className={`h-6 w-6 ${
                                        data.answers[question.id] ===
                                        rating.toString()
                                            ? 'fill-current'
                                            : ''
                                    }`}
                                />
                            </button>
                        ))}
                        <span className="ml-2 text-sm text-gray-500">
                            {data.answers[question.id]
                                ? `${data.answers[question.id]} / 5`
                                : 'Select a rating'}
                        </span>
                    </div>
                )}

                {error && <p className="text-sm text-red-500">{error}</p>}
            </div>
        );
    };

    return (
        <>
            <Head title={survey.title} />
            <div className="flex min-h-screen flex-col bg-gradient-to-b from-blue-50 to-white dark:from-gray-900 dark:to-gray-800">
                {/* Header */}
                <header className="border-b border-gray-200 bg-white/80 backdrop-blur-sm dark:border-gray-700 dark:bg-gray-900/80">
                    <div className="mx-auto flex max-w-2xl items-center justify-between px-4 py-4">
                        <div className="flex items-center gap-2">
                            <ClipboardList className="h-6 w-6 text-blue-600 dark:text-blue-400" />
                            <span className="text-lg font-semibold text-gray-900 dark:text-white">
                                Survey
                            </span>
                        </div>
                        <Link
                            href="/surveys"
                            className="flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Back to Surveys
                        </Link>
                    </div>
                </header>

                {/* Main Content */}
                <main className="flex-1 px-4 py-8">
                    <div className="mx-auto max-w-2xl">
                        <Card className="mb-6">
                            <CardHeader className="text-center">
                                <CardTitle className="text-2xl">
                                    {survey.title}
                                </CardTitle>
                                {survey.description && (
                                    <CardDescription className="text-base">
                                        {survey.description}
                                    </CardDescription>
                                )}
                            </CardHeader>
                        </Card>

                        <form onSubmit={handleSubmit}>
                            <Card>
                                <CardContent className="space-y-8 pt-6">
                                    {/* Optional Respondent Info */}
                                    {survey.collect_respondent_info && (
                                        <div className="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                            <h3 className="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Optional: Your Information
                                            </h3>
                                            <div className="grid gap-4 sm:grid-cols-2">
                                                <div>
                                                    <Label
                                                        htmlFor="respondent_name"
                                                        className="text-sm"
                                                    >
                                                        Name
                                                    </Label>
                                                    <Input
                                                        id="respondent_name"
                                                        type="text"
                                                        value={
                                                            data.respondent_name
                                                        }
                                                        onChange={(e) =>
                                                            setData(
                                                                'respondent_name',
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="Your name (optional)"
                                                    />
                                                </div>
                                                <div>
                                                    <Label
                                                        htmlFor="respondent_email"
                                                        className="text-sm"
                                                    >
                                                        Email
                                                    </Label>
                                                    <Input
                                                        id="respondent_email"
                                                        type="email"
                                                        value={
                                                            data.respondent_email
                                                        }
                                                        onChange={(e) =>
                                                            setData(
                                                                'respondent_email',
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="Your email (optional)"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Questions */}
                                    {survey.questions?.map((question) =>
                                        renderQuestion(question),
                                    )}

                                    {/* Submit Button */}
                                    <div className="pt-4">
                                        <Button
                                            type="submit"
                                            className="w-full"
                                            size="lg"
                                            disabled={processing}
                                        >
                                            {processing ? (
                                                <>
                                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                                    Submitting...
                                                </>
                                            ) : (
                                                'Submit Survey'
                                            )}
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        </form>
                    </div>
                </main>

                {/* Footer */}
                <footer className="border-t border-gray-200 bg-white py-6 dark:border-gray-700 dark:bg-gray-900">
                    <div className="mx-auto max-w-2xl px-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        <p>Your responses are anonymous and confidential.</p>
                    </div>
                </footer>
            </div>
        </>
    );
}
