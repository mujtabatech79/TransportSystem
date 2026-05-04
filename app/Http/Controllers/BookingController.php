<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Userr;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingAcceptanceEmail;
use App\Mail\BookingRejectionEmail;
use App\Jobs\SendBookingAcceptanceEmail;
use App\Jobs\SendBookingRejectionEmail;
use App\Jobs\SendDeliveryStatusEmail;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function tripForm()
    {
        return view('customer.booknow');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }







//     public function tripSubmit(Request $request)
// {
//     // Step 1: Validate inputs
//     $request->validate([
//         'trip_date' => 'required|date',
//         'pickup_location' => 'required|string',
//         'drop_location' => 'required|string',
//         'vehicle_id' => 'required|exists:vehicles,id',
//     ]);

//     // Step 2: Check if user is logged in
//     if (!$request->session()->has('user_id')) {
//         return redirect()->route('login')->withErrors(['msg' => 'Please login first.']);
//     }

//     // Step 3: Create booking
//         Booking::create([
//         'customer_id' => $request->session()->get('user_id'),
//         'vehicle_id' => $request->vehicle_id,
//         'booking_date' => $request->trip_date,
//         'pickup_location' => $request->pickup_location,
//         'dropoff_location' => $request->drop_location,
//         'status' => 'booked',

        


//          // Optional fields - only save if they exist
       



//     ]);
     
//     Vehicle::where('id', $request->vehicle_id)->update(['is_booked' => 'yes']);

//     return redirect()->route('customer.findvehicle')->with('success', 'Trip booked successfully!');
// }


// see trips old
public function providerTrippp()
{
    // Session se provider ka ID lo
    $providerId = session('user_id');

    if (!$providerId || session('role') != 'provider') {
        return redirect()->route('login')->with('error', 'Access denied.');
    }

    // Provider ki vehicles nikal lo
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');

    // Un vehicles ki bookings nikal lo sath customer ka data bhi
    $bookings = Booking::whereIn('vehicle_id', $vehicleIds)
                ->with(['customer', 'vehicle'])
                ->get();


    $totalBookings = $bookings->count();
    $pendingBookings = $bookings->where('status', 'pending')->count();
    $completedBookings = $bookings->where('status', 'completed')->count();
    $totalEarnings = $bookings->sum('fare');

    return view('Provider.booked_vehicle', compact('bookings', 'totalBookings', 'pendingBookings', 'completedBookings', 'totalEarnings'));

    
}




//veiw trip by admin
public function viewtrip()
    {
        $vehicles = Booking::all();
        return view('admin.viewtrip',compact('vehicles'));
    }




    // public function approve(Request $request, Vehicle $vehicle){
    //     $id=$request->id;
    //     $approve=Vehicle::find($id);
    //     if($approve){
    //         $approve->status='approved';
    //         $approve->save();
    //     }
    //     return redirect()->back();
    // }





    public function booking()
    {
        
        return view('Provider.booking');
    }














    public function tripSubmitttttt(Request $request)
{
    // Step 1: Validate inputs
    $request->validate([
        'trip_date' => 'required|date',
        'pickup_location' => 'required|string',
        'drop_location' => 'required|string',
        'vehicle_id' => 'required|exists:vehicles,id',
    ]);

    // Step 2: Check if user is logged in
    if (!$request->session()->has('user_id')) {
        return redirect()->route('login')->withErrors(['error' => 'Please login first.']);
    }

    // Step 3: Check if vehicle is available
    $vehicle = Vehicle::find($request->vehicle_id);
    if (!$vehicle || $vehicle->is_booked === 'yes') {
        return redirect()->back()->withErrors(['error' => 'Vehicle is no longer available.']);
    }

    // Step 4: Create booking
    Booking::create([
        'customer_id' => $request->session()->get('user_id'),
        'vehicle_id' => $request->vehicle_id,
        'booking_date' => $request->trip_date,
        'pickup_location' => $request->pickup_location,
        'dropoff_location' => $request->drop_location,
        'status' => 'booked',
        // Optional fields - store only if provided
        'pickup_time' => $request->pickup_time ?? null,
        'goods_type' => $request->goods_type ?? null,
        'goods_weight' => $request->goods_weight ?? null,
        'special_instructions' => $request->special_instructions ?? null,
        'estimated_fare' => $request->estimated_fare ?? null,
        'payment_method' => $request->payment_method ?? null,
    ]);

    // Step 5: Update vehicle status
    Vehicle::where('id', $request->vehicle_id)->update(['is_booked' => 'yes']);

    return redirect()->route('find.vehicle')->with('success', 'Trip booked successfully!');
}





