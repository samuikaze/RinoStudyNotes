<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    /**
     * 讀取的表格名稱
     *
     * @var string
     */
    protected $table = 'characters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'guild_of',
        'cv_of',
        'race_of',
        'tw_name',
        'jp_name',
        's_image_url',
        'f_image_url',
        't_image_url',
        'description',
        'ages',
        'height',
        'weight',
        'blood_type',
        'likes',
        'birthday',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 關聯 nicknames 資料表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function nicknames()
    {
        return $this->hasMany(Nickname::class, 'character_of', 'id');
    }

    /**
     * 關聯 skills 資料表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skills()
    {
        return $this->hasMany(Skill::class, 'character_of', 'id');
    }

    /**
     * 關聯 guilds 資料表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function guild()
    {
        return $this->hasOne(Guild::class, 'id', 'guild_of');
    }

    /**
     * 關聯 cvs 資料表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cv()
    {
        return $this->hasOne(CV::class, 'id', 'cv_of');
    }

    /**
     * 關聯 races 資料表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function race()
    {
        return $this->hasOne(Race::class, 'id', 'race_of');
    }
}
