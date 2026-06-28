<?php

namespace App\Traits;

use App\Support\PhoneNormalizer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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
        $op = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
        return static::where('name', $op, '%' . trim($name) . '%')->get();
    }
}