public function tripSubmitt(Request $request)
{
    // ✅ ALL FIELDS REQUIRED - Complete validation
    $request->validate([
        'trip_date' => 'required|date',
        'pickup_time' => 'required|string',
        'pickup_location' => 'required|string',
        'pickup_lat' => 'required|numeric',
        'pickup_lng' => 'required|numeric',
        'drop_location' => 'required|string',
        'dropoff_lat' => 'required|numeric',
        'dropoff_lng' => 'required|numeric',
        'vehicle_id' => 'required|exists:vehicles,id',
        'goods_type' => 'required|string',
        'goods_weight' => 'required|numeric|min:1',
        'special_instructions' => 'required|string',
        'estimated_distance' => 'required|numeric',
        'estimated_fare' => 'required|numeric',
        'route_geometry' => 'required|string',
        'payment_method' => 'required|string|in:jazzcash,easypaisa,cod,bank_transfer',
    ]);

    // Check if user is logged in
    if (!$request->session()->has('user_id')) {
        return redirect()->route('login')->withErrors(['error' => 'Please login first.']);
    }

    // Check vehicle availability
    $vehicle = Vehicle::find($request->vehicle_id);
    if (!$vehicle || $vehicle->is_booked === 'yes') {
        return redirect()->back()->withErrors(['error' => 'Vehicle is no longer available.']);
    }

    // Create booking with ALL fields (no null values)
    $booking = Booking::create([
        'customer_id' => $request->session()->get('user_id'),
        'vehicle_id' => $request->vehicle_id,
        'booking_date' => $request->trip_date,
        'pickup_time' => $request->pickup_time,
        'pickup_location' => $request->pickup_location,
        'pickup_lat' => $request->pickup_lat,
        'pickup_lng' => $request->pickup_lng,
        'dropoff_location' => $request->drop_location,
        'dropoff_lat' => $request->dropoff_lat,
        'dropoff_lng' => $request->dropoff_lng,
        'goods_type' => $request->goods_type,
        'goods_weight' => $request->goods_weight,
        'special_instructions' => $request->special_instructions,
        'distance_km' => $request->estimated_distance,
        'estimated_fare' => $request->estimated_fare,
        'route_geometry' => $request->route_geometry,
        'payment_method' => $request->payment_method,
        'status' => 'booked',
    ]);

    // Update vehicle status
    $vehicle->update(['is_booked' => 'yes']);

    return redirect()->route('find.vehicle')->with('success', 'Booking confirmed! Distance: ' . $request->estimated_distance . 'km, Fare: Rs ' . $request->estimated_fare);
}





































 

 private $orsApiKey;
    private $baseFarePerKm = 25; // Base fare per km
    
    public function __construct()
    {
        $this->orsApiKey = env('ORS_API_KEY', 'your-default-key-here');
    }
    
    /**
     * Show booking form with vehicle
     */
    public function showBookingFormmm(Request $request)
    {
        $vehicleId = $request->query('vehicle_id');
        $vehicle = null;
        $recentBookings = collect();
        
        if ($vehicleId) {
            $vehicle = Vehicle::with('user')->find($vehicleId);
            
            // Check if vehicle is available
            if ($vehicle && $vehicle->is_booked == 'yes') {
                return redirect()->route('find.vehicle')
                    ->with('error', 'This vehicle is no longer available.');
            }
        }
        
        // Get recent bookings for logged in user
        if ($request->session()->has('user_id')) {
            $recentBookings = Booking::with('vehicle')
                ->where('customer_id', $request->session()->get('user_id'))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return view('customer.booking', compact('vehicle', 'recentBookings'));
    }
    
    /**
     * Calculate multiple route alternatives using OpenRouteService API
     */
    public function calculateRoute(Request $request)
    {
        try {
            $request->validate([
                'pickup_lat' => 'required|numeric',
                'pickup_lng' => 'required|numeric',
                'dropoff_lat' => 'required|numeric',
                'dropoff_lng' => 'required|numeric',
                'vehicle_type' => 'nullable|string'
            ]);
            
            Log::info('Multiple routes requested', [
                'pickup' => [$request->pickup_lat, $request->pickup_lng],
                'dropoff' => [$request->dropoff_lat, $request->dropoff_lng]
            ]);
            
            $routes = [];
            $vehicleType = $request->vehicle_type ?? 'truck';
            
            // TRY 1: Request with alternative routes parameter
            try {
                $response = Http::timeout(15)->withHeaders([
                    'Authorization' => $this->orsApiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
                    'coordinates' => [
                        [(float)$request->pickup_lng, (float)$request->pickup_lat],
                        [(float)$request->dropoff_lng, (float)$request->dropoff_lat]
                    ],
                    'instructions' => true,
                    'geometry' => true,
                    'instructions_format' => 'text',
                    'language' => 'en',
                    'alternative_routes' => [
                        'target_count' => 3,
                        'share_factor' => 0.6
                    ]
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['routes']) && count($data['routes']) > 0) {
                        foreach ($data['routes'] as $index => $route) {
                            $routes[] = $this->formatRouteData($route, $index, $vehicleType);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('ORS alternative routes failed: ' . $e->getMessage());
            }
            
            // TRY 2: If no routes or less than 2, try with different optimization
            if (count($routes) < 2) {
                try {
                    // Try with different optimization (fastest)
                    $response = Http::timeout(15)->withHeaders([
                        'Authorization' => $this->orsApiKey,
                        'Content-Type' => 'application/json',
                    ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
                        'coordinates' => [
                            [(float)$request->pickup_lng, (float)$request->pickup_lat],
                            [(float)$request->dropoff_lng, (float)$request->dropoff_lat]
                        ],
                        'instructions' => true,
                        'geometry' => true,
                        'instructions_format' => 'text',
                        'language' => 'en',
                        'preference' => 'fastest'
                    ]);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['routes'][0])) {
                            $routes[] = $this->formatRouteData($data['routes'][0], count($routes), $vehicleType);
                        }
                    }
                    
                    // Try with shortest preference
                    $response = Http::timeout(15)->withHeaders([
                        'Authorization' => $this->orsApiKey,
                        'Content-Type' => 'application/json',
                    ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
                        'coordinates' => [
                            [(float)$request->pickup_lng, (float)$request->pickup_lat],
                            [(float)$request->dropoff_lng, (float)$request->dropoff_lat]
                        ],
                        'instructions' => true,
                        'geometry' => true,
                        'instructions_format' => 'text',
                        'language' => 'en',
                        'preference' => 'shortest'
                    ]);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['routes'][0])) {
                            $routes[] = $this->formatRouteData($data['routes'][0], count($routes), $vehicleType);
                        }
                    }
                    
                    // Try with recommended
                    $response = Http::timeout(15)->withHeaders([
                        'Authorization' => $this->orsApiKey,
                        'Content-Type' => 'application/json',
                    ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
                        'coordinates' => [
                            [(float)$request->pickup_lng, (float)$request->pickup_lat],
                            [(float)$request->dropoff_lng, (float)$request->dropoff_lat]
                        ],
                        'instructions' => true,
                        'geometry' => true,
                        'instructions_format' => 'text',
                        'language' => 'en',
                        'preference' => 'recommended'
                    ]);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['routes'][0])) {
                            $routes[] = $this->formatRouteData($data['routes'][0], count($routes), $vehicleType);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('ORS preference routes failed: ' . $e->getMessage());
                }
            }
            
            // TRY 3: If still less than 2 routes, use graphhopper as fallback
            if (count($routes) < 2) {
                try {
                    $graphhopperRoutes = $this->getGraphHopperRoutes($request);
                    if (!empty($graphhopperRoutes)) {
                        $routes = array_merge($routes, $graphhopperRoutes);
                    }
                } catch (\Exception $e) {
                    Log::warning('GraphHopper fallback failed: ' . $e->getMessage());
                }
            }
            
            // FINAL: If still no routes, generate synthetic alternatives
            if (count($routes) < 2) {
                $routes = $this->generateAlternativeRoutes($request);
            }
            
            // Remove duplicates (based on similar distance)
            $routes = $this->removeDuplicateRoutes($routes);
            
            // Ensure we have at least 2 routes
            if (count($routes) < 2) {
                $routes = $this->generateAlternativeRoutes($request);
            }
            
            // Limit to 3 routes maximum
            $routes = array_slice($routes, 0, 3);
            
            // Reindex IDs
            $formattedRoutes = [];
            foreach ($routes as $index => $route) {
                $route['id'] = $index;
                $formattedRoutes[] = $route;
            }
            
            Log::info('Final routes calculated', ['routes_count' => count($formattedRoutes)]);
            
            return response()->json([
                'success' => true,
                'routes' => $formattedRoutes
            ]);
            
        } catch (\Exception $e) {
            Log::error('Route Calculation Error: ' . $e->getMessage());
            // Always return at least generated routes
            return response()->json([
                'success' => true,
                'routes' => $this->generateAlternativeRoutes($request)
            ]);
        }
    }

    /**
     * Format route data consistently
     */
    private function formatRouteData($route, $index, $vehicleType)
    {
        $distanceMeters = $route['summary']['distance'] ?? 0;
        $distanceKm = round($distanceMeters / 1000, 2);
        
        $durationSeconds = $route['summary']['duration'] ?? 0;
        $durationMinutes = round($durationSeconds / 60);
        
        // Calculate fare
        $fare = $this->calculateFare($distanceKm, $vehicleType);
        
        // Check if route has tolls
        $hasTolls = $this->checkForTolls($route);
        
        // Extract steps for directions
        $steps = [];
        if (isset($route['segments'][0]['steps'])) {
            foreach ($route['segments'][0]['steps'] as $step) {
                $steps[] = [
                    'instruction' => $step['instruction'] ?? '',
                    'distance' => $step['distance'] ?? 0,
                    'duration' => $step['duration'] ?? 0
                ];
            }
        }
        
        // Determine route name based on index and characteristics
        $name = $this->getRouteName($index, $hasTolls);
        
        // Add preference info to name
        if (isset($route['summary']['preference'])) {
            $preference = $route['summary']['preference'];
            if ($preference === 'fastest') {
                $name = 'Fastest Route' . ($hasTolls ? ' (With Tolls)' : '');
            } elseif ($preference === 'shortest') {
                $name = 'Shortest Route' . ($hasTolls ? ' (With Tolls)' : '');
            }
        }
        
        return [
            'id' => $index,
            'name' => $name,
            'distance' => $distanceKm,
            'distance_text' => $distanceKm . ' km',
            'duration' => $durationMinutes,
            'duration_text' => $this->formatDuration($durationMinutes),
            'fare' => $fare,
             'duration' => $durationMinutes,        // ✅ Store in minutes
        'duration_seconds' => $durationSeconds, // Optional: store in seconds
        'duration_text' => $this->formatDuration($durationMinutes),
            'fare_text' => 'Rs ' . number_format($fare),
            'geometry' => $route['geometry'] ?? null,
            'directions' => $steps,
            'has_tolls' => $hasTolls,
            'toll_cost' => $hasTolls ? $this->estimateTollCost($distanceKm) : 0,
            'summary' => $this->generateRouteSummary($route)
        ];
    }

    /**
     * Get routes from GraphHopper API as fallback
     */
    private function getGraphHopperRoutes(Request $request)
    {
        $routes = [];
        
        try {
            // Try with GraphHopper API (free, no key required for limited usage)
            $response = Http::timeout(10)->get('https://graphhopper.com/api/1/route', [
                'point' => [
                    $request->pickup_lat . ',' . $request->pickup_lng,
                    $request->dropoff_lat . ',' . $request->dropoff_lng
                ],
                'vehicle' => 'car',
                'locale' => 'en',
                'instructions' => 'true',
                'calc_points' => 'true',
                'points_encoded' => 'false',
                'alternative_route.max_paths' => 3,
                'key' => '8c1cdfb0-6b2b-4b8d-9f0a-5e1b2c3d4e5f' // Free demo key - replace with your own
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['paths']) && count($data['paths']) > 0) {
                    foreach ($data['paths'] as $index => $path) {
                        $distanceKm = round($path['distance'] / 1000, 2);
                        $durationMinutes = round($path['time'] / 1000 / 60);
                        $vehicleType = $request->vehicle_type ?? 'truck';
                        $fare = $this->calculateFare($distanceKm, $vehicleType);
                        
                        // Extract steps
                        $steps = [];
                        if (isset($path['instructions'])) {
                            foreach ($path['instructions'] as $instruction) {
                                $steps[] = [
                                    'instruction' => $instruction['text'] ?? '',
                                    'distance' => $instruction['distance'] ?? 0,
                                    'duration' => ($instruction['time'] ?? 0) / 1000
                                ];
                            }
                        }
                        
                        $hasTolls = (strpos($path['description'] ?? '', 'toll') !== false);
                        
                        $routes[] = [
                            'id' => $index,
                            'name' => $index === 0 ? 'Fastest Route' : ($index === 1 ? 'Alternative Route' : 'Scenic Route'),
                            'distance' => $distanceKm,
                            'distance_text' => $distanceKm . ' km',
                            'duration' => $durationMinutes,
                            'duration_text' => $this->formatDuration($durationMinutes),
                            'fare' => $fare,
                            'fare_text' => 'Rs ' . number_format($fare),
                            'geometry' => [
                                'type' => 'LineString',
                                'coordinates' => $path['points']['coordinates'] ?? []
                            ],
                            'directions' => $steps,
                            'has_tolls' => $hasTolls,
                            'toll_cost' => $hasTolls ? $this->estimateTollCost($distanceKm) : 0,
                            'summary' => $path['description'] ?? 'Standard route'
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('GraphHopper error: ' . $e->getMessage());
        }
        
        return $routes;
    }

    /**
     * Remove duplicate routes (similar distance)
     */
    private function removeDuplicateRoutes($routes)
    {
        if (count($routes) < 2) {
            return $routes;
        }
        
        $unique = [];
        $threshold = 0.15; // 15% difference threshold (increased from 10%)
        
        foreach ($routes as $route) {
            $isDuplicate = false;
            
            foreach ($unique as $existing) {
                $diff = abs($route['distance'] - $existing['distance']) / $existing['distance'];
                if ($diff < $threshold) {
                    $isDuplicate = true;
                    break;
                }
            }
            
            if (!$isDuplicate) {
                $unique[] = $route;
            }
        }
        
        return $unique;
    }

    /**
     * Enhanced alternative routes generation with more variety
     */
    private function generateAlternativeRoutes(Request $request, $mainRoute = null)
    {
        $lat1 = (float)$request->pickup_lat;
        $lon1 = (float)$request->pickup_lng;
        $lat2 = (float)$request->dropoff_lat;
        $lon2 = (float)$request->dropoff_lng;
        
        // Calculate direct distance
        $directDistance = $this->calculateHaversineDistance($lat1, $lon1, $lat2, $lon2);
        
        $routes = [];
        $vehicleType = $request->vehicle_type ?? 'truck';
        
        // Get location context to generate realistic route names
        $locationContext = $this->getLocationContext($lat1, $lon1, $lat2, $lon2);
        
        // Route 1: Fastest (motorway/highway)
        $fastDistance = round($directDistance * 1.1, 2);
        $fastDuration = round(($fastDistance / 70) * 60); // 70 km/h average
        $fastFare = $this->calculateFare($fastDistance, $vehicleType);
        $routes[] = [
            'id' => 0,
            'name' => 'Fastest Route (Motorway)',
            'distance' => $fastDistance,
            'distance_text' => $fastDistance . ' km',
            'duration' => $fastDuration,
            'duration_text' => $this->formatDuration($fastDuration),
            'fare' => $fastFare,
            'fare_text' => 'Rs ' . number_format($fastFare),
            'has_tolls' => true,
            'toll_cost' => $this->estimateTollCost($fastDistance),
            'summary' => $locationContext['highway'] . ' - Fastest route with tolls',
            'directions' => $this->generateLocationBasedDirections($locationContext, 'fastest'),
            'geometry' => null
        ];
        
        // Route 2: Economic (avoid tolls)
        $econDistance = round($directDistance * 1.4, 2); // 40% longer
        $econDuration = round(($econDistance / 45) * 60); // 45 km/h average
        $econFare = $this->calculateFare($econDistance, $vehicleType);
        $routes[] = [
            'id' => 1,
            'name' => 'Economic Route (No Tolls)',
            'distance' => $econDistance,
            'distance_text' => $econDistance . ' km',
            'duration' => $econDuration,
            'duration_text' => $this->formatDuration($econDuration),
            'fare' => $econFare,
            'fare_text' => 'Rs ' . number_format($econFare),
            'has_tolls' => false,
            'toll_cost' => 0,
            'summary' => $locationContext['local'] . ' - Avoid tolls, longer but economical',
            'directions' => $this->generateLocationBasedDirections($locationContext, 'economic'),
            'geometry' => null
        ];
        
        // Route 3: Balanced Route
        $balancedDistance = round($directDistance * 1.25, 2);
        $balancedDuration = round(($balancedDistance / 55) * 60); // 55 km/h average
        $balancedFare = $this->calculateFare($balancedDistance, $vehicleType);
        $routes[] = [
            'id' => 2,
            'name' => 'Balanced Route',
            'distance' => $balancedDistance,
            'distance_text' => $balancedDistance . ' km',
            'duration' => $balancedDuration,
            'duration_text' => $this->formatDuration($balancedDuration),
            'fare' => $balancedFare,
            'fare_text' => 'Rs ' . number_format($balancedFare),
            'has_tolls' => false,
            'toll_cost' => 0,
            'summary' => 'Mix of highways and local roads - Good balance',
            'directions' => $this->generateLocationBasedDirections($locationContext, 'balanced'),
            'geometry' => null
        ];
        
        return $routes;
    }

    /**
     * Get location context for generating realistic route names
     */
    private function getLocationContext($lat1, $lon1, $lat2, $lon2)
    {
        // Default Pakistani context
        $context = [
            'highway' => 'Via Motorway M-1/M-2',
            'local' => 'Via GT Road',
            'scenic' => 'Via Murree/Kashmir Highway'
        ];
        
        // You can enhance this with reverse geocoding to get actual road names
        try {
            // Simple reverse geocoding to get area names
            $response = Http::timeout(3)->get("https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat1}&lon={$lon1}&zoom=10");
            if ($response->successful()) {
                $data = $response->json();
                $city = $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['village'] ?? '';
                
                $response2 = Http::timeout(3)->get("https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat2}&lon={$lon2}&zoom=10");
                if ($response2->successful()) {
                    $data2 = $response2->json();
                    $city2 = $data2['address']['city'] ?? $data2['address']['town'] ?? $data2['address']['village'] ?? '';
                    
                    if ($city && $city2) {
                        $context['highway'] = "Via Motorway from {$city} to {$city2}";
                        $context['local'] = "Via local roads from {$city} to {$city2}";
                        $context['scenic'] = "Via scenic route from {$city} to {$city2}";
                    }
                }
            }
        } catch (\Exception $e) {
            // Use defaults
        }
        
        return $context;
    }

    /**
     * Generate location-based directions
     */
    private function generateLocationBasedDirections($context, $type)
    {
        $directions = [];
        
        switch ($type) {
            case 'fastest':
                $directions = [
                    ['instruction' => 'Start from pickup location', 'distance' => 0, 'duration' => 0],
                    ['instruction' => 'Head towards motorway entrance', 'distance' => 3000, 'duration' => 180],
                    ['instruction' => 'Merge onto motorway', 'distance' => 50000, 'duration' => 1800],
                    ['instruction' => 'Continue on motorway', 'distance' => 80000, 'duration' => 2700],
                    ['instruction' => 'Take exit towards destination', 'distance' => 5000, 'duration' => 300],
                    ['instruction' => 'Continue to destination', 'distance' => 2000, 'duration' => 120],
                    ['instruction' => 'Arrive at destination', 'distance' => 0, 'duration' => 0]
                ];
                break;
            case 'economic':
                $directions = [
                    ['instruction' => 'Start from pickup location', 'distance' => 0, 'duration' => 0],
                    ['instruction' => 'Head towards GT Road', 'distance' => 2000, 'duration' => 120],
                    ['instruction' => 'Continue on GT Road', 'distance' => 60000, 'duration' => 3600],
                    ['instruction' => 'Pass through local markets', 'distance' => 20000, 'duration' => 1800],
                    ['instruction' => 'Turn right towards destination', 'distance' => 15000, 'duration' => 900],
                    ['instruction' => 'Continue straight', 'distance' => 10000, 'duration' => 600],
                    ['instruction' => 'Arrive at destination', 'distance' => 0, 'duration' => 0]
                ];
                break;
            case 'balanced':
                $directions = [
                    ['instruction' => 'Start from pickup location', 'distance' => 0, 'duration' => 0],
                    ['instruction' => 'Take main highway', 'distance' => 15000, 'duration' => 900],
                    ['instruction' => 'Continue on national highway', 'distance' => 50000, 'duration' => 2700],
                    ['instruction' => 'Take connecting road', 'distance' => 20000, 'duration' => 1200],
                    ['instruction' => 'Follow signs to destination', 'distance' => 15000, 'duration' => 900],
                    ['instruction' => 'Arrive at destination', 'distance' => 0, 'duration' => 0]
                ];
                break;
            default:
                $directions = [
                    ['instruction' => 'Start from pickup location', 'distance' => 0, 'duration' => 0],
                    ['instruction' => 'Take scenic highway', 'distance' => 25000, 'duration' => 1800],
                    ['instruction' => 'Enjoy mountain views', 'distance' => 35000, 'duration' => 2700],
                    ['instruction' => 'Continue through valleys', 'distance' => 40000, 'duration' => 3000],
                    ['instruction' => 'Descend towards destination', 'distance' => 20000, 'duration' => 1500],
                    ['instruction' => 'Arrive at destination', 'distance' => 0, 'duration' => 0]
                ];
        }
        
        return $directions;
    }

    /**
     * Fallback distance calculation using Haversine formula
     */
    private function fallbackDistanceCalculation(Request $request)
    {
        $lat1 = (float)$request->pickup_lat;
        $lon1 = (float)$request->pickup_lng;
        $lat2 = (float)$request->dropoff_lat;
        $lon2 = (float)$request->dropoff_lng;
        
        // Haversine formula
        $R = 6371; // Earth's radius in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = round($R * $c, 2);
        
        // Add 30% for road distance approximation
        $roadDistance = round($distance * 1.3, 2);
        
        // Calculate duration (approx 40 km/h average speed)
        $durationMinutes = round(($roadDistance / 40) * 60);
        $durationHours = floor($durationMinutes / 60);
        $remainingMinutes = $durationMinutes % 60;
        $durationText = $durationHours > 0 
            ? "{$durationHours} hr {$remainingMinutes} min (approx)" 
            : "{$durationMinutes} min (approx)";
        
        // Calculate fare
        $vehicleType = $request->vehicle_type ?? 'truck';
        $fare = $this->calculateFare($roadDistance, $vehicleType);
        
        // Create a simple straight line geometry
        $geometry = [
            'type' => 'LineString',
            'coordinates' => [
                [$lon1, $lat1],
                [$lon2, $lat2]
            ]
        ];
        
        // Create simple directions for fallback
        $directions = [
            [
                'instruction' => 'Start from pickup location',
                'distance' => $roadDistance * 1000, // Convert to meters
                'duration' => $durationMinutes * 60, // Convert to seconds
                'type' => 1
            ],
            [
                'instruction' => 'Continue to destination',
                'distance' => 0,
                'duration' => 0,
                'type' => 2
            ]
        ];
        
        // Create a single route for fallback
        $routes[] = [
            'id' => 0,
            'name' => 'Recommended Route',
            'distance' => $roadDistance,
            'distance_text' => $roadDistance . ' km (approx)',
            'duration' => $durationMinutes,
            'duration_text' => $durationText,
            'fare' => $fare,
            'fare_text' => 'Rs ' . number_format($fare),
            'geometry' => $geometry,
            'directions' => $directions,
            'has_tolls' => false,
            'toll_cost' => 0,
            'summary' => 'Standard route',
            'is_approximate' => true
        ];
        
        return response()->json([
            'success' => true,
            'routes' => $routes
        ]);
    }
    
    /**
     * Calculate fare based on distance and vehicle type
     */
    private function calculateFare($distance, $vehicleType = 'truck')
    {
        // Base rates per km for different vehicle types
        $rates = [
            'motorcycle' => 15,
            'car' => 20,
            'pickup' => 25,
            'truck' => 35,
            'container' => 50,
            'trailer' => 65
        ];
        
        $ratePerKm = $rates[$vehicleType] ?? $this->baseFarePerKm;
        
        // Calculate base fare
        $baseFare = $distance * $ratePerKm;
        
        // Add minimum fare
        $minimumFare = 300;
        $fare = max($baseFare, $minimumFare);
        
        // Add taxes (16% GST)
        $tax = $fare * 0.16;
        $totalFare = $fare + $tax;
        
        return round($totalFare);
    }
    
    /**
     * Submit trip booking yeh use ni ho rha bcz tt
     */
 public function ttripSubmit(Request $request)
{
    try {
        // Validate inputs
        $request->validate([
            'trip_date' => 'required|date',
            'pickup_location' => 'required|string',
            'drop_location' => 'required|string',
            'vehicle_id' => 'required|exists:vehicles,id',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'destination_lat' => 'nullable|numeric',
            'destination_lng' => 'nullable|numeric',
            'payment_method' => 'required|string',
            'estimated_distance' => 'nullable|numeric',
            'estimated_fare' => 'nullable|numeric',
            'selected_route_data' => 'nullable|json'
        ]);

        // Check if user is logged in
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Please login first.']);
        }

        // Check if vehicle is available
        $vehicle = Vehicle::find($request->vehicle_id);
        if (!$vehicle || $vehicle->is_booked === 'yes') {
            return redirect()->back()->withErrors(['error' => 'Vehicle is no longer available.']);
        }

        // Log booking data for debugging
        Log::info('Creating booking with data:', $request->all());

        // Parse selected route data if available
        $selectedRoute = null;
        if ($request->selected_route_data) {
            $selectedRoute = json_decode($request->selected_route_data, true);
        }

        // Parse route options if available
        $routeOptions = null;
        if ($request->route_options) {
            $routeOptions = json_decode($request->route_options, true);
            
            // 🛑 FIX: Limit the size of route_options to avoid database issues
            if (is_array($routeOptions) && count($routeOptions) > 0) {
                // Store only essential fields to reduce size
                $routeOptions = array_map(function($route) {
                    return [
                        'id' => $route['id'] ?? null,
                        'name' => $route['name'] ?? null,
                        'distance' => $route['distance'] ?? null,
                        'duration' => $route['duration'] ?? null,
                        'fare' => $route['fare'] ?? null,
                        'has_tolls' => $route['has_tolls'] ?? false,
                        'toll_cost' => $route['toll_cost'] ?? null,
                        'summary' => $route['summary'] ?? null
                        // ❌ Remove 'directions' and 'geometry' to reduce size
                    ];
                }, $routeOptions);
            }
        }

        // Create booking with all fields
        $bookingData = [
            'customer_id' => $request->session()->get('user_id'),
            'vehicle_id' => $request->vehicle_id,
            'booking_date' => $request->trip_date,
            'pickup_location' => $request->pickup_location,
            'pickup_lat' => $request->pickup_lat,
            'pickup_lng' => $request->pickup_lng,
            'dropoff_location' => $request->drop_location,
            'dropoff_lat' => $request->destination_lat,
            'dropoff_lng' => $request->destination_lng,
            'status' => 'booked',
            'pickup_time' => $request->pickup_time,
            'goods_type' => $request->goods_type,
            'goods_weight' => $request->goods_weight,
            'special_instructions' => $request->special_instructions,
            'estimated_distance' => $request->estimated_distance,
            'estimated_fare' => $request->estimated_fare,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'route_polyline' => $request->route_polyline,
            'route_directions' => $request->route_directions,
            // New fields for route selection
            'selected_route_name' => $selectedRoute['name'] ?? null,
            'has_tolls' => $selectedRoute['has_tolls'] ?? false,
            'toll_cost' => $selectedRoute['toll_cost'] ?? null,
            'route_options' => $routeOptions ? json_encode($routeOptions) : null
        ];

        // 🛑 FIX: Log the data before insert to see if anything is wrong
        Log::info('Booking data prepared:', $bookingData);

        $booking = Booking::create($bookingData);

        // Update vehicle status
        Vehicle::where('id', $request->vehicle_id)->update(['is_booked' => 'yes']);

        // Clear session storage
        return redirect()->route('customer.login')
            ->with('success', 'Trip booked successfully! Booking ID: #' . $booking->id);
            
    } catch (\Exception $e) {
        // 🛑 FIX: Proper error handling with actual error message
        Log::error('Booking submission error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Failed to create booking: ' . $e->getMessage()]);
    }
}
    
    /**
     * Get saved route for a booking
     */
    public function getBookingRoute($id)
    {
        try {
            $booking = Booking::with('vehicle')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'booking' => $booking,
                'pickup' => [
                    'lat' => $booking->pickup_lat,
                    'lng' => $booking->pickup_lng,
                    'address' => $booking->pickup_location
                ],
                'dropoff' => [
                    'lat' => $booking->dropoff_lat,
                    'lng' => $booking->dropoff_lng,
                    'address' => $booking->dropoff_location
                ],
                'distance' => $booking->estimated_distance,
                'fare' => $booking->estimated_fare,
                'polyline' => $booking->route_polyline,
                'selected_route' => $booking->selected_route_name,
                'has_tolls' => $booking->has_tolls,
                'toll_cost' => $booking->toll_cost,
                'route_options' => $booking->route_options
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }
    }

    /**
     * Calculate Haversine distance
     */
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return round($R * $c, 2);
    }

    /**
     * Format duration
     */
    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        if ($hours > 0) {
            return $hours . ' hr ' . $mins . ' min';
        }
        return $mins . ' min';
    }

    /**
     * Check if route has tolls (simplified)
     */
    private function checkForTolls($route)
    {
        // Check if route has toll roads based on road names or instructions
        if (isset($route['segments'][0]['steps'])) {
            foreach ($route['segments'][0]['steps'] as $step) {
                $instruction = strtolower($step['instruction'] ?? '');
                $name = strtolower($step['name'] ?? '');
                
                // Check for toll indicators
                if (strpos($instruction, 'toll') !== false || 
                    strpos($name, 'toll') !== false ||
                    strpos($name, 'motorway') !== false ||
                    strpos($name, 'm-') !== false ||
                    strpos($name, 'm1') !== false ||
                    strpos($name, 'm2') !== false) {
                    return true;
                }
            }
        }
        
        // For demo purposes, assume highways have tolls
        if (isset($route['summary']['tags'])) {
            $tags = $route['summary']['tags'];
            if (in_array('highway', $tags) || in_array('motorway', $tags)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Estimate toll cost
     */
    private function estimateTollCost($distance)
    {
        // Simplified toll calculation (Rs 3-5 per km for trucks)
        // For motorways: Rs 4-6 per km
        $tollRatePerKm = 4.5;
        return round($distance * $tollRatePerKm, -1);
    }

    /**
     * Generate route summary
     */
    private function generateRouteSummary($route)
    {
        if (isset($route['segments'][0]['steps'])) {
            $steps = $route['segments'][0]['steps'];
            $mainRoads = [];
            $roadCount = 0;
            
            foreach ($steps as $step) {
                if (isset($step['name']) && $step['name'] && $step['name'] !== '-' && $roadCount < 3) {
                    $roadName = $step['name'];
                    // Clean up road name
                    $roadName = preg_replace('/[0-9]+/', '', $roadName); // Remove numbers
                    $roadName = trim($roadName);
                    
                    if (!empty($roadName) && !in_array($roadName, $mainRoads)) {
                        $mainRoads[] = $roadName;
                        $roadCount++;
                    }
                }
            }
            
            if (!empty($mainRoads)) {
                return 'Via ' . implode(', ', $mainRoads);
            }
        }
        return 'Standard route';
    }

    /**
     * Get route name based on index
     */
    private function getRouteName($index, $hasTolls)
    {
        $names = [
            0 => 'Fastest Route',
            1 => 'Economic Route',
            2 => 'Balanced Route'
        ];
        
        $baseName = $names[$index] ?? 'Route ' . ($index + 1);
        
        if ($hasTolls) {
            return $baseName . ' (With Tolls)';
        }
        
        return $baseName;
    }

    /**
     * Get all bookings for a customer
     */
    public function customerBookings(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }
        
        $bookings = Booking::with('vehicle')
            ->where('customer_id', $request->session()->get('user_id'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('customer.bookings', compact('bookings'));
    }

    /**
     * Get booking details
     */
    public function bookingDetailss($id)
    {
        $booking = Booking::with(['customer', 'vehicle'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->cancel()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking cancelled successfully'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be cancelled'
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Cancel booking error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking'
            ], 500);
        }
    }

    /**
     * Complete a booking
     */
    public function completeBooking($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->complete()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking completed successfully'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be completed'
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Complete booking error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete booking'
            ], 500);
        }
    }

























































 public function showBookingForm(Request $request)
    {
        $vehicleId = $request->query('vehicle_id');
        $vehicle = null;
        $recentBookings = collect();
        
        if ($vehicleId) {
            $vehicle = Vehicle::with('user')->find($vehicleId);
            
            // Check if vehicle is available
            if ($vehicle && $vehicle->is_booked == 'yes') {
                return redirect()->route('find.vehicle')
                    ->with('error', 'This vehicle is no longer available.');
            }
        }
        
        // Get recent bookings for logged in user
        if ($request->session()->has('user_id')) {
            $recentBookings = Booking::with('vehicle')
                ->where('customer_id', $request->session()->get('user_id'))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return view('customer.booking', compact('vehicle', 'recentBookings'));
    }

    /**
     * Submit trip booking (creates a pending request)
     */
   public function tripSubmit(Request $request)
{
    try {
        // Validate inputs
        $request->validate([
            'trip_date' => 'required|date',
            'pickup_location' => 'required|string',
            'drop_location' => 'required|string',
            'vehicle_id' => 'required|exists:vehicles,id',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'destination_lat' => 'nullable|numeric',
            'destination_lng' => 'nullable|numeric',
            'payment_method' => 'required|string|in:jazzcash,easypaisa,cod,card',
            'estimated_distance' => 'nullable|numeric',
            'estimated_fare' => 'nullable|numeric',
            'estimated_duration' => 'nullable|integer',
            'selected_route_data' => 'nullable|json',
            'pickup_time' => 'nullable|string',
            'goods_type' => 'nullable|string',
            'goods_weight' => 'nullable|numeric',
            'special_instructions' => 'nullable|string',
            'route_polyline' => 'nullable|string',
            'route_directions' => 'nullable|string',
            'route_options' => 'nullable|string'
        ]);

        // Check if user is logged in
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Please login first.']);
        }

        // Check if vehicle is available
        $vehicle = Vehicle::find($request->vehicle_id);
        if (!$vehicle) {
            return redirect()->back()->withErrors(['error' => 'Vehicle not found.']);
        }
        
        if ($vehicle->is_booked === 'yes') {
            return redirect()->back()->withErrors(['error' => 'Vehicle is no longer available.']);
        }

        Log::info('Creating booking request with data:', $request->all());

        // Parse selected route data if available
        $selectedRoute = null;
        $estimatedDuration = null;
        
        if ($request->selected_route_data) {
            $selectedRoute = json_decode($request->selected_route_data, true);
            $estimatedDuration = $selectedRoute['duration'] ?? null;
        }

        // Get duration from hidden input if available
        if ($request->estimated_duration) {
            $estimatedDuration = $request->estimated_duration;
        }

        // Parse route options if available
        $routeOptions = null;
        if ($request->route_options) {
            $routeOptions = json_decode($request->route_options, true);
            
            if (is_array($routeOptions) && count($routeOptions) > 0) {
                // Store only essential fields to reduce size
                $routeOptions = array_map(function($route) {
                    return [
                        'id' => $route['id'] ?? null,
                        'name' => $route['name'] ?? null,
                        'distance' => $route['distance'] ?? null,
                        'duration' => $route['duration'] ?? null,
                        'fare' => $route['fare'] ?? null,
                        'has_tolls' => $route['has_tolls'] ?? false,
                        'toll_cost' => $route['toll_cost'] ?? null,
                        'summary' => $route['summary'] ?? null
                    ];
                }, $routeOptions);
            }
        }

        // Calculate total fare
        $estimatedFare = $request->estimated_fare ?? 0;
        
        // Format duration text
        $durationText = $this->formatDuration($estimatedDuration);

        // Create booking with 'request' status
        $bookingData = [
            'customer_id' => $request->session()->get('user_id'),
            'vehicle_id' => $request->vehicle_id,
            'booking_date' => $request->trip_date,
            'pickup_location' => $request->pickup_location,
            'pickup_lat' => $request->pickup_lat,
            'pickup_lng' => $request->pickup_lng,
            'dropoff_location' => $request->drop_location,
            'dropoff_lat' => $request->destination_lat,
            'dropoff_lng' => $request->destination_lng,
            'status' => 'request',
            'pickup_time' => $request->pickup_time ?? '09:00',
            'goods_type' => $request->goods_type,
            'goods_weight' => $request->goods_weight,
            'special_instructions' => $request->special_instructions,
            'estimated_distance' => $request->estimated_distance,
            'estimated_fare' => $estimatedFare,
            'estimated_duration' => $estimatedDuration,
            'duration_text' => $durationText,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'route_polyline' => $request->route_polyline,
            'route_directions' => $request->route_directions,
            'selected_route_name' => $selectedRoute['name'] ?? null,
            'has_tolls' => $selectedRoute['has_tolls'] ?? false,
            'toll_cost' => $selectedRoute['toll_cost'] ?? null,
            'route_options' => $routeOptions ? json_encode($routeOptions) : null,
            'request_status' => 'pending',
            'delivery_status' => null,
            'is_booking_complete' => 'no'
        ];

        Log::info('Booking data prepared:', $bookingData);

        $booking = Booking::create($bookingData);

        // For COD, redirect directly to payment page (which will show COD as payment method)
        // For online payments, also go to payment page
        return redirect()->route('payment.show', $booking->id)
            ->with('success', 'Booking created! Please complete payment to confirm your booking. Booking ID: #' . $booking->id);
            
    } catch (\Exception $e) {
        Log::error('Booking submission error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Failed to create booking: ' . $e->getMessage()]);
    }
}


