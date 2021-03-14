<?php

namespace App\Http\Controllers;

use App\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function Psy\debug;

class ApplicationController extends Controller
{

    public function Home()
    {
        $form = Config::get("forms.home");

        return view("application.home")->with('form', $form);
    }

    public function StoreApplicationType()
    {
        session()->put("application.first", request()->all());

        return redirect()->to('information');
    }

    public function ProvideApplicationInfo()
    {
        $subject = session()->get('application.first.subject');

        $form = $this->SwitchForm($subject);
        return view("application.information")->with('form', $form);
    }

    public function SwitchForm($formName)
    {
        switch ($formName) {
            case "ঘর ‘ক’ (ভূমিহীন ও গৃহহীন)":
                return Config::get('forms.formka');
            case "ঘর ‘খ’ ( সর্বোচ্চ ১০ শতাংশ জমি আছে কিন্তু ঘর নেই )":
                return Config::get("forms.formkha");
            case "গভীর নলকূপ":
                return Config::get("forms.nalkup");
            case "ঢেউটিন":
                return Config::get("forms.dheutin");
            case "টি.আর":
                return Config::get("forms.tr");
            case "কাবিখা/ কাবিটা":
                return Config::get("forms.kabikha");
            case "বার্ষিক উন্নয়ন কমসূচী (এডিপি)":
                return Config::get("forms.adp");
            case "আর্থিক অনুদান (চিকিৎসা)":
                return Config::get("forms.medical");
            case "কম্বলের আবেদন":
                return Config::get("forms.kombol");
            case "বিবিধ":
                return Config::get("forms.bibidho");
            case "ভাতা":
                return Config::get("forms.vata");
            default:
                return null;
        }
    }

