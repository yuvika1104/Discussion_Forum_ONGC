// JavaScript for Organization Discussion Forum

document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.getElementById('imagePreview');
            
            if (previewContainer) {
                previewContainer.innerHTML = '';
                
                Array.from(files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'img-thumbnail';
                            img.style.maxWidth = '100px';
                            img.style.margin = '5px';
                            previewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    });
    
    // Image modal functionality
    const images = document.querySelectorAll('.image-gallery img');
    images.forEach(img => {
        img.addEventListener('click', function() {
            showImageModal(this.src);
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const departmentFilter = document.getElementById('departmentFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterThreads);
    }
    
    if (departmentFilter) {
        departmentFilter.addEventListener('change', filterThreads);
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.classList.contains('show')) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        }, 5000);
    });
    
    // Character count for textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        if (textarea.hasAttribute('maxlength')) {
            const maxLength = textarea.getAttribute('maxlength');
            const counterDiv = document.createElement('div');
            counterDiv.className = 'text-muted small text-end';
            counterDiv.innerHTML = `<span id="charCount_${textarea.id}">0</span>/${maxLength}`;
            textarea.parentNode.appendChild(counterDiv);
            
            textarea.addEventListener('input', function() {
                const charCount = this.value.length;
                document.getElementById(`charCount_${this.id}`).textContent = charCount;
                
                if (charCount > maxLength * 0.9) {
                    counterDiv.classList.add('text-warning');
                } else {
                    counterDiv.classList.remove('text-warning');
                }
            });
        }
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showAlert('Please fill in all required fields.', 'danger');
            }
        });
    });
    
    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});

// Function to show image modal
function showImageModal(imageSrc) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${imageSrc}" class="modal-img" alt="Image preview">
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to filter threads
function filterThreads() {
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const departmentFilter = document.getElementById('departmentFilter')?.value || '';
    const threads = document.querySelectorAll('.thread-card');
    
    threads.forEach(thread => {
        const title = thread.querySelector('.thread-title')?.textContent.toLowerCase() || '';
        const content = thread.querySelector('.thread-content')?.textContent.toLowerCase() || '';
        const department = thread.querySelector('.thread-department')?.textContent || '';
        
        const matchesSearch = title.includes(searchTerm) || content.includes(searchTerm);
        const matchesDepartment = !departmentFilter || department === departmentFilter;
        
        if (matchesSearch && matchesDepartment) {
            thread.style.display = 'block';
        } else {
            thread.style.display = 'none';
        }
    });
}

// Function to show alert messages
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        if (alertDiv.classList.contains('show')) {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }
    }, 5000);
}

// Function to format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Function to validate image files
function validateImageFile(file) {
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!allowedTypes.includes(file.type)) {
        showAlert('Please select a valid image file (JPEG, PNG, GIF).', 'danger');
        return false;
    }
    
    if (file.size > maxSize) {
        showAlert('Image file size must be less than 5MB.', 'danger');
        return false;
    }
    
    return true;
}

// Function to show loading spinner
function showLoading(element) {
    const spinner = document.createElement('span');
    spinner.className = 'spinner me-2';
    element.prepend(spinner);
    element.disabled = true;
}

// Function to hide loading spinner
function hideLoading(element) {
    const spinner = element.querySelector('.spinner');
    if (spinner) {
        spinner.remove();
    }
    element.disabled = false;
}

// Function to auto-resize textarea
function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

// Apply auto-resize to all textareas
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            autoResizeTextarea(this);
        });
    });
});