// Update bookingRequests method to use status field
public function bookingRequests()
{
    // Session se provider ka ID lo
    $providerId = session('user_id');

    if (!$providerId || session('role') != 'provider') {
        return redirect()->route('login')->with('error', 'Access denied.');
    }

    // Provider ki vehicles nikal lo
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');

    // Sirf pending requests dikhao (status = 'request')
    $bookingRequests = Booking::whereIn('vehicle_id', $vehicleIds)
        ->where('status', 'request')
        ->with(['customer', 'vehicle'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('Provider.booking_request', compact('bookingRequests'));
}

// Update acceptBooking method to set status to accept reject by vehicle owner
public function acceptBooking(Request $request, $id)
{
    try {
        $providerId = session('user_id');
        
        if (!$providerId || session('role') != 'provider') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        DB::beginTransaction();
        
        $booking = Booking::with(['vehicle.user', 'customer'])->findOrFail($id);

        // Verify that this booking belongs to provider's vehicle
        $vehicle = Vehicle::where('id', $booking->vehicle_id)
            ->where('user_id', $providerId)
            ->first();

        if (!$vehicle) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to accept this booking.'
            ], 403);
        }

        // Check if vehicle is still available
        if ($vehicle->is_booked === 'yes') {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'This vehicle is already booked.'
            ], 400);
        }

        // Accept the booking
        $booking->acceptRequest();

        // Payment method check karke payment_status update karo
        // Card, JazzCash, Easypaisa = paid (kyunke payment already ho chuki hai)
        // COD = pending hi rahega (delivery pe pay karega)
        if (in_array($booking->payment_method, ['card', 'jazzcash', 'easypaisa'])) {
            $booking->payment_status = 'paid';
            $booking->save();
            
            // Payment record bhi completed mark karo agar pending hai
            Payment::where('booking_id', $booking->id)
                ->where('status', Payment::STATUS_COMPLETED)
                ->update(['status' => Payment::STATUS_COMPLETED]);
        }
        // COD ke liye kuch nahi karna - payment_status pending rahega

        // Dispatch emails to queue (runs in background)
        // Send email to customer
        if ($booking->customer && $booking->customer->email) {
            SendBookingAcceptanceEmail::dispatch($booking, $booking->customer, 'customer');
        }
        
        // Send email to provider (vehicle owner)
        if ($booking->vehicle->user && $booking->vehicle->user->email) {
            SendBookingAcceptanceEmail::dispatch($booking, $booking->vehicle->user, 'provider');
        }
        
        DB::commit();

        Log::info('Booking accepted successfully', [
            'booking_id' => $booking->id,
            'provider_id' => $providerId,
            'payment_method' => $booking->payment_method,
            'payment_status' => $booking->payment_status,
            'customer_email' => $booking->customer->email ?? 'N/A',
            'provider_email' => $booking->vehicle->user->email ?? 'N/A'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking request accepted successfully! Emails will be sent shortly.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Accept booking error: ' . $e->getMessage(), [
            'booking_id' => $id,
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to accept booking: ' . $e->getMessage()
        ], 500);
    }
}

