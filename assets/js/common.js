// Client-side validation for registration form
function validateForm() {
    const username = $('#username').val();
    const email = $('#email').val();
    const password = $('#password').val();
    const confirmPassword = $('#confirm_password').val();
    
    // Reset error messages
    $('.error-message').remove();
    let isValid = true;

    // Username validation
    if (username.length < 3) {
        showError('username', 'Username must be at least 3 characters');
        isValid = false;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('email', 'Please enter a valid email address');
        isValid = false;
    }

    // Password validation
    if (password.length < 6) {
        showError('password', 'Password must be at least 6 characters');
        isValid = false;
    }

    // Confirm password validation
    if (password !== confirmPassword) {
        showError('confirm_password', 'Passwords do not match');
        isValid = false;
    }

    return isValid;
}

function showError(fieldId, message) {
    const $field = $('#' + fieldId);
    // Remove existing error message for this field
    removeError(fieldId);
    const $errorDiv = $('<div>', {
        'class': 'error-message text-danger small',
        text: message
    });
    $errorDiv.insertAfter($field);
}

// Add event listeners for real-time validation
$(document).ready(function() {
    // Username validation
    $('#username').on('input', function() {
        if ($(this).val().length < 3) {
            showError('username', 'Username must be at least 3 characters');
        } else {
            removeError('username');
        }
    });

    // Email validation
    $('#email').on('input', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test($(this).val())) {
            showError('email', 'Please enter a valid email address');
        } else {
            removeError('email');
        }
    });

    // Password validation
    $('#password').on('input', function() {
        if ($(this).val().length < 6) {
            showError('password', 'Password must be at least 6 characters');
        } else {
            removeError('password');
        }
        
        // Also check password match
        const confirmPassword = $('#confirm_password').val();
        if (confirmPassword !== '' && $(this).val() !== confirmPassword) {
            showError('confirm_password', 'Passwords do not match');
        } else {
            removeError('confirm_password');
        }
    });

    // Confirm password validation
    $('#confirm_password').on('input', function() {
        const password = $('#password').val();
        if (password !== $(this).val()) {
            showError('confirm_password', 'Passwords do not match');
        } else {
            removeError('confirm_password');
        }
    });
});

function removeError(fieldId) {
    // Remove only the error message immediately after the field
    const $field = $('#' + fieldId);
    if ($field.next('.error-message').length) {
        $field.next('.error-message').remove();
    }
}
