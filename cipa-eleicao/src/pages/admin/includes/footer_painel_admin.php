</div> <!-- Fecha o div container mx-auto px-4 py-8 -->
    </main>

    <footer class="bg-gray-900 text-gray-400 text-center p-4 shadow-inner mt-auto">
        <p class="text-sm">&copy; <?php echo date("Y"); ?> CIPA Fácil Online. Painel Administrativo.</p>
    </footer>

    <script>
        // Script para menu mobile do admin
        const mobileMenuButtonAdmin = document.getElementById('mobile-menu-button-admin');
        const mobileMenuAdmin = document.getElementById('mobile-menu-admin');
        if (mobileMenuButtonAdmin && mobileMenuAdmin) {
            mobileMenuButtonAdmin.addEventListener('click', () => {
                const isExpanded = mobileMenuButtonAdmin.getAttribute('aria-expanded') === 'true' || false;
                mobileMenuButtonAdmin.setAttribute('aria-expanded', !isExpanded);
                mobileMenuAdmin.classList.toggle('hidden');
                mobileMenuButtonAdmin.querySelectorAll('svg').forEach(icon => icon.classList.toggle('hidden'));
            });
        }
    </script>
</body>
</html>
