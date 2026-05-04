<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\Userr;
use Illuminate\Http\Request;
use Auth;
use App\Models\Fraud;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail; // Add this line
use App\Mail\VehicleRegistrationConfirmation; // Add this line
use App\Mail\VehicleUpdateConfirmation; 
use App\Mail\VehicleApprovalStatus; 
class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexx()
    {
        
    }

    public function show_reg_vehicle()
    {
      return view('provider.regvehicle');
    }


public function Register_Vehicle(Request $request)
{
    $request->validate([
        'vehicle_number'  => 'required|string|unique:vehicles,vehicle_number',
        'chassis_number'  => 'required|string|unique:vehicles,chassis_number',
        'vehicle_type'    => 'required|string|max:50',
        'can_carry'       => 'required|string|max:100',
        'weight_capacity' => 'required|integer|min:0',
        'vehicle_image'   => 'required|image|mimes:jpg,jpeg,png|max:4096',
        'smartcard_image' => 'required|image|mimes:jpg,jpeg,png|max:4096',
    ]);

    $user_id = session('user_id');
    
    // Get the user details for email
    $user = Userr::find($user_id);
    
    if (!$user) {
        return redirect()->back()->with('error', 'User not found. Please login again.');
    }

    // Upload Vehicle Image
    if ($request->hasFile('vehicle_image')) {
        $vehicleImg = $request->file('vehicle_image');
        $vehicleImageName = time() . '_vehicle_' . uniqid() . '.' . $vehicleImg->getClientOriginalExtension();
        $vehicleImg->move(public_path('uploads/vehicles'), $vehicleImageName);
    } else {
        $vehicleImageName = null;
    }

    // Upload Smartcard Image
    if ($request->hasFile('smartcard_image')) {
        $smartcardImg = $request->file('smartcard_image');
        $smartcardImageName = time() . '_smartcard_' . uniqid() . '.' . $smartcardImg->getClientOriginalExtension();
        $smartcardImg->move(public_path('uploads/smartcards'), $smartcardImageName);
    } else {
        $smartcardImageName = null;
    }

    // Create vehicle with pending status
    $vehicle = Vehicle::create([
        'user_id' => $user_id,
        'vehicle_number' => $request->vehicle_number,
        'chassis_number' => $request->chassis_number,
        'vehicle_type' => strtolower($request->vehicle_type),
        'can_carry' => $request->can_carry,
        'weight_capacity' => $request->weight_capacity,
        'vehicle_image' => $vehicleImageName,
        'smartcard_image' => $smartcardImageName,
        'status' => 'pending', // Set as pending for admin approval
        'is_booked' => 'no',
        'is_active' => false, // Not active until approved
    ]);

    // Send confirmation email to vehicle owner
   try {
        // ... vehicle creation code ...
        
        // Send email
        try {
            Mail::to($user->email)->send(new VehicleRegistrationConfirmation($vehicle, $user));
            $emailSent = true;
        } catch (\Exception $e) {
            \Log::error('Email failed: ' . $e->getMessage());
            $emailSent = false;
        }
        
        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle registered successfully! ' . ($emailSent ? 'Confirmation email sent.' : 'Email could not be sent, but vehicle is registered.')
            ]);
        }
        
        // Regular redirect for non-AJAX
        return redirect()->route('my.vehicle')->with('success', 'Vehicle registered successfully! Confirmation email will sent soon after Admin approval.');
        
    } catch (\Exception $e) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()->with('error', $e->getMessage());
    }
}
    
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
    public function show(Vehicle $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        //
    }


// pending vehicle for admin
    public function pendingvehicle()
{
    // Eager load service provider info
    $pendingVehicles = Vehicle::where('status', 'pending')->with('user') ->paginate(12);

    $approvedCount = Vehicle::where('status', 'approved')->count();
    $rejectedCount = Vehicle::where('status', 'rejected')->count();
    $totalCount = Vehicle::count();

    return view('admin.pendingvehicle', compact('pendingVehicles',
        'approvedCount', 
        'rejectedCount', 
        'totalCount'

        ));
        }

