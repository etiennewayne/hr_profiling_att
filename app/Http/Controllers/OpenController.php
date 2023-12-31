<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LearningDevelopmentType;
use App\Models\Specialization;
use App\Models\CidSubRole;

use App\Models\Degree;
use App\Models\EngagementStatus;
use App\Models\Educationlevel;

class OpenController extends Controller
{
    //

    public function loadLearningDevTypes(Request $req){

        return LearningDevelopmentType::orderBy('ld_type', 'asc')
            ->get();
        
    }

    public function loadSpecializations(Request $req){

        return Specialization::orderBy('specialization', 'asc')
            ->get();
        
    }


    public function loadSubRoles(){
        return CidSubRole::orderBy('cid_sub_role', 'asc')
            ->get();
    }

    public function loadDegrees(){
        return Degree::orderBy('degree_program', 'asc')
            ->get();
    }

    public function loadEngagementStatus(){
        return EngagementStatus::orderBy('engagement_status', 'asc')
            ->get();
    }

    public function loadEducationLevels(){
        return Educationlevel::orderBy('education_level', 'asc')
            ->get();
    }


    
    

}
