import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

/** Mirrors canonical abilities used by the shell UI; see RolePermissionSeeder. */
export interface AuthCan {
    view_users: boolean;
    view_activity_logs: boolean;
    manage_system_settings: boolean;
    manage_roles: boolean;
    view_telescope: boolean;
    view_horizon: boolean;
}

export interface Auth {
    user: User | null;
    can: AuthCan | null;
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
    role?: string;
    current_role?: string;
    roles?: Role[];
    [key: string]: unknown; // This allows for additional properties...
}

export interface Role {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
    permissions?: Permission[];
    permissions_count?: number;
}

export interface Permission {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
}
