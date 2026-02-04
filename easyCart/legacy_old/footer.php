<?php
// Footer Component - Used in all pages
?>
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>EasyCart</h4>
                    <p>Your trusted e-commerce partner for quality products and exceptional service.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                    <a href="index.php#about">About</a>
                    <a href="index.php#contact">Contact</a>
                </div>
                <div class="footer-section">
                    <h4>Customer Service</h4>
                    <a href="#">Shipping Info</a>
                    <a href="#">Returns</a>
                    <a href="#">FAQ</a>
                    <a href="#">Support</a>
                </div>
                <div class="footer-section">
                    <h4>Follow Us</h4>
                    <a href="#">Facebook</a>
                    <a href="#">Twitter</a>
                    <a href="#">Instagram</a>
                    <a href="#">LinkedIn</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 EasyCart. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('accountModal');
            if (modal && event.target === modal) {
                closeAccountModal();
            }
        }

        // Add to cart with quantity
        function addToCart(productId, quantity = 1) {
            quantity = quantity || 1;
            
            // Create form to submit to cart.php
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'cart.php';
            
            const productInput = document.createElement('input');
            productInput.type = 'hidden';
            productInput.name = 'action';
            productInput.value = 'add';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'product_id';
            idInput.value = productId;
            
            const qtyInput = document.createElement('input');
            qtyInput.type = 'hidden';
            qtyInput.name = 'quantity';
            qtyInput.value = quantity;
            
            form.appendChild(productInput);
            form.appendChild(idInput);
            form.appendChild(qtyInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>
