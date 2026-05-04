<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\UserrController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LiveLocationController;
Route::get('/a', function () {

    return view('home');

});


// for user register pp

Route::get('/register', [UserrController::class, 'index']);
Route::post('/reg', [UserrController::class, 'store'])->name('user.register');

//for login 
Route::get('/login', [UserrController::class, 'login'])->name('login');
Route::post('/loginn', [UserrController::class, 'loginn'])->name('user.login');

//for admin provider and customer login 

Route::get('/alogin', [UserrController::class, 'adminlogin'])->name('admin.login');
Route::get('/plogin', [UserrController::class, 'providerlogin'])->name('provider.login');
Route::get('/clogin', [UserrController::class, 'customerlogin'])->name('customer.login');

// for logout
Route::get('/logout', [UserrController::class, 'logout'])->name('user.logout');

// for register vehicle pp


//my vehicle to add vehicle
Route::get('/show_reg', [VehicleController::class, 'show_reg_vehicle'])->name('show.registerform');
Route::get('/myvehicle', [VehicleController::class, 'myvehicle'])->name('my.vehicle');

Route::post('/reg_vehicle', [VehicleController::class, 'Register_Vehicle'])->name('vehicle_register');
Route::get('/seetrip', [BookingController::class, 'providerTrip'])->name('see.trip');

//admin dashbord pending vehicles
Route::get('/pending', [VehicleController::class, 'pendingvehicle'])->name('admin.pendingVehicles');
Route::post('/admin/vehicle/{id}/approve', [VehicleController::class, 'approveVehicle'])->name('vehicle.approve');
Route::post('/admin/vehicle/{id}/reject', [VehicleController::class, 'rejectVehicle'])->name('vehicle.reject');
Route::get('/admin/vehicle/{id}/details', [VehicleController::class, 'getVehicleDetailsss'])->name('vehicle.details');

//admin dashbord available vehicles
Route::get('/available', [VehicleController::class, 'showAvailable'])->name('admin.availableVehicles');
Route::get('/adminviewtrip', [BookingController::class, 'viewtrip'])->name('admin.seetrip');

// admin disable available vehicle
// Admin: toggle vehicle active/inactive
Route::post('/admin/vehicle/{id}/toggle-active', [\App\Http\Controllers\VehicleController::class, 'toggleActive'])
    ->name('vehicle.toggleActive');



//customer dashbord->trip bok 
// Route::get('/Cus_see_available', [VehicleController::class, 'showCusAvailable'])->name('customer.seeavailable');
Route::get('/findvehicle', [VehicleController::class, 'showCusAvailable'])->name('find.vehicle');
Route::get('/Allavaiblevehicle', [VehicleController::class, 'All_Available'])->name('all.vehicle');
#api for filter


Route::get('/trip_form', [BookingController::class, 'tripForm'])->name('trip.form');

Route::post('/trip_submit', [BookingController::class, 'tripSubmit'])->name('trip.submitt');
//see booked trip
Route::get('/see_bookedTrip', [VehicleController::class, 'SeeBookedTrip'])->name('see.bookedtrip');



//provider for see trip
// Route::get('/seee_trip', [BookingController::class, 'providerTrip'])->name('see.tripp');

// Route::post('/abcapprove', [BookingController::class, 'approve'])->name('abc.dot');




Route::get('/', function () {
    return view('landingpage');
});





// for vehicle =service provider to add and edit vehicles
Route::get('/my-vehicles', [VehicleController::class, 'myvehicle'])->name('provider.vehicles');
Route::get('/vehicle/{id}', [VehicleController::class, 'getVehicleDetails'])->name('vehicle.details');
Route::get('/vehicle/{id}/edit', [VehicleController::class, 'editVehicle'])->name('vehicle.edit');
Route::post('/vehicle/{id}/status', [VehicleController::class, 'updateVehicleStatus'])->name('vehicle.status.update');
Route::post('/vehicle/{id}/update', [VehicleController::class, 'updateVehicle'])->name('vehicle.update');
Route::post('/reg_vehicle', [VehicleController::class, 'Register_Vehicle'])->name('vehicle_register');






Route::get('/vehicle/{id}', [VehicleController::class, 'getVehicleDetails'])->name('vehicle.details');
Route::post('/vehicle/{id}/status', [VehicleController::class, 'updateVehicleStatus'])->name('vehicle.status.update');



