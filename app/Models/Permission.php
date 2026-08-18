<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'adminlte_permissions';

    protected $fillable = [
        'name',
        'label',
        'group',
    ];

    /**
     * Display order for known permission groups on the Permissions page and
     * the Roles grouped checkbox lists. Any group not listed here (including
     * the ungrouped "General" bucket) sorts alphabetically after these.
     */
    public const GROUP_ORDER = [
        'Dashboard',
        'Quotes',
        'Categories',
        'Users',
        'Roles & Permissions',
        'Artisan Runner',
    ];

    protected static function booted(): void
    {
        // Super Admin is meant to hold every permission with nothing to assign
        // or remove by hand, so newly created permissions join it automatically.
        static::created(function (Permission $permission): void {
            Role::where('name', 'super_admin')->first()?->permissions()->syncWithoutDetaching($permission);
        });
    }

    /**
     * Roles that are granted this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'adminlte_permission_role');
    }

    /**
     * All permissions sorted by the canonical group order (then label within
     * each group), for the Permissions page and Roles permission checklists.
     *
     * @return Collection<int, self>
     */
    public static function allOrderedByGroup(): Collection
    {
        return static::all()->sort(function (self $a, self $b) {
            $groupIndex = fn (self $permission) => array_search($permission->group, self::GROUP_ORDER, true);

            $aIndex = $groupIndex($a);
            $bIndex = $groupIndex($b);

            if ($aIndex !== $bIndex) {
                // Unlisted groups (false) sort after every known group.
                return ($aIndex === false ? PHP_INT_MAX : $aIndex)
                    <=> ($bIndex === false ? PHP_INT_MAX : $bIndex);
            }

            if ($a->group !== $b->group) {
                return strcasecmp((string) $a->group, (string) $b->group);
            }

            return strcasecmp($a->label ?? $a->name, $b->label ?? $b->name);
        })->values();
    }
}