public function approveVehicle($id)
{
    $vehicle = Vehicle::with('user')->findOrFail($id);
    $user = $vehicle->user;
    
    try {
        $vehicle->status = 'approved';
        $vehicle->is_active = true;
        $vehicle->save();
        
        // Send approval email
        try {
            Mail::to($user->email)->send(new VehicleApprovalStatus($vehicle, $user, 'approved'));
            $emailSent = true;
        } catch (\Exception $e) {
            Log::error('Approval email failed: ' . $e->getMessage());
            $emailSent = false;
        }
        
        // Check if AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $emailSent ? 'Vehicle approved successfully! Confirmation email sent to owner.' : 'Vehicle approved but email could not be sent.'
            ]);
        }
        
        return redirect()->back()->with('success', $emailSent ? 'Vehicle approved successfully! Email sent to owner.' : 'Vehicle approved but email failed.');
        
    } catch (\Exception $e) {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()->with('error', 'Error approving vehicle: ' . $e->getMessage());
    }
}

public function rejectVehicle(Request $request, $id)
{
    $request->validate([
        'rejection_reason' => 'required|string|min:10'
    ]);
    
    $vehicle = Vehicle::with('user')->findOrFail($id);
    $user = $vehicle->user;
    
    try {
        $vehicle->status = 'rejected';
        $vehicle->is_active = false;
        $vehicle->save();
        
        // Send rejection email with reason
        try {
            Mail::to($user->email)->send(new VehicleApprovalStatus($vehicle, $user, 'rejected', $request->rejection_reason));
            $emailSent = true;
        } catch (\Exception $e) {
            Log::error('Rejection email failed: ' . $e->getMessage());
            $emailSent = false;
        }
        
        // Check if AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $emailSent ? 'Vehicle rejected successfully! Notification email sent to owner.' : 'Vehicle rejected but email could not be sent.'
            ]);
        }
        
        return redirect()->back()->with('success', $emailSent ? 'Vehicle rejected successfully! Email sent to owner.' : 'Vehicle rejected but email failed.');
        
    } catch (\Exception $e) {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()->with('error', 'Error rejecting vehicle: ' . $e->getMessage());
    }

}

public function getVehicleDetailsss($id)
{
    $vehicle = Vehicle::with('user')->findOrFail($id);
    return response()->json($vehicle);
}
//for admin

public function showAvailable()
    {
        $availableVehicles = Vehicle::where('status', 'approved')->where('is_booked', 'no')->with('user')->get();
        $puree=Vehicle::where('status', 'approved')->where('is_booked', 'no')->with('is_active', '1')->count();
         $onTripCount = Vehicle::where('status', 'approved')
                          ->where('is_booked', 'yes')
                          ->count();
        $totalCount = Vehicle::whereIn('status', ['pending', 'approved'])->count();
          $totalActive = Vehicle::where('status', 'approved')->where('is_active', true)->count();

         return view('admin.seeavailable', compact(
        'availableVehicles', 
        'onTripCount', 
        'totalActive',
        'totalCount'
    ));
    }



// show customer all available vehicle with filter by type

    public function All_Availablee(Request $request)
{
    $query = Vehicle::where('status', 'approved')
                    
                    ->where('is_active', true)
                    ->with('user');
    
    // Filter by vehicle type if provided
    if ($request->has('vehicle_type') && !empty($request->vehicle_type)) {
        $query->where('vehicle_type', $request->vehicle_type);
    }
    
    $availableVehicles = $query->get();
    
    return view('customer.allAvaiableVehicle', compact('availableVehicles'));
}


