document.addEventListener('DOMContentLoaded', function() {

    document.getElementById('title').addEventListener('input', function(e) {
        const charCount = e.target.value.length;
        const maxLength = e.target.getAttribute('maxlength');
        
        let counter = e.target.parentNode.querySelector('.char-counter');
        if (!counter) {
            counter = document.createElement('div');
            counter.className = 'char-counter text-muted small text-end';
            e.target.parentNode.appendChild(counter);
        }
        
        counter.innerHTML = `${charCount}/${maxLength}`;
        
        if (charCount > maxLength * 0.9) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }
    });

    // Character counter for content
    document.getElementById('content').addEventListener('input', function(e) {
        const charCount = e.target.value.length;
        const maxLength = e.target.getAttribute('maxlength');
        
        let counter = e.target.parentNode.querySelector('.char-counter');
        if (!counter) {
            counter = document.createElement('div');
            counter.className = 'char-counter text-muted small text-end';
            e.target.parentNode.appendChild(counter);
        }
        
        counter.innerHTML = `${charCount}/${maxLength}`;
        
        if (charCount > maxLength * 0.9) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }
    });

    // Image preview and validation
    document.getElementById('images').addEventListener('change', function(e) {
        const files = e.target.files;
        const previewContainer = document.getElementById('imagePreview');
        const maxFiles = 5;
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        previewContainer.innerHTML = '';
        
        if (files.length > maxFiles) {
            alert(`You can only upload maximum ${maxFiles} images.`);
            e.target.value = '';
            return;
        }
        
        Array.from(files).forEach((file, index) => {
            if (!file.type.startsWith('image/')) {
                alert('Please select only image files.');
                e.target.value = '';
                return;
            }
            
            if (file.size > maxSize) {
                alert('Each image must be less than 5MB.');
                e.target.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'd-inline-block position-relative me-2 mb-2';
                div.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 120px; max-height: 120px;">
                    <div class="small text-muted text-center">${file.name}</div>
                `;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // Form submission with loading state
    document.getElementById('threadForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
        submitBtn.disabled = true;
    });

    // Auto-resize textarea
    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    document.getElementById('content').addEventListener('input', function() {
        autoResize(this);
    });

    // Initialize textarea size
    autoResize(document.getElementById('content'));

});
