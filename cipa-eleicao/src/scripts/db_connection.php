<?php
// Variáveis de configuração do banco de dados
$db_host = 'localhost';
$db_name = 'cipa_eleicao';
$db_user = 'root';
$db_pass = ''; // Senha padrão do XAMPP/WAMPP geralmente é vazia
$charset = 'utf8mb4';

// Caminho para o arquivo .env na raiz do projeto
$envPath = __DIR__ . '/../../.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $envVariables = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) { // Ignorar comentários
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            // Remover aspas simples ou duplas do valor, se existirem
            $value = trim($value, "\"'");
            $_ENV[$name] = $value;
        }
    }
} else {
    // Adicionar um log ou aviso de que o arquivo .env não foi encontrado e valores padrão estão sendo usados.
    // error_log("Arquivo .env não encontrado. Usando configurações padrão de banco de dados.");
}

// Sobrescrever com variáveis do .env se elas existirem
$db_host = $_ENV['MYSQL_HOST'] ?? $db_host;
$db_name = $_ENV['MYSQL_DB'] ?? $db_name;
$db_user = $_ENV['MYSQL_USER'] ?? $db_user;
$db_pass = $_ENV['MYSQL_PASSWORD'] ?? $db_pass;

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lançar exceções em erros
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retornar arrays associativos por padrão
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar prepared statements nativos
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    error_log("Erro de conexão com o banco de dados: " . $e->getMessage() . " (DSN: $dsn, User: $db_user)");
    // Em um ambiente de produção, você não deveria exibir detalhes do erro para o usuário.
    // Para o desenvolvimento, pode ser útil exibir o erro:
    // die("Erro de conexão com o banco de dados: " . $e->getMessage() . "<br>DSN: $dsn<br>User: $db_user");
    // Para o usuário final, uma mensagem mais amigável:
    die("Não foi possível conectar ao banco de dados. Verifique as configurações ou tente novamente mais tarde.");
}

// A variável $pdo estará disponível para os scripts que incluírem este arquivo.
// Adicionar um comentário sobre a importância do .env
// echo "<!-- db_connection.php: Conexão estabelecida. Idealmente, configure via .env -->";
?>
