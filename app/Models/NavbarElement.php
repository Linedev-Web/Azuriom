<?php

namespace Azuriom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NavbarElement extends Model
{
    /**
     * The navbar elements types
     *
     * @var array
     */
    public const types = [
        'home', 'link', 'page', 'post', 'posts', 'dropdown',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'value', 'position', 'type', 'parent_id', 'new_tab',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'new_tab' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function elements()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    public function getLink()
    {
        switch ($this->type) {
            case 'home':
                return route('home');
            case 'link':
                return $this->value;
            case 'page':
                return route('pages.show', $this->value);
            case 'post':
                return route('posts.show', $this->value);
            case 'posts':
                return route('posts.index');
            default:
                return '#';
        }
    }

    public function getTypeValue(string $type)
    {
        return $this->type === $type ? $this->value : '';
    }

    public function isDropDown()
    {
        return $this->type === 'dropdown';
    }

    public function hasParent()
    {
        return $this->parent_id !== null;
    }

    public function scopeParent(Builder $query)
    {
        return $query->where('parent_id', null);
    }
}