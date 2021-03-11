<?php

return array(
    array(
        'title' => 'আবেদনকারীর নাম',
        'type' => 'text',
        'class' => 'applicant_name_bn',
        'name' => 'applicant_name_bn',
        'isHidden' => false
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
        'type' => 'radio',
        'class' => 'marital_status',
        'name' => 'marital_status',
        'options' => array(
            'অবিবাহিত',
            'বিবাহিত'
        ),
        'isHidden' => true,
        'onchange' => "showOption('.marital_status', {'বিবাহিত' : '.spouse_name'})",
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
        'isHidden' => false
    ),
    array(
        'title' => 'লিঙ্গ',
        'type' => 'radio',
        'class' => 'sex',
        'name' => 'sex',
        'options' => array(
            'পুরুষ',
            'মহিলা'
        ),
        'isHidden' => false
    ),
    array(
        'title' => 'জাতীয় পরিচয় পত্র নম্বর',
        'type' => 'text',
        'class' => 'nid',
        'name' => 'nid',
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
        'title' => 'স্বামী/ স্ত্রীর জাতীয় পরিচয়পত্র নম্বর',
        'type' => 'text',
        'class' => 'spouse_nid',
        'name' => 'spouse_nid',
        'isHidden' => false
    ),
    array(
        'title' => 'পরিবারের সদস্য সংখ্যা',
        'type' => 'text',
        'class' => 'head_count',
        'name' => 'head_count',
        'isHidden' => false
    ),
    array(
        'title' => 'মাসিক আয়',
        'type' => 'text',
        'class' => 'monthly_income',
        'name' => 'monthly_income',
        'isHidden' => false
    ),
    array(
        'title' => 'পরিবারের কোন সদস্য প্রতিবন্ধী কিনা ',
        'type' => 'radio',
        'class' => 'disable_in_family',
        'name' => 'disable_in_family',
        'options' => array(
            'হ্যা',
            'না'
        ),
        'isHidden' => false
    ),
    array(
        'title' => 'ছবি(নিজ)',
        'type' => 'photo',
        'class' => 'photo file-upload',
        'name' => 'photo',
        'isHidden' => false
    ),
    array(
        'title' => 'ছবি (স্বামী/স্ত্রী, অবিবাহিত হলে পরিবার প্রধানের ছবি) ',
        'type' => 'photo',
        'class' => 'family_photo file-upload',
        'name' => 'family_photo',
        'isHidden' => false
    )
);
