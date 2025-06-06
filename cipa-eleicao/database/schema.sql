-- Arquivo de schema do banco de dados para o projeto CIPA Eleição

-- Tabela para armazenar informações das empresas cadastradas
CREATE TABLE IF NOT EXISTS `empresas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único da empresa',
    `nome_fantasia` VARCHAR(255) NOT NULL COMMENT 'Nome fantasia da empresa',
    `razao_social` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Razão social da empresa (deve ser única)',
    `cnpj` VARCHAR(18) NOT NULL UNIQUE COMMENT 'CNPJ da empresa (formato XX.XXX.XXX/XXXX-XX, deve ser único)',
    `email` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email de contato principal da empresa (deve ser único)',
    `senha_hash` VARCHAR(255) NOT NULL COMMENT 'Hash da senha de acesso da empresa',
    `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora do cadastro da empresa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabela de empresas';

-- Tabela para armazenar informações das filiais das empresas
CREATE TABLE IF NOT EXISTS `filiais` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único da filial',
    `empresa_id` INT NOT NULL COMMENT 'Identificador da empresa à qual a filial pertence',
    `nome_fantasia` VARCHAR(255) NOT NULL COMMENT 'Nome fantasia da filial',
    `cnpj` VARCHAR(18) UNIQUE COMMENT 'CNPJ da filial (formato XX.XXX.XXX/XXXX-XX, deve ser único se preenchido)',
    `endereco` TEXT COMMENT 'Endereço completo da filial',
    `cidade` VARCHAR(100) COMMENT 'Cidade da filial',
    `estado` VARCHAR(2) COMMENT 'Sigla do estado (UF) da filial',
    `cep` VARCHAR(9) COMMENT 'CEP da filial (formato XXXXX-XXX)',
    `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora do cadastro da filial',
    FOREIGN KEY (`empresa_id`) REFERENCES `empresas`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabela de filiais de empresas';

-- Tabela para armazenar informações sobre as eleições da CIPA
CREATE TABLE IF NOT EXISTS `eleicoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único da eleição',
    `empresa_id` INT NOT NULL COMMENT 'ID da empresa que está realizando a eleição',
    `filial_id` INT NULL COMMENT 'ID da filial específica para a eleição (opcional, NULL se for para a empresa como um todo)',
    `titulo_eleicao` VARCHAR(255) NOT NULL COMMENT 'Título descritivo da eleição (ex: Eleição CIPA 2023/2024 - Matriz)',
    `descricao` TEXT NULL COMMENT 'Descrição adicional sobre a eleição',
    `ano_referencia` YEAR NOT NULL COMMENT 'Ano de referência da gestão da CIPA (ex: 2023)',
    `data_convocacao` DATE NOT NULL COMMENT 'Data de convocação da eleição (NR-05: min 60 dias antes do término do mandato atual)',
    `data_inicio_inscricao_candidatos` DATETIME NOT NULL COMMENT 'Data e hora de início do período de inscrição de candidatos',
    `data_fim_inscricao_candidatos` DATETIME NOT NULL COMMENT 'Data e hora de término do período de inscrição de candidatos',
    `data_inicio_votacao` DATETIME NOT NULL COMMENT 'Data e hora de início da votação',
    `data_fim_votacao` DATETIME NOT NULL COMMENT 'Data e hora de término da votação',
    `data_apuracao` DATETIME NOT NULL COMMENT 'Data e hora da apuração dos votos',
    `data_posse_eleitos` DATE NOT NULL COMMENT 'Data da posse dos membros eleitos da CIPA',
    `status_eleicao` ENUM(
        'Planejada',
        'Convocada',
        'Inscrições Abertas',
        'Inscrições Encerradas',
        'Em Votação',
        'Votação Encerrada',
        'Em Apuração',
        'Resultados Publicados',
        'Finalizada',
        'Cancelada'
    ) NOT NULL DEFAULT 'Planejada' COMMENT 'Status atual do processo eleitoral',
    `numero_titulares_previstos` INT DEFAULT 0 COMMENT 'Número de membros titulares a serem eleitos',
    `numero_suplentes_previstos` INT DEFAULT 0 COMMENT 'Número de membros suplentes a serem eleitos',
    `observacoes_gerais` TEXT NULL COMMENT 'Observações ou anotações gerais sobre a eleição',
    `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora de criação do registro da eleição',
    `data_atualizacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data e hora da última atualização do registro da eleição',
    FOREIGN KEY (`empresa_id`) REFERENCES `empresas`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`filial_id`) REFERENCES `filiais`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabela de eleições da CIPA';