// for booking screen for service provider





//customer 













// Dashboard Routes




Route::get('/verify-email/{token}', [UserrController::class, 'verifyEmail'])->name('email.verify');

Route::post('/resend-verification', [UserrController::class, 'resendVerification'])->name('verification.resend');






#routes for trip submit through map
Route::post('/trip_submit', [BookingController::class, 'tripSubmit'])->name('trip.submit');
Route::post('/calculate-route', [BookingController::class, 'calculateRoute'])->name('calculate.route');
Route::get('/booking/{id}/route', [BookingController::class, 'getBookingRoute'])->name('booking.route');











// for booking submission

Route::get('/booking-form', [BookingController::class, 'showBookingForm'])->name('booking.form');
Route::post('/calculate-route', [BookingController::class, 'calculateRoute'])->name('calculate.route');
Route::post('/trip-submit', [BookingController::class, 'tripSubmit'])->name('trip.submit');
Route::get('/customer-bookings', [BookingController::class, 'customerBookings'])->name('customer.bookings');
Route::get('/booking-route/{id}', [BookingController::class, 'getBookingRoute'])->name('booking.route');
Route::post('/cancel-booking/{id}', [BookingController::class, 'cancelBooking'])->name('cancel.booking');
Route::post('/complete-booking/{id}', [BookingController::class, 'completeBooking'])->name('complete.booking');

// Provider Booking Management Routes accept or reject dispatch
Route::get('/seetrip', [BookingController::class, 'providerTrip'])->name('see.trip');
Route::get('/booking-requests', [BookingController::class, 'bookingRequests'])->name('booking.requests');
Route::post('/accept-booking/{id}', [BookingController::class, 'acceptBooking'])->name('accept.booking');
Route::post('/reject-booking/{id}', [BookingController::class, 'rejectBooking'])->name('reject.booking');
Route::post('/update-delivery-status/{id}', [BookingController::class, 'updateDeliveryStatus'])->name('update.delivery.status');
Route::get('/booking-details/{id}', [BookingController::class, 'bookingDetails'])->name('booking.details');




// detail of booking for customer in dashboard trck + informotion
Route::get('/booking/details/{id}', [UserrController::class, 'getBookingDetails'])->name('booking.details');
Route::get('/customer/bookings', [UserrController::class, 'getBookings'])->name('customer.bookings');
Route::get('/customer/tracking/{id}', [UserrController::class, 'getTrackingInfo'])->name('customer.tracking');
Route::get('/booking/details/{id}', [UserrController::class, 'getBookingDetails'])->name('booking.details');
Route::get('/clogin', [UserrController::class, 'customerlogin'])->name('customer.login');


// cutomer booking detail in My booking tab

Route::prefix('customer')->group(function() {
    // My Bookings Page
    Route::get('/my-bookings', [UserrController::class, 'myBookings'])->name('mybookings');
    
    // AJAX Routes for Bookings
    Route::get('/bookings-data', [UserrController::class, 'getBookingsData'])->name('customer.bookings.data');
    Route::get('/booking/{id}/details', [UserrController::class, 'getBookingDetailss'])->name('customer.booking.details');
    Route::get('/booking/{id}/tracking', [UserrController::class, 'getTrackingInfoo'])->name('customer.booking.tracking');
    Route::get('/booking/{id}/route-map', [UserrController::class, 'getRouteMap'])->name('customer.booking.route');
     Route::get('/booking/{id}/resubmit-data', [UserrController::class, 'getResubmitData'])->name('customer.booking.resubmit.data');
    Route::post('/resubmit-booking', [UserrController::class, 'resubmitBooking'])->name('resubmit.booking');
});
    



// customer see live location
Route::prefix('customer')->group(function() {
    // My Bookings Page
    Route::get('/my-bookingss', [liveLocationController::class, 'myBookings'])->name('mybookingss');
    
    // AJAX Routes for Bookings
    Route::get('/bookings-dataa', [liveLocationController::class, 'getBookingsData'])->name('customer.bookings.data');
    Route::get('/booking/{id}/detailss', [liveLocationController::class, 'getBookingDetailss'])->name('customer.booking.details');
    Route::get('/booking/{id}/trackingg', [liveLocationController::class, 'getTrackingInfoo'])->name('customer.booking.tracking');
    Route::get('/booking/{id}/route-mapp', [liveLocationController::class, 'getRouteMap'])->name('customer.booking.route');
     Route::get('/booking/{id}/resubmit-dataa', [liveLocationController::class, 'getResubmitData'])->name('customer.booking.resubmit.data');
    Route::post('/resubmit-bookingg', [liveLocationController::class, 'resubmitBooking'])->name('resubmit.booking');
});

















