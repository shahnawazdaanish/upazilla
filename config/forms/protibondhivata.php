<?php

return array(
    array(
        'title' => 'আবেদনকারীর নাম',
        'type' => 'text',
        'class' => 'applicant_name_bn',
        'name' => 'applicant_name_bn',
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
        'title' => 'বৈবাহিক অবস্থা',
        'type' => 'select',
        'class' => 'marital_status',
        'name' => 'marital_status',
        'options' => array(
            0 => 'অবিবাহিত',
            1 =>'বিবাহিত'
        ),
        'isHidden' => false,
        'onchange' => "showOption('.marital_status', {'1' : '.spouse_name'})",
        'sub-form' => array(
            array(
                'title' => 'স্বামী/ স্ত্রী নাম',
                'type' => 'text',
                'class' => 'spouse_name',
                'name' => 'spouse_name',
                'isHidden' => true
            ),
        )
    ),
    array(
        'title' => 'জন্ম তারিখ',
        'type' => 'text',
        'class' => 'date_of_birth',
        'name' => 'date_of_birth',
        'html_extra' => "autocomplete=\"false\" onchange=\"calculateAge('.date_of_birth input', '.self_age input')\"",
        'isHidden' => false
    ),
    array(
        'title' => 'বয়স',
        'type' => 'text',
        'class' => 'self_age',
        'name' => 'self_age',
        'html_extra' => 'disabled',
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
                        'backend_rules' => 'required|max:4'
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
        'title' => 'মোবাইল নম্বরঃ',
        'type' => 'text',
        'class' => 'mobile_no',
        'name' => 'mobile_no',
        'isHidden' => false
    ),
    array(
        'title' => 'পরিবারের সদস্য সংখ্যা',
        'type' => 'text',
        'class' => 'family_members_count',
        'name' => 'family_members_count',
        'isHidden' => false
    ),
    array(
        'title' => 'পরিবার প্রধানের মাসিক আয়',
        'type' => 'text',
        'class' => 'family_main_man_income',
        'name' => 'family_main_man_income',
        'isHidden' => false
    ),
    array(
        'title' => 'ছবি(নিজ)',
        'type' => 'photo',
        'class' => 'photo file-upload',
        'name' => 'self_picture',
        'isHidden' => false
    ),
    array(
        'title' => 'অন্য কোন ভাতাভুক্ত কিনা',
        'type' => 'select',
        'class' => 'getting_other_vata',
        'name' => 'getting_other_vata',
        'options' => array(
            1 => 'হ্যা',
            0 =>'না'
        ),
        'isHidden' => false,
        'onchange' => "showOption('.getting_other_vata', {'1' : '.other_vata_name'})",
        'sub-form' => array(
            array(
                'title' => 'ভাতার নাম',
                'type' => 'text',
                'class' => 'other_vata_name',
                'name' => 'other_vata_name',
                'isHidden' => true
            ),
        )
    ),
    array(
        'title' => 'প্রতিবন্ধী নিবন্ধন কার্ড',
        'type' => 'photo',
        'class' => 'photo doc-upload',
        'name' => 'disable_registration_card',
        'isHidden' => false
    )
);
