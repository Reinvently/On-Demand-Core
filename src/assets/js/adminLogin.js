/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
$(function () {
    $('.form-signin').submit(function () {
        $.ajax({
            url: '/api/auth/login',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                window.location.href = '/admin';
            },
            error: function(response) {
                $('.form-group').removeClass('has-error');
                $('.help-block').html('');
                $.each(response.responseJSON.errors, function(key, value) {
                    var input = $('input[name="' + key + '"]');
                    input.parent('.form-group').addClass('has-error');
                    input.next('.help-block').html(value);
                });
            }
        });
        return false;
    });
});