// Contact Form AJAX Submission
document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const formMessage = document.getElementById('formMessage');
            const originalBtnText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '⏳ Sending...';
            submitBtn.disabled = true;
            formMessage.style.display = 'none';

            // Get form data
            const formData = new FormData(this);

            // Submit via AJAX
            fetch('contact-handler.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        formMessage.style.display = 'block';
                        formMessage.style.background = 'linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%)';
                        formMessage.style.color = '#155724';
                        formMessage.style.border = '2px solid #c3e6cb';
                        formMessage.innerHTML = '✅ ' + data.message;

                        // Reset form
                        contactForm.reset();

                        // Hide message after 5 seconds
                        setTimeout(() => {
                            formMessage.style.display = 'none';
                        }, 5000);
                    } else {
                        // Show error message
                        formMessage.style.display = 'block';
                        formMessage.style.background = 'linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%)';
                        formMessage.style.color = '#721c24';
                        formMessage.style.border = '2px solid #f5c6cb';
                        formMessage.innerHTML = '❌ ' + data.message;
                    }

                    // Reset button
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    formMessage.style.display = 'block';
                    formMessage.style.background = 'linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%)';
                    formMessage.style.color = '#721c24';
                    formMessage.style.border = '2px solid #f5c6cb';
                    formMessage.innerHTML = '❌ An error occurred. Please try again.';

                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });
    }
});
