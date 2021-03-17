<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->uuid("application_id")->index("index_application_id");
            $table->string("application_type")->index("index_application_type");
            $table->string("application_to");
            $table->unsignedBigInteger("application_to_id");
            $table->json("form_data");
            $table->string("applicant_name_bn")->index("index_app_name_bn");
            $table->string("applicant_name_en")->index("index_app_name_en");
            $table->string("applicant_father_name_bn");
            $table->string("applicant_father_name_en");
            $table->string("applicant_mother_name_bn");
            $table->string("applicant_mother_name_en");
            $table->string("marital_status");
            $table->string("spouse_name")->nullable();
            $table->string("date_of_birth");
            $table->tinyInteger("gender")->default(1)->comment("1=male, 2= female, 3=transgender");
            $table->string("nid_no")->index("index_nid_no");
            $table->string("addr_perm_road");
            $table->string("addr_perm_union");
            $table->string("addr_perm_upazilla");
            $table->string("addr_perm_zilla");
            $table->string("addr_pres_road");
            $table->string("addr_pres_union");
            $table->string("addr_pres_upazilla");
            $table->string("addr_pres_zilla");
            $table->string("mobile_no")->index("index_mobile_no");
            $table->string("spouse_nid")->nullable();
            $table->integer("family_members_count")->nullable();
            $table->decimal("monthly_income", 15, 2)->nullable();
            $table->tinyInteger("is_family_member_disabled")->nullable();
            $table->string("self_picture");
            $table->string("spouse_or_family_member_picture")->nullable();
            $table->string("land_size")->nullable();
            $table->string("land_mouja")->nullable();
            $table->string("land_daag")->nullable();
            $table->string("land_khatian")->nullable();
            $table->string("org_name")->nullable();
            $table->string("org_address")->nullable();
            $table->string("beneficiary_count")->nullable();
            $table->string("beneficiary_family_count")->nullable();
            $table->tinyInteger("has_own_house")->nullable();
            $table->tinyInteger("got_tin_earlier")->nullable();
            $table->integer("tin_count")->nullable();
            $table->string("project_name")->nullable();
            $table->string("project_addr_union")->nullable();
            $table->tinyInteger("project_taken_earlier")->nullable();
            $table->string("project_earlier_name")->nullable();
            $table->string("project_earlier_share")->nullable();
            $table->string("project_earlier_year")->nullable();
            $table->tinyInteger("project_has_valuable_places")->nullable();
            $table->string("valuable_places_name")->nullable();
            $table->string("beneficiary_count_if_project_given")->nullable();
            $table->integer("self_age")->nullable();
            $table->string("disease_name")->nullable();
            $table->string("suffering_since")->nullable();
            $table->string("doctor_prescription")->nullable();
            $table->tinyInteger("getting_other_vata")->nullable();
            $table->string("other_vata_name")->nullable();
            $table->string("spouse_death_no")->nullable();
            $table->string("spouse_death_date")->nullable();
            $table->string("family_main_man_income")->nullable();
            $table->string("disable_registration_card")->nullable();


            $table->tinyInteger("first_step_approval")->nullable()->default(0);
            $table->integer("first_step_approved_by")->nullable();
            $table->tinyInteger("second_step_approval")->nullable()->default(0);
            $table->integer("second_step_approved_by")->nullable();

            $table->integer("waiting_for_approval")->nullable();
            $table->tinyInteger("short_listed")->nullable()->default(0);
            $table->string("status")->default("SUBMITTED");

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