public function rejectBooking(Request $request, $id)
{
    try {
        $providerId = session('user_id');
        
        if (!$providerId || session('role') != 'provider') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        
        $booking = Booking::with(['vehicle.user', 'customer'])->findOrFail($id);

        // Verify that this booking belongs to provider's vehicle
        $vehicle = Vehicle::where('id', $booking->vehicle_id)
            ->where('user_id', $providerId)
            ->first();

        if (!$vehicle) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to reject this booking.'
            ], 403);
        }

        $rejectionReason = $request->rejection_reason ?? 'No specific reason provided by service provider.';

        // Reject the booking
        $booking->rejectRequest($rejectionReason);

        // Dispatch emails to queue (runs in background)
        // Send email to customer
        if ($booking->customer && $booking->customer->email) {
            SendBookingRejectionEmail::dispatch($booking, $booking->customer, $rejectionReason);
        }
        
        // Send email to provider (vehicle owner)
        if ($booking->vehicle->user && $booking->vehicle->user->email) {
            SendBookingRejectionEmail::dispatch($booking, $booking->vehicle->user, $rejectionReason);
        }
        
        DB::commit();

        Log::info('Booking rejected successfully', [
            'booking_id' => $booking->id,
            'provider_id' => $providerId,
            'reason' => $rejectionReason,
            'customer_email' => $booking->customer->email ?? 'N/A',
            'provider_email' => $booking->vehicle->user->email ?? 'N/A'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking request rejected successfully! Emails will be sent shortly.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Reject booking error: ' . $e->getMessage(), [
            'booking_id' => $id,
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to reject booking: ' . $e->getMessage()
        ], 500);
    }
}
// Update providerTrip method to use status field
public function providerTrip()
{
    // Session se provider ka ID lo
    $providerId = session('user_id');

    if (!$providerId || session('role') != 'provider') {
        return redirect()->route('login')->with('error', 'Access denied.');
    }

    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');

    // Sirf accepted bookings dikhao (status = 'accept') aur jo complete nahi hain
    $bookings = Booking::whereIn('vehicle_id', $vehicleIds)
        ->where('status', 'accept') // Sirf accepted bookings
        ->where('is_booking_complete', 'no')
        ->with(['customer', 'vehicle'])
        ->orderBy('created_at', 'desc')
        ->get();

    $totalBookings = $bookings->count();
    $pendingDeliveries = $bookings->where('delivery_status', '!=', 'delivered')->count();
    $completedBookings = $bookings->where('delivery_status', 'delivered')->count();
    $totalEarnings = $bookings->where('delivery_status', 'delivered')->sum('estimated_fare');

    return view('Provider.booked_vehicle', compact(
        'bookings', 
        'totalBookings', 
        'pendingDeliveries', 
        'completedBookings', 
        'totalEarnings'
    ));
}

