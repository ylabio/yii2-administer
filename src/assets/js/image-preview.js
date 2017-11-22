function showImage(item, container)
{
    if (item.files && item.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#' + container).html('<img src="' + e.target.result + '" width=200><br><br>');
        };

        reader.readAsDataURL(item.files[0]);
    }
}
