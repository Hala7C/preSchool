<?php

use App\Http\Controllers\API\ClassController;
use App\Http\Controllers\API\FeesStudentController;
use App\Http\Controllers\API\LevelController;
use App\Http\Controllers\API\SubjectController as APISubjectController;
use App\Models\FeesConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    AdvertisementController,
    AnswerController,
    AssignStudentsToClassController,
    AuthController,
    EmployeeController,
    StudentController,
    BusController,
    DeviceTokenController,
    UserController,
    StudentFeesController,
    VRPPython,
    SchoolController,
    CategoryController,
    ExamController,
    HomeworkController,
    LessonController,
    QuestionController,
    QuizeController,
    Report,
    TeacherController,
    SubjectController,
    StudentBusController,
    VRPCopyController
};
use App\Http\Middleware\Employee;
use App\Models\Advertisement;
use App\Models\Bus;
use App\Models\Student;
use App\Models\BusTrack;
use App\Models\Category;
use App\Models\Exam;
use App\Models\Question;
use App\Models\StudentFees;
use App\Models\Subject;
use Laravel\Jetstream\Rules\Role;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);




Route::middleware([
    'auth:sanctum',
    'isTeacher'
])->group(function () {

    //questions
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::post('/questions/{id}', [QuestionController::class, 'update']);
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy']);
    Route::get('/questions/{id}', [QuestionController::class, 'show']);
    Route::get('/questions', [QuestionController::class, 'index']);


    //categories
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::post('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::get('/categories/teacher/{id}', [CategoryController::class, 'categoryQuestions']);




    Route::get('/teacher/class', [TeacherController::class, 'teacherClasess']);

    Route::get('/teacher/subjects', [TeacherController::class, 'OwnteacherSubjects']); //:)





    ////////Exam
    Route::get('/exams/{sID}', [ExamController::class, 'index'])->middleware('TeacherSubject');
    Route::post('/exams', [ExamController::class, 'store'])->middleware('addExam');;
    Route::post('/exams/{id}', [ExamController::class, 'update'])->middleware('addExam');;
    Route::delete('/exams/{id}', [ExamController::class, 'destroy']); //validate if auth teacher own this exam in controller



    //////abs
    Route::post('/abs', [App\Http\Controllers\API\AbsenceController::class, 'registerjson']);



    Route::get('/subject/class/{id}', [TeacherController::class, 'teacherClassinXSubject']); /////////////

    //lesson
    Route::post('/lesson', [LessonController::class, 'store']); /////////////////
    Route::post('/lesson/{id}', [LessonController::class, 'update']); ////////////
    Route::delete('/lesson/{id}', [LessonController::class, 'destroy']); ///////////////

    Route::get('/lesson/homeworks/{id}', [LessonController::class, 'homeworks']); /////////////
    Route::get('/lesson/change/stauts/{cID}/{lID}', [LessonController::class, 'lessonStatus']); ////////
    Route::get('/lesson/send/homework/{id}', [LessonController::class, 'sendHomework']); ///wait for ads and notification to finish



    //homeworks
    Route::post('/homework', [HomeworkController::class, 'store']); ///////////////
    Route::post('/homework/{id}', [HomeworkController::class, 'update']); /////////
    Route::delete('/homework/{id}', [HomeworkController::class, 'destroy']); //////////////




    Route::get('/subject/lessons/{sid}', [SubjectController::class, 'subjectLessons']); //

    //////templates
    Route::get('teacher/templates', [App\Http\Controllers\API\TemplateController::class, 'teacherTemplates'])->middleware('auth:sanctum');
});

Route::middleware([
    'auth:sanctum',

])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    // Route::post('/profile/{id}/updatepassword', [AuthController::class, 'updatepassword']);
    Route::post('/profile/{id}', [AuthController::class, 'updateProfile']);


    Route::post('/web/profile/{id}', [AuthController::class, 'updateProfileWeb']);

    //  Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    ///quizes for student
    Route::get('/categories', [CategoryController::class, 'index'])->middleware('role:user,teacher');
    Route::get('/class/students/{classID}', [AssignStudentsToClassController::class, 'show'])->middleware('role:employee,teacher');; //
    Route::get('/teacher/classes/{tid}', [TeacherController::class, 'teacherClases'])->middleware('role:employee');; //:)
    Route::get('/student/{id}',    [StudentController::class, 'show'])->middleware('role:admin,teacher');
    Route::get('supervisor/bus/students/{id}', [BusController::class, 'SupervisorAllStudent'])->middleware('role:admin');
    Route::get('/buses/students', [BusController::class, 'allBusStudent'])->middleware('role:employee,user,admin,bus_supervisor');

    //recruit
    Route::post('/employee/store',  [EmployeeController::class, 'store'])->middleware('role:employee,admin');;
    Route::get('/employees',        [EmployeeController::class, 'index'])->middleware('role:employee,manager,admin');
    Route::get('/employee/{id}',    [EmployeeController::class, 'show'])->middleware('role:employee,admin');
    Route::post('/employee/{id}',   [EmployeeController::class, 'update'])->middleware('role:employee,admin');
    Route::delete('/employee/{id}', [EmployeeController::class, 'destroy'])->middleware('role:employee,admin');


    Route::get('/students',        [StudentController::class, 'index'])->middleware('role:employee,manager');;
});

