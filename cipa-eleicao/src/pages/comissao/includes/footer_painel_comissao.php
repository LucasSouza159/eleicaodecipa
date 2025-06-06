</div> <!-- Fecha o div container mx-auto px-4 py-8 -->
    </main>

    <footer class="bg-gray-800 text-gray-300 text-center p-4 shadow-inner mt-auto">
        <p class="text-sm">&copy; <?php echo date("Y"); ?> CIPA Fácil Online. Painel da Comissão.</p>
    </footer>

    <script>
        // Script para menu mobile da comissão
        const mobileMenuButtonComissao = document.getElementById('mobile-menu-button-comissao');
        const mobileMenuComissao = document.getElementById('mobile-menu-comissao');
        if (mobileMenuButtonComissao && mobileMenuComissao) {
            mobileMenuButtonComissao.addEventListener('click', () => {
                const isExpanded = mobileMenuButtonComissao.getAttribute('aria-expanded') === 'true' || false;
                mobileMenuButtonComissao.setAttribute('aria-expanded', !isExpanded);
                mobileMenuComissao.classList.toggle('hidden');
                mobileMenuButtonComissao.querySelectorAll('svg').forEach(icon => icon.classList.toggle('hidden'));
            });
        }
    </script>
</body>
</html>
