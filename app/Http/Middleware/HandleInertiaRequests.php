<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * @return array<string, bool>
     */
    protected function authorizationMap(User $user): array
    {
        return [
            'view_users' => $user->can('view_users'),
            'view_activity_logs' => $user->can('view_activity_logs'),
            'manage_system_settings' => $user->can('manage_system_settings'),
            'manage_roles' => $user->can('manage_roles'),
            'view_telescope' => $user->can('view_telescope'),
            'view_horizon' => $user->can('view_horizon'),
        ];
    }
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $user = $request->user();

        $unreadNotifications = $user
            ? $user->unreadNotifications()->latest()->limit(5)->get()
            : collect();

        $unreadCount = $user
            ? $user->unreadNotifications()->count()
            : 0;

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user ? [
                    ...$user->toArray(),
                    'role' => $user->role,
                ] : null,
                'can' => $user instanceof User ? $this->authorizationMap($user) : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'unreadNotifications' => $unreadNotifications,
            'unreadNotificationCount' => $unreadCount,
        ];
    }
}
