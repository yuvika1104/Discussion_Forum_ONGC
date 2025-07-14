document.addEventListener('DOMContentLoaded', function() {
    // Profile form validation
    const profileForm = document.querySelector('form[action="executeProfileUpdate.php"]');
    if (profileForm) {
        // Name validation
        const nameInput = document.getElementById('name');
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                if (this.value.trim().length > 0) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        }

        // Email validation
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailRegex.test(this.value)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        }

        // Department validation
        const departmentSelect = document.getElementById('department');
        if (departmentSelect) {
            departmentSelect.addEventListener('change', function() {
                if (this.value) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        }

        // Image preview
        const profileImageInput = document.getElementById('profile_image');
        const imagePreview = document.getElementById('imagePreview');
        if (profileImageInput && imagePreview) {
            profileImageInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imagePreview.innerHTML = `
                            <img src="${event.target.result}" class="img-thumbnail mt-2" style="max-width: 200px;">
                        `;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    }

    // Password form validation
    const passwordForm = document.querySelector('form[action="executeProfileUpdate.php"][method="POST"]:not([enctype])');
    if (passwordForm) {
        // Current password validation
        const currentPasswordInput = document.getElementById('current_password');
        
        // New password validation
        const newPasswordInput = document.getElementById('new_password');
        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', function() {
                if (this.value.length >= 6) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
                validatePasswordMatch();
            });
        }

        // Confirm password validation
        const confirmPasswordInput = document.getElementById('confirm_password');
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', validatePasswordMatch);
        }

        function validatePasswordMatch() {
            if (newPasswordInput && confirmPasswordInput) {
                if (newPasswordInput.value && confirmPasswordInput.value) {
                    if (newPasswordInput.value === confirmPasswordInput.value) {
                        confirmPasswordInput.setCustomValidity('');
                        confirmPasswordInput.classList.remove('is-invalid');
                        confirmPasswordInput.classList.add('is-valid');
                    } else {
                        confirmPasswordInput.setCustomValidity('Passwords do not match');
                        confirmPasswordInput.classList.remove('is-valid');
                        confirmPasswordInput.classList.add('is-invalid');
                    }
                }
            }
        }
    }

    // Form submission handling
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Trigger validation for all fields
            const inputs = form.querySelectorAll('input, select, textarea');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    });
});