jQuery(document).ready(function ($) {
    $('.delete-link').on('click', function (e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete this custom data?')) {
            return;
        }

        var id = $(this).data('id');
        $.post(customData.ajaxurl, {
            action: 'delete_custom_data',
            nonce: customData.delete_nonce,
            id: id
        }, function (response) {
            if (response.success) {
                location.reload();
            } else {
                alert('An error occurred while deleting the custom data.');
            }
        });
    });
});