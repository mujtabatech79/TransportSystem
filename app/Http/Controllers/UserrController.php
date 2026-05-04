<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Log; 

use App\Models\Complaint;
use App\Models\Userr;
use App\Models\Vehicle;
use App\Models\Booking;
use Illuminate\Http\Request;
use Hash;
use Auth;
class UserrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('userregister');
    }

    public function login()
    {
        return view('Auth.login');
    }





     public function adminlogin()
{
    $totalUsers = Userr::count();
    
    // Verified Vehicles Count (status = 'approved')
    $verifiedVehicles = Vehicle::where('status', 'approved')->count();
    
    // Active Bookings Count (status = 'booked' OR status = 'accept' or 'request')
    $activeBookings = Booking::whereIn('status', ['request', 'accept', 'booked'])->count();
    
    // Pending Vehicles (status = 'pending')
    $pendingVehicles = Vehicle::where('status', 'pending')->with('user')->get();
    $pendingVehiclesCount = $pendingVehicles->count();
    
    // Pending Complaints (status = 'pending' or 'in_review')
    $pendingComplaints = Complaint::whereIn('status', ['pending', 'in_review'])->count();
    
    // Recent Complaints (last 5)
    $recentComplaints = Complaint::with('customer')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    // Bookings with relations
    $vehicles = Booking::with(['customer', 'vehicle.user'])->orderBy('created_at', 'desc')->limit(10)->get();
    
    // Providers for add vehicle modal
    $providers = Userr::where('role', 'provider')->get();
    
    return view('dashbord.admin', compact(
        'vehicles',
        'totalUsers',
        'verifiedVehicles',
        'activeBookings',
        'pendingVehicles',
        'pendingVehiclesCount',
        'pendingComplaints',
        'recentComplaints',
        'providers'
    ));
}




// Get pending verifications (vehicles with status = 'pending')
public function getPendingVerifications()
{
    $pendingVehicles = Vehicle::where('status', 'pending')
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($vehicle) {
            return [
                'id' => $vehicle->id,
                'vehicle_number' => $vehicle->vehicle_number,
                'vehicle_type' => $vehicle->vehicle_type,
                'owner_name' => $vehicle->user->name ?? 'N/A',
                'created_at' => $vehicle->created_at->diffForHumans(),
                'can_carry' => $vehicle->can_carry,
                'weight_capacity' => $vehicle->weight_capacity
            ];
        });
    
    return response()->json([
        'success' => true,
        'vehicles' => $pendingVehicles,
        'count' => $pendingVehicles->count()
    ]);
}

// Get recent complaints (status = 'pending' or 'in_review')
public function getRecentComplaints()
{
    $recentComplaints = Complaint::whereIn('status', ['pending', 'in_review'])
        ->with(['customer', 'provider', 'booking'])
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get()
        ->map(function($complaint) {
            // Determine priority based on complaint_type
            $priority = 'medium';
            $highPriorityTypes = ['fraud', 'scam', 'damage', 'accident', 'safety'];
            $lowPriorityTypes = ['general', 'feedback', 'suggestion', 'inquiry'];
            
            $typeLower = strtolower($complaint->complaint_type ?? '');
            if (in_array($typeLower, $highPriorityTypes)) {
                $priority = 'high';
            } elseif (in_array($typeLower, $lowPriorityTypes)) {
                $priority = 'low';
            }
            
            return [
                'id' => $complaint->id,
                'complaint_type' => $complaint->complaint_type ?? 'General',
                'subject' => $complaint->subject ?? 'No subject',
                'description' => $complaint->description ?? '',
                'status' => $complaint->status,
                'status_label' => $complaint->getStatusTextAttribute(),
                'customer_name' => $complaint->customer->name ?? 'N/A',
                'provider_name' => $complaint->provider->name ?? 'N/A',
                'created_at' => $complaint->created_at->diffForHumans(),
                'priority' => $priority
            ];
        });
    
    $pendingCount = Complaint::where('status', 'pending')->count();
    
    return response()->json([
        'success' => true,
        'complaints' => $recentComplaints,
        'pending_count' => $pendingCount,
        'total_count' => $recentComplaints->count()
    ]);
}


































    


//before

      public function providerrlogin()
    {


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






        return view('dashbord.provider',compact('totalBookings'));
    }








