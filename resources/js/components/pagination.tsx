import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight, MoreHorizontal } from 'lucide-react';
import * as React from 'react';

import { buttonVariants } from '@/components/ui/button';
import { cn } from '@/lib/utils';

export interface PaginationProps {
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

const Pagination = ({ links }: PaginationProps) => {
    if (links.length <= 3) return null;

    return (
        <nav
            role="navigation"
            aria-label="pagination"
            className="mx-auto flex w-full justify-center"
        >
            <ul className="flex flex-row items-center gap-1">
                {links.map((link, index) => {
                    const isFirst = index === 0;
                    const isLast = index === links.length - 1;

                    if (isFirst) {
                        return (
                            <li key={index}>
                                <Link
                                    href={link.url || '#'}
                                    className={cn(
                                        buttonVariants({
                                            variant: 'ghost',
                                            size: 'default',
                                        }),
                                        'gap-1 pl-2.5',
                                        !link.url && 'pointer-events-none opacity-50'
                                    )}
                                    preserveScroll
                                >
                                    <ChevronLeft className="h-4 w-4" />
                                    <span>Previous</span>
                                </Link>
                            </li>
                        );
                    }

                    if (isLast) {
                        return (
                            <li key={index}>
                                <Link
                                    href={link.url || '#'}
                                    className={cn(
                                        buttonVariants({
                                            variant: 'ghost',
                                            size: 'default',
                                        }),
                                        'gap-1 pr-2.5',
                                        !link.url && 'pointer-events-none opacity-50'
                                    )}
                                    preserveScroll
                                >
                                    <span>Next</span>
                                    <ChevronRight className="h-4 w-4" />
                                </Link>
                            </li>
                        );
                    }

                    // Handle ellipsis if Laravel uses them (sometimes label is '...')
                    if (link.label === '...') {
                        return (
                            <li key={index}>
                                <span className="flex h-9 w-9 items-center justify-center">
                                    <MoreHorizontal className="h-4 w-4" />
                                    <span className="sr-only">More pages</span>
                                </span>
                            </li>
                        );
                    }

                    return (
                        <li key={index}>
                            <Link
                                href={link.url || '#'}
                                className={buttonVariants({
                                    variant: link.active ? 'default' : 'outline',
                                    size: 'icon',
                                })}
                                preserveScroll
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
};

export { Pagination };
