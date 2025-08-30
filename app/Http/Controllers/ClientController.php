<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;


class ClientController extends Controller
{
 //api crud
 public function index()
 {
     // Get all clients
     return response()->json(Client::all());
 }

 public function show($id)
 {
     // Get a single client
     return response()->json(Client::find($id));
 }

 public function store(Request $request)
 {
     // Create a new client
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:clients',
        'phone' => 'nullable|string|max:20',
    ]);

     $client = Client::create($request->all());
     return response()->json($client, 201);
 }

 public function update(Request $request, $id)
 {
     // Update an existing client
     $client = Client::find($id);
     $client->update($request->all());
     return response()->json($client);
 }

 

 public function destroy($id)
 {
     // Delete a client
     $client = Client::find($id);
     $client->delete();
     return response()->json(null, 204);
 }

}