// Update updateDeliveryStatus method to also update status to 'complete' when delivered
public function updateDeliveryStatus(Request $request, $id)
{
    try {
        $providerId = session('user_id');
        
        if (!$providerId || session('role') != 'provider') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:order_confirmed,vehicle_dispatched,in_transit,delivered'
        ]);

        DB::beginTransaction();
        
        $booking = Booking::with(['vehicle.user', 'customer'])->findOrFail($id);

        // Verify that this booking belongs to provider's vehicle
        $vehicle = Vehicle::where('id', $booking->vehicle_id)
            ->where('user_id', $providerId)
            ->first();

        if (!$vehicle) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this booking.'
            ], 403);
        }

        // Check if booking is accepted
        if ($booking->status !== 'accept' && $booking->status !== 'complete') {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Only accepted bookings can be updated.'
            ], 400);
        }

        $newStatus = $request->status;
        $oldStatus = $booking->delivery_status;
        
        // Check if status is being updated in correct sequence
        $statusOrder = ['order_confirmed', 'vehicle_dispatched', 'in_transit', 'delivered'];
        $currentIndex = array_search($oldStatus, $statusOrder);
        $newIndex = array_search($newStatus, $statusOrder);
        
        if ($newIndex <= $currentIndex) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Invalid status update sequence.'
            ], 400);
        }

        // Update delivery status - this will also calculate penalty if status is 'delivered'
        $penaltyResult = $booking->updateDeliveryStatus($newStatus);
        
        // Prepare response message
        $responseMessage = 'Delivery status updated to ' . $booking->delivery_status_text;
        
        // Add penalty information to response if delivered
        if ($newStatus === 'delivered' && $penaltyResult) {
            $responseMessage = $penaltyResult['message'] . ' Final fare: Rs ' . number_format($penaltyResult['actual_fare']) . '.';
        }
        
        // Send email notifications (only for dispatch, transit, and deliver)
        if (in_array($newStatus, ['vehicle_dispatched', 'in_transit', 'delivered'])) {
            // Send email to customer
            if ($booking->customer && $booking->customer->email) {
                SendDeliveryStatusEmail::dispatch($booking, $booking->customer, $newStatus);
            }
            
            // Send email to provider (vehicle owner)
            if ($booking->vehicle->user && $booking->vehicle->user->email) {
                SendDeliveryStatusEmail::dispatch($booking, $booking->vehicle->user, $newStatus);
            }
        }
        
        DB::commit();

        Log::info('Delivery status updated', [
            'booking_id' => $booking->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'provider_id' => $providerId,
            'customer_email' => $booking->customer->email ?? 'N/A',
            'provider_email' => $booking->vehicle->user->email ?? 'N/A',
            'penalty' => $penaltyResult['penalty'] ?? 0,
            'actual_fare' => $booking->actual_fare ?? $booking->estimated_fare
        ]);

        return response()->json([
            'success' => true,
            'message' => $responseMessage,
            'status' => $booking->delivery_status,
            'booking_status' => $booking->status,
            'is_complete' => $booking->is_booking_complete,
            'penalty' => $penaltyResult ? [
                'amount' => $penaltyResult['penalty'],
                'delay_hours' => $penaltyResult['delay_hours'],
                'actual_fare' => $penaltyResult['actual_fare'],
                'message' => $penaltyResult['message']
            ] : null
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Update delivery status error: ' . $e->getMessage(), [
            'booking_id' => $id,
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to update status: ' . $e->getMessage()
        ], 500);
    }
}
public function bookingDetails($id)
    {
        try {
            $providerId = session('user_id');
            
            if (!$providerId || session('role') != 'provider') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied.'
                ], 403);
            }

            $booking = Booking::with(['customer', 'vehicle'])->findOrFail($id);

            // Verify that this booking belongs to provider's vehicle
            $vehicle = Vehicle::where('id', $booking->vehicle_id)
                ->where('user_id', $providerId)
                ->first();

            if (!$vehicle) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to view this booking.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'booking' => $booking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }
    }












































































































































