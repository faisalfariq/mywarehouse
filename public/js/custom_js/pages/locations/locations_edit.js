$(document).ready(function () {
    $('#form_edit_location').on('submit', function (e) {
        e.preventDefault();
        var form = this;
        var btn = $(form).find('button[type="submit"]');
        btn.prop('disabled', true);
        // Reset error
        $(form).find('.invalid-feedback').text('');
        $(form).find('.form-control').removeClass('is-invalid');
        var form_data = new FormData(form);
        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: form_data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                if (res.status) {
                    swal({
                        title: 'Success',
                        text: res.message,
                        icon: 'success',
                    }).then(function(){
                        window.location.href = '/locations';
                    });
                } else {
                    swal({
                        title: 'Something Wrong!',
                        text: res.message,
                        icon: 'error',
                    });
                }
            },
            error: function (xhr) {
                swal({
                    title: 'Something Wrong!',
                    text: xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred.',
                    icon: 'error',
                });
                if(xhr.status === 422 && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function (key) {
                        var errorMessage = '';
                        errors[key].forEach(function (msg) {
                            errorMessage += msg + '\n';
                        });
                        var errorElement = $(form).find('[name="' + key + '"]');
                        errorElement.addClass('is-invalid');
                        $(form).find('.' + key + '_error').html(errorMessage);
                    });
                }
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
}); 