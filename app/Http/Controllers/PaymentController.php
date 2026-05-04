<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Models\Userr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Sandbox credentials for testing
     */
    private $sandboxCredentials = [
        'jazzcash' => [
            'merchant_id' => 'SANDBOX_MERCHANT_001',
            'password' => 'sandbox_jazzcash_pass_123',
            'integrity_salt' => 'jazzcash_salt_sandbox_2024',
            'api_url' => 'https://sandbox.jazzcash.com.pk/api/payment',
        ],
        'easypaisa' => [
            'store_id' => 'SANDBOX_STORE_001',
            'hash_key' => 'easypaisa_sandbox_hash_456',
            'api_url' => 'https://sandbox.easypaisa.com/api/v1/payment',
        ],
        'card' => [
            'api_key' => 'sandbox_card_api_key_789',
            'api_secret' => 'sandbox_card_secret_101112',
            'api_url' => 'https://sandbox.paymentgateway.com/api/process',
        ]
    ];

    /**
     * Sandbox test cards
     */
    private $sandboxCards = [
        'visa_success' => [
            'card_number' => '4111111111111111',
            'cvv' => '123',
            'expiry_month' => '12',
            'expiry_year' => '2025',
            'result' => 'success'
        ],
        'mastercard_success' => [
            'card_number' => '5555555555554444',
            'cvv' => '123',
            'expiry_month' => '12',
            'expiry_year' => '2025',
            'result' => 'success'
        ],
        'visa_failure' => [
            'card_number' => '4000000000000002',
            'cvv' => '123',
            'expiry_month' => '12',
            'expiry_year' => '2025',
            'result' => 'failure'
        ],
        'insufficient_balance' => [
            'card_number' => '4012888888881881',
            'cvv' => '123',
            'expiry_month' => '12',
            'expiry_year' => '2025',
            'result' => 'insufficient'
        ]
    ];

    /**
     * Show payment page for a booking
     */
    public function showPaymentPage(Request $request, $bookingId)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $booking = Booking::with(['vehicle', 'customer'])
            ->where('id', $bookingId)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        // Check if payment already exists and is completed
        $existingPayment = Payment::where('booking_id', $bookingId)
            ->where('status', Payment::STATUS_COMPLETED)
            ->first();

        if ($existingPayment) {
            return redirect()->route('payment.success', $bookingId)
                ->with('success', 'Payment already completed for this booking');
        }

        // Create or get pending payment
        $payment = Payment::firstOrCreate(
            ['booking_id' => $bookingId, 'status' => Payment::STATUS_PENDING],
            [
                'customer_id' => $customerId,
                'provider_id' => $booking->vehicle->user_id ?? null,
                'payment_method' => $booking->payment_method ?? 'card',
                'amount' => $booking->actual_fare ?? $booking->estimated_fare ?? 0,
                'sandbox_mode' => true
            ]
        );

        return view('payment.pay', compact('booking', 'payment'));
    }

    /**
     * Process payment (sandbox mode)
     */
 public function processPayment(Request $request, $bookingId)
{
    $customerId = session('user_id');
    
    if (!$customerId) {
        return response()->json(['success' => false, 'message' => 'Please login first'], 401);
    }

    $request->validate([
        'payment_method' => 'required|in:jazzcash,easypaisa,card,cod',
        'card_number' => 'required_if:payment_method,card|nullable|string',
        'cvv' => 'required_if:payment_method,card|nullable|string|size:3',
        'expiry_month' => 'required_if:payment_method,card|nullable|string|size:2',
        'expiry_year' => 'required_if:payment_method,card|nullable|string|size:4',
        'phone_number' => 'required_if:payment_method,jazzcash,easypaisa|nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $booking = Booking::where('id', $bookingId)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $payment = Payment::where('booking_id', $bookingId)
            ->where('status', Payment::STATUS_PENDING)
            ->first();

        if (!$payment) {
            $payment = Payment::create([
                'booking_id' => $bookingId,
                'customer_id' => $customerId,
                'provider_id' => $booking->vehicle->user_id ?? null,
                'payment_method' => $request->payment_method,
                'amount' => $booking->actual_fare ?? $booking->estimated_fare ?? 0,
                'sandbox_mode' => true,
                'status' => Payment::STATUS_PENDING
            ]);
        }

        // Update payment method if changed
        $payment->payment_method = $request->payment_method;
        $payment->save();

        // Process based on payment method
        $result = $this->processSandboxPayment($request, $payment, $booking);

        if ($result['success']) {
            // Payment record ko completed mark karo
            $payment->markAsCompleted($result['transaction_id']);
            
            // Booking table mein payment_status PENDING hi rahega
            // Yeh acceptBooking pe update hoga (card/jazzcash/easypaisa ke liye)
            // COD ke liye delivered pe update hoga
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully!',
                'redirect_url' => route('payment.success', $bookingId)
            ]);
        } else {
            $payment->markAsFailed(['error' => $result['message']]);
            DB::commit();

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Payment processing error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Payment processing failed: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Process sandbox payment simulation
     */
    private function processSandboxPayment($request, $payment, $booking)
    {
        $method = $request->payment_method;
        
        // Simulate processing delay
        usleep(500000); // 0.5 seconds delay for realism
        
        switch ($method) {
            case 'cod':
                return $this->processCODPayment($request, $payment, $booking);
                
            case 'card':
                return $this->processCardPayment($request, $payment, $booking);
                
            case 'jazzcash':
                return $this->processJazzCashPayment($request, $payment, $booking);
                
            case 'easypaisa':
                return $this->processEasypaisaPayment($request, $payment, $booking);
                
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    /**
     * Process COD payment (always succeeds in sandbox)
     */
    private function processCODPayment($request, $payment, $booking)
    {
        $transactionId = Payment::generateSandboxTransactionId();
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'message' => 'Cash on Delivery selected. You will pay when the vehicle arrives.'
        ];
    }

    /**
     * Process card payment with sandbox testing
     */
    private function processCardPayment($request, $payment, $booking)
    {
        $cardNumber = str_replace(' ', '', $request->card_number);
        $cvv = $request->cvv;
        $expiryMonth = $request->expiry_month;
        $expiryYear = $request->expiry_year;
        
        // Mask card number for storage (only last 4 digits)
        $payment->card_number_masked = '**** **** **** ' . substr($cardNumber, -4);
        $payment->save();
        
        // Check against sandbox test cards
        foreach ($this->sandboxCards as $key => $testCard) {
            if ($cardNumber === $testCard['card_number']) {
                if ($testCard['result'] === 'success') {
                    $transactionId = Payment::generateSandboxTransactionId();
                    
                    // Store response
                    $payment->payment_response = [
                        'card_type' => strpos($key, 'visa') !== false ? 'VISA' : 'MasterCard',
                        'last_four' => substr($cardNumber, -4),
                        'sandbox_test' => true
                    ];
                    $payment->save();
                    
                    return [
                        'success' => true,
                        'transaction_id' => $transactionId,
                        'message' => 'Card payment successful!'
                    ];
                } elseif ($testCard['result'] === 'insufficient') {
                    return [
                        'success' => false,
                        'message' => 'Insufficient balance. Please use a different card.'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Card declined. Please check your card details or use a different card.'
                    ];
                }
            }
        }
        
        // For any other card number in sandbox, simulate success with random outcome
        // 90% success rate for sandbox testing
        $randomOutcome = rand(1, 100);
        
        if ($randomOutcome <= 90) {
            $transactionId = Payment::generateSandboxTransactionId();
            $payment->payment_response = [
                'card_type' => $this->detectCardType($cardNumber),
                'last_four' => substr($cardNumber, -4),
                'sandbox_random' => true
            ];
            $payment->save();
            
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Card payment successful! (Sandbox Mode)'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Transaction failed. Please try again. (Sandbox random failure for testing)'
            ];
        }
    }

    /**
     * Process JazzCash payment (sandbox)
     */
    private function processJazzCashPayment($request, $payment, $booking)
    {
        $phoneNumber = $request->phone_number;
        
        // Validate phone number format (Pakistan)
        if (!preg_match('/^03[0-9]{9}$/', $phoneNumber)) {
            return [
                'success' => false,
                'message' => 'Invalid JazzCash phone number. Must be a valid Pakistan mobile number (03XXXXXXXXX)'
            ];
        }
        
        // Simulate OTP verification (in sandbox, always success)
        $sandboxOtp = '123456';
        
        $transactionId = Payment::generateSandboxTransactionId();
        
        $payment->payment_response = [
            'phone_number' => $phoneNumber,
            'sandbox_otp' => $sandboxOtp,
            'merchant_id' => $this->sandboxCredentials['jazzcash']['merchant_id']
        ];
        $payment->save();
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'message' => 'JazzCash payment successful! (Sandbox Mode - OTP: ' . $sandboxOtp . ')'
        ];
    }

    /**
     * Process Easypaisa payment (sandbox)
     */
    private function processEasypaisaPayment($request, $payment, $booking)
    {
        $phoneNumber = $request->phone_number;
        
        // Validate phone number format
        if (!preg_match('/^03[0-9]{9}$/', $phoneNumber)) {
            return [
                'success' => false,
                'message' => 'Invalid Easypaisa phone number. Must be a valid Pakistan mobile number (03XXXXXXXXX)'
            ];
        }
        
        $transactionId = Payment::generateSandboxTransactionId();
        
        $payment->payment_response = [
            'phone_number' => $phoneNumber,
            'store_id' => $this->sandboxCredentials['easypaisa']['store_id']
        ];
        $payment->save();
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'message' => 'Easypaisa payment successful! (Sandbox Mode)'
        ];
    }

    /**
     * Detect card type from number
     */
    private function detectCardType($cardNumber)
    {
        $cardNumber = str_replace(' ', '', $cardNumber);
        
        if (preg_match('/^4/', $cardNumber)) {
            return 'VISA';
        } elseif (preg_match('/^5[1-5]/', $cardNumber)) {
            return 'MasterCard';
        } elseif (preg_match('/^3[47]/', $cardNumber)) {
            return 'American Express';
        } elseif (preg_match('/^6(?:011|5)/', $cardNumber)) {
            return 'Discover';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Payment success page
     */
    public function paymentSuccess($bookingId)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $booking = Booking::with(['vehicle', 'customer'])
            ->where('id', $bookingId)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $payment = Payment::where('booking_id', $bookingId)
            ->where('status', Payment::STATUS_COMPLETED)
            ->first();

        if (!$payment) {
            return redirect()->route('payment.show', $bookingId)
                ->with('error', 'Payment not completed yet');
        }

        return view('payment.success', compact('booking', 'payment'));
    }

    /**
     * Payment failure page
     */
    public function paymentFailure($bookingId)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return redirect()->route('login');
        }

        $booking = Booking::where('id', $bookingId)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $payment = Payment::where('booking_id', $bookingId)
            ->where('status', Payment::STATUS_FAILED)
            ->first();

        return view('payment.failure', compact('booking', 'payment'));
    }

    /**
     * Retry payment
     */
    public function retryPayment($bookingId)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $booking = Booking::where('id', $bookingId)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        // Reset failed payment status to pending
        Payment::where('booking_id', $bookingId)
            ->where('status', Payment::STATUS_FAILED)
            ->update(['status' => Payment::STATUS_PENDING]);

        return redirect()->route('payment.show', $bookingId)
            ->with('info', 'Please try payment again');
    }

    /**
     * Get payment status (AJAX)
     */
    public function getPaymentStatus($bookingId)
    {
        $customerId = session('user_id');
        
        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $payment = Payment::where('booking_id', $bookingId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'method' => $payment->payment_method,
            'transaction_id' => $payment->transaction_id,
            'paid_at' => $payment->paid_at
        ]);
    }

    /**
     * Payment webhook (simulate provider notification)
     */
    public function paymentWebhook(Request $request)
    {
        // This simulates a payment gateway webhook
        $transactionId = $request->input('transaction_id');
        $status = $request->input('status');
        
        $payment = Payment::where('transaction_id', $transactionId)->first();
        
        if ($payment) {
            if ($status === 'completed') {
                $payment->markAsCompleted($transactionId);
            } else {
                $payment->markAsFailed(['webhook_status' => $status]);
            }
            
            Log::info('Payment webhook received', [
                'transaction_id' => $transactionId,
                'status' => $status
            ]);
        }
        
        return response()->json(['received' => true]);
    }








































































    // provider see his payments
    /**
 * Display analytics dashboard for vehicle owner
 */