public function providerlogin()
{
    $providerId = session('user_id');

    if (!$providerId || session('role') != 'provider') {
        return redirect()->route('login')->with('error', 'Access denied.');
    }

    // Provider ki vehicles nikal lo
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');

    // Un vehicles ki bookings nikal lo sath customer ka data bhi
    $bookings = Booking::whereIn('vehicle_id', $vehicleIds)
                ->with(['customer', 'vehicle'])
                ->latest() // Recent bookings first
                ->limit(5) // Only recent 5 bookings
                ->get();

    $totalBookings = $bookings->count();
    // Completed bookings count (jahan is_booking_complete = 'yes')
    $completedBookings = Booking::whereIn('vehicle_id', $vehicleIds)
                                ->where('is_booking_complete', 'yes')
                                ->count();
    
    // Pending requests count (jahan request_status = 'pending')
    $pendingRequest = Booking::whereIn('vehicle_id', $vehicleIds)
                             ->where('request_status', 'pending')
                             ->count();
    
    // Rejected requests count (jahan request_status = 'rejected')
    $rejectedRequest = Booking::whereIn('vehicle_id', $vehicleIds)
                              ->where('request_status', 'accepted')->where('is_booking_complete','no')
                              ->count();

    // Add this line to pass bookings to view
    return view('dashbord.provider', compact('totalBookings', 'completedBookings', 'pendingRequest', 'rejectedRequest', 'bookings'));

}





































    //   public function customerlogin()
    // {

    //     $customerId = session('user_id');

    // if (!$customerId || session('role') != 'customer') {
    //     return redirect()->route('login')->with('error', 'Access denied.');
    // }

    // // Provider ki vehicles nikal lo
    // $vehicleIds = Vehicle::where('user_id', $customerId)->pluck('id');

    // // Un vehicles ki bookings nikal lo sath customer ka data bhi
    // $bookings = Booking::whereIn('vehicle_id', $vehicleIds)
    //             ->with(['customer', 'vehicle'])
    //             ->get();


    // $totalBookings = $bookings->count();





    //     return view('dashbord.customer',compact('totalBookings'));
    // }









// yeh use ho rha ha

    public function customerlogin()
{
    // Session se user ID lein
    $customerId = session('user_id');
    
    if (!$customerId) {
        return redirect()->route('login')->with('error', 'Please login first');
    }
    
    // Customer ki recent bookings fetch karein (limit 3)
    $recentBookings = Booking::where('customer_id', $customerId)
        ->with(['vehicle', 'vehicle.user'])
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    // Total bookings count
    $totalBookings = Booking::where('customer_id', $customerId)->count();
    
    // Completed bookings count
    
    $completedBookings = Booking::where('customer_id', $customerId)
        ->where('status', 'complete')
        ->count();
    
    // In-progress bookings count (booked status wali)
    $inProgressBookings = Booking::where('customer_id', $customerId)
        ->where('request_status','accepted')->where('is_booking_complete','no')
        ->count();

        
    
    // Total spent calculate karein (agar pricing model ho)
    $reject = Booking::where('customer_id', $customerId)
        ->where('request_status', 'rejected')
        ->count(); // Temporary - agar price column ho to calculate karein
    
    // User ka name session se lein
    $userName = session('name', 'Customer');
    
    return view('dashbord.customer', [
        'recentBookings' => $recentBookings,
        'totalBookings' => $totalBookings,
        'completedBookings' => $completedBookings,
        'inProgressBookings' => $inProgressBookings,
        'reject' => $reject,
        'userName' => $userName
    ]);
}































    


    public function logout(Request $request)
{
    // Session data clear karo
    $request->session()->flush();

    // Wapas login page par redirect karo
    return redirect('/');
}





public function dashboard()
{
    // Pending vehicles
    $pendingVehicles = \App\Models\Vehicle::where('status', 'pending')->get();

    // Approved/available vehicles
    $availableVehicles = \App\Models\Vehicle::where('status', 'approved')->get();


    // All vehicles
    $vehicles = \App\Models\Vehicle::all();

    return view('dashbord.adminn', compact('pendingVehicles', 'availableVehicles', 'vehicles'));
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


    // public function store(Request $request)
    // {
    //      $request->validate([
    //     'name' => 'required|string|max:255',
    //     'email' => 'required|email|unique:users',
    //     'cnic' => 'required|string|max:15',
    //     'role' => 'required|in:provider,customer',
    //     'password' => 'required|string|min:8|confirmed',
    // ]);

    
    // $user = Userr::create([
    //     'name' => $request->name,
    //     'email' => $request->email,
    //     'cnic' => $request->cnic,
    //     'role' => $request->role,
    //     'password' => Hash::make($request->password),
    // ]);

    // return redirect('/login');

    // }

    /**
     * Display the specified resource.
     */
    public function show(Userr $userr)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Userr $userr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Userr $userr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Userr $userr)
    {
        //
    }



    // public function loginn(Request $request)
    // {
    //     $exit=Userr::where('email', $request->email)->first();
    // if($exit){
    //     if(Hash::check($request->password, $exit->password)){
    //         if($exit->role=='admin'){
    //             return redirect()->route('admin.login');
    //         }
    //         else if($exit->role=='provider'){
    //             return redirect()->route('provider.login');
    //             // return redirect()->route('provider.login', ['id' => $user->id]);
    //         }
    //          else if($exit->role=='customer'){
    //             return redirect()->route('customer.login');
    //         }
    //         else{
    //             return redirect()->back();
    //         }
    //     }else{
    //          return redirect()->back();
    //     }
    // }else{
    //      return redirect()->back();
    // }

    // }






    public function loginnn(Request $request)
{
    $user = Userr::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {

        // Save user in session
        session([
            'user_id' => $user->id,
            'role' => $user->role,
            'name' => $user->name,
        ]);

        // Redirect according to role
        if ($user->role == 'admin') {
            return redirect()->route('admin.login');
        } elseif ($user->role == 'provider') {
            return redirect()->route('provider.login');
        } elseif ($user->role == 'customer') {
            return redirect()->route('customer.login');
        } else {
            return redirect()->back()->with('error', 'Invalid role.');
        }

    } else {
        return redirect()->back()->with('error', 'Email ya password ghalat hai.');
    }
}













