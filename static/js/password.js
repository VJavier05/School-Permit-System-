function togglePasswordVisibility(el) {
    // Find the closest form-password-toggle container
    var formPasswordToggle = el.closest('.form-password-toggle');
    var formPasswordToggleIcon = formPasswordToggle.querySelector('i');
    var formPasswordToggleInput = formPasswordToggle.querySelector('input');
    
    // Check the current input type and toggle it
    if (formPasswordToggleInput.getAttribute('type') === 'text') {
        formPasswordToggleInput.setAttribute('type', 'password');
        formPasswordToggleIcon.classList.replace('bx-show', 'bx-hide');
    } else if (formPasswordToggleInput.getAttribute('type') === 'password') {
        formPasswordToggleInput.setAttribute('type', 'text');
        formPasswordToggleIcon.classList.replace('bx-hide', 'bx-show');
    }
}
