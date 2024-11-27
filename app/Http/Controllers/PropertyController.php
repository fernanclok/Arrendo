<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Zone;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    public function getProperties() {
        $properties = Property::all();

        return response()->json($properties);
    }

    public function show($id)
    {
        $property = Property::findOrFail($id);
        return response()->json($property);    
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }



    public function getFilteredProperties(Request $request) {
        $params = $request->all();
    
        $params['allowPets'] = $params['allowPets'] === 'true';
        $params['parking'] = $params['parking'] === 'true';
    
        $filteredParams = array_filter($params, function($param) {
            return $param !== null && $param !== false && $param !== '';
        });
    
        $properties = $this->formatQuery($filteredParams);
        
        Log::debug($properties);
        return response()->json($properties);
    }
    
    public function formatQuery($params) {
        $query = Property::query();
    
        if (isset($params['maxPrice'])) {
            if ($params['maxPrice'] === '+10000') {
                $query->where('property_price', '>', 10000);
            } else {
                $query->where('property_price', '<=', $params['maxPrice']);
            }
        }
    
        if (isset($params['selectedZone'])) {
            $zone = Zone::where('name', $params['selectedZone'])->first();
            if ($zone) {
                $query->where('zone_id', $zone->id);
            }
        }
    
        if (isset($params['allowPets'])) {
            $query->where('allow_pets', $params['allowPets']);
        }
    
        if (isset($params['parking'])) {
            $query->where('have_parking', $params['parking']);
        }
    
        if (isset($params['rooms'])) {
            $query->where('total_rooms', '>=', $params['rooms']);
        }
    
        if (isset($params['bathrooms'])) {
            $query->where('total_bathrooms', '>=', $params['bathrooms']);
        }
    
        if (isset($params['m2'])) {
            $query->where('total_m2', '>=', $params['m2']);
        }
    
        return $query->get();
    }
   
    public function get(Request $request)
    {
        // Validar que el user_id exista
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $properties = Property::where('owner_user_id', $request->user_id)->get()->map(function ($property) {
            $photos = $property->property_photos_path ? json_decode($property->property_photos_path, true) : [];
            $property->property_photos_path = is_array($photos) 
                ? array_map(fn($photo) => asset($photo), $photos) 
                : [];
            return $property;
        });

        return response()->json($properties);
    }

    // Insertar en la base de datos
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'availability' => 'required|string',
            'total_bathrooms' => 'required|integer',
            'total_rooms' => 'required|integer',
            'total_m2' => 'required|integer',
            'have_parking' => 'required|boolean',
            'accept_mascots' => 'required|boolean',
            'property_price' => 'required|numeric',
            'property_details' => 'required|string',
            'zone_id' => 'required|integer|exists:zones,id',

            'colony' => 'nullable|string|max:100',
            'half_bathrooms' => 'nullable|integer',
            'surface_built' => 'nullable|integer',
            'total_surface' => 'nullable|integer',
            'antiquity' => 'nullable|integer',
            'maintenance' => 'nullable|numeric',
            'state_conservation' => 'nullable|string|max:50',
            'wineries' => 'nullable|integer',
            'closets' => 'nullable|integer',
            'levels' => 'nullable|integer',
            'property_photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $property = new Property();
        $property->street = $validatedData['street'];
        $property->number = $validatedData['number'];
        $property->city = $validatedData['city'];
        $property->state = $validatedData['state'];
        $property->postal_code = $validatedData['postal_code'];
        $property->availability = $validatedData['availability'];
        $property->total_bathrooms = $validatedData['total_bathrooms'];
        $property->total_rooms = $validatedData['total_rooms'];
        $property->total_m2 = $validatedData['total_m2'];
        $property->have_parking = $validatedData['have_parking'];
        $property->accept_mascots = $validatedData['accept_mascots'];
        $property->property_price = $validatedData['property_price'];
        $property->property_details = $validatedData['property_details'];
        $property->zone_id = $validatedData['zone_id'];
        $property->owner_user_id = $request->user_id;

        $property->colony = $validatedData['colony'];
        $property->half_bathrooms = $validatedData['half_bathrooms'];
        $property->surface_built = $validatedData['surface_built'];
        $property->total_surface = $validatedData['total_surface'];
        $property->antiquity = $validatedData['antiquity'];
        $property->maintenance = $validatedData['maintenance'];
        $property->state_conservation = $validatedData['state_conservation'];
        $property->wineries = $validatedData['wineries'];
        $property->closets = $validatedData['closets'];
        $property->levels = $validatedData['levels'];



        // Guardar las fotos de la propiedad
        if ($request->hasFile('property_photos')) {
            $photos = [];
            foreach ($request->file('property_photos') as $photo) {
                $originalName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $photo->getClientOriginalExtension();
                $timestamp = now()->format('YmdHis');
                $newName = "{$originalName}_{$timestamp}.{$extension}";
                $destinationPath = public_path('properties_photos');
                $photo->move($destinationPath, $newName);
                $photos[] = "properties_photos/{$newName}";
            }
            $property->property_photos_path = json_encode($photos);
        }

        $property->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Property created successfully'
        ]);
    }


    public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'street' => 'required|string|max:255',
        'number' => 'required|string|max:10',
        'city' => 'required|string|max:100',
        'state' => 'required|string|max:100',
        'postal_code' => 'required|string|max:20',
        'availability' => 'required|string',
        'total_bathrooms' => 'required|integer',
        'total_rooms' => 'required|integer',
        'total_m2' => 'required|integer',
        'have_parking' => 'required|boolean',
        'accept_mascots' => 'required|boolean',
        'property_price' => 'required|numeric',
        'property_details' => 'required|string',

        'colony' => 'nullable|string|max:100',
        'half_bathrooms' => 'nullable|integer',
        'surface_built' => 'nullable|integer',
        'total_surface' => 'nullable|integer',
        'antiquity' => 'nullable|integer',
        'maintenance' => 'nullable|numeric',
        'state_conservation' => 'nullable|string|max:50',
        'wineries' => 'nullable|integer',
        'closets' => 'nullable|integer',
        'levels' => 'nullable|integer',
    ]);

    $property = Property::findOrFail($id);
    $property->update($validatedData);

    return response()->json($property);
}

}