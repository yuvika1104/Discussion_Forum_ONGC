document.addEventListener('DOMContentLoaded', function() {
    const cpfInput = document.getElementById('cpf_no');
    
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            if (value.length > 5) {
                value = value.substring(0, 5); // Limit to 5 digits
            }
            e.target.value = value;
        });
    }
});