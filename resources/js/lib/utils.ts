import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { useTrans } from '@/composables/trans';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

export function dateDifference(date: string): string {
    const now = new Date();
    const pastDate = new Date(date);
    const diffInSeconds = Math.floor((now.getTime() - pastDate.getTime()) / 1000);

    if (diffInSeconds < 60) {
        return `${diffInSeconds} seconds ago`;
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
}

export function formatDateShort(date?: string | null): string {
    if (!date) {
        return '';
    }

    const parsed = new Date(date);
    if (Number.isNaN(parsed.getTime())) {
        return '';
    }

    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
    }).format(parsed);
}

export function estimateReadTime(content?: string | null, wordsPerMinute = 200): string {
    if (!content) {
        return useTrans('posts.estimated_read_time', { time: '0' });
    }

    const text = content.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
    if (!text) {
        return useTrans('posts.estimated_read_time', { time: '0' });
    }

    const words = text.split(' ').length;
    const minutes = Math.max(1, Math.ceil(words / wordsPerMinute));
    return useTrans('posts.estimated_read_time', { time: String(minutes) });
}