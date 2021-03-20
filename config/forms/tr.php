<?php

return array(
    array(
        'title' => 'আবেদনকারীর নাম',
        'type' => 'text',
        'class' => 'applicant_name_bn',
        'name' => 'applicant_name_bn',
        'isHidden' => false,
        'js_rules' => "{required: true, maxlength: 100}",
        'backend_rules' => "required",
//        'html_extra' => 'pattern="[0-9]{1,5}"'
    ),
    array(
        'title' => 'APPLICANT NAME',
        'type' => 'text',
        'class' => 'applicant_name_en',
        'name' => 'applicant_name_en',
        'isHidden' => false
    ),
    array(
        'title' => 'পিতার নাম',
        'type' => 'text',
        'class' => 'applicant_father_name_bn',
        'name' => 'applicant_father_name_bn',
        'isHidden' => false
    ),
    array(
        'title' => 'Father\'s Name',
        'type' => 'text',
        'class' => 'applicant_father_name_en',
        'name' => 'applicant_father_name_en',
        'isHidden' => false
    ),
    array(
        'title' => 'মাতার নাম',
        'type' => 'text',
        'class' => 'applicant_mother_name_bn',
        'name' => 'applicant_mother_name_bn',
        'isHidden' => false
    ),
    array(
        'title' => 'Mother\'s Name',
        'type' => 'text',
        'class' => 'applicant_mother_name_en',
        'name' => 'applicant_mother_name_en',
        'isHidden' => false
    ),
    array(
        'title' => 'ঠিকানা',
        'type' => 'label',
        'class' => 'address',
        'name' => '',
        'isHidden' => false,
        'sub-form' => array(
            array(
                'title' => 'স্থায়ীঃ',
                'type' => 'label',
                'class' => 'permanent_address',
                'isHidden' => false,
                'sub-form' => array(
                    array(
                        'title' => 'গ্রাম/ রাস্তা',
                        'type' => 'text',
                        'class' => 'perm_road',
                        'name' => 'addr_perm_road',
                        'isHidden' => false,
                        'backend_rules' => 'required'
                    ),
                    array(
                        'title' => 'ইউনিয়ন',
                        'type' => 'text',
                        'class' => 'perm_union',
                        'name' => 'addr_perm_union',
                        'isHidden' => false,
                    ),
                    array(
                        'title' => 'উপজেলা',
                        'type' => 'text',
                        'class' => 'perm_upazilla',
                        'name' => 'addr_perm_upazilla',
                        'isHidden' => false,
                    ),
                    array(
                        'title' => 'জেলা',
                        'type' => 'text',
                        'class' => 'perm_zilla',
                        'name' => 'addr_perm_zilla',
                        'isHidden' => false,
                    )
                )
            ),
            array(
                'title' => 'বতমানঃ',
                'type' => 'label',
                'class' => 'present_address',
                'isHidden' => false,
                'sub-form' => array(
                    array(
                        'title' => 'গ্রাম/ রাস্তা',
                        'type' => 'text',
                        'class' => 'pre_road',
                        'name' => 'addr_pres_road',
                        'isHidden' => false,
                    ),
                    array(
                        'title' => 'ইউনিয়ন',
                        'type' => 'text',
                        'class' => 'pre_union',
                        'name' => 'addr_pres_union',
                        'isHidden' => false,
                    ),
                    array(
                        'title' => 'উপজেলা',
                        'type' => 'text',
                        'class' => 'pre_upazilla',
                        'name' => 'addr_pres_upazilla',
                        'isHidden' => false,
                    ),
                    array(
                        'title' => 'জেলা',
                        'type' => 'text',
                        'class' => 'pre_zilla',
                        'name' => 'addr_pres_zilla',
                        'isHidden' => false,
                    )
                )
            )
        )
    ),
    array(
        'title' => 'জাতীয় পরিচয় পত্র নম্বর',
        'type' => 'text',
        'class' => 'nid_no',
        'name' => 'nid_no',
        'isHidden' => false
    ),
    array(
        'title' => 'জন্ম তারিখ',
        'type' => 'text',
        'class' => 'date_of_birth',
        'name' => 'date_of_birth',
        'isHidden' => false
    ),
    array(
        'title' => 'প্রতিষ্ঠানের নাম (প্রযোজ্য ক্ষেত্রে)',
        'type' => 'text',
        'class' => 'org_name',
        'name' => 'org_name',
        'isHidden' => false,
        'required' => false
    ),
    array(
        'title' => 'প্রতিষ্ঠানের ঠিকানা',
        'type' => 'text',
        'class' => 'org_address',
        'name' => 'org_address',
        'isHidden' => false,
        'required' => false
    ),
    array(
        'title' => 'প্রকল্পের নাম',
        'type' => 'text',
        'class' => 'project_name',
        'name' => 'project_name',
        'isHidden' => false
    ),
    array(
        'title' => 'প্রকল্পের অবস্থান',
        'type' => 'label',
        'name' => 'project_place',
        'isHidden' => 'false',
        'sub-form' => array(
            'title' => 'ইউনিয়নের নাম',
            'type' => 'text',
            'class' => 'project_addr_union',
            'name' => 'project_addr_union',
            'isHidden' => false
        ),
    ),
    array(
        'title' => 'মোবাইল নম্বরঃ',
        'type' => 'text',
        'class' => 'mobile_no',
        'name' => 'mobile_no',
        'isHidden' => false
    ),
    array(
        'title' => 'জমির বিবরণ',
        'type' => 'label',
        'class' => 'land',
        'name' => '',
        'isHidden' => false,
        'sub-form' => array(
            array(
                'title' => 'জমির পরিমান',
                'type' => 'text',
                'class' => 'land_size',
                'name' => 'land_size',
                'isHidden' => false,
                'backend_rules' => 'required'
            ),
            array(
                'title' => 'মৌজা',
                'type' => 'text',
                'class' => 'land_mouja',
                'name' => 'land_mouja',
                'isHidden' => false,
            ),
            array(
                'title' => 'দাগ',
                'type' => 'text',
                'class' => 'land_daag',
                'name' => 'land_daag',
                'isHidden' => false,
            ),
            array(
                'title' => 'খতিয়ান নং',
                'type' => 'text',
                'class' => 'land_khatian',
                'name' => 'land_khatian',
                'isHidden' => false,
            )
        )
    ),
    array(
        'title' => 'ইতিপূর্বে প্রকল্প গ্রহণ করা হয়েছে কিনা',
        'type' => 'select',
        'class' => 'project_taken_earlier',
        'name' => 'project_taken_earlier',
        'options' => array(
            0 => 'না',
            1 => 'হ্যা'
        ),
        'isHidden' => false,
        'onchange' => "showOption('.project_taken_earlier', {'1' : ['.project_earlier_name', '.project_earlier_share','.project_earlier_year']})",
        'sub-form' => array(
            array(
                'title' => 'প্রকল্পের নাম',
                'type' => 'text',
                'class' => 'project_earlier_name',
                'name' => 'project_earlier_name',
                'isHidden' => true
            ),
            array(
                'title' => 'বরাদ্দের পরিমান',
                'type' => 'text',
                'class' => 'project_earlier_share',
                'name' => 'project_earlier_share',
                'isHidden' => true
            ),
            array(
                'title' => 'অর্থবছর',
                'type' => 'text',
                'class' => 'project_earlier_year',
                'name' => 'project_earlier_year',
                'isHidden' => true
            )
        )
    ),
    array(
        'title' => 'প্রকল্প সংলগ্ন কোনা গুরুত্ব পূন প্রতিষ্ঠান আছে কিনা',
        'type' => 'select',
        'class' => 'project_has_valuable_places',
        'name' => 'project_has_valuable_places',
        'isHidden' => false,
        'options' => array(
            0 => 'না',
            1 => 'হ্যা'
        ),
        'onchange' => "showOption('.project_has_valuable_places', {'1' : '.valuable_places_name'})",
        'sub-form' => array(
            array(
                'title' => 'নাম',
                'type' => 'text',
                'class' => 'valuable_places_name',
                'name' => 'valuable_places_name',
                'isHidden' => true
            ),
        )
    ),
    array(
        'title' => 'প্রকল্প বাস্তবায়ন কর হলে কতজন লোক উপকৃত হবেঃ',
        'type' => 'text',
        'class' => 'beneficiary_count_if_project_given',
        'name' => 'beneficiary_count_if_project_given',
        'isHidden' => false,
        'required'=> false
    )
);
