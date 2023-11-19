<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Models\User;





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



//open for combo box
Route::get('/get-open-learning-dev-types', [App\Http\Controllers\OpenController::class, 'loadLearningDevTypes']);
Route::get('/get-open-specializations', [App\Http\Controllers\OpenController::class, 'loadSpecializations']);
Route::get('/get-open-cid-sub-roles', [App\Http\Controllers\OpenController::class, 'loadSubRoles']);
Route::get('/load-open-degrees', [App\Http\Controllers\OpenController::class, 'loadDegrees']);



Auth::routes([
    'login' => 'false'
]);

Route::get('/load-user', function(){
    if(Auth::check()){
        return Auth::user();
    }
});




Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/sign-up', [App\Http\Controllers\SignUpController::class, 'index']);
Route::post('/sign-up', [App\Http\Controllers\SignUpController::class, 'store']);


Route::get('/get-user/{id}', [App\Http\Controllers\OpenUserController::class, 'getUser']);




//ADDRESS
Route::get('/load-provinces', [App\Http\Controllers\AddressController::class, 'loadProvinces']);
Route::get('/load-cities', [App\Http\Controllers\AddressController::class, 'loadCities']);
Route::get('/load-barangays', [App\Http\Controllers\AddressController::class, 'loadBarangays']);


/*     ADMINSITRATOR          */


Route::middleware(['auth', 'admin'])->group(function () {

    Route::resource('/admin/dashboard', App\Http\Controllers\Administrator\AdminDashboardController::class);


    Route::resource('/admin/events', App\Http\Controllers\Administrator\AdminEventController::class);
    Route::post('/admin/events-update/{id}', [App\Http\Controllers\Administrator\AdminEventController::class, 'updateEvent']);
    Route::get('/admin/get-events', [App\Http\Controllers\Administrator\AdminEventController::class, 'getData']);


    Route::resource('/admin/training-developments', App\Http\Controllers\Administrator\AdminTrainingDevelopmentController::class);
    Route::post('/admin/training-developments/{id}', [App\Http\Controllers\Administrator\AdminTrainingDevelopmentController::class, 'updateTrainingDev']);
    Route::get('/admin/get-training-developments', [App\Http\Controllers\Administrator\AdminTrainingDevelopmentController::class, 'getData']);


    Route::resource('/admin/users', App\Http\Controllers\Administrator\UserController::class);
    Route::get('/admin/get-users', [App\Http\Controllers\Administrator\UserController::class, 'getUsers']);




    Route::resource('/learning-dev', App\Http\Controllers\LearningDevelopmentTypeController::class);
    Route::get('/get-learning-dev', [App\Http\Controllers\LearningDevelopmentTypeController::class, 'getLearningDevelopmentTypes']);

    Route::resource('/specialization', App\Http\Controllers\Administrator\SpecializationController::class);
    Route::get('/get-specialization', [App\Http\Controllers\Administrator\SpecializationController::class, 'getSpecialization']);

    Route::resource('/cid-sub-role', App\Http\Controllers\Administrator\CidSubRoleController::class);
    Route::get('/get-cid-sub-roles', [App\Http\Controllers\Administrator\CidSubRoleController::class, 'getCidSubRoles']);




});


/*     ADMINSITRATOR          */


/*     AUTH          */

Route::middleware(['auth'])->group(function () {
    //routes that only need an authentication
    Route::get('/change-password',[App\Http\Controllers\Auth\ChangePasswordController::class, 'index']);;
    Route::post('/reset-password',[App\Http\Controllers\Auth\ChangePasswordController::class, 'changePassword']);
});




// FACULTY ROUTES
Route::get('/pending-page', function(){
    return view('faculty.pending-page');
});


