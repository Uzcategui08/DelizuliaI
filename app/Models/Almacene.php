<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Almacene
 *
 * @property $id_almacen
 * @property $nombre
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Almacene extends Model
{

    protected $perPage = 20;

    protected $primaryKey = 'id_almacen';

    public function almacen()
    {
        return $this->belongsTo(Almacene::class, 'inventario', 'id_almacen');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre'];


    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_almacen', 'id_almacen');
    }
}
