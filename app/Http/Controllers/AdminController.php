<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Booking;
use App\Models\Userr;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Display complaints management page
     */
    public function complaints()
    {
        $totalUsers = Userr::count();
        $verifiedVehicles = Vehicle::where('status', 'approved')->count();
        $activeBookings = Booking::where('status', 'booked')->count();
        $pendingComplaints = Complaint::where('status', 'pending')->count();
        
        return view('admin.complaints', compact(
            'totalUsers',
            'verifiedVehicles', 
            'activeBookings',
            'pendingComplaints'
        ));
    }
    
    /**
     * Get complaints data for AJAX requests
     */
    public function getComplaintsData(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $status = $request->get('status', 'all');
        
        $complaintsQuery = Complaint::with(['booking.customer', 'booking.vehicle.user', 'customer', 'provider']);
        
        if ($status !== 'all') {
            $complaintsQuery->where('status', $status);
        }
        
        $complaintsQuery->orderBy('created_at', 'desc');
        
        $complaints = $complaintsQuery->paginate($perPage);
        
        $formattedComplaints = [];
        foreach ($complaints as $complaint) {
            $formattedComplaints[] = [
                'id' => $complaint->id,
                'booking_id' => $complaint->booking_id,
                'complaint_type' => $complaint->complaint_type,
                'subject' => $complaint->subject,
                'description' => $complaint->description,
                'status' => $complaint->status,
                'status_text' => $complaint->status_text,
                'status_badge' => $complaint->status_badge,
                'created_at' => $complaint->created_at ? $complaint->created_at->format('Y-m-d H:i:s') : null,
                'created_at_formatted' => $complaint->created_at ? $complaint->created_at->format('M d, Y h:i A') : null,
                'admin_response' => $complaint->admin_response,
                'resolved_at' => $complaint->resolved_at ? $complaint->resolved_at->format('Y-m-d H:i:s') : null,
                
                // Customer info
                'customer' => $complaint->customer ? [
                    'id' => $complaint->customer->id,
                    'name' => $complaint->customer->name,
                    'email' => $complaint->customer->email,
                    'cnic' => $complaint->customer->cnic
                ] : null,
                
                // Provider info
                'provider' => $complaint->provider ? [
                    'id' => $complaint->provider->id,
                    'name' => $complaint->provider->name,
                    'email' => $complaint->provider->email,
                    'cnic' => $complaint->provider->cnic
                ] : null,
                
                // Booking info
                'booking' => $complaint->booking ? [
                    'id' => $complaint->booking->id,
                    'pickup_location' => $complaint->booking->pickup_location,
                    'dropoff_location' => $complaint->booking->dropoff_location,
                    'pickup_lat' => $complaint->booking->pickup_lat,
                    'pickup_lng' => $complaint->booking->pickup_lng,
                    'dropoff_lat' => $complaint->booking->dropoff_lat,
                    'dropoff_lng' => $complaint->booking->dropoff_lng,
                    'goods_type' => $complaint->booking->goods_type,
                    'goods_weight' => $complaint->booking->goods_weight,
                    'estimated_fare' => $complaint->booking->estimated_fare,
                    'actual_fare' => $complaint->booking->actual_fare,
                    'booking_date' => $complaint->booking->booking_date ? $complaint->booking->booking_date->format('Y-m-d') : null,
                    'pickup_time' => $complaint->booking->pickup_time,
                    'status' => $complaint->booking->status,
                    'status_text' => $complaint->booking->status_text,
                    'vehicle' => $complaint->booking->vehicle ? [
                        'id' => $complaint->booking->vehicle->id,
                        'vehicle_number' => $complaint->booking->vehicle->vehicle_number,
                        'vehicle_type' => $complaint->booking->vehicle->vehicle_type,
                        'weight_capacity' => $complaint->booking->vehicle->weight_capacity,
                        'vehicle_image' => $complaint->booking->vehicle->vehicle_image,
                        'provider' => $complaint->booking->vehicle->user ? [
                            'id' => $complaint->booking->vehicle->user->id,
                            'name' => $complaint->booking->vehicle->user->name,
                            'email' => $complaint->booking->vehicle->user->email,
                            'mobile' => $complaint->booking->vehicle->user->mobile,
                            'cnic' => $complaint->booking->vehicle->user->cnic
                        ] : null
                    ] : null,
                    'customer' => $complaint->booking->customer ? [
                        'id' => $complaint->booking->customer->id,
                        'name' => $complaint->booking->customer->name,
                        'email' => $complaint->booking->customer->email,
                        'mobile' => $complaint->booking->customer->mobile,
                        'cnic' => $complaint->booking->customer->cnic
                    ] : null
                ] : null
            ];
        }
        
        return response()->json([
            'success' => true,
            'complaints' => $formattedComplaints,
            'current_page' => $complaints->currentPage(),
            'last_page' => $complaints->lastPage(),
            'total' => $complaints->total(),
            'per_page' => $complaints->perPage(),
            'pending_count' => Complaint::where('status', 'pending')->count()
        ]);
    }
    
    /**
     * Get single complaint details for modal
     */
    public function getComplaintDetails($id)
    {
        $complaint = Complaint::with(['booking.customer', 'booking.vehicle.user', 'customer', 'provider'])
            ->find($id);
        
        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
            ], 404);
        }
        
        $response = [
            'id' => $complaint->id,
            'booking_id' => $complaint->booking_id,
            'complaint_type' => $complaint->complaint_type,
            'subject' => $complaint->subject,
            'description' => $complaint->description,
            'status' => $complaint->status,
            'status_text' => $complaint->status_text,
            'admin_response' => $complaint->admin_response,
            'created_at' => $complaint->created_at ? $complaint->created_at->format('M d, Y h:i A') : null,
            'resolved_at' => $complaint->resolved_at ? $complaint->resolved_at->format('M d, Y h:i A') : null,
            
            // Customer info
            'customer' => $complaint->customer ? [
                'id' => $complaint->customer->id,
                'name' => $complaint->customer->name,
                'email' => $complaint->customer->email,
                'mobile' => $complaint->customer->mobile,
                'cnic' => $complaint->customer->cnic
            ] : ($complaint->booking?->customer ? [
                'id' => $complaint->booking->customer->id,
                'name' => $complaint->booking->customer->name,
                'email' => $complaint->booking->customer->email,
                'mobile' => $complaint->booking->customer->mobile,
                'cnic' => $complaint->booking->customer->cnic
            ] : null),
            
            // Provider info
            'provider' => $complaint->provider ? [
                'id' => $complaint->provider->id,
                'name' => $complaint->provider->name,
                'email' => $complaint->provider->email,
                'mobile' => $complaint->provider->mobile,
                'cnic' => $complaint->provider->cnic
            ] : ($complaint->booking?->vehicle?->user ? [
                'id' => $complaint->booking->vehicle->user->id,
                'name' => $complaint->booking->vehicle->user->name,
                'email' => $complaint->booking->vehicle->user->email,
                'mobile' => $complaint->booking->vehicle->user->mobile,
                'cnic' => $complaint->booking->vehicle->user->cnic
            ] : null),
            
            // Booking details
            'booking' => $complaint->booking ? [
                'id' => $complaint->booking->id,
                'pickup_location' => $complaint->booking->pickup_location,
                'dropoff_location' => $complaint->booking->dropoff_location,
                'pickup_lat' => $complaint->booking->pickup_lat,
                'pickup_lng' => $complaint->booking->pickup_lng,
                'dropoff_lat' => $complaint->booking->dropoff_lat,
                'dropoff_lng' => $complaint->booking->dropoff_lng,
                'pickup_time' => $complaint->booking->pickup_time,
                'booking_date' => $complaint->booking->booking_date ? $complaint->booking->booking_date->format('M d, Y') : null,
                'goods_type' => $complaint->booking->goods_type,
                'goods_weight' => $complaint->booking->goods_weight,
                'special_instructions' => $complaint->booking->special_instructions,
                'estimated_distance' => $complaint->booking->estimated_distance,
                'estimated_fare' => $complaint->booking->estimated_fare,
                'actual_distance' => $complaint->booking->actual_distance,
                'actual_fare' => $complaint->booking->actual_fare,
                'payment_method' => $complaint->booking->payment_method,
                'payment_status' => $complaint->booking->payment_status,
                'status' => $complaint->booking->status,
                'status_text' => $complaint->booking->status_text,
                'delivery_status' => $complaint->booking->delivery_status,
                'delivery_status_text' => $complaint->booking->delivery_status_text,
                'rejection_reason' => $complaint->booking->rejection_reason,
                'created_at' => $complaint->booking->created_at ? $complaint->booking->created_at->format('M d, Y h:i A') : null,
                'accepted_at' => $complaint->booking->accepted_at ? $complaint->booking->accepted_at->format('M d, Y h:i A') : null,
                'delivered_at' => $complaint->booking->delivered_at ? $complaint->booking->delivered_at->format('M d, Y h:i A') : null,
                'vehicle' => $complaint->booking->vehicle ? [
                    'id' => $complaint->booking->vehicle->id,
                    'vehicle_number' => $complaint->booking->vehicle->vehicle_number,
                    'vehicle_type' => $complaint->booking->vehicle->vehicle_type,
                    'weight_capacity' => $complaint->booking->vehicle->weight_capacity,
                    'vehicle_image' => $complaint->booking->vehicle->vehicle_image,
                    'provider' => $complaint->booking->vehicle->user ? [
                        'name' => $complaint->booking->vehicle->user->name,
                        'email' => $complaint->booking->vehicle->user->email,
                        'mobile' => $complaint->booking->vehicle->user->mobile,
                        'cnic' => $complaint->booking->vehicle->user->cnic
                    ] : null
                ] : null
            ] : null
        ];
        
        return response()->json([
            'success' => true,
            'complaint' => $response
        ]);
    }
    
    /**
     * Resolve a complaint and notify customer
     */
    public function resolveComplaint(Request $request, $id)
    {
        $complaint = Complaint::find($id);
        
        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
            ], 404);
        }
        
        $request->validate([
            'admin_response' => 'required|string|min:10'
        ]);
        
        // Mark complaint as resolved
        $complaint->markResolved($request->admin_response);
        
        // Get customer email
        $customerEmail = $complaint->customer?->email ?? $complaint->booking?->customer?->email;
        
        // Send email to customer about resolution
        if ($customerEmail) {
            $this->sendCustomerResolutionEmail($complaint, $customerEmail, $request->admin_response);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Complaint resolved successfully. Email sent to customer.'
        ]);
    }
    
    /**
     * Send email notification to vehicle owner about complaint
     */
    public function notifyVehicleOwner($id)
    {
        $complaint = Complaint::with(['booking.vehicle.user'])->find($id);
        
        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
            ], 404);
        }
        
        $vehicleOwner = $complaint->booking?->vehicle?->user;
        
        if (!$vehicleOwner || !$vehicleOwner->email) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle owner email not found'
            ], 404);
        }
        
        $this->sendVehicleOwnerNotificationEmail($complaint, $vehicleOwner->email);
        
        return response()->json([
            'success' => true,
            'message' => 'Email sent to vehicle owner successfully'
        ]);
    }
    
    /**
     * Send email to customer about complaint resolution
     */
    public function notifyCustomerResolved($id)
    {
        $complaint = Complaint::find($id);
        
        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
            ], 404);
        }
        
        if ($complaint->status !== 'resolved') {
            return response()->json([
                'success' => false,
                'message' => 'Complaint is not resolved yet. Please resolve first.'
            ]);
        }
        
        $customerEmail = $complaint->customer?->email ?? $complaint->booking?->customer?->email;
        
        if (!$customerEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Customer email not found'
            ], 404);
        }
        
        $this->sendCustomerResolutionEmail($complaint, $customerEmail, $complaint->admin_response);
        
        return response()->json([
            'success' => true,
            'message' => 'Email resent to customer successfully'
        ]);
    }
    
    /**
     * Send email to vehicle owner about complaint
     */
    private function sendVehicleOwnerNotificationEmail($complaint, $ownerEmail)
    {
        try {
            $data = [
                'complaint' => $complaint,
                'booking' => $complaint->booking,
                'customer' => $complaint->customer ?? $complaint->booking?->customer,
                'subject' => "Complaint Filed Against Your Vehicle - Booking #{$complaint->booking_id}"
            ];
            
            Mail::send('emails.vehicle_owner_complaint', $data, function ($message) use ($ownerEmail, $data) {
                $message->to($ownerEmail)
                        ->subject($data['subject'])
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });
            
            Log::info("Complaint notification email sent to vehicle owner: {$ownerEmail}", [
                'complaint_id' => $complaint->id,
                'booking_id' => $complaint->booking_id
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send vehicle owner complaint email: " . $e->getMessage());
        }
    }
    
    /**
     * Send email to customer about complaint resolution
     */
    private function sendCustomerResolutionEmail($complaint, $customerEmail, $adminResponse)
    {
        try {
            $data = [
                'complaint' => $complaint,
                'booking' => $complaint->booking,
                'admin_response' => $adminResponse,
                'subject' => "Your Complaint Has Been Resolved - Booking #{$complaint->booking_id}"
            ];
            
            Mail::send('emails.customer_complaint_resolved', $data, function ($message) use ($customerEmail, $data) {
                $message->to($customerEmail)
                        ->subject($data['subject'])
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });
            
            Log::info("Complaint resolution email sent to customer: {$customerEmail}", [
                'complaint_id' => $complaint->id,
                'booking_id' => $complaint->booking_id
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send customer complaint resolution email: " . $e->getMessage());
        }
    }














































    // list of users in admin dashboard
 
// Add these methods to your existing AdminController

/**
   */
public function seeUsers()
{
    // Get all customers (role = 'customer')
    $customers = Userr::where('role', 'customer')
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'customers_page');
    
    // Get all providers
    $providers = Userr::where('role', 'provider')
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'providers_page');
    
    // Load vehicles separately for each provider
    foreach ($providers as $provider) {
        $provider->vehicles = Vehicle::where('user_id', $provider->id)
            ->with('bookings')
            ->get();
    }
    
    // Statistics
    $totalCustomers = Userr::where('role', 'customer')->count();
    $totalProviders = Userr::where('role', 'provider')->count();
    // $totalVehicles = Vehicle::count();
    $totalVehicles = Vehicle::whereIn('status', ['pending', 'approved'])->count();
    $bookedVehicles = Vehicle::where('is_booked', 'yes')->count();
    $availableVehicles = Vehicle::where('status', 'approved')
                            ->where('is_booked', 'no')
                            ->count();
    $verifiedVehicles = Vehicle::where('status', 'approved')->count();
    $pendingVehicles = Vehicle::where('status', 'pending')->count();
    
    return view('admin.userlist', compact(
        'customers',
        'providers',
        'totalCustomers',
        'totalProviders',
        'totalVehicles',
        'bookedVehicles',
        'availableVehicles',
        'verifiedVehicles',
        'pendingVehicles'
    ));
}
public function getCustomerDetails($id)
{
    $customer = Userr::with(['bookings.vehicle'])->find($id);
    
    if (!$customer) {
        return response()->json(['success' => false, 'message' => 'Customer not found']);
    }
    
    return response()->json([
        'success' => true,
        'customer' => $customer
    ]);
}

public function getVehicleDetails($id)
{
    $vehicle = Vehicle::with(['user', 'bookings.customer'])->find($id);
    
    if (!$vehicle) {
        return response()->json(['success' => false, 'message' => 'Vehicle not found']);
    }
    
    return response()->json([
        'success' => true,
        'vehicle' => $vehicle
    ]);
}

public function deleteUser($id)
{
    $user = Userr::find($id);
    
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found']);
    }
    
    // Delete related data
    if ($user->role == 'provider') {
        // Delete all vehicles of this provider
        foreach ($user->vehicles as $vehicle) {
            $vehicle->bookings()->delete();
            $vehicle->delete();
        }
    } else {
        // Delete customer's bookings
        $user->bookings()->delete();
    }
    
    $user->delete();
    
    return response()->json([
        'success' => true,
        'message' => 'User deleted successfully'
    ]);
}





























// admin see all booking

// Add these methods to your UserrController.php

/**
 * Admin See Bookings Page
 */

    public function adminSeeBookings()
    {
        // Check if admin is logged in - FIXED: Check for admin login
        if (!session('admin_logged_in') && session('role') != 'admin') {
            // For testing, allow access - REMOVE IN PRODUCTION
            // return redirect()->route('login')->with('error', 'Access denied. Admin only.');
        }
        
        $adminName = session('admin_name') ?? 'Admin';
        
        // Get stats for the page
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'request')->count();
        $acceptedBookings = Booking::where('status', 'accept')->count();
        $completedBookings = Booking::where('status', 'complete')->count();
        $rejectedBookings = Booking::where('status', 'reject')->count();
        
        return view('admin.allbooking', [
            'adminName' => $adminName,
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'acceptedBookings' => $acceptedBookings,
            'completedBookings' => $completedBookings,
            'rejectedBookings' => $rejectedBookings
        ]);
    }

    /**
     * Get Admin Bookings Data (AJAX)
     */
    public function getAdminBookingsData(Request $request)
    {
        try {
            // FIXED: Make authentication optional for debugging
            // Remove this check or modify based on your session setup
            // if (session('role') != 'admin' && !session('admin_logged_in')) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Not authenticated as admin'
            //     ], 401);
            // }
            
            $perPage = $request->get('per_page', 6);
            $page = $request->get('page', 1);
            $filter = $request->get('filter', 'all');
            $search = $request->get('search', '');
            
            // Get bookings with all relations
            $bookingsQuery = Booking::with(['customer', 'vehicle.user']);
            
            // Apply filter
            if ($filter !== 'all') {
                $bookingsQuery->where('status', $filter);
            }
            
            // Apply search if provided
            if (!empty($search)) {
                $bookingsQuery->where(function($q) use ($search) {
                    $q->where('pickup_location', 'like', "%{$search}%")
                      ->orWhere('dropoff_location', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('vehicle.user', function($vq) use ($search) {
                          $vq->where('name', 'like', "%{$search}%");
                      });
                });
            }
            
            $bookingsQuery->orderBy('created_at', 'desc');
            
            // Paginate results
            $bookings = $bookingsQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Format bookings for response
            $formattedBookings = [];
            foreach ($bookings as $booking) {
                $badgeClass = 'bg-secondary';
                $statusText = $booking->status_text ?? $this->getStatusText($booking->status);
                
                if ($booking->status === 'request') {
                    $badgeClass = 'bg-warning';
                } elseif ($booking->status === 'accept') {
                    $badgeClass = 'bg-success';
                } elseif ($booking->status === 'reject') {
                    $badgeClass = 'bg-danger';
                } elseif ($booking->status === 'complete') {
                    $badgeClass = 'bg-info';
                }
                
                $formattedBookings[] = [
                    'id' => $booking->id,
                    'pickup_location' => $booking->pickup_location ?? 'N/A',
                    'dropoff_location' => $booking->dropoff_location ?? 'N/A',
                    'goods_type' => $booking->goods_type ?? 'N/A',
                    'goods_weight' => $booking->goods_weight ?? 0,
                    'vehicle_type' => $booking->vehicle ? $booking->vehicle->vehicle_type : 'N/A',
                    'vehicle_number' => $booking->vehicle ? $booking->vehicle->vehicle_number : 'N/A',
                    'customer_name' => $booking->customer ? $booking->customer->name : 'N/A',
                    'provider_name' => ($booking->vehicle && $booking->vehicle->user) ? $booking->vehicle->user->name : 'N/A',
                    'booking_date' => $booking->booking_date ? date('Y-m-d', strtotime($booking->booking_date)) : date('Y-m-d', strtotime($booking->created_at)),
                    'status' => $booking->status,
                    'status_text' => $statusText,
                    'badge_class' => $badgeClass,
                    'estimated_fare' => $booking->estimated_fare ?? 0,
                    'created_at' => $booking->created_at ? date('Y-m-d H:i:s', strtotime($booking->created_at)) : null,
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
            
        } catch (\Exception $e) {
            Log::error('Error in getAdminBookingsData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Admin Booking Details (AJAX)
     */
    public function getAdminBookingDetails($id)
    {
        try {
            // Remove or comment authentication check for debugging
            // if (session('role') != 'admin' && !session('admin_logged_in')) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Not authenticated as admin'
            //     ], 401);
            // }
            
            // Get booking with all relations
            $booking = Booking::with(['vehicle.user', 'customer'])
                ->where('id', $id)
                ->first();
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }
            
            // Format dates safely
            $bookingDate = $booking->booking_date ? date('F d, Y', strtotime($booking->booking_date)) : null;
            $createdDate = $booking->created_at ? date('F d, Y h:i A', strtotime($booking->created_at)) : null;
            $acceptedDate = $booking->accepted_at ? date('F d, Y h:i A', strtotime($booking->accepted_at)) : null;
            $rejectedDate = $booking->rejected_at ? date('F d, Y h:i A', strtotime($booking->rejected_at)) : null;
            $deliveredDate = $booking->delivered_at ? date('F d, Y h:i A', strtotime($booking->delivered_at)) : null;
            
            // Prepare response with all details
            $response = [
                'id' => $booking->id,
                'status' => $booking->status,
                'status_text' => $booking->status_text ?? $this->getStatusText($booking->status),
                'booking_date' => $bookingDate,
                'created_at' => $createdDate,
                'accepted_at' => $acceptedDate,
                'rejected_at' => $rejectedDate,
                'delivered_at' => $deliveredDate,
                
                // Location details
                'pickup_location' => $booking->pickup_location ?? '',
                'pickup_lat' => $booking->pickup_lat ?? 0,
                'pickup_lng' => $booking->pickup_lng ?? 0,
                'dropoff_location' => $booking->dropoff_location ?? '',
                'dropoff_lat' => $booking->dropoff_lat ?? 0,
                'dropoff_lng' => $booking->dropoff_lng ?? 0,
                'pickup_time' => $booking->pickup_time ?? '',
                
                // Goods details
                'goods_type' => $booking->goods_type ?? '',
                'goods_weight' => $booking->goods_weight ?? 0,
                'special_instructions' => $booking->special_instructions ?? '',
                
                // Fare details
                'estimated_distance' => $booking->estimated_distance ?? 0,
                'estimated_fare' => $booking->estimated_fare ?? 0,
                'actual_distance' => $booking->actual_distance ?? 0,
                'actual_fare' => $booking->actual_fare ?? 0,
                
                // Payment details
                'payment_method' => $booking->payment_method ?? '',
                'payment_status' => $booking->payment_status ?? 'pending',
                
                // Route details
                'route_polyline' => $booking->route_polyline ?? null,
                'route_directions' => $booking->route_directions ?? null,
                'selected_route_name' => $booking->selected_route_name ?? '',
                'has_tolls' => $booking->has_tolls ?? false,
                'toll_cost' => $booking->toll_cost ?? 0,
                
                // Rejection reason
                'rejection_reason' => $booking->rejection_reason ?? '',
                
                // Customer details
                'customer' => null,
                
                // Provider/Owner details
                'provider' => null,
                
                // Vehicle details
                'vehicle' => null
            ];
            
            // Add customer details
            if ($booking->customer) {
                $response['customer'] = [
                    'id' => $booking->customer->id,
                    'name' => $booking->customer->name ?? 'N/A',
                    'email' => $booking->customer->email ?? 'N/A',
                    'mobile' => $booking->customer->mobile ?? 'N/A',
                    'cnic' => $booking->customer->cnic ?? 'N/A',
                    'role' => $booking->customer->role ?? 'customer',
                    'profile_image' => $booking->customer->profile_image ?? 'https://randomuser.me/api/portraits/men/32.jpg',
                ];
            }
            
            // Add vehicle and provider details
            if ($booking->vehicle) {
                $response['vehicle'] = [
                    'id' => $booking->vehicle->id,
                    'vehicle_number' => $booking->vehicle->vehicle_number ?? 'N/A',
                    'chassis_number' => $booking->vehicle->chassis_number ?? 'N/A',
                    'vehicle_type' => $booking->vehicle->vehicle_type ?? 'N/A',
                    'weight_capacity' => $booking->vehicle->weight_capacity ?? 0,
                    'can_carry' => $booking->vehicle->can_carry ?? 'N/A',
                    'status' => $booking->vehicle->status ?? 'N/A',
                    'is_booked' => $booking->vehicle->is_booked ?? 'no',
                    'vehicle_image' => $booking->vehicle->vehicle_image ?? null,
                ];
                
                if ($booking->vehicle->user) {
                    $response['provider'] = [
                        'id' => $booking->vehicle->user->id,
                        'name' => $booking->vehicle->user->name ?? 'N/A',
                        'email' => $booking->vehicle->user->email ?? 'N/A',
                        'mobile' => $booking->vehicle->user->mobile ?? 'N/A',
                        'cnic' => $booking->vehicle->user->cnic ?? 'N/A',
                        'role' => $booking->vehicle->user->role ?? 'provider',
                        'profile_image' => $booking->vehicle->user->profile_image ?? 'https://randomuser.me/api/portraits/men/32.jpg',
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'booking' => $response
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getAdminBookingDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to get status text
     */
    private function getStatusText($status)
    {
        $texts = [
            'request' => 'Pending',
            'accept' => 'Accepted',
            'reject' => 'Rejected',
            'complete' => 'Completed'
        ];
        return $texts[$status] ?? ucfirst($status);
    }

    /**
     * Get Admin Booking Tracking/Timeline
     */
    public function getAdminBookingTracking($id)
    {
        try {
            $booking = Booking::with(['vehicle.user', 'customer'])
                ->where('id', $id)
                ->first();
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }
            
            // Build comprehensive timeline based on status and timestamps
            $timeline = [];
            $progressPercentage = 0;
            
            // Timeline item: Booking Created
            if ($booking->created_at) {
                $timeline[] = [
                    'status' => 'completed',
                    'title' => 'Booking Created',
                    'description' => 'Customer created a booking request.',
                    'timestamp' => date('M d, Y h:i A', strtotime($booking->created_at)),
                    'icon' => 'fa-calendar-plus'
                ];
            }
            
            // Timeline item: Booking Request (Pending)
            if ($booking->status === 'request') {
                $progressPercentage = 15;
                $timeline[] = [
                    'status' => 'active',
                    'title' => 'Awaiting Provider Response',
                    'description' => 'Booking request is pending. Waiting for provider to accept or reject.',
                    'timestamp' => null,
                    'icon' => 'fa-clock'
                ];
            } 
            // Timeline item: Booking Accepted
            elseif ($booking->accepted_at) {
                $progressPercentage = 40;
                $timeline[] = [
                    'status' => 'completed',
                    'title' => 'Booking Accepted',
                    'description' => 'Provider accepted the booking request.',
                    'timestamp' => date('M d, Y h:i A', strtotime($booking->accepted_at)),
                    'icon' => 'fa-check-circle'
                ];
                
                // Timeline item: Order Confirmed
                $timeline[] = [
                    'status' => 'completed',
                    'title' => 'Order Confirmed',
                    'description' => 'Booking has been confirmed by the provider.',
                    'timestamp' => date('M d, Y h:i A', strtotime($booking->accepted_at)),
                    'icon' => 'fa-clipboard-check'
                ];
                
                // Timeline item: Vehicle Dispatched
                if ($booking->dispatched_at) {
                    $progressPercentage = 60;
                    $timeline[] = [
                        'status' => 'completed',
                        'title' => 'Vehicle Dispatched',
                        'description' => 'Vehicle has been dispatched from source location.',
                        'timestamp' => date('M d, Y h:i A', strtotime($booking->dispatched_at)),
                        'icon' => 'fa-truck'
                    ];
                } else {
                    $timeline[] = [
                        'status' => 'pending',
                        'title' => 'Awaiting Dispatch',
                        'description' => 'Vehicle dispatch pending.',
                        'timestamp' => null,
                        'icon' => 'fa-hourglass-half'
                    ];
                }
                
                // Timeline item: In Transit
                if ($booking->in_transit_at) {
                    $progressPercentage = 80;
                    $timeline[] = [
                        'status' => 'completed',
                        'title' => 'In Transit',
                        'description' => 'Vehicle is en route to destination.',
                        'timestamp' => date('M d, Y h:i A', strtotime($booking->in_transit_at)),
                        'icon' => 'fa-road'
                    ];
                } elseif ($booking->dispatched_at) {
                    $timeline[] = [
                        'status' => 'active',
                        'title' => 'In Transit',
                        'description' => 'Vehicle is on the way.',
                        'timestamp' => null,
                        'icon' => 'fa-truck-moving'
                    ];
                }
                
                // Timeline item: Delivered
                if ($booking->delivered_at) {
                    $progressPercentage = 100;
                    $timeline[] = [
                        'status' => 'completed',
                        'title' => 'Delivered',
                        'description' => 'Goods have been delivered successfully.',
                        'timestamp' => date('M d, Y h:i A', strtotime($booking->delivered_at)),
                        'icon' => 'fa-flag-checkered'
                    ];
                } elseif ($booking->status === 'accept') {
                    $timeline[] = [
                        'status' => 'pending',
                        'title' => 'Awaiting Delivery',
                        'description' => 'Delivery in progress.',
                        'timestamp' => null,
                        'icon' => 'fa-truck-fast'
                    ];
                }
            } 
            // Timeline item: Booking Rejected
            elseif ($booking->rejected_at) {
                $progressPercentage = 15;
                $timeline[] = [
                    'status' => 'completed',
                    'title' => 'Booking Rejected',
                    'description' => $booking->rejection_reason ?? 'Provider rejected this booking request.',
                    'timestamp' => date('M d, Y h:i A', strtotime($booking->rejected_at)),
                    'icon' => 'fa-times-circle'
                ];
            }
            
            // Timeline item: Completed
            if ($booking->status === 'complete' && $booking->delivered_at) {
                $progressPercentage = 100;
                $hasDelivered = false;
                foreach ($timeline as $item) {
                    if ($item['title'] === 'Delivered') {
                        $hasDelivered = true;
                        break;
                    }
                }
                if (!$hasDelivered) {
                    $timeline[] = [
                        'status' => 'completed',
                        'title' => 'Booking Completed',
                        'description' => 'Booking has been successfully completed.',
                        'timestamp' => date('M d, Y h:i A', strtotime($booking->delivered_at)),
                        'icon' => 'fa-check-double'
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'booking' => [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'status_text' => $booking->status_text ?? $this->getStatusText($booking->status),
                    'pickup_location' => $booking->pickup_location ?? 'N/A',
                    'dropoff_location' => $booking->dropoff_location ?? 'N/A',
                    'customer_name' => $booking->customer ? $booking->customer->name : 'N/A',
                    'provider_name' => ($booking->vehicle && $booking->vehicle->user) ? $booking->vehicle->user->name : 'N/A',
                    'progress_percentage' => $progressPercentage,
                    'timeline' => $timeline
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getAdminBookingTracking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Admin Booking Reviews
     */
    public function getAdminBookingReviews($id)
    {
        try {
            $booking = Booking::find($id);
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }
            
            $reviews = $booking->reviews()->with('customer')->get();
            
            $formattedReviews = [];
            foreach ($reviews as $review) {
                $formattedReviews[] = [
                    'id' => $review->id,
                    'rating' => $review->rating ?? 0,
                    'rating_stars' => $review->rating_stars ?? '',
                    'review' => $review->review ?? '',
                    'is_approved' => $review->is_approved ?? true,
                    'customer' => [
                        'id' => $review->customer ? $review->customer->id : null,
                        'name' => $review->customer ? $review->customer->name : 'Unknown',
                        'email' => $review->customer ? $review->customer->email : 'N/A',
                        'profile_image' => ($review->customer && $review->customer->profile_image) ? $review->customer->profile_image : 'https://randomuser.me/api/portraits/men/32.jpg',
                    ],
                    'created_at' => $review->created_at ? date('F d, Y h:i A', strtotime($review->created_at)) : null,
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $formattedReviews,
                'total' => count($formattedReviews)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getAdminBookingReviews: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Admin Booking Complaints
     */
    public function getAdminBookingComplaints($id)
    {
        try {
            $booking = Booking::find($id);
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }
            
            $complaints = $booking->complaints()->with(['customer', 'provider'])->get();
            
            $formattedComplaints = [];
            foreach ($complaints as $complaint) {
                $statusBadge = 'bg-warning';
                $statusText = 'Pending';
                
                if ($complaint->status === 'resolved') {
                    $statusBadge = 'bg-success';
                    $statusText = 'Resolved';
                } elseif ($complaint->status === 'in_review') {
                    $statusBadge = 'bg-info';
                    $statusText = 'Under Review';
                } elseif ($complaint->status === 'rejected') {
                    $statusBadge = 'bg-danger';
                    $statusText = 'Rejected';
                }
                
                $formattedComplaints[] = [
                    'id' => $complaint->id,
                    'subject' => $complaint->subject ?? '',
                    'complaint_type' => $complaint->complaint_type ?? '',
                    'description' => $complaint->description ?? '',
                    'status' => $complaint->status ?? 'pending',
                    'status_text' => $statusText,
                    'status_badge' => $statusBadge,
                    'admin_response' => $complaint->admin_response ?? null,
                    'customer' => $complaint->customer ? [
                        'id' => $complaint->customer->id,
                        'name' => $complaint->customer->name,
                        'email' => $complaint->customer->email,
                    ] : null,
                    'provider' => $complaint->provider ? [
                        'id' => $complaint->provider->id,
                        'name' => $complaint->provider->name,
                        'email' => $complaint->provider->email,
                    ] : null,
                    'created_at' => $complaint->created_at ? date('F d, Y h:i A', strtotime($complaint->created_at)) : null,
                    'resolved_at' => $complaint->resolved_at ? date('F d, Y h:i A', strtotime($complaint->resolved_at)) : null,
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $formattedComplaints,
                'total' => count($formattedComplaints)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getAdminBookingComplaints: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Admin Booking Payment Details
     */
    public function getAdminBookingPayment($id)
    {
        try {
            $booking = Booking::find($id);
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }
            
            // Get payment record if exists
            $payment = Payment::where('booking_id', $id)->first();
            
            $paymentData = null;
            if ($payment) {
                $paymentData = [
                    'id' => $payment->id,
                    'payment_method' => $payment->payment_method,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'status_badge' => $this->getPaymentStatusBadge($payment->status),
                    'transaction_id' => $payment->transaction_id,
                    'sandbox_mode' => $payment->sandbox_mode,
                    'card_number_masked' => $payment->card_number_masked,
                    'paid_at' => $payment->paid_at ? date('F d, Y h:i A', strtotime($payment->paid_at)) : null,
                    'created_at' => $payment->created_at ? date('F d, Y h:i A', strtotime($payment->created_at)) : null,
                ];
            }
            
            // Calculate payment breakdown
            $estimatedFare = $booking->estimated_fare ?? 0;
            $actualFare = $booking->actual_fare ?? 0;
            $penaltyAmount = $booking->penalty_amount ?? 0;
            $tollCost = $booking->toll_cost ?? 0;
            
            $paymentBreakdown = [
                'estimated_fare' => $estimatedFare,
                'actual_fare' => $actualFare,
                'penalty_amount' => $penaltyAmount,
                'toll_cost' => $tollCost,
                'net_payable' => $actualFare,
                'difference' => $actualFare - $estimatedFare,
            ];
            
            $paymentStatusBadge = 'bg-warning';
            if ($booking->payment_status === 'paid') {
                $paymentStatusBadge = 'bg-success';
            } elseif ($booking->payment_status === 'failed') {
                $paymentStatusBadge = 'bg-danger';
            }
            
            return response()->json([
                'success' => true,
                'booking' => [
                    'id' => $booking->id,
                    'estimated_fare' => $estimatedFare,
                    'actual_fare' => $actualFare,
                    'payment_method' => $booking->payment_method,
                    'payment_status' => $booking->payment_status ?? 'pending',
                    'payment_status_badge' => $paymentStatusBadge,
                ],
                'payment' => $paymentData,
                'payment_breakdown' => $paymentBreakdown
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getAdminBookingPayment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment status badge class
     */
    private function getPaymentStatusBadge($status)
    {
        $badges = [
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'completed' => 'bg-success',
            'failed' => 'bg-danger',
            'refunded' => 'bg-secondary'
        ];
        
        return $badges[$status] ?? 'bg-secondary';
    }
}