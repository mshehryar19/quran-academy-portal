<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StaffNotice extends Model
{
    public const CATEGORY_POLICY = 'policy';

    public const CATEGORY_VIOLATION = 'violation';

    public const CATEGORY_WARNING = 'warning';

    public const CATEGORY_REMINDER = 'reminder';

    public const CATEGORY_OPERATIONAL_ALERT = 'operational_alert';

    public const MODE_ALL_STAFF = 'all_staff';

    public const MODE_ROLES = 'roles';

    public const MODE_USERS = 'users';

    protected $fillable = [
        'uuid',
        'title',
        'short_alert',
        'full_message',
        'category',
        'severity',
        'recipient_mode',
        'channels',
        'created_by_user_id',
        'published_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'channels' => 'array',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (StaffNotice $notice): void {
            if (! $notice->uuid) {
                $notice->uuid = (string) Str::uuid();
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function roleTargets(): HasMany
    {
        return $this->hasMany(StaffNoticeTargetRole::class);
    }

    public function userTargets(): HasMany
    {
        return $this->hasMany(StaffNoticeTargetUser::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(StaffNoticeRead::class);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q): void {
            $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
        });
    }

    /**
     * @param  Builder<StaffNotice>  $query
     * @return Builder<StaffNotice>
     */
    public function scopeVisibleToUser($query, User $user)
    {
        if ($user->hasAnyRole(['Student', 'Parent'])) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($user): void {
            $q->where('recipient_mode', self::MODE_ALL_STAFF)
                ->orWhere(function ($q2) use ($user): void {
                    $q2->where('recipient_mode', self::MODE_ROLES)
                        ->whereHas('roleTargets', function ($r) use ($user): void {
                            $names = $user->getRoleNames()->map(fn ($n) => (string) $n)->all();
                            $r->whereIn('role_name', $names);
                        });
                })
                ->orWhere(function ($q3) use ($user): void {
                    $q3->where('recipient_mode', self::MODE_USERS)
                        ->whereHas('userTargets', fn ($u) => $u->where('user_id', $user->id));
                });
        });
    }

    public function isVisibleTo(User $user): bool
    {
        return self::query()->whereKey($this->id)->visibleToUser($user)->exists();
    }

    /**
     * @return Collection<int, User>
     */
    public function resolveRecipientUsers(): Collection
    {
        $staffRoleNames = ['Admin', 'HR', 'Supervisor', 'Teacher', 'Accountant'];

        if ($this->recipient_mode === self::MODE_ALL_STAFF) {
            return User::query()
                ->whereHas('roles', fn ($q) => $q->whereIn('name', $staffRoleNames))
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        if ($this->recipient_mode === self::MODE_ROLES) {
            $roles = $this->roleTargets()->pluck('role_name')->all();
            if ($roles === []) {
                return collect();
            }

            return User::query()
                ->whereHas('roles', fn ($q) => $q->whereIn('name', $roles))
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        $ids = $this->userTargets()->pluck('user_id')->all();

        return User::query()->whereIn('id', $ids)->where('is_active', true)->orderBy('name')->get();
    }
}
