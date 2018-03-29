//== Class Definition
var SnippetLogin = function() {

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

    var displaySignUpForm = function() {
        login.removeClass('m-login--forget-password');
        login.removeClass('m-login--signin');

        login.addClass('m-login--signup');
        login.find('.m-login__signup').animateClass('flipInX animated');
    }

    var displaySignInForm = function() {
        login.removeClass('m-login--forget-password');
        login.removeClass('m-login--signup');

        login.addClass('m-login--signin');
        login.find('.m-login__signin').animateClass('flipInX animated');
    }

    var displayForgetPasswordForm = function() {
        login.removeClass('m-login--signin');
        login.removeClass('m-login--signup');

        login.addClass('m-login--forget-password');
        login.find('.m-login__forget-password').animateClass('flipInX animated');
    }

    var handleFormSwitch = function() {
        $('#m_login_forget_password').click(function(e) {
            e.preventDefault();
            displayForgetPasswordForm();
        });

        $('#m_login_forget_password_cancel').click(function(e) {
            e.preventDefault();
            displaySignInForm();
        });

        $('#m_login_signup').click(function(e) {
            e.preventDefault();
            displaySignUpForm();
        });

        $('#m_login_signup_cancel').click(function(e) {
            e.preventDefault();
            displaySignInForm();
        });
    }

    var handleSignInFormSubmit = function() {
        $('#m_login_signin_submit').click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $(this).closest('form');

            form.validate({
                rules: {
                    'LoginForm[username]': {
                        required: true,
                    },
                    'LoginForm[password]': {
                        required: true
                    }
                }
            });

            if (!form.valid()) {
                return;
            }
            removeErrors(form);

            btn.addClass('m-loader m-loader--right m-loader--light').attr('disabled', true);

            form.submit();
        });
    }

    var handleSignUpFormSubmit = function() {
        $('#m_login_signup_submit').click(function(e) {
            e.preventDefault();

            var btn = $(this);
            var form = $(this).closest('form');

            form.validate({
                rules: {
                    'SignupForm[username]': {
                        required: true
                    },
                    'SignupForm[email]': {
                        required: true,
                        email: true
                    },
                    'SignupForm[password]': {
                        required: true
                    },
                    'SignupForm[password_confirm]': {
                        required: true
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
                    if (response.errors) {
                        btn.removeClass('m-loader m-loader--right m-loader--light').attr('disabled', false);
                        var field = $('<ul/>');
                        $.each(response.errors, function(_, error) {
                            field.append('<li>' + error + '</li>');
                        });

                        showErrorMsg(form, 'danger', field);
                        return;
                    }
                    if (response.redirect) {
                        showErrorMsg(form, 'success', 'Thank you. Registration complete');
                        window.location = response.redirect;
                    }
                }
            });
        });
    }

    var handleForgetPasswordFormSubmit = function() {
        $('#m_login_forget_password_submit').click(function(e) {
            e.preventDefault();

            var btn = $(this);
            var form = $(this).closest('form');

            form.validate({
                rules: {
                    'PasswordResetRequestForm[email]': {
                        required: true,
                        email: true
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
            handleFormSwitch();
            handleSignInFormSubmit();
            handleSignUpFormSubmit();
            handleForgetPasswordFormSubmit();
        }
    };
}();

//== Class Initialization
jQuery(document).ready(function() {
    SnippetLogin.init();
});