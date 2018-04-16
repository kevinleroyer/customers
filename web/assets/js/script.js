$(document).ready(function() {
    $('#search-customer-input').on('keyup', function(event) {
        var text = $(this).val();
        if (text.length >= 3) {
            $.ajax({
                method: "POST",
                url: $('#ajax_path').val(),
                data: { text: text },
                success: function(response) {
                    $('#customers-list').html(response.data);
                }
            })
        } else if (text.length == 0) {
            $.ajax({
                method: "POST",
                url: $('#ajax_path').val(),
                data: { text: text },
                success: function(response) {
                    $('#customers-list').html(response.data);
                }
            })
        }
    });
});