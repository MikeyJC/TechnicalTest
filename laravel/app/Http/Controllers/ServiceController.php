<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        return Service::with('serviceProduct')->paginate($limit);
    }
}
