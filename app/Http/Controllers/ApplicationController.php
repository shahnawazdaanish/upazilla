<?php

namespace App\Http\Controllers;

use App\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use function Psy\debug;

class ApplicationController extends Controller
{

    public function Home()
    {
        $form = Config::get("forms.home");

        return view("application.home")->with('form', $form);
    }

    public function StoreApplicationType() {
        session()->put("application.first", request()->all());

        return redirect()->to('information');
    }

    public function ProvideApplicationInfo() {
        $subject = session()->get('application.first.subject');

        $form = $this->SwitchForm($subject);
        return view("application.information")->with('form', $form);
    }

    public function SwitchForm($formName) {
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

    public function StoreApplicationInfo() {
        $form_data = session()->get('application.first');

        $inputs = request()->all();

        $photo = request()->hasFile('photo') ? request()->file("photo")->store('photos') : null;
        $photo = request()->hasFile('family_photo') ? request()->file("family_photo")->store('photos') : null;

        $form_data = array_merge($form_data, $inputs);

        $application = new Application();
        $application->application_id = (string) Str::uuid();
        $application->form_data = json_encode($form_data);
        $application->save();

        return redirect('thankyou')->with('uuid', $application->application_id);
    }

    public function ThankYou() {
        dd(request());
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