public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:userrs',
        'cnic' => 'required|string|max:15',
        'role' => 'required|in:provider,customer',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // Create verification token
    $verificationToken = Str::random(60);

    $user = Userr::create([
        'name' => $request->name,
        'email' => $request->email,
        'cnic' => $request->cnic,
        'role' => $request->role,
        'password' => Hash::make($request->password),
        'email_verified' => false, // Default false
        'verification_token' => $verificationToken, // Token store karein
    ]);

    // Send verification email
    try {
        Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationToken));
        
        return redirect('/login')->with([
            'success' => 'Registration successful! Please check your email for verification link.',
            'form_type' => 'register'
        ]);
    } catch (\Exception $e) {
        // If email sending fails, still create user
        return redirect('/login')->with([
            'success' => 'Registration successful! But email verification could not be sent. Please contact support.',
            'form_type' => 'register'
        ]);
    }
}





//  use ho rha
public function loginn(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = Userr::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        
        // Check if email is verified
        if (!$user->email_verified) {
            return redirect()->back()->with([
                'error' => 'Please verify your email first. Check your inbox for verification link.',
                'form_type' => 'login'
            ]);
        }

        // Save user in session
        session([
            'user_id' => $user->id,
            'role' => $user->role,
            'name' => $user->name,
        ]);

        // Redirect according to role
        if ($user->role == 'admin') {
            return redirect()->route('admin.login');
        } elseif ($user->role == 'provider') {
            return redirect()->route('provider.login');
        } elseif ($user->role == 'customer') {
            return redirect()->route('customer.login');
        } else {
            return redirect()->back()->with('error', 'Invalid role.');
        }

    } else {
        return redirect()->back()->with([
            'error' => 'Email ya password ghalat hai.',
            'form_type' => 'login'
        ]);
    }
}








public function verifyEmail($token)
{
    $user = Userr::where('verification_token', $token)->first();

    if (!$user) {
        return redirect('/login')->with([
            'error' => 'Invalid verification link.',
            'form_type' => 'login'
        ]);
    }

    if ($user->email_verified) {
        return redirect('/login')->with([
            'success' => 'Email already verified. You can login now.',
            'form_type' => 'login'
        ]);
    }

    $user->verifyEmail();

    return redirect('/login')->with([
        'success' => 'Email verified successfully! You can now login.',
        'form_type' => 'login'
    ]);
}





public function resendVerification(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:userrs,email',
    ]);

    $user = Userr::where('email', $request->email)->first();

    if ($user->email_verified) {
        return back()->with('error', 'Email already verified.');
    }

    // Generate new token
    $verificationToken = Str::random(60);
    $user->verification_token = $verificationToken;
    $user->save();

    // Resend email
    Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationToken));

    return back()->with('success', 'Verification email sent successfully!');
}


































// for customer booking details in dashboard after clicking on details button in dashboard

