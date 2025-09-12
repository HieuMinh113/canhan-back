 <?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


use App\Http\Controllers\{AuthController, UserController, ProductController, 
    HotelController, NewsController, BookingController, AppointmentController, 
    ContactController, FeedbackController, BookingHotelController, CartController,
    DashBoard,ServiceController,BillController,PaymentController,BannerController,
     ProfileController, WorkScheduleController,PetController,
    CouponController, LoyaltyController,WeatherController};
use App\Http\Controllers\Auth\FogotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/doctor/{id}', [UserController::class, 'showDoctor']);
    Route::get('/staff/{id}', [UserController::class, 'showStaff']);

    Route::get('/products/{id}', [ProductController::class, 'show']);

    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{id}', [NewsController::class, 'show']);

    Route::get('/hotel', [HotelController::class, 'index']);
    Route::get('/hotel/{id}', [HotelController::class, 'show']);

    Route::get('/doctor-booking', [BookingController::class, 'doctor']);
    Route::get('/booking', [BookingController::class, 'index']);
    Route::get('/booking/{id}', [BookingController::class, 'show']);
    Route::get('/doctor/{id}/booking', [BookingController::class, 'getBookings']);

    Route::get('/staff-appointment', [AppointmentController::class, 'staff']);
    Route::get('/service-appointment', [AppointmentController::class, 'service']);
    Route::get('/appointment', [AppointmentController::class, 'index']);
    Route::get('/appointment/{id}', [AppointmentController::class, 'show']);
    Route::get('/staff/{id}/appointment', [AppointmentController::class, 'getAppointments']);

    Route::get('/contact', [ContactController::class, 'index']);
    Route::post('/contact', [ContactController::class, 'store']);
    Route::get('/commission-staff',[AppointmentController::class,'commissionstaff']);
    Route::get('/commission-doctor',[BookingController::class,'commissionsdoctor']);

    // Route::get('/feedback', [FeedbackController::class, 'index']);
    // Route::get('/user', [FeedbackController::class, 'allUsers']);
    // Route::get('/feedback/{id}', [FeedbackController::class, 'show']);

    Route::get('/bookinghotel', [BookingHotelController::class, 'index']);
    Route::get('/bookinghotel/{id}', [BookingHotelController::class, 'show']);

    

    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/uploadImage', [ProductController::class, 'uploadImage']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::post('/news', [NewsController::class, 'store']);
    Route::post('/newsuploadImage', [NewsController::class, 'uploadImage']);
    Route::put('/news/{id}', [NewsController::class, 'update']);
    Route::delete('/news/{id}', [NewsController::class, 'destroy']);

    Route::post('/hotel', [HotelController::class, 'store']);
    Route::post('/hoteluploadImage', [HotelController::class, 'uploadImage']);
    Route::put('/hotel/{id}', [HotelController::class, 'update']);
    Route::delete('/hotel/{id}', [HotelController::class, 'destroy']);

    Route::post('/booking', [BookingController::class, 'store']);
    Route::put('/booking/{id}', [BookingController::class, 'update']);

    Route::post('/appointment', [AppointmentController::class, 'store']);
    Route::put('/appointment/{id}', [AppointmentController::class, 'update']);

    Route::post('/bookinghotel', [BookingHotelController::class, 'store']);
    Route::put('/bookinghotel/{id}', [BookingHotelController::class, 'update']);

    Route::delete('/contact/{id}', [ContactController::class, 'destroy']);
    Route::post('/feedback', [FeedbackController::class, 'store']);
    Route::put('/feedback/{id}', [FeedbackController::class, 'update']);
    Route::post('/forgot',[FogotPasswordController::class,'fogot']);
    Route::post('/reset',[ResetPasswordController::class,'reset']);
    Route::get('/dashboard',[DashBoard::class,'index']);
    Route::get('/service',[ServiceController::class,'index']);
    Route::post('/service',[ServiceController::class,'store']);
    Route::put('/service/{id}', [ServiceController::class, 'update']);
    Route::delete('/service/{id}', [ServiceController::class, 'destroy']);
    Route::get('/service/{id}', [ServiceController::class, 'showservice']);
    Route::put('/bill/{id}',[BillController::class,'update']);
    Route::get('/commission',[DashBoard::class,'commissionsAll']);
    Route::get('/total',[DashBoard::class,'total']);


    Route::get('/pet/{id}',[PetController::class,'show']);
    Route::post('/pet',[PetController::class,'store']);
    Route::put('/pet/{id}',[PetController::class,'update']);
    Route::delete('/pet/{id}',[PetController::class,'destroy']);
    Route::post('/petuploadImage', [BannerController::class, 'uploadImage']);

    Route::get('/banners',[BannerController::class,'index']);
    Route::post('/banners',[BannerController::class,'store']);
    Route::put('/banners/{id}', [BannerController::class, 'update']);
    Route::delete('/banners/{id}', [BannerController::class, 'destroy']);
    Route::post('/banneruploadImage', [BannerController::class, 'uploadImage']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart/getDB', [CartController::class, 'getCartDB']);
    Route::post('/cart/addDB', [CartController::class, 'addToCartDB']);
    Route::put('/cart/updateDB', [CartController::class, 'updateCartDB']);
    Route::delete('/cart/deleteDB', [CartController::class, 'removeFromCartDB']);
    Route::delete('/cart/DB/clear', [CartController::class, 'clearCartDB']);
    Route::post('/cart/syn', [CartController::class, 'syn']);
    Route::get('/profile',[ProfileController::class,'show']);
    Route::post('/profile/uploadImage', [ProfileController::class, 'uploadImage']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::post('/work-schedule', [WorkScheduleController::class, 'store']);
    Route::get('/work-schedule', [WorkScheduleController::class, 'index']);

    Route::get('/feedback', [FeedbackController::class, 'index']);
    Route::get('/user', [FeedbackController::class, 'allUsers']);
    Route::get('/feedback/{id}', [FeedbackController::class, 'show']);

    Route::get('/bill', [BillController::class, 'index']);
    Route::post('/bill', [BillController::class, 'store']);
    Route::get('/bills/{id}', [BillController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/loyalty',[LoyaltyController::class,'me']);
    Route::get('/loyaltyy',[LoyaltyController::class,'index']);
    Route::get('loyalty-by-email',[LoyaltyController::class,'getemail']);
});
    Route::post('/bill-add',[BillController::class,'add']);

    Route::get('/coupon',[CouponController::class,'index']);
    Route::post('/coupon',[CouponController::class,'store']);
    Route::put('/coupon/{id}', [CouponController::class, 'update']);
    Route::delete('/coupon/{id}', [CouponController::class, 'destroy']);
    Route::post('/apply-coupon',[CouponController::class,'apply']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/pet',[PetController::class,'index']);
    Route::get('/breed',[PetController::class,'getbreed']);

    Route::get('/countproduct',[ProductController::class,'countproductstock']);
    Route::get('/countpet',[PetController::class,'countpetstock']);

    Route::post('/momo-payment', [PaymentController::class, 'momo_payment']);
    Route::post('/momo-callback', [PaymentController::class, 'momoCallback']);

    Route::get('/bestseller-product',[DashBoard::class,'bestsellerproduct']);
    Route::get('/bestseller-pet',[DashBoard::class,'bestsellerpet']);

    Route::get('/bill-search',[BillController::class,'indexx']);

    Route::get('/countproduct',[DashBoard::class,'totalproduct']);
    Route::get('/countpet',[DashBoard::class,'totalpet']);
    Route::get('/countuser',[DashBoard::class,'totaluser']);
    Route::get('/counthotel',[DashBoard::class,'totalhotel']);
    Route::get('/countbill',[DashBoard::class,'totalbill']);

    Route::get('/weather',[WeatherController::class,'getweather']);
    Route::get('/weather-suggest',[WeatherController::class,'suggestProducts']);


