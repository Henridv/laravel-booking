<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RoleScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // filter out ext/int bookings based on gates
        if (Auth::check()) {
            if (Auth::user()->can('view.only.external')) {
                $builder->where('ext_booking', true);
            } elseif (Auth::user()->can('view.only.internal')) {
                $builder->where('ext_booking', false);
            }
        }
    }
}
