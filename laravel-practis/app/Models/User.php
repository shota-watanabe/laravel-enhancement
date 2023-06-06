<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
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
        return $this->hasMany(CsvExportHistory::class, 'download_user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function scopeIsNotAdmin(Builder $builder, Request $request)
    {
        return $builder->when(!$request->user()->isAdmin(), function (Builder $query) use ($request) {
            $query->where('company_id', $request->user()->company_id);
        });
    }

    public function scopeSearchUser(Builder $builder, Request $request)
    {
        $user_name = $request->search_keyword;

        return $builder->when($request->search_type === 'user' && $request->filled('search_keyword'), function ($query) use ($request, $user_name) {
            // 全角スペースを半角に
            $spaceConvert = mb_convert_kana($user_name, 's');

            // 空白で区切る
            $keywords = preg_split('/[\s]+/', $spaceConvert, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($keywords as $index => $word) {
                // 最初のキーワードに対してはwhereを使用し、それ以降のキーワードに対してはorWhereを使用
                if ($index === 0) {
                    $query->where('users.name', 'like', '%' . $word . '%');
                } else {
                    $query->orWhere('users.name', 'like', '%' . $word . '%');
                }
            }
            return $query;
        });
    }

    public function scopeSearchCompany(Builder $builder, Request $request)
    {
        $company_name = $request->search_keyword;

        // 単語をループで回す
        return $builder->when($request->search_type === 'company' && $request->filled('search_keyword'), function ($query) use ($company_name) {
            // 全角スペースを半角に
            $spaceConvert = mb_convert_kana($company_name, 's');

            // 空白で区切る
            $keywords = preg_split('/[\s]+/', $spaceConvert, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($keywords as $word) {
                $query->orWhereHas('company', function ($query) use ($word) {
                    $query->where('name', 'like', '%' . $word . '%');
                });
            }
        });
    }

    public function scopeSearchSection(Builder $builder, Request $request)
    {
        $section_name = $request->search_keyword;

        return $builder->when($request->search_type === 'section' && $request->filled('search_keyword'), function ($query) use ($section_name) {
            // 全角スペースを半角に
            $spaceConvert = mb_convert_kana($section_name, 's');

            // 空白で区切る
            $keywords = preg_split('/[\s]+/', $spaceConvert, -1, PREG_SPLIT_NO_EMPTY);

            return $query->whereHas('sections', function ($query) use ($keywords) {
                foreach ($keywords as $index => $word) {
                    // 最初のキーワードに対してはwhereを使用し、それ以降のキーワードに対してはorWhereを使用
                    if ($index === 0) {
                        $query->where('name', 'like', '%' . $word . '%');
                    } else {
                        $query->orWhere('name', 'like', '%' . $word . '%');
                    }
                }
            });
        });
    }
}
