$('.clear-filters').on('click', function (e) {
    $(this).closest('form').find(':input')
        .not(':button, :submit, :reset, :hidden')
        .val('')
        .removeAttr('checked');
    $(this).closest('form').find('select').each(function () {
        $(this).val($(this).find('option:first').val());
    });
});
$('.filter-toggle').on('click', function () {
    $('.filter-form').toggle();
});