/**
 * Provider: Start sharing live location
 * FIX: delivery_status restriction hatai — kisi bhi active status pe share kar sakte hain
 */
public function startLocationSharing(Request $request, $id)
{
    $providerId = session('user_id');
 
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Access denied.'
        ], 403);
    }
 
    $booking = Booking::with('vehicle')->findOrFail($id);
 
    $vehicle = Vehicle::where('id', $booking->vehicle_id)
        ->where('user_id', $providerId)
        ->first();
 
    if (!$vehicle) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. This vehicle does not belong to you.'
        ], 403);
    }
 
    // Sirf yeh check karo — delivered booking pe sharing ka koi matlab nahi
    $allowedStatuses = ['order_confirmed', 'vehicle_dispatched', 'in_transit'];
    if (!in_array($booking->delivery_status, $allowedStatuses)) {
        return response()->json([
            'success' => false,
            'message' => 'Location sharing is only available for active bookings (order confirmed, dispatched, or in transit).'
        ], 400);
    }
 
    $booking->update([
        'is_sharing_location' => true,
        'location_updated_at' => now()
    ]);
 
    \Log::info('Location sharing started', [
        'booking_id' => $id,
        'provider_id' => $providerId,
        'delivery_status' => $booking->delivery_status
    ]);
 
    return response()->json([
        'success' => true,
        'message' => 'Location sharing started successfully.'
    ]);
}
 
