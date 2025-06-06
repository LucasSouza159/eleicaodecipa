module.exports = {
  content: [
    "./index.php",
    "./src/pages/**/*.{php,html,js}",
    // Adicione outros caminhos se você tiver componentes ou templates em outros locais
    // Ex: "./src/components/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        'cinza-chumbo': '#2E2E2E', // Exemplo de cor personalizada
        'roxo-principal': '#6B3FA0', // Exemplo de cor personalizada
        'azul-cipa': '#3A7CA5', // Um azul corporativo
        'verde-cipa': '#4CAF50', // Um verde para sucesso/confirmação
        'alerta-cipa': '#FFC107', // Amarelo para alertas
        'erro-cipa': '#F44336',   // Vermelho para erros
      },
      fontFamily: {
        'sans': ['Inter', 'Arial', 'sans-serif'], // Define Inter como a fonte sans-serif padrão, com fallbacks
      },
    },
  },
  plugins: [
    // require('@tailwindcss/forms'), // Exemplo de plugin, se você for usar formulários estilizados pelo Tailwind
  ],
}
