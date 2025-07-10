$(document).ready(function () {
    $(document).on('click', '.btn-delete-user', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        swal({
            title: "Attention",
            text: "Are you sure you want to delete this user?",
            icon: "warning",
            buttons: "Ok",
            confirmButtonColor: '#4b68ef',
            dangerMode: true,
        }).then((result) => {
            if (result) {
                var btn = $(this);
                btn.prop('disabled', true);
                $.ajax({
                    url: '/users/' + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name=\'csrf-token\']').attr('content')
                    },
                    success: function (res) {
                        if (res.status) {
                            swal({
                                title: 'Success',
                                text: res.message,
                                icon: 'success',
                            }).then(function(){
                                location.reload();
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
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                    }
                });
            }
        });
    });
}); 