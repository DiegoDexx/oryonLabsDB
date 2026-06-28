<?php

namespace App\Traits;

use App\Support\PhoneNormalizer;
use Illuminate\Database\Eloquent\Collection;

trait Searchable
{
    public static function searchByEmail(string $email): Collection
    {
        return static::where('email', trim($email))->get();
    }

    public static function searchByPhone(string $phone): Collection
    {
        $normalized = PhoneNormalizer::normalize($phone);
        if (!$normalized) return collect();

        return static::where('phone', $normalized)->get();
    }

    public static function searchByName(string $name): Collection
    {
        return static::where('name', 'ILIKE', '%' . trim($name) . '%')->get();
    }
}