public function analytics()
{
    $providerId = session('user_id');

    if (!$providerId || session('role') != 'provider') {
        return redirect()->route('login')->with('error', 'Access denied.');
    }

    // Get provider name
    $provider = Userr::find($providerId);
    $userName = $provider ? $provider->name : 'Vehicle Owner';

    // Get all vehicles owned by this provider
    $vehicles = Vehicle::where('user_id', $providerId)->get();
    $vehicleIds = $vehicles->pluck('id');

    // Get all completed bookings (status = complete) for these vehicles
    $completedBookings = Booking::whereIn('vehicle_id', $vehicleIds)
        ->where('status', 'complete')
        ->with(['vehicle', 'customer'])
        ->orderBy('delivered_at', 'desc')
        ->get();

    // Calculate Total Income (sum of actual_fare from completed bookings)
    $totalIncome = $completedBookings->sum('actual_fare');
    
    // Calculate Total Estimated Fare (sum of estimated_fare from completed bookings)
    $totalEstimatedFare = $completedBookings->sum('estimated_fare');
    
    // Calculate Total Penalty Amount
    $totalPenalty = $completedBookings->sum('penalty_amount');
    
    // Calculate Average Fare Per Trip
    $avgFarePerTrip = $completedBookings->count() > 0 ? $totalIncome / $completedBookings->count() : 0;
    
    // Total Completed Trips
    $totalTrips = $completedBookings->count();

    // Vehicle-wise earnings
    $vehicleEarnings = [];
    foreach ($vehicles as $vehicle) {
        $vehicleBookings = $completedBookings->where('vehicle_id', $vehicle->id);
        $vehicleEarnings[] = [
            'vehicle_id' => $vehicle->id,
            'vehicle_number' => $vehicle->vehicle_number,
            'vehicle_type' => $vehicle->vehicle_type,
            'total_trips' => $vehicleBookings->count(),
            'total_estimated_fare' => $vehicleBookings->sum('estimated_fare'),
            'total_actual_fare' => $vehicleBookings->sum('actual_fare'),
            'total_penalty' => $vehicleBookings->sum('penalty_amount'),
            'total_earnings' => $vehicleBookings->sum('actual_fare'),
            'avg_fare_per_trip' => $vehicleBookings->count() > 0 ? $vehicleBookings->sum('actual_fare') / $vehicleBookings->count() : 0,
            'bookings' => $vehicleBookings
        ];
    }

    // Monthly Earnings (last 12 months)
    $monthlyEarnings = [];
    $monthlyData = [];
    
    for ($i = 11; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $monthName = $month->format('M Y');
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        
        $monthlyBookings = $completedBookings->filter(function($booking) use ($monthStart, $monthEnd) {
            $deliveredAt = $booking->delivered_at ? \Carbon\Carbon::parse($booking->delivered_at) : null;
            return $deliveredAt && $deliveredAt->between($monthStart, $monthEnd);
        });
        
        $monthlyEarnings[] = [
            'month' => $monthName,
            'month_key' => $month->format('M'),
            'earnings' => $monthlyBookings->sum('actual_fare'),
            'trips' => $monthlyBookings->count(),
            'penalty' => $monthlyBookings->sum('penalty_amount')
        ];
        
        $monthlyData[] = $monthlyBookings->sum('actual_fare');
    }

    // Get recent bookings for table display
    $recentBookings = $completedBookings->take(10);

    // Yearly Earnings (last 5 years)
    $yearlyEarnings = [];
    $currentYear = now()->year;
    for ($i = 4; $i >= 0; $i--) {
        $year = $currentYear - $i;
        $yearStart = \Carbon\Carbon::create($year, 1, 1)->startOfYear();
        $yearEnd = \Carbon\Carbon::create($year, 12, 31)->endOfYear();
        
        $yearlyBookings = $completedBookings->filter(function($booking) use ($yearStart, $yearEnd) {
            $deliveredAt = $booking->delivered_at ? \Carbon\Carbon::parse($booking->delivered_at) : null;
            return $deliveredAt && $deliveredAt->between($yearStart, $yearEnd);
        });
        
        $yearlyEarnings[] = [
            'year' => $year,
            'earnings' => $yearlyBookings->sum('actual_fare'),
            'trips' => $yearlyBookings->count()
        ];
    }

    // Performance metrics
    $performanceMetrics = [
        'total_vehicles' => $vehicles->count(),
        'active_vehicles' => $vehicles->where('is_active', true)->count(),
        'total_trips' => $totalTrips,
        'total_income' => $totalIncome,
        'total_estimated_fare' => $totalEstimatedFare,
        'total_penalty' => $totalPenalty,
        'avg_fare_per_trip' => $avgFarePerTrip,
        'total_vehicles_with_earnings' => collect($vehicleEarnings)->filter(function($v) { return $v['total_trips'] > 0; })->count()
    ];

    // Best performing vehicle
    $bestVehicle = collect($vehicleEarnings)->sortByDesc('total_earnings')->first();

    return view('Provider.provider_analytics', compact(
        'userName',
        'vehicles',
        'completedBookings',
        'totalIncome',
        'totalEstimatedFare',
        'totalPenalty',
        'avgFarePerTrip',
        'totalTrips',
        'vehicleEarnings',
        'monthlyEarnings',
        'yearlyEarnings',
        'recentBookings',
        'performanceMetrics',
        'bestVehicle',
        'monthlyData'
    ));
}



















