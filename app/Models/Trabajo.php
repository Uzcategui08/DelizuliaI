<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Trabajo
 *
 * @property $id_trabajo
 * @property $nombre
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Trabajo extends Model
{

    protected $perPage = 20;
    protected $primaryKey = 'id_trabajo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_trabajo', 'nombre', 'traducciones'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'traducciones' => 'array'
    ];

    /**
     * @param string $idioma 'es' o 'en'
     * @return string
     */
    public function getNombreEnIdioma(string $idioma = 'es')
    {
        if (is_string($this->traducciones)) {
            $traducciones = json_decode($this->traducciones, true);
        } else {
            $traducciones = $this->traducciones ?? [];
        }

        if (isset($traducciones[$idioma])) {
            return $traducciones[$idioma];
        }

        return $idioma === 'en' ? $this->nombre . ' (No English Translation)' : $this->nombre;
    }
}