Route::middleware(['auth', 'employee'])->group(function () {
    Route::resource('/employee/dashboard', App\Http\Controllers\Faculty\EmployeeDashboardController::class);
    Route::get('/employee/get-posted-events', [App\Http\Controllers\Faculty\EmployeeDashboardController::class, 'getPostedEvents']);
    Route::post('/employee/dashboard-upload-attachment', [App\Http\Controllers\Faculty\EmployeeDashboardController::class, 'uploadAttachment']);
    Route::get('/employee/get-by-user-event-attachment', [App\Http\Controllers\Faculty\EmployeeDashboardController::class, 'getByUserEventAttachment']);

    Route::resource('/employee/personal-data-sheet', App\Http\Controllers\Faculty\EmployeePDSController::class);
    
    Route::post('/seminar-im-in', [App\Http\Controllers\Faculty\FacultyHomeController::class, 'imIn']);


    Route::resource('/faculty/educational-backgrounds', App\Http\Controllers\Faculty\FacultyEducationalBackgroundController::class);
    Route::resource('/faculty/children', App\Http\Controllers\Faculty\FacultyChildController::class);
    Route::resource('/faculty/eligibilities', App\Http\Controllers\Faculty\FacultyCSEController::class);
    Route::resource('/faculty/work-experiences', App\Http\Controllers\Faculty\FacultyWorkExperienceController::class);
    Route::resource('/faculty/learning-developments', App\Http\Controllers\Faculty\FacultyLearningDevelopmentController::class);
    Route::resource('/faculty/other-informations', App\Http\Controllers\Faculty\FacultyOtherInformationController::class);

    Route::resource('/faculty/trainings-interventions', App\Http\Controllers\Faculty\TrainingInterventionController::class);
    Route::post('/faculty/trainings-interventions-update/{id}', [App\Http\Controllers\Faculty\TrainingInterventionController::class, 'update']);

    Route::get('/faculty/get-learning-trainings', [App\Http\Controllers\Faculty\TrainingInterventionController::class, 'getLearningTrainings']);

    //certificates
    Route::post('/faculty/upload-certificates/{id}', [App\Http\Controllers\Faculty\CertificateController::class, 'upload']);
    Route::delete('/faculty/remove-certificate/{id}', [App\Http\Controllers\Faculty\CertificateController::class, 'delete']);

});



Route::middleware(['auth', 'point_person'])->group(function () {
    Route::resource('/point-person/dashboard', App\Http\Controllers\PointPerson\PointPersonDashboarcController::class);
    
    Route::resource('/point-person/events', App\Http\Controllers\PointPerson\PointPersonEventController::class);
    Route::get('/point-person/get-events', [App\Http\Controllers\PointPerson\PointPersonEventController::class, 'getData']);
    Route::get('/point-person/events-view/{id}', [App\Http\Controllers\PointPerson\PointPersonEventController::class, 'eventView']);
    Route::post('/point-person/events-attachment-set-status', [App\Http\Controllers\PointPerson\PointPersonEventController::class, 'eventAttachmentSetStatus']);
    //load list of attendees base on event id
    Route::get('/point-person/load-attendees-events-view', [App\Http\Controllers\PointPerson\PointPersonEventController::class, 'loadAttendees']);
    

});
//HRLD
Route::resource('/hrld/home', App\Http\Controllers\Hrld\HrldHomeController::class);

//Route::get('/hrld/seminar-posts',[App\Http\Controllers\Hrld\HrldSeminarPostController::class. 'index']); YAWA KA PERIODA KA!!!!
Route::get('/hrld/seminar-posts', [App\Http\Controllers\Hrld\HrldSeminarPostController::class, 'index']);
Route::get('/hrld/get-seminars', [App\Http\Controllers\Hrld\HrldSeminarPostController::class, 'getSeminars']);
Route::delete('/hrld/seminar-posts/{id}', [App\Http\Controllers\Hrld\HrldSeminarPostController::class, 'destroy']);

Route::post('/hrld/seminar-posts-store', [App\Http\Controllers\Hrld\HrldSeminarPostController::class, 'store']);
Route::get('/hrld/get-seminar-posts/{id}', [App\Http\Controllers\Hrld\HrldSeminarPostController::class, 'show']);
Route::post('/hrld/seminar-posts-update/{id}', [App\Http\Controllers\Hrld\HrldSeminarPostController::class, 'update']);
Route::get('/hrld/posted-seminars', [App\Http\Controllers\Hrld\PostedSeminarController::class, 'index']);



