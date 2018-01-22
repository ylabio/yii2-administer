$('.clear-filters').on('click', function (e) {
    $(this).parents('form')[0].reset();
});
$('.filter-toggle').on('click', function () {
    $('.filter-form').toggle();
});