-- Tabela para armazenar dados dos funcionários das empresas
CREATE TABLE IF NOT EXISTS `funcionarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único do funcionário',
    `empresa_id` INT NOT NULL COMMENT 'ID da empresa à qual o funcionário pertence',
    `filial_id` INT NULL COMMENT 'ID da filial à qual o funcionário está alocado (opcional)',
    `nome_completo` VARCHAR(255) NOT NULL COMMENT 'Nome completo do funcionário',
    `cpf` VARCHAR(14) NOT NULL COMMENT 'CPF do funcionário (formato XXX.XXX.XXX-XX)',
    `data_nascimento` DATE NULL COMMENT 'Data de nascimento do funcionário (para autenticação na votação)',
    `email` VARCHAR(255) NULL COMMENT 'Email do funcionário (opcional)',
    `matricula` VARCHAR(50) NULL COMMENT 'Número de matrícula do funcionário na empresa (opcional)',
    `data_admissao` DATE NOT NULL COMMENT 'Data de admissão do funcionário',
    `cargo` VARCHAR(100) NULL COMMENT 'Cargo do funcionário',
    `departamento` VARCHAR(100) NULL COMMENT 'Departamento do funcionário',
    `status_funcionario` ENUM('Ativo', 'Inativo', 'Demitido', 'Afastado') NOT NULL DEFAULT 'Ativo' COMMENT 'Status atual do funcionário na empresa',
    `permite_votar` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Indica se o funcionário está apto a votar nas eleições da CIPA',
    `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora do cadastro do funcionário no sistema',
    `data_atualizacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data e hora da última atualização do registro do funcionário',

    FOREIGN KEY (`empresa_id`) REFERENCES `empresas`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`filial_id`) REFERENCES `filiais`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,

    UNIQUE KEY `uk_funcionario_empresa_cpf` (`empresa_id`, `cpf`) COMMENT 'Garante que um CPF seja único por empresa',
    UNIQUE KEY `uk_funcionario_empresa_email` (`empresa_id`, `email`) COMMENT 'Garante que um email seja único por empresa, se fornecido',
    UNIQUE KEY `uk_funcionario_empresa_matricula` (`empresa_id`, `matricula`) COMMENT 'Garante que uma matrícula seja única por empresa, se fornecida'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabela de funcionários das empresas';

-- Tabela para armazenar os membros da comissão eleitoral
CREATE TABLE IF NOT EXISTS `comissao` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único do membro da comissão',
    `eleicao_id` INT NOT NULL COMMENT 'ID da eleição à qual este membro pertence',
    `funcionario_id` INT NULL COMMENT 'ID do funcionário (se aplicável e cadastrado no sistema)',
    `nome_completo` VARCHAR(255) NOT NULL COMMENT 'Nome completo do membro da comissão',
    `email` VARCHAR(255) NOT NULL COMMENT 'Email do membro da comissão (para contato/login)',
    `cpf` VARCHAR(14) NOT NULL COMMENT 'CPF do membro da comissão (formato XXX.XXX.XXX-XX)',
    `papel_comissao` ENUM('Presidente', 'Secretário', 'Membro') NOT NULL COMMENT 'Papel do membro na comissão eleitoral',
    `senha_hash_comissao` VARCHAR(255) NULL COMMENT 'Hash da senha para acesso do membro ao painel da comissão (se houver)',
    `data_designacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora da designação do membro para a comissão',

    FOREIGN KEY (`eleicao_id`) REFERENCES `eleicoes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,

    UNIQUE KEY `uk_comissao_eleicao_email` (`eleicao_id`, `email`) COMMENT 'Garante que um email seja único por eleição',
    UNIQUE KEY `uk_comissao_eleicao_cpf` (`eleicao_id`, `cpf`) COMMENT 'Garante que um CPF seja único por eleição'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabela de membros da comissão eleitoral';

-- Tabela para armazenar os candidatos de uma eleição
CREATE TABLE IF NOT EXISTS `candidatos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único do candidato',
    `eleicao_id` INT NOT NULL COMMENT 'ID da eleição para a qual o funcionário se candidatou',
    `funcionario_id` INT NOT NULL COMMENT 'ID do funcionário que se candidatou',
    `numero_candidato` INT NULL COMMENT 'Número do candidato na urna (opcional, pode ser gerado/definido pela comissão)',
    `nome_urna` VARCHAR(100) NULL COMMENT 'Nome que aparecerá na urna (pode ser nome completo ou apelido)',
    `plataforma_propostas` TEXT NULL COMMENT 'Propostas e plataforma do candidato',
    `status_candidatura` ENUM('Inscrito', 'Aprovado', 'Reprovado', 'Eleito', 'Suplente', 'Não Eleito') NOT NULL DEFAULT 'Inscrito' COMMENT 'Status da candidatura',
    `data_inscricao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora da inscrição do candidato',

    FOREIGN KEY (`eleicao_id`) REFERENCES `eleicoes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,

    UNIQUE KEY `uk_candidato_eleicao_funcionario` (`eleicao_id`, `funcionario_id`) COMMENT 'Garante que um funcionário não se candidate múltiplas vezes na mesma eleição',
    UNIQUE KEY `uk_candidato_eleicao_numero` (`eleicao_id`, `numero_candidato`) COMMENT 'Garante que o número do candidato seja único por eleição, se utilizado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabela de candidatos por eleição';

