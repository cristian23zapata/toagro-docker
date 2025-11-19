<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class City
 *
 * Represents a city used as the origin or destination of a route.  Cities are
 * created on‑the‑fly when new trips are created and a route includes a new
 * origin or destination.  Having a separate table of cities makes it easier
 * to analyse trips by city and prevents duplicate city names from being
 * inserted repeatedly.
 */
class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];
}
