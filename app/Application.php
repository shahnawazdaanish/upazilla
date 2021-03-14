<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    /**
     * Class Application
     * @property int $id
     * @property string $application_id
     * @property string $application_type
     * @property string $application_to
     * @property string $form_data
     * @property string $applicant_name_bn
     * @property string $applicant_name_en
     * @property string $applicant_father_name_bn
     * @property string $applicant_father_name_en
     * @property string $applicant_mother_name_bn
     * @property string $applicant_mother_name_en
     * @property string $marital_status
     * @property string $spouse_name
     * @property string $date_of_birth
     * @property int $gender
     * @property string $nid_no
     * @property string $addr_perm_road
     * @property string $addr_perm_union
     * @property string $addr_perm_upazilla
     * @property string $addr_perm_zilla
     * @property string $addr_pres_road
     * @property string $addr_pres_union
     * @property string $addr_pres_upazilla
     * @property string $addr_pres_zilla
     * @property string $mobile_no
     * @property string $spouse_nid
     * @property string $family_members_count
     * @property double $monthly_income
     * @property int $is_family_member_disabled
     * @property string $self_picture
     * @property string $spouse_or_family_member_picture
     * @property string $land_size
     * @property string $land_mouja
     * @property string $land_daag
     * @property string $land_khatian
     * @property string $org_name
     * @property string $org_address
     * @property string $beneficiary_count
     * @property string $beneficiary_family_count
     * @property int $has_own_house
     * @property int $got_tin_earlier
     * @property int $tin_count
     * @property string $project_name
     * @property string $project_addr_union
     * @property int $project_taken_earlier
     * @property string $project_earlier_name
     * @property string $project_earlier_share
     * @property string $project_earlier_year
     * @property int $project_has_valuable_places
     * @property string $valuable_places_name
     * @property string $beneficiary_count_if_project_given
     * @property string $self_age
     * @property string $disease_name
     * @property string $suffering_since
     * @property string $doctor_prescription
     * @property int $getting_other_vata
     * @property string $other_vata_name
     * @property string $spouse_death_no
     * @property string $spouse_death_date
     * @property string $family_main_man_income
     * @property string $disable_registration_card
     * @property int $first_step_approval
     * @property int $first_step_approved_by
     * @property int $second_step_approval
     * @property int $second_step_approved_by
     * @property int $waiting_for_approval
     * @property int $short_listed
     * @property string $status
     * @property string $created_at
     * @property string $updated_at
     * @property string $deleted_at
     * @mixin \Eloquent
     * @package App
     */

    /*public $id;
    public $application_id;
    public $application_type;
    public $application_to;
    public $form_data;
    public $applicant_name_bn;
    public $applicant_name_en;
    public $applicant_father_name_bn;
    public $applicant_father_name_en;
    public $applicant_mother_name_bn;
    public $applicant_mother_name_en;
    public $marital_status;
    public $spouse_name;
    public $date_of_birth;
    public $gender;
    public $nid_no;
    public $addr_perm_road;
    public $addr_perm_union;
    public $addr_perm_upazilla;
    public $addr_perm_zilla;
    public $addr_pres_road;
    public $addr_pres_union;
    public $addr_pres_upazilla;
    public $addr_pres_zilla;
    public $mobile_no;
    public $spouse_nid;
    public $family_members_count;
    public $monthly_income;
    public $is_family_member_disabled;
    public $self_picture;
    public $spouse_or_family_member_picture;
    public $land_size;
    public $land_mouja;
    public $land_daag;
    public $land_khatian;
    public $org_name;
    public $org_address;
    public $beneficiary_count;
    public $beneficiary_family_count;
    public $has_own_house;
    public $got_tin_earlier;
    public $tin_count;
    public $project_name;
    public $project_addr_union;
    public $project_taken_earlier;
    public $project_earlier_name;
    public $project_earlier_share;
    public $project_earlier_year;
    public $project_has_valuable_places;
    public $valuable_places_name;
    public $beneficiary_count_if_project_given;
    public $self_age;
    public $disease_name;
    public $suffering_since;
    public $doctor_prescription;
    public $getting_other_vata;
    public $other_vata_name;
    public $spouse_death_no;
    public $spouse_death_date;
    public $family_main_man_income;
    public $disable_registration_card;
    public $first_step_approval;
    public $first_step_approved_by;
    public $second_step_approval;
    public $second_step_approved_by;
    public $waiting_for_approval;
    public $short_listed;
    public $status;
    public $created_at;
    public $updated_at;
    public $deleted_at;*/

    protected $table = "applications";
    //

    public function getMaritalStatusAttribute($value)
    {
        return $value == 0 ? "অবিবাহিত" : "বিবাহিত";
    }
    public function getGenderAttribute($value)
    {
        return $value == 0 ? "পুরুষ" : "মহিলা";
    }

    public function getIsFamilyMemberDisabledAttribute($value){
        return $value == 0 ? "না" : "হ্যা";
    }
    public function getHasOwnHouseAttribute($value){
        return $value == 0 ? "না" : "হ্যা";
    }
    public function getGotTinEarlierAttribute($value){
        return $value == 0 ? "না" : "হ্যা";
    }
    public function getProjectTakenEarlierAttribute($value){
        return $value == 0 ? "না" : "হ্যা";
    }
    public function getProjectHasValuablePlacesAttribute($value){
        return $value == 0 ? "না" : "হ্যা";
    }
    public function getGettingOtherVataAttribute($value){
        return $value == 0 ? "না" : "হ্যা";
    }
}
