$(document).ready(function () {
    // Add location row
    $('#addLocation').click(function () {
        var locationRow = `
            <div class="row location-row">
                <div class="col-md-5">
                    <select class="form-control location-select" name="location_ids[]">
                        <option value="">Select Location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control" name="stock[]" placeholder="Stock" min="0" value="0">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-remove-location">Remove</button>
                </div>
            </div>
        `;
        $('#locationContainer').append(locationRow);
    });

    // Remove location row
    $(document).on('click', '.btn-remove-location', function () {
        $(this).closest('.location-row').remove();
    });

    // Handle form submission
    $('#createProductForm').submit(function (e) {
        e.preventDefault();
        var form = this;
        var submitBtn = $(form).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
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
                        window.location.href = '/products';
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
                submitBtn.prop('disabled', false);
            }
        });
    });
}); 