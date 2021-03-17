<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ApplicationReport implements FromCollection, WithMapping, WithHeadings
{
    protected $applications;

    public function __construct(Collection $applications)
    {
        $this->applications = $applications;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->applications;
    }

    /**
     * @var Payment $application
     */
    public function map($application): array
    {
        return [
            $application->id,
            $application->application_id,
            $application->application_type,
            $application->application_to,
            $application->applicant_name_bn,
            $application->applicant_name_en,
            $application->applicant_father_name_bn,
            $application->applicant_father_name_en,
            $application->applicant_mother_name_bn,
            $application->applicant_mother_name_en,
            $application->marital_status,
            $application->spouse_name,
            $application->date_of_birth,
            $application->gender,
            $application->nid_no,
            $application->addr_perm_road,
            $application->addr_perm_union,
            $application->addr_perm_upazilla,
            $application->addr_perm_zilla,
            $application->addr_pres_road,
            $application->addr_pres_union,
            $application->addr_pres_upazilla,
            $application->addr_pres_zilla,
            $application->mobile_no,
            $application->spouse_nid,
            $application->family_members_count,
            $application->monthly_income,
            $application->is_family_member_disabled,
            $application->self_picture,
            $application->spouse_or_family_member_picture,
            $application->land_size,
            $application->land_mouja,
            $application->land_daag,
            $application->land_khatian,
            $application->org_name,
            $application->org_address,
            $application->beneficiary_count,
            $application->beneficiary_family_count,
            $application->has_own_house,
            $application->got_tin_earlier,
            $application->tin_count,
            $application->project_name,
            $application->project_addr_union,
            $application->project_taken_earlier,
            $application->project_earlier_name,
            $application->project_earlier_share,
            $application->project_earlier_year,
            $application->project_has_valuable_places,
            $application->valuable_places_name,
            $application->beneficiary_count_if_project_given,
            $application->self_age,
            $application->disease_name,
            $application->suffering_since,
            $application->doctor_prescription,
            $application->getting_other_vata,
            $application->other_vata_name,
            $application->spouse_death_no,
            $application->spouse_death_date,
            $application->family_main_man_income,
            $application->disable_registration_card,
            $application->first_step_approval,
            $application->short_listed,
            $application->status,
            Date::dateTimeToExcel($application->created_at),
            Date::dateTimeToExcel($application->updated_at)
        ];
    }

    public function headings(): array
    {
        return [
            "#",
            "এপ্লিকেশন আইডি",
            "আবেদনের বিষয়",
            "বরাবর",
            "আবেদনকারীর নাম বাংলায়",
            "আবেদনকারীর নাম ইংরেজীতে",
            "পিতার নাম বাংলায়",
            "পিতার নাম ইংরেজীতে",
            "মাতার নাম বাংলায়",
            "মাতার নাম ইংরেজীতে",
            "বৈবাহিক অবস্থা",
            "স্বামী/ স্ত্রী নাম",
            "জন্ম তারিখ",
            "লিঙ্গ",
            "জাতীয় পরিচয় পত্র নম্বর",
            "স্থায়ীঃ গ্রাম/ রাস্তা",
            "স্থায়ীঃ ইউনিয়ন",
            "স্থায়ীঃ উপজেলা",
            "স্থায়ীঃ জেলা",
            "বর্তমানঃ গ্রাম/ রাস্তা",
            "বর্তমানঃ ইউনিয়ন",
            "বর্তমানঃ উপজেলা",
            "বর্তমানঃ জেলা",
            "মোবাইল নম্বর",
            "স্বামী/ স্ত্রীর জাতীয় পরিচয়পত্র নম্বর",
            "পরিবারের সদস্য সংখ্যা",
            "মাসিক আয়",
            "পরিবারের কোন সদস্য প্রতিবন্ধী কিনা",
            "ছবি(নিজ)",
            "ছবি (স্বামী/স্ত্রী, অবিবাহিত হলে পরিবার প্রধানের ছবি)",
            "জমির বিবরণ- পরিমান",
            "জমির বিবরণ- মৌজা",
            "জমির বিবরণ- দাগ",
            "জমির বিবরণ- খতিয়ান নং",
            "প্রতিষ্ঠানের নাম",
            "প্রতিষ্ঠানের ঠিকানা",
            "উপকারভোগীর সংখ্যা",
            "উপকারভোগীর পরিবার সংখ্যা",
            "নিজের ঘর আছে কিনা",
            "ইতোপূর্বে টিন পেয়েছেন কিনা",
            "পরিমান (বান্ডিল)",
            "প্রকল্পের নাম",
            "প্রকল্পের অবস্থান (ইউনিয়নের নাম)",
            "ইতিপূর্বে প্রকল্প গ্রহণ করা হয়েছে কিনা",
            "ইতিপূর্বে প্রকল্পের নাম",
            "ইতিপূর্বে প্রকল্পের বরাদ্দের পরিমান",
            "ইতিপূর্বে প্রকল্পের অর্থ বছর",
            "প্রকল্প সংলগ্ন কোনা গুরুত্বপূর্ণ প্রতিষ্ঠান আছে কিনা",
            "প্রকল্প সংলগ্ন গুরুত্বপূর্ণ প্রতিষ্ঠানের নাম",
            "প্রকল্প বাস্তবায়ন কর হলে কতজন লোক উপকৃত হবে",
            "বয়স",
            "রোগের নাম",
            "এই রোগে কতদিন ভুগছেন",
            "ডাক্তারের ব্যবস্থাপত্র",
            "অন্য কোন ভাতাভুক্ত কিনা",
            "ভাতার নাম",
            "স্বামীর মৃত্যু নিবন্ধন নম্বর",
            "মৃত্যুবরণের তারিখ",
            "পরিবার প্রধানের মাসিক আয়",
            "প্রতিবন্ধী নিবন্ধন কার্ড",
            "এপ্রুভ কি না?",
            "শর্ট লিস্টেড কি না?",
            "সর্বশেষ স্টেটাস",
            "তৈরির তারিখ",
            "পরিবর্তনের তারিখ"
        ];
    }
}