Route::post('/update/student/location/{id}', [StudentController::class, 'updateStudentLocation'])->middleware(['auth:sanctum', 'isBusRegistry']);

//Route::middleware(['auth:sanctum', 'isManager'])->group(function () {

//});
Route::get('/vrp', [VRPPython::class, 'testPythonScript'])->middleware(['isBusExist', 'BusCapacities']);

Route::middleware([
    'auth:sanctum',
    'isEmployee',
])->group(function () {
    Route::get('/supervisors',  [BusController::class, 'allBusSupervisor']);
    Route::post('/buses/store', [BusController::class, 'store']);
    Route::get('/buses',         [BusController::class, 'index']);
    Route::post('/buses/{id}',   [BusController::class, 'update']);
    Route::delete('/buses/{id}', [BusController::class, 'destroy']);
    Route::get('/students/without/bus', [BusController::class, 'allStudentWithoutBus']); //
    // Route::get('/vrp', [VRPPython::class, 'testPythonScript'])->middleware(['isStudentDistributed', 'isBusExist', 'BusCapacities']);

    Route::get('/day/exams', [ExamController::class, 'TodayExam']);

    ///assign
    // Route::post('/remove/students/{classID}',[AssignStudentsToClassController::class,'deleteStudentFromClass']);//
    Route::delete('/remove/students/{sid}/{classID}', [AssignStudentsToClassController::class, 'deleteStudentFromClass']); //
    Route::post('/assign/student/{classID}', [AssignStudentsToClassController::class, 'store']); //:)
    Route::get('/unassignes/students', [AssignStudentsToClassController::class, 'StudentNotAssigned']); //


    Route::apiResource('classes',  App\Http\Controllers\API\ClassController::class);
    Route::apiResource('levels',   App\Http\Controllers\API\LevelController::class);
    Route::apiResource('subject',  SubjectController::class);



    //////
    //teacher assignment
    Route::post('/assign/teacher', [TeacherController::class, 'assignTeacherToClassWithSubjects']); //:)
    Route::get('/teachers', [TeacherController::class, 'allTeacher']); //:)
    Route::get('/teacher/subjects/{tid}', [TeacherController::class, 'teacherSubjects']); //:)
    Route::get('/subject/teacher/{sid}', [TeacherController::class, 'SubjectTeachers']); //:)
    Route::get('/class/teacher/{cid}', [TeacherController::class, 'ClassTeachers']); //:)
    Route::get('/teacher/subject/in/class/{cid}/{tid}', [TeacherController::class, 'teacherSubjectinXClass']); //


    Route::delete('/teacher/subject/in/class/{cid}/{tid}/{sid}', [TeacherController::class, 'unAssignsubjectFromTeacher']); //

    Route::delete('/teacher/all/subject/in/class/{cid}/{tid}', [TeacherController::class, 'unAssignAllsubjectFromTeacher']); //

    Route::get('/all/data', [TeacherController::class, 'allAssignDate']); //:)

    //registry
    Route::post('/student/store', [StudentController::class, 'store1']);
    Route::post('/student/{id}',   [StudentController::class, 'update']);
    Route::delete('/student/{id}', [StudentController::class, 'destroy']);


    ///////////fees

    Route::post('/studentFees/store', [StudentFeesController::class, 'store'])->middleware('InitYearConfig'); ///
    Route::get('/studentFees/{id}', [StudentFeesController::class, 'index'])->middleware('InitYearConfig'); ///

    Route::get('/unpaided/studentFees', [StudentFeesController::class, 'unPaidedStudent'])->middleware('InitYearConfig'); ///
    Route::get('/paided/studentFees', [StudentFeesController::class, 'PaidedStudent'])->middleware('InitYearConfig');; ///
    Route::get('/complete/studentFees', [StudentFeesController::class, 'CompletePaidedStudent'])->middleware('InitYearConfig');; ///
    Route::get('/latePaymentStudents/studentFees', [StudentFeesController::class, 'latePaymentStudents'])->middleware('InitYearConfig'); ///

    Route::get('/allStudent/studentFees', [StudentFeesController::class, 'allStudentInfo'])->middleware('InitYearConfig');; ///


    Route::get('/send/notification', [StudentFeesController::class, 'getAllLateStudentNotifications']);
    Route::delete('/remove/notification/{id}', [StudentFeesController::class, 'removeNotification']);

    ///////abs
    Route::get('/abs', [App\Http\Controllers\API\AbsenceController::class, 'index']);
    Route::put('/abs/{id}', [App\Http\Controllers\API\AbsenceController::class, 'updateJustification']);
    Route::delete('/abs/{id}', [App\Http\Controllers\API\AbsenceController::class, 'deleteStudentFromAbsence']);

    ///////template
    Route::post('template/store', [App\Http\Controllers\API\TemplateController::class, 'store'])->middleware('auth:sanctum');
    Route::put('template/updateStatus/{id}', [App\Http\Controllers\API\TemplateController::class, 'updateStatus'])->middleware('auth:sanctum');
    Route::delete('template/delete/{id}', [App\Http\Controllers\API\TemplateController::class, 'destroy'])->middleware('auth:sanctum');
    Route::get('manager/templates', [App\Http\Controllers\API\TemplateController::class, 'index'])->middleware('auth:sanctum');
});

