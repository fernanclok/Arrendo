<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    public function getProperties() {
    $properties = Property::join('zones', 'zones.id', '=', 'properties.zone_id')
        ->select('properties.*', 'zones.name as zone_name')
        ->get()
        ->map(function ($property) {
            $photos = $property->property_photos_path ? json_decode($property->property_photos_path, true) : [];
            $property->property_photos_path = is_array($photos) 
                ? array_map(fn($photo) => asset($photo), $photos) 
                : [];
            return $property;
        });

    return response()->json($properties);
}

    public function getFilteredProperties(Request $request) {
        $params = $request->all();
    
        $params['allowPets'] = $params['allowPets'] === 'true';
        $params['parking'] = $params['parking'] === 'true';
    
        $filteredParams = array_filter($params, function($param) {
            return $param !== null && $param !== false && $param !== '';
        });
    
        $properties = $this->formatQuery($filteredParams);

        return response()->json($properties);
    }
    
    public function formatQuery($params) {
        $query = Property::query();
    
        if (isset($params['maxPrice'])) {
            if ($params['maxPrice'] === '+10000') {
                $query->where('property_price', '>', 0);
            } else {
                $query->where('property_price', '<=', $params['maxPrice']);
            }
        }
    
        $query->join('zones', 'zones.id', '=', 'properties.zone_id')
              ->select('properties.*', 'zones.name as zone_name');
    
        if (isset($params['selectedZone'])) {
            $query->where('zones.name', 'like', '%' . $params['selectedZone'] . '%');
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

        $properties = $query->get()
            ->map(function ($property) {
                $photos = $property->property_photos_path ? json_decode($property->property_photos_path, true) : [];
                $property->property_photos_path = is_array($photos) 
                    ? array_map(fn($photo) => asset($photo), $photos) 
                    : [];
                return $property;
            });
    
        return $properties;
    }
    
   
    public function get(Request $request)
    {
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
            'total_bathrooms' => 'required|numeric',
            'total_rooms' => 'required|integer',
            'total_m2' => 'required|integer',
            'have_parking' => 'required|boolean',
            'accept_mascots' => 'required|boolean',
            'property_price' => 'required|numeric',
            'property_details' => 'required|string',
            'zone_id' => 'required|integer|exists:zones,id',
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
}