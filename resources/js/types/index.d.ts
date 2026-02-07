
export interface Page {
    id: number;
    title: string;
    slug: string;
}

export interface PageResourceType {
  id: number;
  language_id: number;
  title: string;
  slug: string;
  content_md: string;
  is_active: boolean;
}

import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    isDisplayed?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface Post {
    id: number;
    language_id: number;
    title: string;
    slug: string;
    content_md: string;
    status: string;
    published_at: string | null;
    categories: Category[];
    post_view?: PostView | null;
    main_photo?: {
        url: string | null;
        file_name: string;
        mime_type: string;
        size: number;
    } | null;
}

export interface PostsResponse {
    data: Post[];
    meta: PaginationMeta;
}

export interface PostView {
    post_id: number;
    view_count: number;
    last_viewed_at: string | null;
}

export interface Pages {
    id: number;
    language_id: number;
    title: string;
    slug: string;
    content_md: string;
    is_active: boolean;
}

export interface Language {
    id: number;
    code: string;
    name: string;
    native_name: string;
    is_active: boolean;
    is_default: boolean;
}

export interface Category {
    id: number;
    parent_id: number | null;
    name: string | null;
    slug: string | null;
    created_at: string;
    updated_at: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export type BreadcrumbItemType = BreadcrumbItem;