// ==================== REVIEW ROUTES ====================
Route::post('/customer/review', [App\Http\Controllers\ReviewController::class, 'store'])->name('review.store');
Route::get('/customer/booking/{bookingId}/reviews', [App\Http\Controllers\ReviewController::class, 'getBookingReviews']);
Route::put('/customer/review/{reviewId}', [App\Http\Controllers\ReviewController::class, 'update']);
Route::delete('/customer/review/{reviewId}', [App\Http\Controllers\ReviewController::class, 'destroy']);

// ==================== COMPLAINT ROUTES ====================
Route::post('/customer/complaint', [App\Http\Controllers\ComplaintController::class, 'store'])->name('complaint.store');
Route::get('/customer/my-complaints', [App\Http\Controllers\ComplaintController::class, 'getMyComplaints']);
Route::get('/customer/booking/{bookingId}/complaints', [App\Http\Controllers\ComplaintController::class, 'getBookingComplaints']);
Route::get('/customer/complaint/{complaintId}', [App\Http\Controllers\ComplaintController::class, 'show']);
Route::delete('/customer/complaint/{complaintId}/cancel', [App\Http\Controllers\ComplaintController::class, 'cancel']);






// for mybooking tab in provider
// Provider (Vehicle Owner) Routes
Route::prefix('provider')->group(function() {
    Route::get('/my-bookings', [UserrController::class, 'providerBookings'])->name('provider.bookings');
    
    // AJAX Routes
    Route::get('/bookings-data', [UserrController::class, 'getProviderBookingsData'])->name('provider.bookings.data');
    Route::get('/booking/{id}/details', [UserrController::class, 'getProviderBookingDetails'])->name('provider.booking.details');
    Route::get('/booking/{id}/tracking', [UserrController::class, 'getProviderBookingTracking'])->name('provider.booking.tracking');
    Route::get('/booking/{id}/reviews', [UserrController::class, 'getProviderBookingReviews'])->name('provider.booking.reviews');
    Route::get('/booking/{id}/complaints', [UserrController::class, 'getProviderBookingComplaints'])->name('provider.booking.complaints');
    
    // Action Routes
    Route::post('/booking/{id}/accept', [UserrController::class, 'acceptBooking'])->name('provider.booking.accept');
    Route::post('/booking/{id}/reject', [UserrController::class, 'rejectBooking'])->name('provider.booking.reject');
    Route::post('/booking/{id}/complete', [UserrController::class, 'completeBooking'])->name('provider.booking.complete');
});




// for allbooking for admin

// Add these routes to your existing routes file

// Admin Booking Routes
Route::prefix('admin')->group(function () {
    // Main view - using your existing adminlogin method
   
    
    // See all bookings page
    Route::get('/see-bookings', [AdminController::class, 'adminSeeBookings'])->name('admin.see-bookings');
    
    // AJAX Routes for bookings
    Route::get('/bookings-data', [AdminController::class, 'getAdminBookingsData'])->name('admin.bookings.data');
    Route::get('/booking/{id}/details', [AdminController::class, 'getAdminBookingDetails'])->name('admin.booking.details');
    Route::get('/booking/{id}/tracking', [AdminController::class, 'getAdminBookingTracking'])->name('admin.booking.tracking');
    Route::get('/booking/{id}/reviews', [AdminController::class, 'getAdminBookingReviews'])->name('admin.booking.reviews');
    Route::get('/booking/{id}/complaints', [AdminController::class, 'getAdminBookingComplaints'])->name('admin.booking.complaints');
    Route::get('/booking/{id}/payment', [AdminController::class, 'getAdminBookingPayment'])->name('admin.booking.payment');
});













// Admin complaint dkh skhta ha 

