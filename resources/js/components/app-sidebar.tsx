import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { FileText, LayoutGrid, Settings, Shield, Users } from 'lucide-react';
import AppLogo from './app-logo';

export function AppSidebar() {
    const { auth } = usePage<SharedData>().props;
    const can = auth.can;

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
        ...(can?.view_users
            ? [
                  {
                      title: 'Users',
                      href: '/users',
                      icon: Users,
                  } as NavItem,
              ]
            : []),
    ];

    const footerNavItems: NavItem[] = [
        ...(can?.view_activity_logs
            ? [
                  {
                      title: 'Activity Logs',
                      href: '/activity-logs',
                      icon: FileText,
                  } as NavItem,
              ]
            : []),
        ...(can?.manage_system_settings
            ? [
                  {
                      title: 'Settings',
                      href: '/settings/system',
                      icon: Settings,
                  } as NavItem,
              ]
            : []),
        ...(can?.manage_roles
            ? [
                  {
                      title: 'Roles',
                      href: '/roles',
                      icon: Shield,
                  } as NavItem,
              ]
            : []),
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
