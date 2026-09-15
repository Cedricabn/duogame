<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuoSession extends Model
{
    protected $table = 'duo_sessions';

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $attributes = [
        'game' => '{}',
        'questions' => '{}',
        'ttt' => '{}',
        'chat' => '[]',
    ];

    protected $fillable = ['code', 'name_a', 'name_b', 'answers', 'game', 'questions', 'ttt', 'chat', 'idx'];
    protected $casts = [
        'answers' => 'array',
        'game' => 'array',
        'questions' => 'array',
        'ttt'     => 'array',
        'chat'    => 'array',
    ];

   // Transforme les champs snake_case en camelCase attendus par le front (nameA, nameB)
public function toArray(): array
{
    return [
        'code'      => $this->code,
        'nameA'     => $this->name_a,
        'nameB'     => $this->name_b,
        'answers'   => $this->answers ?? (object) [],
        'game'      => $this->game ?? (object) [],
        'questions' => $this->questions ?? (object) [],
        'ttt'       => $this->ttt ?? (object) [],
        'chat'      => $this->chat ?? [],
        'idx'       => $this->idx,
    ];
}
}
