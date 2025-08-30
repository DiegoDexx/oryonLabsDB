<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
USE App\Models\ProjectField;

class ProjectFieldController extends Controller
{
    //
    
    /**
     * Listar todos los campos (con opción de filtrar por categoría).
     */
    public function index(Request $request)
    {
        $query = ProjectField::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->orderBy('order')->get());
    }

    /**
     * Crear un nuevo campo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'   => 'required|string|max:100',
            'field_name' => 'required|string|max:100|unique:project_fields,field_name',
            'label'      => 'required|string|max:255',
            'type'       => 'required|string|in:text,textarea,boolean,select,number,date',
            'options'    => 'nullable|array',
            'required'   => 'boolean',
            'order'      => 'integer',
        ]);

        $field = ProjectField::create($validated);

        return response()->json($field, 201);
    }



    /**
     * Actualizar un campo.
     */
    public function update(Request $request, ProjectField $projectField)
    {
        $validated = $request->validate([
            'category'   => 'sometimes|required|string|max:100',
            'field_name' => 'sometimes|required|string|max:100|unique:project_fields,field_name,' . $projectField->id,
            'label'      => 'sometimes|required|string|max:255',
            'type'       => 'sometimes|required|string|in:text,textarea,boolean,select,number,date',
            'options'    => 'nullable|array',
            'required'   => 'boolean',
            'order'      => 'integer',
        ]);

        $projectField->update($validated);

        return response()->json($projectField);
    }

    /**
     * Eliminar un campo.
     */
    public function destroy(ProjectField $projectField)
    {
        $projectField->delete();
        return response()->json(['message' => 'Campo eliminado correctamente']);
    }

    /**
     * Obtener campos por categoría (atajo).
     */
public function byCategory($category)
{
    $fields = ProjectField::where('category', $category)
        ->orderBy('order')
        ->get();

    if ($fields->isEmpty()) {
        return response()->json([
            'message' => 'No se encontraron campos para esta categoría',
            'data' => []
        ], 404);
    }

    return response()->json([
        'message' => 'Campos obtenidos correctamente',
        'data' => $fields
    ]);
}

}
