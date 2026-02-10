<?php include TEMPLATES_PATH . '/header.php'; ?>

<section class="container" style="padding: 60px 20px;">
    <h2 class="section-title">Contact Us</h2>
    <div class="contact-grid">
        <div class="contact-info-card">
            <h3 style="margin-bottom: 20px; font-size: 1.5rem;">Get in Touch</h3>
            <div class="contact-info-item">
                <strong>📍 Address</strong>
                <p>123 Commerce St, Tech City</p>
            </div>
            <div class="contact-info-item">
                <strong>📧 Email</strong>
                <p>support@easycart.com</p>
            </div>
            <div>
                <strong>📞 Phone</strong>
                <p>+1 (555) 123-4567</p>
            </div>
        </div>

        <div class="contact-form-card">
            <h3 style="margin-bottom: 20px; font-size: 1.5rem; color: #111827;">Send Message</h3>
            <form id="contactForm" onsubmit="event.preventDefault(); submitContactForm(this);">
                <div class="contact-form-group">
                    <label class="contact-label">Name</label>
                    <input type="text" name="name" required class="contact-input">
                </div>
                <div class="contact-form-group">
                    <label class="contact-label">Email</label>
                    <input type="email" name="email" required class="contact-input">
                </div>
                <div class="contact-form-group">
                    <label class="contact-label">Message</label>
                    <textarea name="message" required rows="3" class="contact-input"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
            </form>
            
            <script>
            function submitContactForm(form) {
                const formData = new FormData(form);
                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                
                btn.innerHTML = 'Sending...';
                btn.disabled = true;
                
                fetch('contact-process', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        form.reset();
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred. Please try again.', 'error');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            }
            </script>
        </div>
    </div>
</section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
