<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    /**
     * 讀取的表格名稱
     *
     * @var string
     */
    protected $table = 'skills';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_of',
        'skill_type_of',
        'description',
        'effect',
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
     * 關聯 skill_types 資料表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skill_type()
    {
        return $this->hasOne(SkillType::class, 'id', 'skill_type_of');
    }

    /**
     * 關聯 characters 資料表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function character()
    {
        return $this->hasOne(Character::class, 'id', 'character_of');
    }
}