Route::middleware([
    'auth:sanctum',
    'isAdmin',
])->group(function () {



    //user management
    // Route::post('/user/store', [App\Http\Controllers\API\UserController::class, 'store']);
    Route::get('/users',        [UserController::class, 'index']);
    Route::post('/user/{id}',   [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);


    //school management
    // Route::get('/school/{id}',        [SchoolController::class, 'show']);
    Route::post('/school/{id}',   [SchoolController::class, 'update']);
    Route::post('/school/update/location/{id}',   [SchoolController::class, 'updatelocation']);
});

Route::middleware([
    'auth:sanctum',
    // 'isAdminOrUser',
])->group(function () {
    Route::get('/school/{id}',        [SchoolController::class, 'show']);
});
Route::middleware([
    'auth:sanctum',
    'isStudent',
])->group(function () {
    Route::get('/categories/Student/{id}', [CategoryController::class, 'categoryQuestionsStudent']);
    Route::get('/buses/students/{id}', [BusController::class, 'allStudent']);
});

Route::middleware([
    'auth:sanctum',
    'isManager',
])->group(function () {
    ///report
    Route::get('/report/monthlyFeesReport', [Report::class, 'getMonthlyFeesReport'])->middleware('role:employee,manager');;
    Route::get('/report/yearlyFeesReport', [Report::class, 'getYearlyFeesReport'])->middleware('role:employee,manager');;
    Route::get('/report', [Report::class, 'getAllReport'])->middleware('role:employee,manager');

    ////config

    Route::apiResource('config',   App\Http\Controllers\API\FeesStudentController::class)->middleware('role:employee,manager'); //
    Route::apiResource('yearFees',   App\Http\Controllers\API\YearConfigController::class)->middleware('role:employee,manager'); //


});

/**
 *
 *                    **********Class Route **********
 *
 *   GET|HEAD  http://127.0.0.1:8000/api/classes           ==> classes.index
 *   POST      http://127.0.0.1:8000/api/classes           ==> classes.store
 *   GET|HEAD  http://127.0.0.1:8000/api/classes/{class}   ==> classes.show
 *   PUT|PATCH http://127.0.0.1:8000/api/classes/{class}   ==> classes.update
 *   DELETE    http://127.0.0.1:8000/api/classes/{class}   ==> classes.destroy
 *
 *                    **********Config Route **********
 *
 *   GET|HEAD  http://127.0.0.1:8000/api/config            ==> config.index
 *   POST      http://127.0.0.1:8000/api/config            ==> config.store
 *   GET|HEAD  http://127.0.0.1:8000/api/config/{config}   ==> config.show
 *   PUT|PATCH http://127.0.0.1:8000/api/config/{config}   ==> config.update
 *   DELETE    http://127.0.0.1:8000/api/config/{config}   ==> config.destroy
 *
 *                    **********Levels Route **********
 *
 *   GET|HEAD  http://127.0.0.1:8000/api/levels            ==> levels.index
 *   POST      http://127.0.0.1:8000/api/levels            ==> levels.store
 *   GET|HEAD  http://127.0.0.1:8000/api/levels/{level}    ==> levels.show
 *   PUT|PATCH http://127.0.0.1:8000/api/levels/{level}    ==> levels.update
 *   DELETE    http://127.0.0.1:8000/api/levels/{level}    ==> levels.destroy
 *
 *                    **********Subject Route **********
 *
 *   GET|HEAD  http://127.0.0.1:8000/api/subject           ==> subject.index
 *   POST      http://127.0.0.1:8000/api/subject           ==> subject.store
 *   GET|HEAD  http://127.0.0.1:8000/api/subject/{subject} ==> subject.show
 *   PUT|PATCH http://127.0.0.1:8000/api/subject/{subject} ==> subject.update
 *   DELETE    http://127.0.0.1:8000/api/subject/{subject} ==> subject.destroy
 */





////report


Route::get('/busTrack/show/{id}', [App\Http\Controllers\API\BusTrackingController::class, 'show']);
Route::put('/busTrack/{busTrack}', [App\Http\Controllers\API\BusTrackingController::class, 'update']);
Route::post('/device-token', [DeviceTokenController::class, "store"]);

//Route::post('/absjs', [App\Http\Controllers\API\AbsenceController::class, 'registerjson']);



Route::get('/busTrack/show/{id}', [App\Http\Controllers\API\BusTrackingController::class, 'show']);
Route::put('/busTrack/{busTrack}', [App\Http\Controllers\API\BusTrackingController::class, 'update']);


// Route::apiResource('categories',  CategoryController::class);
// Route::apiResource('answers',  AnswerController::class);
















//Assign students to class





////////////
// Route::get('/buses/studentss', [StudentBusController::class, 'allBusStudent']);
// Route::get('/buses/studentss/{id}', [StudentBusController::class, 'allStudent']);
// Route::get('/studentss',        [StudentController::class, 'indexCopy']);
// Route::get('/students/{id}',    [StudentController::class, 'showCopy']);
// Route::post('/updates/student/location/{id}', [StudentController::class, 'updateStudentLocationCopy']);
// Route::post('/update/student/time/{id}', [StudentController::class, 'updateStudentArrivalTimeCopy']);
// Route::get('/vrp', [VRPCopyController::class, 'testPythonScript']);


Route::get('/count/student', [DashboardController::class, 'getCountStudent']);
Route::get('/count/employee', [DashboardController::class, 'getCountEmployee']);
Route::get('/count/teacher', [DashboardController::class, 'getCountTeacher']);
Route::get('/count/bus', [DashboardController::class, 'getCountBus']);
Route::get('/feesRate', [DashboardController::class, 'getFeesRate']);