public function getBookingDetails($id)
{
    try {
        // Session se user ID lein
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Booking fetch karein with relations
        $booking = Booking::with(['vehicle', 'vehicle.user'])
            ->where('id', $id)
            ->where('customer_id', $customerId) // Ensure booking belongs to this customer
            ->first();
        
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error fetching booking details: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}











// ni use ho rha
public function customerloginn()
{
    // Session se user ID lein
    $customerId = session('user_id');
    
    if (!$customerId) {
        return redirect()->route('login')->with('error', 'Please login first');
    }
    
    // Customer ki saari bookings fetch karein (no limit)
    $allBookings = Booking::where('customer_id', $customerId)
        ->with(['vehicle', 'vehicle.user'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Total bookings count
    $totalBookings = $allBookings->count();
    
    // Completed bookings count
    $completedBookings = $allBookings->where('status', 'complete')->count();
    
    // In-progress bookings count (accept status wali)
    $inProgressBookings = $allBookings->where('status', 'accept')->count();
    
    // Total spent calculate karein (agar actual_fare ho to)
    $totalSpent = $allBookings->where('status', 'complete')->sum('actual_fare');
    
    // User ka name session se lein
    $userName = session('name', 'Customer');
    
    return view('dashbord.customer', [
        'allBookings' => $allBookings,
        'totalBookings' => $totalBookings,
        'completedBookings' => $completedBookings,
        'inProgressBookings' => $inProgressBookings,
        'totalSpent' => $totalSpent,
        'userName' => $userName
    ]);
}

















 
// for tracking in dashboard for customer after clicking on  vehicle
public function getBookings(Request $request)
{
    try {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $page = $request->get('page', 1);
        $perPage = 3; // Show 3 bookings per page
        
        $bookings = Booking::with(['vehicle', 'vehicle.user'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
        
        // Format bookings for response
        $formattedBookings = $bookings->map(function($booking) {
            $badgeClass = 'bg-primary';
            if($booking->status == 'request') $badgeClass = 'bg-warning';
            if($booking->status == 'accept') $badgeClass = 'bg-success';
            if($booking->status == 'reject') $badgeClass = 'bg-danger';
            if($booking->status == 'complete') $badgeClass = 'bg-info';
            
            return [
                'id' => $booking->id,
                'pickup_location' => $booking->pickup_location,
                'dropoff_location' => $booking->dropoff_location,
                'goods_type' => $booking->goods_type,
                'goods_weight' => $booking->goods_weight,
                'booking_date' => $booking->booking_date,
                'status' => $booking->status,
                'status_text' => $booking->status_text,
                'badge_class' => $badgeClass,
                'vehicle_type' => $booking->vehicle ? $booking->vehicle->vehicle_type : null,
                'provider_name' => $booking->vehicle && $booking->vehicle->user ? $booking->vehicle->user->name : null,
                'created_at' => $booking->created_at->format('Y-m-d H:i:s')
            ];
        });
        
        return response()->json([
            'success' => true,
            'bookings' => $formattedBookings,
            'current_page' => $bookings->currentPage(),
            'last_page' => $bookings->lastPage(),
            'total' => $bookings->total(),
            'per_page' => $bookings->perPage()
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error fetching bookings: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}

/**
 * Get tracking information for a specific booking
 */
public function getTrackingInfo($id)
{
    try {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $booking = Booking::with(['vehicle', 'vehicle.user'])
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();
        
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        
        // Determine progress percentage based on delivery status
        $progressPercentage = 0;
        $timelineItems = [];
        
        // Define timeline items based on booking status
        if ($booking->status == 'accept' || $booking->status == 'complete') {
            // Order Confirmed
            $timelineItems[] = [
                'title' => 'Order Confirmed',
                'description' => 'Your booking has been confirmed.',
                'timestamp' => $booking->accepted_at ? 
                    \Carbon\Carbon::parse($booking->accepted_at)->format('d M, h:i A') : 
                    \Carbon\Carbon::parse($booking->created_at)->format('d M, h:i A'),
                'status' => 'completed'
            ];
            
            // Vehicle Dispatched
            $dispatchedCompleted = !is_null($booking->dispatched_at) || $booking->delivery_status == 'vehicle_dispatched' || $booking->delivery_status == 'in_transit' || $booking->delivery_status == 'delivered';
            $timelineItems[] = [
                'title' => 'Vehicle Dispatched',
                'description' => 'Vehicle has been dispatched to pickup location.',
                'timestamp' => $booking->dispatched_at ? 
                    \Carbon\Carbon::parse($booking->dispatched_at)->format('d M, h:i A') : null,
                'status' => $dispatchedCompleted ? 'completed' : 'pending'
            ];
            
            // In Transit
            $inTransitCompleted = !is_null($booking->in_transit_at) || $booking->delivery_status == 'in_transit' || $booking->delivery_status == 'delivered';
            $timelineItems[] = [
                'title' => 'In Transit',
                'description' => 'Your shipment is currently in transit.',
                'timestamp' => $booking->in_transit_at ? 
                    \Carbon\Carbon::parse($booking->in_transit_at)->format('d M, h:i A') : 
                    ($booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M, Y') : null),
                'status' => $inTransitCompleted ? 'completed' : 'active'
            ];
            
            // Delivery
            $deliveryCompleted = !is_null($booking->delivered_at) || $booking->delivery_status == 'delivered';
            $timelineItems[] = [
                'title' => 'Delivery',
                'description' => 'Your shipment will be delivered.',
                'timestamp' => $booking->delivered_at ? 
                    \Carbon\Carbon::parse($booking->delivered_at)->format('d M, h:i A') : null,
                'status' => $deliveryCompleted ? 'completed' : 'pending'
            ];
            
            // Calculate progress percentage
            $completedCount = 0;
            foreach ($timelineItems as $item) {
                if ($item['status'] == 'completed') $completedCount++;
                if ($item['status'] == 'active') break;
            }
            $progressPercentage = ($completedCount / 4) * 100;
            
            // If booking is complete, set all to completed
            if ($booking->status == 'complete') {
                $progressPercentage = 100;
                foreach ($timelineItems as &$item) {
                    $item['status'] = 'completed';
                    if (!$item['timestamp']) {
                        $item['timestamp'] = $booking->delivered_at ? 
                            \Carbon\Carbon::parse($booking->delivered_at)->format('d M, h:i A') : 
                            \Carbon\Carbon::now()->format('d M, h:i A');
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'pickup_location' => $booking->pickup_location,
                'dropoff_location' => $booking->dropoff_location,
                'status' => $booking->status,
                'delivery_status' => $booking->delivery_status,
                'status_text' => $booking->status_text,
                'delivery_status_text' => $booking->delivery_status_text,
                'progress_percentage' => $progressPercentage,
                'timeline' => $timelineItems,
                'vehicle' => $booking->vehicle ? [
                    'vehicle_type' => $booking->vehicle->vehicle_type,
                    'registration_number' => $booking->vehicle->registration_number
                ] : null
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error fetching tracking info: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}


























































// for cutomer side my booking tab

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
        
        return view('customer.mybookings', [
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
    



















      public function getResubmitData($id)
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
        
        // Check if booking can be resubmitted
        if (!$booking->canResubmit()) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be resubmitted'
            ], 400);
        }
        
        // Prepare data for resubmit form
        $resubmitData = [
            'booking_id' => $booking->id,
            'vehicle_id' => $booking->vehicle_id,
            'pickup_location' => $booking->pickup_location,
            'pickup_lat' => $booking->pickup_lat,
            'pickup_lng' => $booking->pickup_lng,
            'dropoff_location' => $booking->dropoff_location,
            'dropoff_lat' => $booking->dropoff_lat,
            'dropoff_lng' => $booking->dropoff_lng,
            'pickup_time' => $booking->pickup_time,
            'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
            'goods_type' => $booking->goods_type,
            'goods_weight' => $booking->goods_weight,
            'special_instructions' => $booking->special_instructions,
            'estimated_distance' => $booking->estimated_distance,
            'estimated_fare' => $booking->estimated_fare,
            'payment_method' => $booking->payment_method,
            'selected_route_name' => $booking->selected_route_name,
            'has_tolls' => $booking->has_tolls,
            'toll_cost' => $booking->toll_cost,
            'vehicle' => $booking->vehicle ? [
                'id' => $booking->vehicle->id,
                'vehicle_number' => $booking->vehicle->vehicle_number,
                'vehicle_type' => $booking->vehicle->vehicle_type,
                'rate_per_km' => $booking->vehicle->rate_per_km ?? 35,
                'vehicle_image' => $booking->vehicle->vehicle_image
            ] : null,
            'rejection_reason' => $booking->rejection_reason
        ];
        
        return response()->json([
            'success' => true,
            'data' => $resubmitData
        ]);
    }
    
    /**
     * Process resubmitted booking
     */
    public function resubmitBooking(Request $request)
    {
        try {
            $customerId = session('user_id');
            
            if (!$customerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401);
            }
            
            $validated = $request->validate([
                'original_booking_id' => 'required|exists:bookings,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'trip_date' => 'required|date',
                'pickup_location' => 'required|string',
                'drop_location' => 'required|string',
                'pickup_lat' => 'nullable|numeric',
                'pickup_lng' => 'nullable|numeric',
                'destination_lat' => 'nullable|numeric',
                'destination_lng' => 'nullable|numeric',
                'pickup_time' => 'nullable|string',
                'goods_type' => 'nullable|string',
                'goods_weight' => 'nullable|numeric',
                'special_instructions' => 'nullable|string',
                'estimated_distance' => 'nullable|numeric',
                'estimated_fare' => 'nullable|numeric',
                'payment_method' => 'nullable|string',
                'selected_route_data' => 'nullable|json',
                'route_polyline' => 'nullable|string',
                'route_directions' => 'nullable|string',
                'has_tolls' => 'nullable|boolean',
                'toll_cost' => 'nullable|numeric'
            ]);
            
            // Check if original booking exists and belongs to this customer
            $originalBooking = Booking::where('id', $validated['original_booking_id'])
                ->where('customer_id', $customerId)
                ->first();
            
            if (!$originalBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Original booking not found'
                ], 404);
            }
            
            // Check if original booking is rejected
            if ($originalBooking->status !== 'reject') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only rejected bookings can be resubmitted'
                ], 400);
            }
            
            // Check if vehicle is available
            $vehicle = Vehicle::find($validated['vehicle_id']);
            if (!$vehicle || $vehicle->is_booked === 'yes') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle is no longer available'
                ], 400);
            }
            
            // Parse selected route data if available
            $selectedRoute = null;
            if (isset($validated['selected_route_data'])) {
                $selectedRoute = json_decode($validated['selected_route_data'], true);
            }
            
            // Create new booking with resubmit flag
            $bookingData = [
                'customer_id' => $customerId,
                'vehicle_id' => $validated['vehicle_id'],
                'booking_date' => $validated['trip_date'],
                'pickup_location' => $validated['pickup_location'],
                'pickup_lat' => $validated['pickup_lat'],
                'pickup_lng' => $validated['pickup_lng'],
                'dropoff_location' => $validated['drop_location'],
                'dropoff_lat' => $validated['destination_lat'],
                'dropoff_lng' => $validated['destination_lng'],
                'status' => 'request',
                'pickup_time' => $validated['pickup_time'],
                'goods_type' => $validated['goods_type'],
                'goods_weight' => $validated['goods_weight'],
                'special_instructions' => $validated['special_instructions'],
                'estimated_distance' => $validated['estimated_distance'],
                'estimated_fare' => $validated['estimated_fare'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'route_polyline' => $validated['route_polyline'] ?? null,
                'route_directions' => $validated['route_directions'] ?? null,
                'selected_route_name' => $selectedRoute['name'] ?? null,
                'has_tolls' => $validated['has_tolls'] ?? false,
                'toll_cost' => $validated['toll_cost'] ?? null,
                'request_status' => 'pending',
                'delivery_status' => null,
                'is_booking_complete' => 'no',
                'is_resubmit' => true,
                'original_booking_id' => $originalBooking->id
            ];
            
            $newBooking = Booking::create($bookingData);
            
            // Mark original booking as resubmitted (optional)
            // $originalBooking->update(['is_resubmitted' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Booking resubmitted successfully!',
                'booking_id' => $newBooking->id,
                'redirect_url' => route('mybookings')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Resubmit booking error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resubmit booking: ' . $e->getMessage()
            ], 500);
        }
    }



// In UserrController.php, update the getBookingsData method

public function getBookingsData(Request $request)
{
    $customerId = session('user_id');
    
    if (!$customerId) {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    $perPage = $request->get('per_page', 6);
    $page = $request->get('page', 1);
    $filter = $request->get('filter', 'all');
    
    // Customer ki saari bookings fetch karein with vehicle and provider details
    $bookingsQuery = Booking::with(['vehicle.user'])
        ->where('customer_id', $customerId);
    
    // Apply filter
    if ($filter !== 'all') {
        $bookingsQuery->where('status', $filter);
    }
    
    $bookingsQuery->orderBy('created_at', 'desc');
    
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
            'created_at' => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : null,
            // Add resubmit flag
            'can_resubmit' => $booking->canResubmit()
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
















































































































// provider mybookingtab
// ==================== PROVIDER BOOKINGS FUNCTIONS ====================

public function providerBookings()
{
    $providerId = session('user_id');
    
    if (!$providerId || session('role') != 'provider') {
        return redirect()->route('login')->with('error', 'Access denied');
    }
    
    $provider = Userr::find($providerId);
    
    return view('Provider.mybooking', [
        'userName' => $provider->name
    ]);
}

public function getProviderBookingsData(Request $request)
{
    $providerId = session('user_id');
    
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    $perPage = $request->get('per_page', 6);
    $page = $request->get('page', 1);
    $filter = $request->get('filter', 'all');
    
    // Get all vehicles belonging to this provider
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');
    
    // Get bookings for these vehicles with customer details
    $bookingsQuery = Booking::with(['customer', 'vehicle'])
        ->whereIn('vehicle_id', $vehicleIds);
    
    // Apply filter
    if ($filter !== 'all') {
        $bookingsQuery->where('status', $filter);
    }
    
    $bookingsQuery->orderBy('created_at', 'desc');
    
    // Paginate results
    $bookings = $bookingsQuery->paginate($perPage, ['*'], 'page', $page);
    
    // Format bookings for response
    $formattedBookings = [];
    foreach ($bookings as $booking) {
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
        
        $formattedBookings[] = [
            'id' => $booking->id,
            'pickup_location' => $booking->pickup_location,
            'dropoff_location' => $booking->dropoff_location,
            'goods_type' => $booking->goods_type,
            'goods_weight' => $booking->goods_weight,
            'vehicle_type' => $booking->vehicle ? $booking->vehicle->vehicle_type : 'N/A',
            'customer_name' => $booking->customer ? $booking->customer->name : 'N/A',
            'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
            'status' => $booking->status,
            'status_text' => $statusText,
            'badge_class' => $badgeClass,
            'estimated_fare' => $booking->estimated_fare,
            'created_at' => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : null,
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

public function getProviderBookingDetails($id)
{
    $providerId = session('user_id');
    
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    // Get vehicles belonging to this provider
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');
    
    // Booking fetch karein with all relations, ensuring it belongs to provider's vehicle
    $booking = Booking::with(['vehicle', 'customer'])
        ->where('id', $id)
        ->whereIn('vehicle_id', $vehicleIds)
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
    $deliveredDate = $booking->delivered_at ? $booking->delivered_at->format('F d, Y h:i A') : null;
    
    // Prepare response
    $response = [
        'id' => $booking->id,
        'status' => $booking->status,
        'status_text' => $booking->status_text,
        'booking_date' => $bookingDate,
        'created_at' => $createdDate,
        'accepted_at' => $acceptedDate,
        'rejected_at' => $rejectedDate,
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
        
        // Rejection reason
        'rejection_reason' => $booking->rejection_reason,
        
        // Customer details
        'customer_name' => $booking->customer ? $booking->customer->name : 'N/A',
        'customer_email' => $booking->customer ? $booking->customer->email : 'N/A',
        'customer_mobile' => $booking->customer ? $booking->customer->mobile : 'N/A',
        'customer_image' => $booking->customer ? ($booking->customer->profile_image ?? 'https://randomuser.me/api/portraits/men/32.jpg') : 'https://randomuser.me/api/portraits/men/32.jpg',
        
        // Vehicle details
        'vehicle' => null
    ];
    
    if ($booking->vehicle) {
        $response['vehicle'] = [
            'id' => $booking->vehicle->id,
            'vehicle_number' => $booking->vehicle->vehicle_number,
            'vehicle_type' => $booking->vehicle->vehicle_type,
            'weight_capacity' => $booking->vehicle->weight_capacity,
            'vehicle_image' => $booking->vehicle->vehicle_image,
        ];
    }
    
    return response()->json([
        'success' => true,
        'booking' => $response
    ]);
}

public function getProviderBookingTracking($id)
{
    $providerId = session('user_id');
    
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');
    
    $booking = Booking::with(['vehicle', 'customer'])
        ->where('id', $id)
        ->whereIn('vehicle_id', $vehicleIds)
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
            'description' => 'Customer has submitted a booking request.',
            'timestamp' => $booking->created_at ? $booking->created_at->format('M d, Y h:i A') : null
        ];
        $timeline[] = [
            'status' => 'active',
            'title' => 'Awaiting Your Response',
            'description' => 'Please accept or reject the booking request.',
            'timestamp' => null
        ];
    } 
    elseif ($booking->status === 'reject') {
        $progressPercentage = 10;
        $timeline[] = [
            'status' => 'completed',
            'title' => 'Booking Requested',
            'description' => 'Customer submitted a booking request.',
            'timestamp' => $booking->created_at ? $booking->created_at->format('M d, Y h:i A') : null
        ];
        $timeline[] = [
            'status' => 'completed',
            'title' => 'Request Rejected',
            'description' => $booking->rejection_reason ?? 'You rejected this booking request.',
            'timestamp' => $booking->rejected_at ? $booking->rejected_at->format('M d, Y h:i A') : null
        ];
    } 
    elseif ($booking->status === 'accept') {
        $progressPercentage = 40;
        
        $timeline[] = [
            'status' => 'completed',
            'title' => 'Booking Requested',
            'description' => 'Customer submitted a booking request.',
            'timestamp' => $booking->created_at ? $booking->created_at->format('M d, Y h:i A') : null
        ];
        
        $timeline[] = [
            'status' => 'completed',
            'title' => 'Booking Accepted',
            'description' => 'You accepted the booking request.',
            'timestamp' => $booking->accepted_at ? $booking->accepted_at->format('M d, Y h:i A') : null
        ];
        
        $timeline[] = [
            'status' => 'active',
            'title' => 'Awaiting Delivery Completion',
            'description' => 'Mark as completed when delivery is done.',
            'timestamp' => null
        ];
    } 
    elseif ($booking->status === 'complete') {
        $progressPercentage = 100;
        
        $timeline[] = [
            'status' => 'completed',
            'title' => 'Booking Requested',
            'description' => 'Customer submitted a booking request.',
            'timestamp' => $booking->created_at ? $booking->created_at->format('M d, Y h:i A') : null
        ];
        
        $timeline[] = [
            'status' => 'completed',
            'title' => 'Booking Accepted',
            'description' => 'You accepted the booking request.',
            'timestamp' => $booking->accepted_at ? $booking->accepted_at->format('M d, Y h:i A') : null
        ];
        
        if ($booking->delivered_at) {
            $timeline[] = [
                'status' => 'completed',
                'title' => 'Delivery Completed',
                'description' => 'You marked this booking as completed.',
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
            'pickup_location' => $booking->pickup_location,
            'dropoff_location' => $booking->dropoff_location,
            'customer_name' => $booking->customer ? $booking->customer->name : 'N/A',
            'progress_percentage' => $progressPercentage,
            'timeline' => $timeline
        ]
    ]);
}

public function getProviderBookingReviews($id)
{
    $providerId = session('user_id');
    
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');
    
    $booking = Booking::where('id', $id)
        ->whereIn('vehicle_id', $vehicleIds)
        ->first();
    
    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }
    
    $reviews = $booking->reviews()->with('customer')->get();
    
    return response()->json([
        'success' => true,
        'data' => $reviews
    ]);
}

public function getProviderBookingComplaints($id)
{
    $providerId = session('user_id');
    
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');
    
    $booking = Booking::where('id', $id)
        ->whereIn('vehicle_id', $vehicleIds)
        ->first();
    
    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }
    
    $complaints = $booking->complaints()->get();
    
    $formattedComplaints = [];
    foreach ($complaints as $complaint) {
        $statusBadge = 'bg-warning';
        $statusText = 'Pending';
        
        if ($complaint->status === 'resolved') {
            $statusBadge = 'bg-success';
            $statusText = 'Resolved';
        } elseif ($complaint->status === 'in_progress') {
            $statusBadge = 'bg-info';
            $statusText = 'In Progress';
        }
        
        $formattedComplaints[] = [
            'id' => $complaint->id,
            'subject' => $complaint->subject,
            'description' => $complaint->description,
            'complaint_type' => $complaint->complaint_type,
            'status' => $complaint->status,
            'status_text' => $statusText,
            'status_badge' => $statusBadge,
            'admin_response' => $complaint->admin_response,
            'created_at' => $complaint->created_at,
        ];
    }
    
    return response()->json([
        'success' => true,
        'data' => $formattedComplaints
    ]);
}

// ==================== ACTION METHODS ====================

public function acceptBooking($id, Request $request)
{
    $providerId = session('user_id');
    
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');
    
    $booking = Booking::where('id', $id)
        ->whereIn('vehicle_id', $vehicleIds)
        ->first();
    
    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }
    
    if ($booking->status !== 'request') {
        return response()->json([
            'success' => false,
            'message' => 'Booking cannot be accepted in its current state'
        ]);
    }
    
    try {
        $booking->acceptRequest();
        
        return response()->json([
            'success' => true,
            'message' => 'Booking accepted successfully! Vehicle has been marked as booked.'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error accepting booking: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error accepting booking. Please try again.'
        ]);
    }
}

public function rejectBooking($id, Request $request)
{
    $providerId = session('user_id');
    
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');
    
    $booking = Booking::where('id', $id)
        ->whereIn('vehicle_id', $vehicleIds)
        ->first();
    
    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }
    
    if ($booking->status !== 'request') {
        return response()->json([
            'success' => false,
            'message' => 'Booking cannot be rejected in its current state'
        ]);
    }
    
    $reason = $request->input('reason', 'No reason provided');
    
    try {
        $booking->rejectRequest($reason);
        
        return response()->json([
            'success' => true,
            'message' => 'Booking rejected successfully.'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error rejecting booking: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error rejecting booking. Please try again.'
        ]);
    }
}

public function completeBooking($id, Request $request)
{
    $providerId = session('user_id');
    
    if (!$providerId || session('role') != 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');
    
    $booking = Booking::where('id', $id)
        ->whereIn('vehicle_id', $vehicleIds)
        ->first();
    
    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }
    
    if ($booking->status !== 'accept') {
        return response()->json([
            'success' => false,
            'message' => 'Booking cannot be marked as completed. It must be accepted first.'
        ]);
    }
    
    try {
        $booking->completeBooking();
        
        return response()->json([
            'success' => true,
            'message' => 'Booking marked as completed successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error completing booking: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error completing booking. Please try again.'
        ]);
    }
}

}
