<?php

namespace App\Http\Controllers;

use App\Models\ServiceProduct;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceProductController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        return ServiceProduct::paginate($limit);
    }

    public function byServiceId($id)
    {
        $service = Service::with('serviceProduct')->find($id);

        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        return response()->json($service->serviceProduct);
    }
}
