function showRegisterForm() {
    $('.formBox').fadeOut('fast', function() {
        $('.registerBox').fadeIn('fast');
        $('.form-footer').fadeOut('fast', function() {
            $('.register-footer').fadeIn('fast');
        });
        $('.modal-title').html('Register with');
    });
    $('.error').removeClass('alert alert-danger').html('');

}

function showformForm() {
    $('#formModal .registerBox').fadeOut('fast', function() {
        $('.formBox').fadeIn('fast');
        $('.register-footer').fadeOut('fast', function() {
            $('.form-footer').fadeIn('fast');
        });

        $('.modal-title').html('form with');
    });
    $('.error').removeClass('alert alert-danger').html('');
}

function openformModal() {
    showformForm();
    setTimeout(function() {
        $('#formModal').modal('show');
    }, 230);

}

function openRegisterModal() {
    showRegisterForm();
    setTimeout(function() {
        $('#formModal').modal('show');
    }, 230);

}


function shakeModal() {
    $('#formModal .modal-dialog').addClass('shake');
    $('.error').addClass('alert alert-danger').html("Invalid email/password combination");
    $('input[type="password"]').val('');
    setTimeout(function() {
        $('#formModal .modal-dialog').removeClass('shake');
    }, 1000);
}