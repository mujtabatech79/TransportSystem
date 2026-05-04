<?php

namespace App\Http\Controllers;

use App\Models\LiveLocation;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Log; 


use App\Models\Userr;
use App\Models\Vehicle;
use App\Models\Booking;

use Hash;
use Auth;


class LiveLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(LiveLocation $liveLocation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LiveLocation $liveLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LiveLocation $liveLocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LiveLocation $liveLocation)
    {
        //
    }











public function myBookings()
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }
        
        $customer = Userr::find($customerId);
        
        if (!$customer) {
            return redirect()->route('login')->with('error', 'User not found');
        }
        
        return view('customer.seelivelocation', [
            'userName' => $customer->name
        ]);
    }
    
    /**
     * Get Bookings Data for AJAX with Pagination
     */
    // yeh use ni ho rha last main method lkha howa ha wo use ho rha ha
    public function getBookingsDataa(Request $request)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }
        
        $perPage = $request->get('per_page', 5);
        $page = $request->get('page', 1);
        
        // Customer ki saari bookings fetch karein with vehicle and provider details
        $bookingsQuery = Booking::with(['vehicle.user'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc');
        
        // Paginate results
        $bookings = $bookingsQuery->paginate($perPage, ['*'], 'page', $page);
        
        // Format bookings for response
        $formattedBookings = [];
        foreach ($bookings as $booking) {
            // Status ke hisaab se badge class
            $badgeClass = 'bg-secondary';
            $statusText = $booking->status_text;
            
            if ($booking->status === 'request') {
                $badgeClass = 'bg-warning';
            } elseif ($booking->status === 'accept') {
                $badgeClass = 'bg-success';
            } elseif ($booking->status === 'reject') {
                $badgeClass = 'bg-danger';
            } elseif ($booking->status === 'complete') {
                $badgeClass = 'bg-info';
            }
            
            // Provider name
            $providerName = null;
            if ($booking->vehicle && $booking->vehicle->user) {
                $providerName = $booking->vehicle->user->name;
            }
            
            $formattedBookings[] = [
                'id' => $booking->id,
                'pickup_location' => $booking->pickup_location,
                'dropoff_location' => $booking->dropoff_location,
                'goods_type' => $booking->goods_type,
                'goods_weight' => $booking->goods_weight,
                'vehicle_type' => $booking->vehicle ? $booking->vehicle->vehicle_type : 'N/A',
                'provider_name' => $providerName,
                'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
                'status' => $booking->status,
                'status_text' => $statusText,
                'badge_class' => $badgeClass,
                'created_at' => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : null
            ];
        }
        
        return response()->json([
            'success' => true,
            'bookings' => $formattedBookings,
            'current_page' => $bookings->currentPage(),
            'last_page' => $bookings->lastPage(),
            'total' => $bookings->total(),
            'per_page' => $bookings->perPage()
        ]);
    }
    
    /**
     * Get Single Booking Details
     */
    public function getBookingDetailss($id)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }
        
        // Booking fetch karein with all relations
        $booking = Booking::with(['vehicle.user', 'customer'])
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }
        
        // Format dates
        $bookingDate = $booking->booking_date ? $booking->booking_date->format('F d, Y') : null;
        $createdDate = $booking->created_at ? $booking->created_at->format('F d, Y h:i A') : null;
        $acceptedDate = $booking->accepted_at ? $booking->accepted_at->format('F d, Y h:i A') : null;
        $rejectedDate = $booking->rejected_at ? $booking->rejected_at->format('F d, Y h:i A') : null;
        $dispatchedDate = $booking->dispatched_at ? $booking->dispatched_at->format('F d, Y h:i A') : null;
        $inTransitDate = $booking->in_transit_at ? $booking->in_transit_at->format('F d, Y h:i A') : null;
        $deliveredDate = $booking->delivered_at ? $booking->delivered_at->format('F d, Y h:i A') : null;
        
        // Prepare response
        $response = [
            'id' => $booking->id,
            'status' => $booking->status,
            'status_text' => $booking->status_text,
            'request_status' => $booking->request_status,
            'delivery_status' => $booking->delivery_status,
            'delivery_status_text' => $booking->delivery_status_text,
            'booking_date' => $bookingDate,
            'created_at' => $createdDate,
            'accepted_at' => $acceptedDate,
            'rejected_at' => $rejectedDate,
            'dispatched_at' => $dispatchedDate,
            'in_transit_at' => $inTransitDate,
            'delivered_at' => $deliveredDate,
            
            // Location details
            'pickup_location' => $booking->pickup_location,
            'pickup_lat' => $booking->pickup_lat,
            'pickup_lng' => $booking->pickup_lng,
            'dropoff_location' => $booking->dropoff_location,
            'dropoff_lat' => $booking->dropoff_lat,
            'dropoff_lng' => $booking->dropoff_lng,
            'pickup_time' => $booking->pickup_time,
            
            // Goods details
            'goods_type' => $booking->goods_type,
            'goods_weight' => $booking->goods_weight,
            'special_instructions' => $booking->special_instructions,
            
            // Fare details
            'estimated_distance' => $booking->estimated_distance,
            'estimated_fare' => $booking->estimated_fare,
            'actual_distance' => $booking->actual_distance,
            'actual_fare' => $booking->actual_fare,
            
            // Payment details
            'payment_method' => $booking->payment_method,
            'payment_status' => $booking->payment_status,
            
            // Route details
            'route_polyline' => $booking->route_polyline,
            'route_directions' => $booking->route_directions,
            'selected_route_name' => $booking->selected_route_name,
            'has_tolls' => $booking->has_tolls,
            'toll_cost' => $booking->toll_cost,
            'route_options' => $booking->route_options,
            
            // Rejection reason
            'rejection_reason' => $booking->rejection_reason,
            
            // Vehicle details
            'vehicle' => null
        ];
        
        if ($booking->vehicle) {
            $response['vehicle'] = [
                'id' => $booking->vehicle->id,
                'vehicle_number' => $booking->vehicle->vehicle_number,
                'vehicle_type' => $booking->vehicle->vehicle_type,
                'can_carry' => $booking->vehicle->can_carry,
                'weight_capacity' => $booking->vehicle->weight_capacity,
                'vehicle_image' => $booking->vehicle->vehicle_image,
                
                // Provider details
                'provider' => $booking->vehicle->user ? [
                    'id' => $booking->vehicle->user->id,
                    'name' => $booking->vehicle->user->name,
                    'email' => $booking->vehicle->user->email,
                    'mobile' => $booking->vehicle->user->mobile,
                    'profile_image' => $booking->vehicle->user->profile_image
                ] : null
            ];
        }
        
        return response()->json([
            'success' => true,
            'booking' => $response
        ]);
    }
    
    /**
     * Get Tracking Information for a Booking
     */
    public function getTrackingInfoo($id)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }
        
        $booking = Booking::with(['vehicle.user'])
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }
        
        // Timeline based on status
        $timeline = [];
        $progressPercentage = 0;
        
        if ($booking->status === 'request') {
            $progressPercentage = 10;
            $timeline[] = [
                'status' => 'completed',
                'title' => 'Booking Requested',
                'description' => 'Your booking request has been submitted.',
                'timestamp' => $booking->created_at ? $booking->created_at->format('M d, Y h:i A') : null
            ];
            $timeline[] = [
                'status' => 'active',
                'title' => 'Awaiting Confirmation',
                'description' => 'Waiting for service provider to confirm your booking.',
                'timestamp' => null
            ];
        } 
        elseif ($booking->status === 'reject') {
            $progressPercentage = 10;
            $timeline[] = [
                'status' => 'completed',
                'title' => 'Booking Requested',
                'description' => 'Your booking request was submitted.',
                'timestamp' => $booking->created_at ? $booking->created_at->format('M d, Y h:i A') : null
            ];
            $timeline[] = [
                'status' => 'completed',
                'title' => 'Request Rejected',
                'description' => $booking->rejection_reason ?? 'Booking request was rejected.',
                'timestamp' => $booking->rejected_at ? $booking->rejected_at->format('M d, Y h:i A') : null
            ];
        } 
        elseif ($booking->status === 'accept') {
            $progressPercentage = 40;
            
            // Always show booking requested
            $timeline[] = [
                'status' => 'completed',
                'title' => 'Booking Requested',
                'description' => 'Your booking request was submitted.',
                'timestamp' => $booking->created_at ? $booking->created_at->format('M d, Y h:i A') : null
            ];
            
            // Show accepted
            $timeline[] = [
                'status' => 'completed',
                'title' => 'Booking Accepted',
                'description' => 'Service provider accepted your booking.',
                'timestamp' => $booking->accepted_at ? $booking->accepted_at->format('M d, Y h:i A') : null
            ];
            
            // Check delivery status
            $statusOrder = ['order_confirmed', 'vehicle_dispatched', 'in_transit', 'delivered'];
            $currentStatusIndex = array_search($booking->delivery_status, $statusOrder);
            
            // Order Confirmed
            if ($booking->delivery_status === 'order_confirmed' || $currentStatusIndex >= 0) {
                $timeline[] = [
                    'status' => $booking->delivery_status === 'order_confirmed' ? 'active' : 'completed',
                    'title' => 'Order Confirmed',
                    'description' => 'Your order has been confirmed and is being processed.',
                    'timestamp' => $booking->accepted_at ? $booking->accepted_at->format('M d, Y h:i A') : null
                ];
            } else {
                $timeline[] = [
                    'status' => 'pending',
                    'title' => 'Order Confirmed',
                    'description' => 'Awaiting order confirmation.',
                    'timestamp' => null
                ];
            }
            
            // Vehicle Dispatched
            if ($booking->delivery_status === 'vehicle_dispatched' || $currentStatusIndex >= 1) {
                $timeline[] = [
                    'status' => $booking->delivery_status === 'vehicle_dispatched' ? 'active' : 'completed',
                    'title' => 'Vehicle Dispatched',
                    'description' => 'Vehicle has been dispatched to pickup location.',
                    'timestamp' => $booking->dispatched_at ? $booking->dispatched_at->format('M d, Y h:i A') : null
                ];
            } else {
                $timeline[] = [
                    'status' => 'pending',
                    'title' => 'Vehicle Dispatched',
                    'description' => 'Waiting for vehicle dispatch.',
                    'timestamp' => null
                ];
            }
            
            // In Transit
            if ($booking->delivery_status === 'in_transit' || $currentStatusIndex >= 2) {
                $timeline[] = [
                    'status' => $booking->delivery_status === 'in_transit' ? 'active' : 'completed',
                    'title' => 'In Transit',
                    'description' => 'Shipment is on the way to destination.',
                    'timestamp' => $booking->in_transit_at ? $booking->in_transit_at->format('M d, Y h:i A') : null
                ];
            } else {
                $timeline[] = [
                    'status' => 'pending',
                    'title' => 'In Transit',
                    'description' => 'Shipment will begin transit soon.',
                    'timestamp' => null
                ];
            }
            
            // Delivered
            if ($booking->delivery_status === 'delivered') {
                $timeline[] = [
                    'status' => 'completed',
                    'title' => 'Delivered',
                    'description' => 'Shipment has been delivered successfully.',
                    'timestamp' => $booking->delivered_at ? $booking->delivered_at->format('M d, Y h:i A') : null
                ];
            } else {
                $timeline[] = [
                    'status' => 'pending',
                    'title' => 'Delivered',
                    'description' => 'Delivery pending.',
                    'timestamp' => null
                ];
            }
            
            // Calculate progress percentage
            if ($booking->delivery_status === 'order_confirmed') $progressPercentage = 40;
            elseif ($booking->delivery_status === 'vehicle_dispatched') $progressPercentage = 60;
            elseif ($booking->delivery_status === 'in_transit') $progressPercentage = 80;
            elseif ($booking->delivery_status === 'delivered') $progressPercentage = 100;
        } 
        elseif ($booking->status === 'complete') {
            $progressPercentage = 100;
            
            $timeline[] = [
                'status' => 'completed',
                'title' => 'Booking Requested',
                'description' => 'Your booking request was submitted.',
                'timestamp' => $booking->created_at ? $booking->created_at->format('M d, Y h:i A') : null
            ];
            
            $timeline[] = [
                'status' => 'completed',
                'title' => 'Booking Accepted',
                'description' => 'Service provider accepted your booking.',
                'timestamp' => $booking->accepted_at ? $booking->accepted_at->format('M d, Y h:i A') : null
            ];
            
            $timeline[] = [
                'status' => 'completed',
                'title' => 'Order Confirmed',
                'description' => 'Your order was confirmed.',
                'timestamp' => $booking->accepted_at ? $booking->accepted_at->format('M d, Y h:i A') : null
            ];
            
            if ($booking->dispatched_at) {
                $timeline[] = [
                    'status' => 'completed',
                    'title' => 'Vehicle Dispatched',
                    'description' => 'Vehicle was dispatched to pickup location.',
                    'timestamp' => $booking->dispatched_at->format('M d, Y h:i A')
                ];
            }
            
            if ($booking->in_transit_at) {
                $timeline[] = [
                    'status' => 'completed',
                    'title' => 'In Transit',
                    'description' => 'Shipment was on the way.',
                    'timestamp' => $booking->in_transit_at->format('M d, Y h:i A')
                ];
            }
            
            if ($booking->delivered_at) {
                $timeline[] = [
                    'status' => 'completed',
                    'title' => 'Delivered',
                    'description' => 'Shipment delivered successfully.',
                    'timestamp' => $booking->delivered_at->format('M d, Y h:i A')
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'status_text' => $booking->status_text,
                'delivery_status' => $booking->delivery_status,
                'delivery_status_text' => $booking->delivery_status_text,
                'pickup_location' => $booking->pickup_location,
                'dropoff_location' => $booking->dropoff_location,
                'goods_type' => $booking->goods_type,
                'goods_weight' => $booking->goods_weight,
                'progress_percentage' => $progressPercentage,
                'vehicle' => $booking->vehicle ? [
                    'vehicle_type' => $booking->vehicle->vehicle_type,
                    'vehicle_number' => $booking->vehicle->vehicle_number,
                    'provider_name' => $booking->vehicle->user ? $booking->vehicle->user->name : null
                ] : null,
                'timeline' => $timeline
            ]
        ]);
    }
    
    /**
     * Get Route Map Data for a Booking
     */
    public function getRouteMap($id)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }
        
        $booking = Booking::where('id', $id)
            ->where('customer_id', $customerId)
            ->first();
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'route' => [
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
                'polyline' => $booking->route_polyline,
                'directions' => $booking->route_directions,
                'route_name' => $booking->selected_route_name,
                'has_tolls' => $booking->has_tolls,
                'toll_cost' => $booking->toll_cost
            ]
        ]);
    }
    











}
