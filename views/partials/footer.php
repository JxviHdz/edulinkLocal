</main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-graduation-cap me-2"></i>EduLink</h5>
                    <p>Plataforma educativa para facilitar la interacción entre profesores y estudiantes.</p>
                </div>
                <div class="col-md-3">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_URL; ?>" class="text-white">Inicio</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/materiales.php" class="text-white">Materiales</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/foros.php" class="text-white">Foros</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i>info@edulink.com</li>
                        <li><i class="fas fa-phone me-2"></i>+57 320-xxx-xxxx</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> EduLink. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript principal -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>