public function All_Available(Request $request)
{
    $query = Vehicle::where('status', 'approved')
                    ->where('is_active', true)
                    ->with(['user', 'bookings.reviews']); // Eager load bookings and reviews
    
    // Filter by vehicle type if provided
    if ($request->has('vehicle_type') && !empty($request->vehicle_type)) {
        $query->where('vehicle_type', $request->vehicle_type);
    }
    
    $availableVehicles = $query->get();
    
    // Calculate average rating and reviews count for each vehicle
    foreach ($availableVehicles as $vehicle) {
        // Get all completed bookings with reviews
        $completedBookings = $vehicle->bookings->where('status', 'complete');
        
        $reviews = collect();
        foreach ($completedBookings as $booking) {
            foreach ($booking->reviews as $review) {
                if ($review->is_approved) {
                    $reviews->push($review);
                }
            }
        }
        
        $vehicle->average_rating = $reviews->avg('rating') ?? 0;
        $vehicle->reviews_count = $reviews->count();
        $vehicle->reviews_list = $reviews->map(function($review) {
            return [
                'customer_name' => $review->customer ? $review->customer->name : 'Anonymous',
                'rating' => $review->rating,
                'review' => $review->review,
                'created_at' => $review->created_at ? $review->created_at->format('M d, Y') : null,
            ];
        });
    }
    
    return view('customer.allAvaiableVehicle', compact('availableVehicles'));
}














    //for customer 

    public function showCusAvailable(Request $request)
{
    $query = Vehicle::where('status', 'approved')
                    ->where('is_booked', 'no')
                    ->where('is_active', true)
                    ->with('user');
    
    // Filter by vehicle type if provided
    if ($request->has('vehicle_type') && !empty($request->vehicle_type)) {
        $query->where('vehicle_type', $request->vehicle_type);
    }
    
    $availableVehicles = $query->get();
    
    return view('customer.findvehicle', compact('availableVehicles'));
}




    // togglebutton for disable vehicle

    public function toggleActive($id)
{
    $vehicle = Vehicle::findOrFail($id);

    // Toggle: if active -> deactivate, else activate
    $vehicle->is_active = !$vehicle->is_active;
    $vehicle->save();

    $status = $vehicle->is_active ? 'activated' : 'deactivated';
    return redirect()->back()->with('success', "Vehicle {$status} successfully.");
}