/**
 * Display analytics dashboard for customer
 */
public function customer_analytics()
{
    $customerId = session('user_id');

    if (!$customerId) {
        return redirect()->route('login')->with('error', 'Please login first');
    }

    // Get customer name
    $customer = Userr::find($customerId);
    $userName = $customer ? $customer->name : 'Customer';

    // Get all completed bookings (status = complete) for this customer
    $completedBookings = Booking::where('customer_id', $customerId)
        ->where('status', 'complete')
        ->with(['vehicle', 'vehicle.user'])
        ->orderBy('delivered_at', 'desc')
        ->get();

    // Calculate Total Spending (sum of actual_fare from completed bookings)
    $totalSpending = $completedBookings->sum('actual_fare');
    
    // Calculate Total Estimated Fare (sum of estimated_fare from completed bookings)
    $totalEstimatedFare = $completedBookings->sum('estimated_fare');
    
    // Calculate Total Penalty Amount (sum of penalty_amount from completed bookings)
    $totalPenalty = $completedBookings->sum('penalty_amount');
    
    // Calculate Average Fare Per Trip
    $avgFarePerTrip = $completedBookings->count() > 0 ? $totalSpending / $completedBookings->count() : 0;
    
    // Total Completed Trips
    $totalTrips = $completedBookings->count();

    // Get total bookings count (all bookings including pending, accepted, completed)
    $totalBookings = Booking::where('customer_id', $customerId)->count();
    
    // Get pending bookings count
    $pendingBookings = Booking::where('customer_id', $customerId)
        ->where('status', 'request')
        ->count();
    
    // Get in-progress bookings count
    $inProgressBookings = Booking::where('customer_id', $customerId)
        ->where('status', 'accept')
        ->count();

    // Vehicle-wise spending (how much paid to each vehicle)
    $vehicleSpending = [];
    $vehiclesUsed = $completedBookings->groupBy('vehicle_id');
    
    foreach ($vehiclesUsed as $vehicleId => $bookings) {
        $vehicle = $bookings->first()->vehicle;
        $vehicleSpending[] = [
            'vehicle_id' => $vehicleId,
            'vehicle_number' => $vehicle->vehicle_number ?? 'N/A',
            'vehicle_type' => $vehicle->vehicle_type ?? 'N/A',
            'total_trips' => $bookings->count(),
            'total_estimated_fare' => $bookings->sum('estimated_fare'),
            'total_actual_fare' => $bookings->sum('actual_fare'),
            'total_penalty' => $bookings->sum('penalty_amount'),
            'total_spending' => $bookings->sum('actual_fare'),
            'avg_fare_per_trip' => $bookings->count() > 0 ? $bookings->sum('actual_fare') / $bookings->count() : 0,
            'bookings' => $bookings
        ];
    }

    // Sort by total spending descending
    usort($vehicleSpending, function($a, $b) {
        return $b['total_spending'] <=> $a['total_spending'];
    });

    // Monthly Spending (last 12 months)
    $monthlySpending = [];
    $monthlyData = [];
    
    for ($i = 11; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $monthName = $month->format('M Y');
        $monthKey = $month->format('M');
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        
        $monthlyBookings = $completedBookings->filter(function($booking) use ($monthStart, $monthEnd) {
            $deliveredAt = $booking->delivered_at ? \Carbon\Carbon::parse($booking->delivered_at) : null;
            return $deliveredAt && $deliveredAt->between($monthStart, $monthEnd);
        });
        
        $monthlySpending[] = [
            'month' => $monthName,
            'month_key' => $monthKey,
            'spending' => $monthlyBookings->sum('actual_fare'),
            'trips' => $monthlyBookings->count(),
            'penalty' => $monthlyBookings->sum('penalty_amount')
        ];
        
        $monthlyData[] = $monthlyBookings->sum('actual_fare');
    }

    // Yearly Spending (last 5 years)
    $yearlySpending = [];
    $currentYear = now()->year;
    for ($i = 4; $i >= 0; $i--) {
        $year = $currentYear - $i;
        $yearStart = \Carbon\Carbon::create($year, 1, 1)->startOfYear();
        $yearEnd = \Carbon\Carbon::create($year, 12, 31)->endOfYear();
        
        $yearlyBookings = $completedBookings->filter(function($booking) use ($yearStart, $yearEnd) {
            $deliveredAt = $booking->delivered_at ? \Carbon\Carbon::parse($booking->delivered_at) : null;
            return $deliveredAt && $deliveredAt->between($yearStart, $yearEnd);
        });
        
        $yearlySpending[] = [
            'year' => $year,
            'spending' => $yearlyBookings->sum('actual_fare'),
            'trips' => $yearlyBookings->count()
        ];
    }

    // Get recent bookings for table display
    $recentBookings = $completedBookings->take(10);

    // Performance metrics
    $performanceMetrics = [
        'total_bookings' => $totalBookings,
        'completed_trips' => $totalTrips,
        'pending_bookings' => $pendingBookings,
        'in_progress_bookings' => $inProgressBookings,
        'total_spending' => $totalSpending,
        'total_estimated_fare' => $totalEstimatedFare,
        'total_penalty' => $totalPenalty,
        'avg_fare_per_trip' => $avgFarePerTrip,
        'unique_vehicles_used' => count($vehicleSpending)
    ];

    // Most used vehicle
    $mostUsedVehicle = !empty($vehicleSpending) ? $vehicleSpending[0] : null;

    // Best value trip (lowest actual_fare)
    $bestValueTrip = $completedBookings->sortBy('actual_fare')->first();
    
    // Most expensive trip (highest actual_fare)
    $mostExpensiveTrip = $completedBookings->sortByDesc('actual_fare')->first();

    return view('customer.customer_analytics', compact(
        'userName',
        'completedBookings',
        'totalSpending',
        'totalEstimatedFare',
        'totalPenalty',
        'avgFarePerTrip',
        'totalTrips',
        'totalBookings',
        'pendingBookings',
        'inProgressBookings',
        'vehicleSpending',
        'monthlySpending',
        'yearlySpending',
        'recentBookings',
        'performanceMetrics',
        'mostUsedVehicle',
        'bestValueTrip',
        'mostExpensiveTrip',
        'monthlyData'
    ));
}
}