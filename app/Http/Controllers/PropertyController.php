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
    
    public function ownerProperties(Request $request) {
        $request = $request->all();

        $properties = Property::where('owner_user_id', $request['id'])->get();

        return response()->json($properties);
    }

    public function index(Request $request) {

    }
}