/**
 * Provider: Stop sharing live location
 */
public function stopLocationSharing(Request $request, $id)
{
    $providerId = session('user_id');
 
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Access denied.'
        ], 403);
    }
 
    $booking = Booking::with('vehicle')->findOrFail($id);
 
    $vehicle = Vehicle::where('id', $booking->vehicle_id)
        ->where('user_id', $providerId)
        ->first();
 
    if (!$vehicle) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. This vehicle does not belong to you.'
        ], 403);
    }
 
    $booking->update([
        'is_sharing_location' => false,
        'live_lat'            => null,
        'live_lng'            => null,
        'location_updated_at' => now()
    ]);
 
    \Log::info('Location sharing stopped', [
        'booking_id' => $id,
        'provider_id' => $providerId
    ]);
 
    return response()->json([
        'success' => true,
        'message' => 'Location sharing stopped successfully.'
    ]);
}
 
/**
 * Provider: Update live location (JS har 60 seconds call karta hai)
 * FIX: is_sharing_location check hataya — direct update hoga
 *      Aur lat/lng validation strict kiya — Islamabad jaise galat values rokne ke liye
 */
public function updateLiveLocation(Request $request, $id)
{
    $providerId = session('user_id');
 
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Access denied.'
        ], 403);
    }
 
    // Strict validation — valid coordinates honi chahiye
    $request->validate([
        'lat' => 'required|numeric|between:-90,90',
        'lng' => 'required|numeric|between:-180,180',
    ]);
 
    $lat = $request->lat;
    $lng = $request->lng;
 
    // Extra check — lat/lng 0,0 mat accept karo (null island)
    if ($lat == 0 && $lng == 0) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid coordinates received (0,0). Please check GPS.'
        ], 400);
    }
 
    $booking = Booking::with('vehicle')->findOrFail($id);
 
    $vehicle = Vehicle::where('id', $booking->vehicle_id)
        ->where('user_id', $providerId)
        ->first();
 
    if (!$vehicle) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. This vehicle does not belong to you.'
        ], 403);
    }
 
    // Delivered booking pe update mat karo
    if ($booking->delivery_status === 'delivered') {
        return response()->json([
            'success' => false,
            'message' => 'Booking is already delivered. Location update not needed.'
        ], 400);
    }
 
    // Direct update — is_sharing_location bhi true set karo agar nahi tha
    $booking->update([
        'live_lat'            => $lat,
        'live_lng'            => $lng,
        'is_sharing_location' => true,
        'location_updated_at' => now()
    ]);
 
    \Log::info('Live location updated', [
        'booking_id'      => $id,
        'provider_id'     => $providerId,
        'lat'             => $lat,
        'lng'             => $lng,
        'delivery_status' => $booking->delivery_status
    ]);
 
    return response()->json([
        'success' => true,
        'message' => 'Location updated successfully.',
        'lat'     => $lat,
        'lng'     => $lng,
        'updated_at' => now()->format('h:i A')
    ]);
}
 
