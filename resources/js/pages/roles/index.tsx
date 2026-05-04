import { type BreadcrumbItem, type Role, type SharedData } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Info, Pencil, Plus, Search, Shield, Trash2, X } from 'lucide-react';
import { useEffect, useState } from 'react';

import DataTablePagination from '@/components/data-table-pagination';
import DeleteConfirmationModal from '@/components/delete-confirmation-modal';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';

interface RolesIndexProps {
    roles: {
        data: Role[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

export default function RolesIndex({ roles }: RolesIndexProps) {
    const { url } = usePage<SharedData>().props;
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [roleToDelete, setRoleToDelete] = useState<{
        id: number;
        name: string;
    } | null>(null);

    // Get search value from URL
    const getSearchFromUrl = () => {
        if (typeof window !== 'undefined') {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('filter[search]') || '';
        }
        return '';
    };

    const [search, setSearch] = useState(getSearchFromUrl());

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Roles',
            href: '/roles',
        },
    ];

    const handleDeleteClick = (roleId: number, roleName: string) => {
        setRoleToDelete({ id: roleId, name: roleName });
        setDeleteModalOpen(true);
    };

    const handleDeleteConfirm = () => {
        if (roleToDelete) {
            router.delete(`/roles/${roleToDelete.id}`, {
                onSuccess: () => {
                    setDeleteModalOpen(false);
                    setRoleToDelete(null);
                },
            });
        }
    };

    const handleDeleteCancel = () => {
        setDeleteModalOpen(false);
        setRoleToDelete(null);
    };

    // Update search when URL changes (e.g., back button)
    useEffect(() => {
        if (typeof window !== 'undefined') {
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('filter[search]') || '';
            setSearch(searchParam);
        }
    }, [url]);

    // Debounced search - triggers when search has at least 3 characters
    useEffect(() => {
        if (typeof window === 'undefined') return;

        const timeoutId = setTimeout(() => {
            const params = new URLSearchParams();
            const trimmedSearch = search.trim();
            const currentParams = new URLSearchParams(window.location.search);
            const hasExistingFilter = currentParams.has('filter[search]');

            // Only search if 3+ characters
            if (trimmedSearch.length >= 3) {
                params.set('filter[search]', trimmedSearch);
                router.get(
                    `/roles?${params.toString()}`,
                    {},
                    {
                        preserveState: true,
                        preserveScroll: false,
                    },
                );
            } else if (trimmedSearch.length === 0 && hasExistingFilter) {
                // Clear filter if search is empty and there was a previous filter
                router.get(
                    '/roles',
                    {},
                    {
                        preserveState: true,
                        preserveScroll: false,
                    },
                );
            }
            // If 1-2 characters, do nothing (wait for more input)
        }, 500); // 500ms debounce

        return () => clearTimeout(timeoutId);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    const handleClearSearch = () => {
        setSearch('');
    };

    // Get per_page from URL
    const getPerPageFromUrl = () => {
        if (typeof window !== 'undefined') {
            const urlParams = new URLSearchParams(window.location.search);
            const perPage = urlParams.get('per_page');
            return perPage ? Number(perPage) : roles.per_page;
        }
        return roles.per_page;
    };

    const [perPage, setPerPage] = useState(getPerPageFromUrl());

    // Update per_page when URL changes
    useEffect(() => {
        if (typeof window !== 'undefined') {
            const urlParams = new URLSearchParams(window.location.search);
            const perPageParam = urlParams.get('per_page');
            if (perPageParam) {
                setPerPage(Number(perPageParam));
            }
        }
    }, [url, roles.per_page]);

    const getPaginationUrl = (page: number) => {
        const params = new URLSearchParams();
        params.set('page', String(page));
        if (search.trim()) {
            params.set('filter[search]', search.trim());
        }
        if (perPage !== 10) {
            params.set('per_page', String(perPage));
        }
        return `/roles?${params.toString()}`;
    };

    const handlePerPageChange = (newPerPage: number) => {
        setPerPage(newPerPage);
        const params = new URLSearchParams();
        if (search.trim()) {
            params.set('filter[search]', search.trim());
        }
        params.set('per_page', String(newPerPage));
        // Reset to page 1 when changing per_page
        params.set('page', '1');

        router.get(
            `/roles?${params.toString()}`,
            {},
            {
                preserveState: true,
                preserveScroll: false,
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Roles" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Roles</h1>
                        <p className="text-muted-foreground">
                            Manage user roles and permissions
                        </p>
                    </div>
                    <Link href="/roles/create">
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Create Role
                        </Button>
                    </Link>
                </div>

                <div className="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950">
                    <div className="flex items-start gap-3">
                        <Info className="mt-0.5 h-5 w-5 text-blue-600 dark:text-blue-400" />
                        <div className="flex-1">
                            <h3 className="text-sm font-medium text-blue-900 dark:text-blue-100">
                                Permissions
                            </h3>
                            <p className="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                Permissions are defined in{' '}
                                <code className="rounded bg-blue-100 px-1.5 py-0.5 font-mono text-xs dark:bg-blue-900">
                                    database/seeders/RolePermissionSeeder.php
                                </code>{' '}
                                and assigned to roles here. Adjust the seeder when
                                you add new abilities, then run migrations and
                                seed.
                            </p>
                        </div>
                    </div>
                </div>

                <div className="rounded-lg border bg-card p-4">
                    <div className="mb-4">
                        <div className="relative">
                            <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                type="text"
                                placeholder="Search roles by name or guard... (min 3 characters)"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="pr-10 pl-10"
                            />
                            {search && (
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    onClick={handleClearSearch}
                                    className="absolute top-1/2 right-1 h-7 w-7 -translate-y-1/2 p-0"
                                >
                                    <X className="h-4 w-4" />
                                </Button>
                            )}
                        </div>
                        {search.length > 0 && search.length < 3 && (
                            <p className="mt-2 text-xs text-muted-foreground">
                                Type at least 3 characters to search
                            </p>
                        )}
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b">
                                    <th className="px-4 py-3 text-left text-sm font-medium">
                                        Name
                                    </th>
                                    <th className="px-4 py-3 text-left text-sm font-medium">
                                        Guard
                                    </th>
                                    <th className="px-4 py-3 text-left text-sm font-medium">
                                        Permissions
                                    </th>
                                    <th className="px-4 py-3 text-left text-sm font-medium">
                                        Created
                                    </th>
                                    <th className="px-4 py-3 text-right text-sm font-medium">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {roles.data.length === 0 ? (
                                    <tr>
                                        <td
                                            colSpan={5}
                                            className="px-4 py-8 text-center text-muted-foreground"
                                        >
                                            No roles found
                                        </td>
                                    </tr>
                                ) : (
                                    roles.data.map((role) => (
                                        <tr
                                            key={role.id}
                                            className="border-b last:border-0 hover:bg-muted/50"
                                        >
                                            <td className="px-4 py-3">
                                                <div className="flex items-center gap-2">
                                                    <Shield className="h-4 w-4 text-muted-foreground" />
                                                    <span className="font-medium">
                                                        {role.name}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3 text-sm text-muted-foreground">
                                                {role.guard_name}
                                            </td>
                                            <td className="px-4 py-3 text-sm text-muted-foreground">
                                                {role.permissions_count ?? 0}
                                            </td>
                                            <td className="px-4 py-3 text-sm text-muted-foreground">
                                                {new Date(
                                                    role.created_at,
                                                ).toLocaleDateString()}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex items-center justify-end gap-2">
                                                    <Link
                                                        href={`/roles/${role.id}/edit`}
                                                    >
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Button>
                                                    </Link>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() =>
                                                            handleDeleteClick(
                                                                role.id,
                                                                role.name,
                                                            )
                                                        }
                                                    >
                                                        <Trash2 className="h-4 w-4 text-destructive" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="border-t">
                        <DataTablePagination
                            currentPage={roles.current_page}
                            lastPage={roles.last_page}
                            perPage={perPage}
                            total={roles.total}
                            onPageChange={getPaginationUrl}
                            onPerPageChange={handlePerPageChange}
                        />
                    </div>
                </div>
            </div>

            <DeleteConfirmationModal
                isOpen={deleteModalOpen}
                onClose={handleDeleteCancel}
                onConfirm={handleDeleteConfirm}
                title="Delete Role"
                description={`Are you sure you want to delete ${roleToDelete?.name}? This action cannot be undone.`}
            />
        </AppLayout>
    );
}
