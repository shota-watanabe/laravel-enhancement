<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id ID
 * @property int $company_id 会社ID
 * @property string $name 氏名
 * @property string $email メールアドレス
 * @property \Illuminate\Support\Carbon|null $email_verified_at メール認証日時
 * @property string $password パスワード
 * @property string|null $remember_token リメンバートークン
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company|null $company
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class);
    }

    public function csv_export_histories(): HasMany
    {
        return $this->hasMany(CsvExportHistory::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function scopeSearchUser($query, $user_name)
    {
        if (!is_null($user_name)) {
            // 全角スペースを半角に
            $spaceConvert = mb_convert_kana($user_name, 's');

            // 空白で区切る
            $keywords = preg_split('/[\s]+/', $spaceConvert, -1, PREG_SPLIT_NO_EMPTY);

            // 単語をループで回す
            if (Auth::user()->isAdmin()) {
                foreach ($keywords as $word) {
                    $query->where('users.name', 'like', '%' . $word . '%');
                }
            } else {
                $company_id = Auth::user()->company_id;
                $query->where('company_id', $company_id)->where(function ($query) use ($keywords) {
                    foreach ($keywords as $word) {
                        $query->where('users.name', 'like', '%' . $word . '%');
                    }
                });
            }
            return $query;
        } else {
            return;
        }
    }

    public function scopeSearchCompany($query, $company_name)
    {
        if (!is_null($company_name)) {
            // 全角スペースを半角に
            $spaceConvert = mb_convert_kana($company_name, 's');

            // 空白で区切る
            $keywords = preg_split('/[\s]+/', $spaceConvert, -1, PREG_SPLIT_NO_EMPTY);

            // 単語をループで回す
            if (Auth::user()->isAdmin()) {
                foreach ($keywords as $word) {
                    $query->whereHas('company', function ($query) use ($word) {
                        $query->where('name', 'like', '%' . $word . '%');
                    });
                }
            }
            return $query;
        } else {
            return;
        }
    }

    public function scopeSearchSection($query, $section_name)
    {
        if (!is_null($section_name)) {
            // 全角スペースを半角に
            $spaceConvert = mb_convert_kana($section_name, 's');

            // 空白で区切る
            $keywords = preg_split('/[\s]+/', $spaceConvert, -1, PREG_SPLIT_NO_EMPTY);

            // 単語をループで回す
            if (Auth::user()->isAdmin()) {
                foreach ($keywords as $word) {
                    $query->whereHas('sections', function ($query) use ($word) {
                        $query->where('name', 'like', '%' . $word . '%');
                    });
                }
            } else {
                $company_id = Auth::user()->company_id;
                $query->where('company_id', $company_id)->where(function ($query) use ($keywords) {
                    $query->whereHas('sections', function ($query) use ($keywords) {
                        foreach ($keywords as $word) {
                            $query->Where('name', 'like', '%' . $word . '%');
                        }
                    });
                });
            }
            return $query;
        } else {
            return;
        }
    }
}