// for provider reg vehicle
 public function myvehicle()
    {
        // Get the logged-in user ID from session
        $user_id = session('user_id');
        
        // Fetch vehicles for this provider with booking statistics
        $vehicles = Vehicle::where('user_id', $user_id)
            ->withCount([
                'bookings as total_bookings',
                'bookings as completed_bookings' => function($query) {
                    $query->where('status', 'completed');
                },
                'bookings as cancelled_bookings' => function($query) {
                    $query->where('status', 'cancelled');
                }
            ])
            ->get();

        // Calculate statistics for the dashboard
        $stats = [
            'total_vehicles' => $vehicles->count(),
            'active_vehicles' => $vehicles->where('status', 'approved')->count(),
            'pending_vehicles' => $vehicles->where('status', 'pending')->count(),
            'inactive_vehicles' => $vehicles->where('status', 'inactive')->count(),
        ];

        return view('Provider.vehicle', compact('vehicles', 'stats'));
    }

    public function getVehicleDetails($id)
    {
        $vehicle = Vehicle::with(['bookings' => function($query) {
                $query->select('id', 'vehicle_id', 'status', 'booking_date', 'pickup_location', 'dropoff_location');
            }])
            ->withCount([
                'bookings as total_bookings',
                'bookings as completed_bookings' => function($query) {
                    $query->where('status', 'completed');
                },
                'bookings as cancelled_bookings' => function($query) {
                    $query->where('status', 'cancelled');
                }
            ])
            ->find($id);

        if (!$vehicle) {
            return response()->json(['error' => 'Vehicle not found'], 404);
        }

        return response()->json($vehicle);
    }

    public function updateVehicleStatus(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        
        if (!$vehicle) {
            return response()->json(['error' => 'Vehicle not found'], 404);
        }

        $vehicle->status = $request->status;
        $vehicle->save();

        return response()->json(['success' => 'Vehicle status updated successfully']);
    }

    public function editVehicle($id)
    {
        $vehicle = Vehicle::find($id);
        
        if (!$vehicle) {
            return response()->json(['error' => 'Vehicle not found'], 404);
        }

        return response()->json($vehicle);
    }

   public function updateVehicle(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        
        if (!$vehicle) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Vehicle not found.'], 404);
            }
            return redirect()->back()->with('error', 'Vehicle not found.');
        }

        // Check if vehicle is booked
        if ($vehicle->is_booked === 'yes') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot update vehicle information. Vehicle is currently booked.']);
            }
            return redirect()->back()->with('error', 'Cannot update vehicle information. Vehicle is currently booked.');
        }

        $request->validate([
            'vehicle_number'  => 'required|string|unique:vehicles,vehicle_number,' . $id,
            'chassis_number'  => 'required|string|unique:vehicles,chassis_number,' . $id,
            'vehicle_type'    => 'required|string|max:50',
            'can_carry'       => 'required|string|max:100',
            'weight_capacity' => 'required|integer|min:0',
            'vehicle_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'smartcard_image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // Handle vehicle image upload
        if ($request->hasFile('vehicle_image')) {
            // Delete old image if exists
            if ($vehicle->vehicle_image && file_exists(public_path('uploads/vehicles/' . $vehicle->vehicle_image))) {
                unlink(public_path('uploads/vehicles/' . $vehicle->vehicle_image));
            }
            
            $vehicleImg = $request->file('vehicle_image');
            $vehicleImageName = time() . '_vehicle_' . uniqid() . '.' . $vehicleImg->getClientOriginalExtension();
            $vehicleImg->move(public_path('uploads/vehicles'), $vehicleImageName);
        } else {
            $vehicleImageName = $vehicle->vehicle_image;
        }

        // Handle smartcard image upload
        if ($request->hasFile('smartcard_image')) {
            // Delete old image if exists
            if ($vehicle->smartcard_image && file_exists(public_path('uploads/smartcards/' . $vehicle->smartcard_image))) {
                unlink(public_path('uploads/smartcards/' . $vehicle->smartcard_image));
            }
            
            $smartcardImg = $request->file('smartcard_image');
            $smartcardImageName = time() . '_smartcard_' . uniqid() . '.' . $smartcardImg->getClientOriginalExtension();
            $smartcardImg->move(public_path('uploads/smartcards'), $smartcardImageName);
        } else {
            $smartcardImageName = $vehicle->smartcard_image;
        }

        // Update vehicle information
        $vehicle->update([
            'vehicle_number' => $request->vehicle_number,
            'chassis_number' => $request->chassis_number,
            'vehicle_type' => strtolower($request->vehicle_type),
            'can_carry' => $request->can_carry,
            'weight_capacity' => $request->weight_capacity,
            'vehicle_image' => $vehicleImageName,
            'smartcard_image' => $smartcardImageName,
            'status' => 'pending', // Set status to pending for admin approval
        ]);

        // Send notification email about update
        try {
            $user = Userr::find($vehicle->user_id);
            if ($user && $user->email) {
                Mail::to($user->email)->send(new VehicleUpdateConfirmation($vehicle, $user));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send vehicle update email: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle information updated successfully! It will be available after admin verification. You will recive an email soon.'
            ]);
        }

        return redirect()->route('my.vehicle')->with('success', 'Vehicle information updated successfully! It will be available after admin verification.');
    }






