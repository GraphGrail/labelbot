//== Class Definition
var SnippetResetPassword = function() {

    var login = $('#m_login');

    var removeErrors = function (form) {
        form.find('.alert').remove();
    }

    var showErrorMsg = function(form, type, msg) {
        var alert = $('<div class="m-alert m-alert--outline alert alert-' + type + ' alert-dismissible" role="alert">\
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\
			<div class="auth-form-err-message-container"><span></span></div>\
		</div>');

        removeErrors(form);
        alert.prependTo(form);
        alert.animateClass('fadeIn animated');
        alert.find('span').html(msg);
    }

    //== Private Functions

    var displayForgetPasswordForm = function() {
        login.removeClass('m-login--signin');
        login.removeClass('m-login--signup');

        login.addClass('m-login--forget-password');
    }


    var handleForgetPasswordFormSubmit = function() {
        $('#m_login_forget_password_submit').click(function(e) {
            e.preventDefault();

            var btn = $(this);
            var form = $(this).closest('form');

            form.validate({
                rules: {
                    'ResetPasswordForm[password]': {
                        required: true,
                    }
                }
            });

            if (!form.valid()) {
                return;
            }
            removeErrors(form);

            btn.addClass('m-loader m-loader--right m-loader--light').attr('disabled', true);

            form.ajaxSubmit({
                success: function(response, status, xhr, $form) {
                    btn.removeClass('m-loader m-loader--right m-loader--light').attr('disabled', false);
                    if (response.errors) {
                        var field = '';
                        $.each(response.errors, function(_, error) {
                            field += error + '<br/>';
                        });

                        showErrorMsg(form, 'danger', field);
                        return;
                    }
                    if (response.success) {
                        showErrorMsg(form, 'success', response.success);
                        response.redirect && (window.location = response.redirect);
                    }
                    form.clearForm(); // clear form
                    form.validate().resetForm(); // reset validation states
                }
            });
        });
    }

    //== Public Functions
    return {
        // public functions
        init: function() {
            displayForgetPasswordForm();
            handleForgetPasswordFormSubmit();
        }
    };
}();

//== Class Initialization
jQuery(document).ready(function() {
    SnippetResetPassword.init();
});