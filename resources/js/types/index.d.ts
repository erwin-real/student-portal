import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
}

export interface User {
    id: number;
    name: string;
    member: {
        first_name: string;
        last_name: string;
    }
    username: string;
    avatar?: string;
    // email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

interface Section {
    id: number
    name: string
    level_id: number
    description: string
}

interface GradeLevel {
    id: number
    name: string
    sections: Section[]
}

export type BreadcrumbItemType = BreadcrumbItem;
