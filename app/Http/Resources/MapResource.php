<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property integer id
 * @property mixed date
 * @property float amount
 * @property string description
 * @property string file
 * @property string type
 * @property string bank
 * @property mixed created_at
 * @property mixed updated_at
 */
class MapResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     * @noinspection PhpMissingParamTypeInspection
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'amount' => $this->amount,
            'description' => $this->description,
            'type' => $this->type,
            'bank' => $this->bank,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