//for customerrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr

  public function FindVehicle()
    {
       

        return view('customer.findvehicle');
    }



































































































  public function index(Request $request)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Get customer details
        $customer = Userr::find($customerId);
        
        // Get all bookings for this customer with pagination
        $bookings = Booking::with(['vehicle.user'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Calculate statistics
        $totalBookings = Booking::where('customer_id', $customerId)->count();
        $completedBookings = Booking::where('customer_id', $customerId)->where('status', 'complete')->count();
        $inProgressBookings = Booking::where('customer_id', $customerId)
            ->where('status', 'accept')
            ->where('delivery_status', '!=', 'delivered')
            ->count();
        $totalSpent = Booking::where('customer_id', $customerId)
            ->where('status', 'complete')
            ->sum('actual_fare');
        
        return view('customer.mybookings', compact(
            'bookings', 
            'customer',
            'totalBookings',
            'completedBookings',
            'inProgressBookings',
            'totalSpent'
        ));
    }

    /**
     * Get bookings via AJAX for pagination
     */
    public function getBookings(Request $request)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }
        
        $page = $request->get('page', 1);
        $perPage = 10;
        
        $bookings = Booking::with(['vehicle.user'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
        
        // Format bookings for display
        $formattedBookings = [];
        foreach ($bookings as $booking) {
            $formattedBookings[] = [
                'id' => $booking->id,
                'pickup_location' => $this->truncateLocation($booking->pickup_location),
                'dropoff_location' => $this->truncateLocation($booking->dropoff_location),
                'goods_type' => $booking->goods_type ?? 'Goods',
                'goods_weight' => $booking->goods_weight ?? 0,
                'vehicle_type' => $booking->vehicle->vehicle_type ?? 'Vehicle',
                'booking_date' => $booking->booking_date,
                'provider_name' => $booking->vehicle->user->name ?? null,
                'status' => $booking->status,
                'status_text' => $booking->status_text,
                'badge_class' => $booking->status_badge,
                'delivery_status' => $booking->delivery_status,
                'delivery_status_text' => $booking->delivery_status_text,
            ];
        }
        
        return response()->json([
            'success' => true,
            'bookings' => $formattedBookings,
            'current_page' => $bookings->currentPage(),
            'last_page' => $bookings->lastPage(),
            'total' => $bookings->total()
        ]);
    }
    
    /**
     * Get single booking details
     */
    public function getBookingDetails($id)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }
        
        $booking = Booking::with(['vehicle.user', 'customer'])
            ->where('customer_id', $customerId)
            ->where('id', $id)
            ->first();
        
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }
        
        // Format dates
        $booking->formatted_booking_date = $booking->booking_date ? date('d M Y', strtotime($booking->booking_date)) : null;
        $booking->formatted_created_at = $booking->created_at ? date('d M Y, h:i A', strtotime($booking->created_at)) : null;
        $booking->formatted_accepted_at = $booking->accepted_at ? date('d M Y, h:i A', strtotime($booking->accepted_at)) : null;
        $booking->formatted_rejected_at = $booking->rejected_at ? date('d M Y, h:i A', strtotime($booking->rejected_at)) : null;
        $booking->formatted_dispatched_at = $booking->dispatched_at ? date('d M Y, h:i A', strtotime($booking->dispatched_at)) : null;
        $booking->formatted_in_transit_at = $booking->in_transit_at ? date('d M Y, h:i A', strtotime($booking->in_transit_at)) : null;
        $booking->formatted_delivered_at = $booking->delivered_at ? date('d M Y, h:i A', strtotime($booking->delivered_at)) : null;
        
        // Format locations
        $booking->full_pickup = $booking->pickup_location;
        $booking->full_dropoff = $booking->dropoff_location;
        
        // Vehicle images if any
        if ($booking->vehicle && $booking->vehicle->vehicle_image) {
            $booking->vehicle_image_url = asset('storage/' . $booking->vehicle->vehicle_image);
        }
        
        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
    }
    
    /**
     * Get tracking information for a booking
     */
    public function getTrackingInfo($id)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }
        
        $booking = Booking::with(['vehicle.user'])
            ->where('customer_id', $customerId)
            ->where('id', $id)
            ->first();
        
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }
        
        // Calculate progress percentage based on delivery status
        $progressPercentage = 0;
        $timeline = [];
        
        switch ($booking->status) {
            case 'request':
                $progressPercentage = 10;
                $timeline = [
                    ['status' => 'active', 'title' => 'Request Sent', 'description' => 'Your booking request has been sent', 'timestamp' => $booking->formatted_created_at],
                    ['status' => 'pending', 'title' => 'Provider Confirmation', 'description' => 'Waiting for service provider to accept', 'timestamp' => null],
                    ['status' => 'pending', 'title' => 'Vehicle Dispatch', 'description' => 'Provider will dispatch vehicle after acceptance', 'timestamp' => null],
                    ['status' => 'pending', 'title' => 'In Transit', 'description' => 'Shipment on the way', 'timestamp' => null],
                    ['status' => 'pending', 'title' => 'Delivered', 'description' => 'Shipment delivered', 'timestamp' => null],
                ];
                break;
                
            case 'accept':
                switch ($booking->delivery_status) {
                    case 'order_confirmed':
                        $progressPercentage = 25;
                        $timeline = [
                            ['status' => 'completed', 'title' => 'Request Sent', 'description' => 'Your booking request has been sent', 'timestamp' => $booking->formatted_created_at],
                            ['status' => 'completed', 'title' => 'Provider Confirmation', 'description' => 'Service provider accepted your booking', 'timestamp' => $booking->formatted_accepted_at],
                            ['status' => 'active', 'title' => 'Vehicle Dispatch', 'description' => 'Provider is preparing to dispatch vehicle', 'timestamp' => null],
                            ['status' => 'pending', 'title' => 'In Transit', 'description' => 'Shipment on the way', 'timestamp' => null],
                            ['status' => 'pending', 'title' => 'Delivered', 'description' => 'Shipment delivered', 'timestamp' => null],
                        ];
                        break;
                        
                    case 'vehicle_dispatched':
                        $progressPercentage = 50;
                        $timeline = [
                            ['status' => 'completed', 'title' => 'Request Sent', 'description' => 'Your booking request has been sent', 'timestamp' => $booking->formatted_created_at],
                            ['status' => 'completed', 'title' => 'Provider Confirmation', 'description' => 'Service provider accepted your booking', 'timestamp' => $booking->formatted_accepted_at],
                            ['status' => 'completed', 'title' => 'Vehicle Dispatch', 'description' => 'Vehicle has been dispatched', 'timestamp' => $booking->formatted_dispatched_at],
                            ['status' => 'active', 'title' => 'In Transit', 'description' => 'Shipment on the way', 'timestamp' => null],
                            ['status' => 'pending', 'title' => 'Delivered', 'description' => 'Shipment delivered', 'timestamp' => null],
                        ];
                        break;
                        
                    case 'in_transit':
                        $progressPercentage = 75;
                        $timeline = [
                            ['status' => 'completed', 'title' => 'Request Sent', 'description' => 'Your booking request has been sent', 'timestamp' => $booking->formatted_created_at],
                            ['status' => 'completed', 'title' => 'Provider Confirmation', 'description' => 'Service provider accepted your booking', 'timestamp' => $booking->formatted_accepted_at],
                            ['status' => 'completed', 'title' => 'Vehicle Dispatch', 'description' => 'Vehicle has been dispatched', 'timestamp' => $booking->formatted_dispatched_at],
                            ['status' => 'completed', 'title' => 'In Transit', 'description' => 'Shipment is on the way', 'timestamp' => $booking->formatted_in_transit_at],
                            ['status' => 'active', 'title' => 'Delivered', 'description' => 'Shipment will be delivered soon', 'timestamp' => null],
                        ];
                        break;
                        
                    case 'delivered':
                        $progressPercentage = 100;
                        $timeline = [
                            ['status' => 'completed', 'title' => 'Request Sent', 'description' => 'Your booking request has been sent', 'timestamp' => $booking->formatted_created_at],
                            ['status' => 'completed', 'title' => 'Provider Confirmation', 'description' => 'Service provider accepted your booking', 'timestamp' => $booking->formatted_accepted_at],
                            ['status' => 'completed', 'title' => 'Vehicle Dispatch', 'description' => 'Vehicle has been dispatched', 'timestamp' => $booking->formatted_dispatched_at],
                            ['status' => 'completed', 'title' => 'In Transit', 'description' => 'Shipment is on the way', 'timestamp' => $booking->formatted_in_transit_at],
                            ['status' => 'completed', 'title' => 'Delivered', 'description' => 'Shipment delivered successfully', 'timestamp' => $booking->formatted_delivered_at],
                        ];
                        break;
                        
                    default:
                        $progressPercentage = 25;
                        $timeline = [
                            ['status' => 'completed', 'title' => 'Request Sent', 'description' => 'Your booking request has been sent', 'timestamp' => $booking->formatted_created_at],
                            ['status' => 'completed', 'title' => 'Provider Confirmation', 'description' => 'Service provider accepted your booking', 'timestamp' => $booking->formatted_accepted_at],
                            ['status' => 'pending', 'title' => 'Vehicle Dispatch', 'description' => 'Provider will dispatch vehicle', 'timestamp' => null],
                            ['status' => 'pending', 'title' => 'In Transit', 'description' => 'Shipment on the way', 'timestamp' => null],
                            ['status' => 'pending', 'title' => 'Delivered', 'description' => 'Shipment delivered', 'timestamp' => null],
                        ];
                }
                break;
                
            case 'reject':
                $progressPercentage = 0;
                $timeline = [
                    ['status' => 'completed', 'title' => 'Request Sent', 'description' => 'Your booking request has been sent', 'timestamp' => $booking->formatted_created_at],
                    ['status' => 'rejected', 'title' => 'Request Rejected', 'description' => $booking->rejection_reason ?? 'Booking was rejected', 'timestamp' => $booking->formatted_rejected_at],
                ];
                break;
                
            case 'complete':
                $progressPercentage = 100;
                $timeline = [
                    ['status' => 'completed', 'title' => 'Request Sent', 'description' => 'Your booking request has been sent', 'timestamp' => $booking->formatted_created_at],
                    ['status' => 'completed', 'title' => 'Provider Confirmation', 'description' => 'Service provider accepted your booking', 'timestamp' => $booking->formatted_accepted_at],
                    ['status' => 'completed', 'title' => 'Vehicle Dispatch', 'description' => 'Vehicle has been dispatched', 'timestamp' => $booking->formatted_dispatched_at],
                    ['status' => 'completed', 'title' => 'In Transit', 'description' => 'Shipment is on the way', 'timestamp' => $booking->formatted_in_transit_at],
                    ['status' => 'completed', 'title' => 'Delivered', 'description' => 'Shipment delivered successfully', 'timestamp' => $booking->formatted_delivered_at],
                ];
                break;
        }
        
        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'pickup_location' => $booking->pickup_location,
                'dropoff_location' => $booking->dropoff_location,
                'status' => $booking->status,
                'delivery_status' => $booking->delivery_status,
                'delivery_status_text' => $booking->delivery_status_text,
                'progress_percentage' => $progressPercentage,
                'timeline' => $timeline,
                'vehicle' => $booking->vehicle ? [
                    'vehicle_type' => $booking->vehicle->vehicle_type,
                    'registration_number' => $booking->vehicle->vehicle_number,
                    'vehicle_image' => $booking->vehicle->vehicle_image ? asset('storage/' . $booking->vehicle->vehicle_image) : null
                ] : null,
                'route_polyline' => $booking->route_polyline,
                'pickup_lat' => $booking->pickup_lat,
                'pickup_lng' => $booking->pickup_lng,
                'dropoff_lat' => $booking->dropoff_lat,
                'dropoff_lng' => $booking->dropoff_lng
            ]
        ]);
    }
    
    /**
     * Helper function to truncate location
     */
    private function truncateLocation($location, $length = 30)
    {
        if (strlen($location) <= $length) {
            return $location;
        }
        return substr($location, 0, $length) . '...';
    }


















































































 public function fraudpendingvehicle()
    {
        $pendingVehicles  = Vehicle::where('status', 'pending')->with('user')->paginate(12);
        $fraudVehicles    = Vehicle::where('fraud_status', 'fraud')->with('user')->latest()->take(20)->get();
        $notFraudVehicles = Vehicle::where('fraud_status', 'not_fraud')->with('user')->latest()->take(20)->get();
 
        $approvedCount = Vehicle::where('status', 'approved')->count();
        $rejectedCount = Vehicle::where('status', 'rejected')->count();
        $totalCount    = Vehicle::count();
        $fraudCount    = Vehicle::where('fraud_status', 'fraud')->count();
        $notFraudCount = Vehicle::where('fraud_status', 'not_fraud')->count();
 
        return view('admin.fraud', compact(
            'pendingVehicles',
            'fraudVehicles',
            'notFraudVehicles',
            'approvedCount',
            'rejectedCount',
            'totalCount',
            'fraudCount',
            'notFraudCount'
        ));
    }
 
}
