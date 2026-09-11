<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'display_name',
        'description',
        'category',
    ];

    /**
     * Get the localized human-readable label for the permission.
     */
    public function getDisplayNameAttribute(): string
    {
        $key = "permissions.items.{$this->name}.label";
        $translated = __($key);

        if ($translated !== $key && is_string($translated)) {
            return $translated;
        }

        return Str::title(str_replace(['_', '-'], ' ', (string) $this->name));
    }

    /**
     * Get the localized explanatory description for the permission.
     */
    public function getDescriptionAttribute(): string
    {
        $key = "permissions.items.{$this->name}.description";
        $translated = __($key);

        if ($translated !== $key && is_string($translated)) {
            return $translated;
        }

        return '';
    }

    /**
     * Get the category identifier for the permission.
     */
    public function getCategoryAttribute(): string
    {
        $locale = app()->getLocale();
        $items = trans('permissions.items', [], $locale);

        if (is_array($items) && isset($items[$this->name]['category'])) {
            return (string) $items[$this->name]['category'];
        }

        // Fallback checks
        $name = strtolower((string) $this->name);
        if (str_contains($name, 'agenda') || str_contains($name, 'sb')) {
            return 'agenda';
        }
        if (str_contains($name, 'store') || str_contains($name, 'lit') || str_contains($name, 'inventory')) {
            return 'store';
        }
        if (str_contains($name, 'calendar') || str_contains($name, 'event')) {
            return 'calendar';
        }
        if (str_contains($name, 'form')) {
            return 'forms';
        }

        return 'general';
    }

    /**
     * Return all (or given) permissions grouped by category with localized titles and icons.
     *
     * @param iterable<\App\Models\Permission>|null $permissions
     * @return array<string, array{key: string, title: string, icon: string, permissions: \Illuminate\Support\Collection}>
     */
    public static function getGrouped(?iterable $permissions = null): array
    {
        if ($permissions === null) {
            $permissions = static::all();
        } elseif (!$permissions instanceof Collection) {
            $permissions = collect($permissions);
        }

        $locale = app()->getLocale();
        $categoriesConfig = trans('permissions.categories', [], $locale);

        if (!is_array($categoriesConfig)) {
            $categoriesConfig = [
                'agenda' => ['title' => 'Service Body Agendas', 'icon' => 'bi-journals'],
                'store' => ['title' => 'Store & Literature', 'icon' => 'bi-box-seam'],
                'calendar' => ['title' => 'Calendar & Events', 'icon' => 'bi-calendar-check'],
                'forms' => ['title' => 'Custom Forms', 'icon' => 'bi-ui-checks'],
                'system' => ['title' => 'System & Legacy Roles', 'icon' => 'bi-shield-lock'],
                'general' => ['title' => 'General & Others', 'icon' => 'bi-gear-fill'],
            ];
        }

        $grouped = [];
        foreach ($categoriesConfig as $catKey => $catData) {
            $grouped[$catKey] = [
                'key' => $catKey,
                'title' => $catData['title'] ?? ucfirst($catKey),
                'icon' => $catData['icon'] ?? 'bi-gear-fill',
                'permissions' => collect(),
            ];
        }

        if (!isset($grouped['general'])) {
            $grouped['general'] = [
                'key' => 'general',
                'title' => 'General & Others',
                'icon' => 'bi-gear-fill',
                'permissions' => collect(),
            ];
        }

        foreach ($permissions as $permission) {
            // Ensure model instance with accessors
            if (!$permission instanceof self && is_object($permission)) {
                $permission = static::find($permission->id) ?? $permission;
            }

            $catKey = $permission->category ?? 'general';
            if (!isset($grouped[$catKey])) {
                $grouped[$catKey] = [
                    'key' => $catKey,
                    'title' => ucfirst($catKey),
                    'icon' => 'bi-gear-fill',
                    'permissions' => collect(),
                ];
            }

            $grouped[$catKey]['permissions']->push($permission);
        }

        return $grouped;
    }
}
