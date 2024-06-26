<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CivilServiceEligibility;
use Illuminate\Http\Request;

class EmployeeCSEController extends Controller
{
    //
    public function __construct(){
        $this->middleware('auth');
    }


    public function destroy($id){
        CivilServiceEligibility::destroy($id);

        return response()->json([
            'status' => 'deleted'
        ],200);
    }

}
