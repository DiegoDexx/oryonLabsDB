<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Subscription;
use App\Support\PhoneNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'type'  => 'required|in:email,phone,name',
            'value' => 'required|string|max:255',
        ]);

        $type  = $request->query('type');
        $value = $request->query('value');

        if ($type === 'phone') {
            $value = PhoneNormalizer::normalize($value) ?? $value;
        }

        $method = 'searchBy' . ucfirst($type);

        return response()->json([
            'clients'       => Client::$method($value),
            'leads'         => Lead::$method($value),
            'invoices'      => Invoice::$method($value),
            'subscriptions' => Subscription::$method($value),
        ]);
    }
}
