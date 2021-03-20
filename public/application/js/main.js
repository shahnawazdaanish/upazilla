(function ($) {
    var fileParent = $('.file-upload input[type="file"]');
    var docParent = $('.doc-upload input[type="file"]');
    // First register any plugins
    $.fn.filepond.registerPlugin(
        FilePondPluginFileValidateType,
        FilePondPluginImagePreview,
        FilePondPluginImageCrop,
        FilePondPluginImageResize,
        FilePondPluginImageEdit
    );

    // Turn input element into a pond
    fileParent.filepond({
        allowImagePreview: true,
        labelIdle: `ছবি তুলুন /<span class="filepond--label-action">আপলোড করুন</span>`,
        imagePreviewHeight: 80,
        imageCropAspectRatio: '1:1',
        imageResizeTargetWidth: 100,
        imageResizeTargetHeight: 100,
        stylePanelLayout: 'compact circle',
        styleLoadIndicatorPosition: 'center bottom',
        styleProgressIndicatorPosition: 'right bottom',
        styleButtonRemoveItemPosition: 'left bottom',
        styleButtonProcessItemPosition: 'right bottom',
    });


    docParent.filepond({
        allowImagePreview: true,
        labelIdle: `ছবি তুলুন /<span class="filepond--label-action">আপলোড করুন</span>`,
        imagePreviewHeight: 80
    });
    // Listen for addfile event
    fileParent.on('FilePond:addfile', function(e) {
        console.log('file added event', e);
    });


    $('select').niceSelect();
    $('.date_of_birth input').datepicker({
        language: 'bn-BD',
        format: 'dd-mm-yyyy'
    });

    $('#meal_preference').parent().append('<ul class="list-item" id="newmeal_preference" name="meal_preference"></ul>');
    $('#meal_preference option').each(function () {
        $('#newmeal_preference').append('<li value="' + $(this).val() + '">' + $(this).text() + '</li>');
    });
    $('#meal_preference').remove();
    $('#newmeal_preference').attr('id', 'meal_preference');
    $('#meal_preference li').first().addClass('init');
    $("#meal_preference").on("click", ".init", function () {
        $(this).closest("#meal_preference").children('li:not(.init)').toggle();
    });

    var allOptions = $("#meal_preference").children('li:not(.init)');
    $("#meal_preference").on("click", "li:not(.init)", function () {
        allOptions.removeClass('selected');
        $(this).addClass('selected');
        $("#meal_preference").children('.init').html($(this).html());
        allOptions.toggle();
    });

    var marginSlider = document.getElementById('slider-margin');
    if (marginSlider != undefined) {
        noUiSlider.create(marginSlider, {
            start: [500],
            step: 10,
            connect: [true, false],
            tooltips: [true],
            range: {
                'min': 0,
                'max': 1000
            },
            format: wNumb({
                decimals: 0,
                thousand: ',',
                prefix: '$ ',
            })
        });
    }
    $('#reset').on('click', function () {
        $('#register-form').reset();
    });

})(jQuery);
