import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

// Survey types
export interface Survey {
    id: number;
    title: string;
    description: string | null;
    slug: string;
    is_active: boolean;
    starts_at: string | null;
    ends_at: string | null;
    created_at: string;
    updated_at: string;
    questions?: Question[];
}

export interface Question {
    id: number;
    survey_id: number;
    question: string;
    type: QuestionType;
    is_required: boolean;
    order: number;
    options?: Option[];
}

export type QuestionType =
    | 'text'
    | 'textarea'
    | 'radio'
    | 'checkbox'
    | 'select'
    | 'rating';

export interface Option {
    id: number;
    question_id: number;
    label: string;
    value: string;
    order: number;
}

export interface SurveyResponse {
    id: number;
    survey_id: number;
    respondent_name: string | null;
    respondent_email: string | null;
    ip_address: string;
    submitted_at: string;
    answers?: Answer[];
}

export interface Answer {
    id: number;
    response_id: number;
    question_id: number;
    value: string | null;
    selected_options: string[] | null;
}