Route::resource('/hrld/recommended-teachers', App\Http\Controllers\Hrld\RecommendedTeacherController::class);
Route::get('/hrld/get-recommended-teachers', [App\Http\Controllers\Hrld\RecommendedTeacherController::class, 'getRecommendedTeachers']);
Route::get('hrld/print-teacher-recommended/{id}', [App\Http\Controllers\Hrld\RecommendedTeacherController::class, 'printTeacher']);



Route::resource('/hrld/teacher-accounts', App\Http\Controllers\Hrld\HrldTeacherAccountController::class);
Route::get('/hrld/get-teacher-accounts', [App\Http\Controllers\Hrld\HrldTeacherAccountController::class, 'getTeacherAccounts']);
Route::post('/hrld/teacher-approve-account/{id}', [App\Http\Controllers\Hrld\HrldTeacherAccountController::class, 'approveAccount']);



//CID
Route::resource('/cid/home', App\Http\Controllers\Cid\CidHomeController::class);

Route::resource('/cid/seminar-list', App\Http\Controllers\Cid\CidSeminarController::class);
Route::get('/cid/get-seminar-posted-list', [App\Http\Controllers\Cid\CidSeminarController::class, 'getTeacherSeminars']);
Route::post('/cid/submit-rating/{id}', [App\Http\Controllers\Cid\CidSeminarController::class, 'updateRate']);


Route::get('/cid/recommended-candidates', [App\Http\Controllers\Cid\RecommendedCandidateController::class, 'index']);
Route::get('/generate-list', [App\Http\Controllers\Cid\RecommendedCandidateController::class, 'generateList']);
Route::get('/get-request-teacher/{id}', [App\Http\Controllers\Cid\RecommendedCandidateController::class, 'getRequestTeacher']);


Route::post('/cid/submit-teachers-list', [App\Http\Controllers\Cid\RecommendedCandidateController::class, 'store']);
Route::get('/cid/get-seminar-specialization-list', [App\Http\Controllers\Cid\RecommendedCandidateController::class, 'getSeminarSpecializationList']);


//DEPED
Route::resource('/deped/home', App\Http\Controllers\Deped\DepedHomeController::class);
Route::get('/deped/teacher-list', [App\Http\Controllers\Deped\DepedTeacherListController::class, 'index']);




Route::get('/session', function(){
    return Session::all();
});


Route::get('/before', function(){
    //return Session::all();


    $beforeDay = date('Y-m-d H:i', strtotime('+24 hour', strtotime(date('Y-m-d H:i'))));

    $data = \DB::table('appointments')
        ->where('appoint_date', date('Y-m-d', strtotime($beforeDay)))
        ->where('appoint_time', date('H:i', strtotime($beforeDay)))
        ->where('is_notify', 0)
        ->get();

    foreach($data as $i){

        $user = User::find($i->user_id);

        $msg = 'Hi '.$user->lname . ', ' . $user->fname . ', this is just a reminder that you have an appointment tomorrow. Your ref no. is: ' . $i->qr_code . '.';
        try{
            Http::withHeaders([
                'Content-Type' => 'text/plain'
            ])->post('http://'. env('IP_SMS_GATEWAY') .'/services/api/messaging?Message='.$msg.'&To='.$user->contact_no.'&Slot=1', []);
        }catch(Exception $e){} //just hide the error

        $appoint = Appointment::find($i->appointment_id);
        $appoint->is_notify = 1;
        $appoint->save();
    }

    //$beforeDay = date($today, strtotime('-1 day'));
    return $data;
});




Route::get('/collect', function(){
    return $collection = collect([1, 2, 3]);
});


Route::get('/applogout', function(Request $req){
    \Auth::logout();
    $req->session()->invalidate();
    $req->session()->regenerateToken();


});