    public function StoreApplicationInfo()
    {
        $form_data = session()->get('application.first');
        if(empty($form_data)) {
            return redirect()->back()->withErrors([
                'Session is timed out, Please fill the form again'
            ]);
        }
        $formRules = $this->SwitchForm(session()->get('application.first.subject'));
        if(!is_array($formRules)) {
            return redirect()->back()->withErrors([
                'Chosen invalid form type, Please fill the form again'
            ]);
        }

        $rules = [];
        foreach ($formRules as $formElem) {
            if(isset($formElem['backend_rules'])) {
                $rules[$formElem['name']] = $formElem['backend_rules'];
            }

            if(isset($formElem['sub-form'])) {
                foreach ($formElem['sub-form'] as $formElemL2) {
                    if (isset($formElemL2['backend_rules'])) {
                        $rules[$formElemL2['name']] = $formElemL2['backend_rules'];
                    }

                    if (isset($formElemL2['sub-form'])) {
                        foreach ($formElemL2['sub-form'] as $formElemL3) {
                            if (isset($formElemL3['backend_rules'])) {
                                $rules[$formElemL3['name']] = $formElemL3['backend_rules'];
                            }
                        }
                    }
                }
            }
        }

        $validator = Validator::make(request()->all(), $rules);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $inputs = request()->all();
        // dd($inputs);
        // dd($this->settleUpload(request()->get("photo")));
        // dd(request()->allFiles());

        // $photo = request()->hasFile('photo') ? request()->file("photo")->store('photos') : null;
        // $photo = request()->hasFile('family_photo') ? request()->file("family_photo")->store('photos') : null;

        $form_data = array_merge($form_data, $inputs);

        $application = new Application();
        $application->application_id = (string)Str::uuid();
        $application->application_type = session()->get('application.first.subject');
        $application->application_to = session()->get('application.first.to');
        $application->form_data = json_encode($form_data);
        $application->applicant_name_bn = request()->get("applicant_name_bn");
        $application->applicant_name_en = request()->get("applicant_name_en");
        $application->applicant_father_name_bn = request()->get("applicant_father_name_bn");
        $application->applicant_father_name_en = request()->get("applicant_father_name_en");
        $application->applicant_mother_name_bn = request()->get("applicant_mother_name_bn");
        $application->applicant_mother_name_en = request()->get("applicant_mother_name_en");
        $application->marital_status = request()->get("marital_status");
        $application->spouse_name = request()->get("spouse_name");
        $application->date_of_birth = request()->get("date_of_birth");
        $application->gender = request()->get("gender");
        $application->nid_no = request()->get("nid_no");
        $application->addr_perm_road = request()->get("addr_perm_road");
        $application->addr_perm_union = request()->get("addr_perm_union");
        $application->addr_perm_upazilla = request()->get("addr_perm_upazilla");
        $application->addr_perm_zilla = request()->get("addr_perm_zilla");
        $application->addr_pres_road = request()->get("addr_pres_road");
        $application->addr_pres_union = request()->get("addr_pres_union");
        $application->addr_pres_upazilla = request()->get("addr_pres_upazilla");
        $application->addr_pres_zilla = request()->get("addr_pres_zilla");
        $application->mobile_no = request()->get("mobile_no");
        $application->spouse_nid = request()->get("spouse_nid");
        $application->family_members_count = request()->get("family_members_count");
        $application->monthly_income = request()->get("monthly_income");
        $application->is_family_member_disabled = request()->get("is_family_member_disabled");
        $application->self_picture = $this->settleUpload(request()->get("self_picture")); //request()->get("self_picture");
        $application->spouse_or_family_member_picture = $this->settleUpload(request()->get("spouse_or_family_member_picture")); //request()->get("spouse_or_family_member_picture");
        $application->land_size = request()->get("land_size");
        $application->land_mouja = request()->get("land_mouja");
        $application->land_daag = request()->get("land_daag");
        $application->land_khatian = request()->get("land_khatian");
        $application->org_name = request()->get("org_name");
        $application->org_address = request()->get("org_address");
        $application->beneficiary_count = request()->get("beneficiary_count");
        $application->beneficiary_family_count = request()->get("beneficiary_family_count");
        $application->has_own_house = request()->get("has_own_house");
        $application->got_tin_earlier = request()->get("got_tin_earlier");
        $application->tin_count = request()->get("tin_count");
        $application->project_name = request()->get("project_name");
        $application->project_addr_union = request()->get("project_addr_union");
        $application->project_taken_earlier = request()->get("project_taken_earlier");
        $application->project_earlier_name = request()->get("project_earlier_name");
        $application->project_earlier_share = request()->get("project_earlier_share");
        $application->project_earlier_year = request()->get("project_earlier_year");
        $application->project_has_valuable_places = request()->get("project_has_valuable_places");
        $application->valuable_places_name = request()->get("valuable_places_name");
        $application->beneficiary_count_if_project_given = request()->get("beneficiary_count_if_project_given");
        $application->self_age = request()->get("self_age");
        $application->disease_name = request()->get("disease_name");
        $application->suffering_since = request()->get("suffering_since");
        $application->doctor_prescription = request()->get("doctor_prescription");
        $application->getting_other_vata = request()->get("getting_other_vata");
        $application->other_vata_name = request()->get("other_vata_name");
        $application->spouse_death_no = request()->get("spouse_death_no");
        $application->spouse_death_date = request()->get("spouse_death_date");
        $application->family_main_man_income = request()->get("family_main_man_income");
        $application->disable_registration_card = request()->get("disable_registration_card");
        $application->first_step_approval = request()->get("first_step_approval");
        $application->first_step_approved_by = request()->get("first_step_approved_by");
        $application->second_step_approval = request()->get("second_step_approval");
        $application->second_step_approved_by = request()->get("second_step_approved_by");
        $application->waiting_for_approval = request()->get("waiting_for_approval");
        $application->short_listed = request()->get("short_listed");
        // $application->status = request()->get("status");
        $application->save();

        return redirect('thankyou/' . $application->application_id);
    }

    public function settleUpload($serverId)
    {
        try {
            // Get the temporary path using the serverId returned by the upload function in `FilepondController.php`
            $filepond = app(\Sopamo\LaravelFilepond\Filepond::class);
            $path = $filepond->getPathFromServerId($serverId);

            $extension = pathinfo(storage_path($path), PATHINFO_EXTENSION);

            // Move the file from the temporary path to the final location
            $filePath = "uploads\\" . uniqid() . '.' . $extension;
            $finalLocation = public_path($filePath);
            File::move($path, $finalLocation);
            return $filePath;
        }catch (\Exception $e) {
            return '';
        }
    }

    public function ThankYou($uuid = "")
    {

        $form = Config::get("forms.formka");
        $dataRow = Application::query()->where('application_id', $uuid)->first();
        $data = json_decode($dataRow->form_data, true);
        return view("application.thankyou")->with("uuid", $uuid)
            ->with('form', $form)
            ->with('data', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Application $application
     * @return \Illuminate\Http\Response
     */
    public function show(Application $application)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Application $application
     * @return \Illuminate\Http\Response
     */
    public function edit(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Application $application
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Application $application
     * @return \Illuminate\Http\Response
     */
    public function destroy(Application $application)
    {
        //
    }
}
