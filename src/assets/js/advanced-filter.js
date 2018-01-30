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
    $('body').prepend('<div id="mask"></div>');
    $('#mask').fadeIn(300);
    $('.filter-form').show();
});
$('body').on('click', '#mask', function () {
    $('.filter-form').hide();
    $('#mask').fadeOut(300, function () {
        $(this).remove();
    });
});
