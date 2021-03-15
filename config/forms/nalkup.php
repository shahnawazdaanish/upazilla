<?php

return array(
    array(
        'title' => 'আবেদনকারীর নাম',
        'type' => 'text',
        'class' => 'applicant_name_bn',
        'name' => 'applicant_name_bn',
        'default' => 'Shahnawaz',
        'isHidden' => false,
        'js_rules' => "{required: true, maxlength: 11}",
        'backend_rules' => "required",
//        'html_extra' => 'pattern="[0-9]{1,5}"'
    ),
    array(
        'title' => 'APPLICANT NAME',
        'type' => 'text',
        'class' => 'applicant_name_en',
        'name' => 'applicant_name_en',
        'default' => 'Shahnawaz',
        'isHidden' => false
    ),
    array(
        'title' => 'পিতার নাম',
        'type' => 'text',
        'class' => 'applicant_father_name_bn',
        'name' => 'applicant_father_name_bn',
        'default' => 'Shahnawaz',
        'isHidden' => false
    ),
    array(
        'title' => 'Father\'s Name',
        'type' => 'text',
        'class' => 'applicant_father_name_en',
        'name' => 'applicant_father_name_en',
        'default' => 'Shahnawaz',
        'isHidden' => false
    ),
    array(
        'title' => 'মাতার নাম',
        'type' => 'text',
        'class' => 'applicant_mother_name_bn',
        'name' => 'applicant_mother_name_bn',
        'default' => 'Shahnawaz',
        'isHidden' => false
    ),
    array(
        'title' => 'Mother\'s Name',
        'type' => 'text',
        'class' => 'applicant_mother_name_en',
        'name' => 'applicant_mother_name_en',
        'default' => 'Shahnawaz',
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
                        'default' => 'অবিবাহিত',
                        'isHidden' => false,
                        'backend_rules' => 'required'
                    ),
                    array(
                        'title' => 'ইউনিয়ন',
                        'type' => 'text',
                        'class' => 'perm_union',
                        'name' => 'addr_perm_union',
                        'default' => 'অবিবাহিত',
                        'isHidden' => false,
                    ),
                    array(
                        'title' => 'উপজেলা',
                        'type' => 'text',
                        'class' => 'perm_upazilla',
                        'name' => 'addr_perm_upazilla',
                        'default' => 'অবিবাহিত',
                        'isHidden' => false,
                    ),
                    array(
                        'title' => 'জেলা',
                        'type' => 'text',
                        'class' => 'perm_zilla',
                        'name' => 'addr_perm_zilla',
                        'default' => 'অবিবাহিত',
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
                        'default' => 'অবিবাহিত',
                        'isHidden' => false,
                    ),
                    array(
                        'title' => 'ইউনিয়ন',
                        'type' => 'text',
                        'class' => 'pre_union',
                        'name' => 'addr_pres_union',
                        'default' => 'অবিবাহিত',
                        'isHidden' => false,
                    ),
                    array(
                        'title' => 'উপজেলা',
                        'type' => 'text',
                        'class' => 'pre_upazilla',
                        'name' => 'addr_pres_upazilla',
                        'default' => 'অবিবাহিত',
                        'isHidden' => false,
                    ),
                    array(
                        'title' => 'জেলা',
                        'type' => 'text',
                        'class' => 'pre_zilla',
                        'name' => 'addr_pres_zilla',
                        'default' => 'অবিবাহিত',
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
        'default' => 'অবিবাহিত',
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
        'default' => 'অবিবাহিত',
        'isHidden' => false,
        'required' => false
    ),
    array(
        'title' => 'প্রতিষ্ঠানের ঠিকানা',
        'type' => 'text',
        'class' => 'org_address',
        'name' => 'org_address',
        'default' => 'অবিবাহিত',
        'isHidden' => false,
        'required' => false
    ),
    array(
        'title' => 'মোবাইল নম্বরঃ',
        'type' => 'text',
        'class' => 'mobile_no',
        'name' => 'mobile_no',
        'default' => 'অবিবাহিত',
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
                'title' => 'মৌজা',
                'type' => 'text',
                'class' => 'land_mouja',
                'name' => 'land_mouja',
                'default' => 'অবিবাহিত',
                'isHidden' => false,
            ),
            array(
                'title' => 'দাগ',
                'type' => 'text',
                'class' => 'land_daag',
                'name' => 'land_daag',
                'default' => 'অবিবাহিত',
                'isHidden' => false,
            ),
            array(
                'title' => 'খতিয়ান নং',
                'type' => 'text',
                'class' => 'land_khatian',
                'name' => 'land_khatian',
                'default' => 'অবিবাহিত',
                'isHidden' => false,
            )
        )
    ),
    array(
        'title' => 'উপকারভোগীর সংখ্যা',
        'type' => 'text',
        'class' => 'beneficiary_count',
        'name' => 'beneficiary_count',
        'isHidden' => false
    )
);