-- Tabela para registrar os votos
CREATE TABLE IF NOT EXISTS `votos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único do voto',
    `eleicao_id` INT NOT NULL COMMENT 'ID da eleição em que o voto foi registrado',
    `funcionario_id` INT NOT NULL COMMENT 'ID do funcionário que votou',
    `candidato_id` INT NULL COMMENT 'ID do candidato que recebeu o voto (NULL para branco/nulo)',
    `tipo_voto_especial` ENUM('Branco', 'Nulo') DEFAULT NULL COMMENT 'Diferencia voto em branco de nulo quando candidato_id é NULL',
    `hash_voto` VARCHAR(255) NOT NULL COMMENT 'Hash representativo do voto para integridade e auditoria',
    `ip_votante` VARCHAR(45) NULL COMMENT 'IP do dispositivo do votante',
    `user_agent_votante` TEXT NULL COMMENT 'User agent do navegador do votante',
    `data_hora_voto` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Momento exato do registro do voto',

    FOREIGN KEY (`eleicao_id`) REFERENCES `eleicoes`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (`candidato_id`) REFERENCES `candidatos`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,

    UNIQUE KEY `uk_voto_eleicao_funcionario` (`eleicao_id`, `funcionario_id`) COMMENT 'Garante que um funcionário vote apenas uma vez por eleição'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabela de registro de votos';

-- Tabela para armazenar Atas e Documentos Gerados
CREATE TABLE IF NOT EXISTS `atas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único da ata/documento',
    `eleicao_id` INT NOT NULL COMMENT 'ID da eleição à qual esta ata se refere',
    `tipo_ata` ENUM('Convocacao', 'InstalacaoPosse', 'ResultadoEleicao', 'ListaVotantes', 'Outra') NOT NULL COMMENT 'Tipo do documento gerado',
    `titulo_documento` VARCHAR(255) NOT NULL COMMENT 'Título descritivo do documento/ata',
    `conteudo_ata` TEXT NULL COMMENT 'Conteúdo HTML ou resumo da ata (opcional)',
    `gerada_por_usuario_id` INT NULL COMMENT 'ID do usuário (comissão/empresa) que gerou o documento (referência futura)',
    `data_geracao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora da geração do documento',
    `nome_arquivo_fisico` VARCHAR(255) NULL COMMENT 'Nome do arquivo PDF salvo no servidor (se aplicável)',

    FOREIGN KEY (`eleicao_id`) REFERENCES `eleicoes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
    -- FOREIGN KEY (`gerada_por_usuario_id`) REFERENCES `usuarios_sistema`(`id`) ON DELETE SET NULL ON UPDATE CASCADE; -- Exemplo se houver tabela de usuários do sistema
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabela para Atas e Documentos Gerados do Processo Eleitoral';
