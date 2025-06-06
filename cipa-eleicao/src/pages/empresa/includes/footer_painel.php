</div> <!-- Fecha o div container mx-auto px-4 py-8 -->
    </main>

    <footer class="bg-gray-800 text-gray-300 text-center p-4 shadow-inner mt-auto"> <!-- mt-auto para empurrar para baixo -->
        <p class="text-sm">&copy; <?php echo date("Y"); ?> CIPA Fácil Online. Todos os direitos reservados.</p>
        <!-- Poderia adicionar mais links ou informações no rodapé do painel se necessário -->
    </footer>

    <script>
        // Script para menu mobile (opcional, mas bom para usabilidade)
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true' || false;
                mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
                mobileMenu.classList.toggle('hidden');
                // Alterna ícones do botão (hambúrguer/fechar)
                mobileMenuButton.querySelectorAll('svg').forEach(icon => icon.classList.toggle('hidden'));
            });
        }
    </script>
</body>
</html>