// Admin Complaint Routes
Route::prefix('admin')->group(function() {
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('admin.complaints');
    Route::get('/complaints/data', [AdminController::class, 'getComplaintsData'])->name('admin.complaints.data');
    Route::get('/complaints/{id}/details', [AdminController::class, 'getComplaintDetails'])->name('admin.complaint.details');
    Route::post('/complaints/{id}/resolve', [AdminController::class, 'resolveComplaint'])->name('admin.complaint.resolve');
    Route::post('/complaints/{id}/notify-owner', [AdminController::class, 'notifyVehicleOwner'])->name('admin.complaint.notify');
    Route::post('/complaints/{id}/notify-customer', [AdminController::class, 'notifyCustomerResolved'])->name('admin.complaint.notify-customer');
});





// Add this to your routes file with the other admin routes for seeing reviews
Route::prefix('admin')->group(function() {
    // ... existing routes ...
    Route::get('/ratings-reviews', [ReviewController::class, 'ratingsReviews'])->name('admin.ratings-reviews');
    
    Route::get('/ratings-reviews/data', [ReviewController::class, 'getRatingsReviewsData'])->name('admin.ratings-reviews.data');
    Route::post('/reviews/{id}/toggle-status', [ReviewController::class, 'toggleReviewStatus'])->name('admin.review.toggle-status');
    Route::delete('/reviews/{id}', [ReviewController::class, 'deleteReview'])->name('admin.review.delete');
});



//  admin for seeing users
// Admin Users Management Routes
Route::get('/admin/users', [AdminController::class, 'seeUsers'])->name('admin.users');

Route::get('/admin/customer/{id}/details', [AdminController::class, 'getCustomerDetails']);
Route::get('/admin/vehicle/{id}/details', [AdminController::class, 'getVehicleDetails']);
Route::delete('/admin/user/{id}/delete', [AdminController::class, 'deleteUser']);



// meassge conversation routes
// Message Routes
Route::get('/messages', [App\Http\Controllers\MessageController::class, 'getConversations'])->name('messages.conversations');
Route::get('/messages/get/{userId}', [App\Http\Controllers\MessageController::class, 'getMessages'])->name('messages.get');
Route::post('/messages/send', [App\Http\Controllers\MessageController::class, 'sendMessage'])->name('messages.send');
Route::post('/messages/start-conversation', [App\Http\Controllers\MessageController::class, 'startConversation'])->name('messages.start');
Route::get('/messages/unread-count', [App\Http\Controllers\MessageController::class, 'getUnreadCount'])->name('messages.unread');














// Payment Routes
Route::prefix('payment')->middleware('auth.session')->group(function () {
    Route::get('/{bookingId}', [PaymentController::class, 'showPaymentPage'])->name('payment.show');
    Route::post('/process/{bookingId}', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/success/{bookingId}', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/failure/{bookingId}', [PaymentController::class, 'paymentFailure'])->name('payment.failure');
    Route::get('/retry/{bookingId}', [PaymentController::class, 'retryPayment'])->name('payment.retry');
    Route::get('/status/{bookingId}', [PaymentController::class, 'getPaymentStatus'])->name('payment.status');
});

// Payment webhook (no auth needed for webhook)
Route::post('/payment-webhook', [PaymentController::class, 'paymentWebhook'])->name('payment.webhook');








// Chatbot Routes (place inside your authenticated routes group)



use App\Http\Controllers\ChatbotController;