/**
 * Customer: Get current live location of vehicle
 */
public function getLiveLocation(Request $request, $id)
{
    $customerId = session('user_id');
 
    if (!$customerId) {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated.'
        ], 401);
    }
 
    $booking = Booking::where('id', $id)
        ->where('customer_id', $customerId)
        ->first();
 
    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found or does not belong to you.'
        ], 404);
    }
 
    if (!$booking->is_sharing_location || !$booking->live_lat || !$booking->live_lng) {
        return response()->json([
            'success'    => true,
            'is_sharing' => false,
            'message'    => 'Provider is not sharing location at the moment.',
            // Pickup/drop info toh denge taake map pe markers dikha sakein
            'pickup_location'  => $booking->pickup_location,
            'dropoff_location' => $booking->dropoff_location,
            'pickup_lat'       => $booking->pickup_lat,
            'pickup_lng'       => $booking->pickup_lng,
            'dropoff_lat'      => $booking->dropoff_lat,
            'dropoff_lng'      => $booking->dropoff_lng,
        ]);
    }
 
    return response()->json([
        'success'          => true,
        'is_sharing'       => true,
        'lat'              => $booking->live_lat,
        'lng'              => $booking->live_lng,
        'updated_at'       => $booking->location_updated_at
            ? \Carbon\Carbon::parse($booking->location_updated_at)->format('h:i A')
            : null,
        'pickup_location'  => $booking->pickup_location,
        'dropoff_location' => $booking->dropoff_location,
        'pickup_lat'       => $booking->pickup_lat,
        'pickup_lng'       => $booking->pickup_lng,
        'dropoff_lat'      => $booking->dropoff_lat,
        'dropoff_lng'      => $booking->dropoff_lng,
    ]);
}

}