// Chatbot Routes
Route::post('/chatbot/message', [ChatbotController::class, 'processMessage'])->name('chatbot.message');
Route::post('/chatbot/clear', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
Route::get('/chatbot/test', [ChatbotController::class, 'testConnection'])->name('chatbot.test');
Route::get('/chatbot/history', [ChatbotController::class, 'getChatHistory'])->name('chatbot.history');
use App\Services\GeminiService;

































// Provider routes - live location share karna
Route::post('/booking/{id}/start-location-sharing', [BookingController::class, 'startLocationSharing'])->name('booking.start.location');
Route::post('/booking/{id}/stop-location-sharing', [BookingController::class, 'stopLocationSharing'])->name('booking.stop.location');
Route::post('/booking/{id}/update-live-location', [BookingController::class, 'updateLiveLocation'])->name('booking.update.location');

// Customer route - live location dekhna
Route::get('/booking/{id}/get-live-location', [BookingController::class, 'getLiveLocation'])->name('booking.get.location');




// provider checking review
Route::get('/provider/ratings-reviews', [ReviewController::class, 'Provider_ratingsReviews'])->name('provider.ratings-reviews');






use App\Http\Controllers\ProviderChatbotController;
 
// Provider Chatbot Routes (no auth middleware — session check controller mein hai)
Route::post('/provider/chatbot/message',  [ProviderChatbotController::class, 'processMessage'])->name('provider.chatbot.message');
Route::post('/provider/chatbot/clear',    [ProviderChatbotController::class, 'clearHistory'])->name('provider.chatbot.clear');
Route::get('/provider/chatbot/history',   [ProviderChatbotController::class, 'getChatHistory'])->name('provider.chatbot.history');
Route::get('/provider/chatbot/test',      [ProviderChatbotController::class, 'testConnection'])->name('provider.chatbot.test');
Route::post('/provider/chatbot/extract-smartcard', [ProviderChatbotController::class, 'extractSmartCard'])->name('provider.chatbot.extract.smartcard');



use App\Http\Controllers\FraudController;
Route::get('/fraudpending', [VehicleController::class, 'fraudpendingvehicle'])->name('fraud.pendingVehicles');



 


// Analyze single vehicle (without AI smartcard extraction — quick check)
Route::post('/fraud/analyze/{id}', [FraudController::class, 'analyzeVehicle'])->name('fraud.analyze');
 
// Analyze + Extract smartcard via AI Vision (full check)
Route::post('/fraud/extract-analyze/{id}', [FraudController::class, 'extractAndAnalyze'])->name('fraud.extractAndAnalyze');
 
// Analyze all pending vehicles at once
Route::post('/fraud/analyze-all', [FraudController::class, 'analyzeAllPending'])->name('fraud.analyzeAll');
 
// Manual override
Route::post('/fraud/mark-fraud/{id}', [FraudController::class, 'markFraud'])->name('fraud.markFraud');
Route::post('/fraud/mark-not-fraud/{id}', [FraudController::class, 'markNotFraud'])->name('fraud.markNotFraud');











// Ai reviews for admin
use App\Http\Controllers\AIReviewController;

Route::prefix('admin')->group(function() {
    Route::get('/ai-reviews',              [AIReviewController::class, 'index'])->name('admin.ai-reviews');
    Route::get('/ai-reviews/data',         [AIReviewController::class, 'getCategorizedReviews'])->name('admin.ai-reviews.data');
    Route::post('/ai-reviews/re-analyze',  [AIReviewController::class, 'reAnalyze'])->name('admin.ai-reviews.reanalyze');
    
    // Yeh existing routes rehne dein as-is:
    Route::post('/reviews/{id}/toggle-status', [AIReviewController::class, 'toggleReviewStatus']);
    Route::delete('/reviews/{id}',             [AIReviewController::class, 'deleteReview']);
});


// customer see his complaint staus 

Route::get('/customer/my-complaints', [ComplaintController::class, 'myComplaints'])->name('customer.complaints');


// provider see his complaints
Route::get('/provider/complaints', [ComplaintController::class, 'complaints'])->name('provider.complaints');


// provider see his analytics
Route::get('/provider/analytics', [PaymentController::class, 'analytics'])->name('provider.analytics');


// customer see his analytics
Route::get('/customer/analytics', [PaymentController::class, 'customer_analytics'])->name('customer.analytics');



// Add this route for fetching vehicle reviews
Route::get('/vehicle/{id}/reviews', [ReviewController::class, 'getVehicleReviews'])->name('vehicle.reviews');





// Add these routes for dashboard data fetching
Route::get('/admin/pending-verifications-data', [UserrController::class, 'getPendingVerifications'])->name('admin.pending.verifications');
Route::get('/admin/recent-complaints-data', [UserrController::class, 'getRecentComplaints'])->name('admin.recent.complaints');




Route::post('/admin/add-vehicle', [UserrController::class, 'addVehicle'])->name('admin.add-vehicle');













































































// ═══════════════════════════════════════════════════════════
// TEMPORARY DIAGNOSTIC ROUTE — routes/web.php mein add karo
// Browser mein visit karo: /test-vision-api
// Test karne ke baad REMOVE kar dena!
// ═══════════════════════════════════════════════════════════

Route::get('/test-vision-api', function () {
    $results = [];

    // ── 1. Gemini keys check ─────────────────────────────
    $geminiKeys = [];
    foreach (['GEMINI_API_KEY_1','GEMINI_API_KEY_2','GEMINI_API_KEY_3','GEMINI_API_KEY_4'] as $k) {
        $val = env($k);
        if ($val && strlen(trim($val)) > 10) {
            $geminiKeys[] = substr(trim($val), 0, 8) . '...';
        }
    }
    $results['gemini_keys_found'] = count($geminiKeys);
    $results['gemini_keys']       = $geminiKeys;

    // ── 2. Groq key check ───────────────────────────────
    $groqKey = env('GROQ_API_KEY');
    $results['groq_key_found'] = !empty($groqKey);
    $results['groq_key_prefix'] = $groqKey ? substr($groqKey, 0, 8) . '...' : 'NOT SET';

    // ── 3. Test Gemini Vision (small 1x1 PNG) ───────────
    $tiny1x1png = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    foreach (['GEMINI_API_KEY_1', 'GEMINI_API_KEY_2'] as $envKey) {
        $apiKey = env($envKey);
        if (!$apiKey) {
            $results['gemini_test'][$envKey] = 'KEY NOT SET';
            continue;
        }

        $model = 'gemini-2.0-flash';
        $url   = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $resp = \Illuminate\Support\Facades\Http::timeout(20)->post($url, [
                'contents' => [
                    ['role' => 'user', 'parts' => [
                        ['inline_data' => ['mime_type' => 'image/png', 'data' => $tiny1x1png]],
                        ['text' => 'What color is this pixel? Reply in one word.'],
                    ]],
                ],
                'generationConfig' => ['maxOutputTokens' => 10],
            ]);

            $results['gemini_test'][$envKey] = [
                'status'  => $resp->status(),
                'ok'      => $resp->successful(),
                'snippet' => $resp->successful()
                    ? substr($resp->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'empty', 0, 80)
                    : substr($resp->body(), 0, 200),
            ];
        } catch (\Exception $e) {
            $results['gemini_test'][$envKey] = 'EXCEPTION: ' . $e->getMessage();
        }
    }

    // ── 4. Test Groq Vision ─────────────────────────────
    if ($groqKey) {
        foreach ([
            'meta-llama/llama-4-scout-17b-16e-instruct',
            'llama-3.2-11b-vision-preview',
        ] as $model) {
            try {
                $resp = \Illuminate\Support\Facades\Http::timeout(30)
                    ->withHeaders(['Authorization' => 'Bearer ' . $groqKey, 'Content-Type' => 'application/json'])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model'      => $model,
                        'max_tokens' => 20,
                        'messages'   => [
                            ['role' => 'user', 'content' => [
                                ['type' => 'image_url', 'image_url' => ['url' => "data:image/png;base64,{$tiny1x1png}"]],
                                ['type' => 'text', 'text' => 'What color is this pixel? One word.'],
                            ]],
                        ],
                    ]);

                $results['groq_test'][$model] = [
                    'status'  => $resp->status(),
                    'ok'      => $resp->successful(),
                    'snippet' => $resp->successful()
                        ? substr($resp->json()['choices'][0]['message']['content'] ?? 'empty', 0, 80)
                        : substr($resp->body(), 0, 200),
                ];
            } catch (\Exception $e) {
                $results['groq_test'][$model] = 'EXCEPTION: ' . $e->getMessage();
            }
        }
    } else {
        $results['groq_test'] = 'GROQ_API_KEY not set in .env';
    }

    // ── 5. Cache status ─────────────────────────────────
    $cacheKeys = [];
    foreach (['GEMINI_API_KEY_1','GEMINI_API_KEY_2','GEMINI_API_KEY_3','GEMINI_API_KEY_4'] as $envKey) {
        $val = env($envKey);
        if ($val) {
            $slot = 'gemini_key_quota_' . md5(trim($val));
            $cacheKeys[$envKey] = \Cache::has($slot) ? '⛔ CACHED AS EXHAUSTED' : '✅ Available';
        }
    }
    $results['cache_status'] = $cacheKeys;

    return response()->json($results, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});