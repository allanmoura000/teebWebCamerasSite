<?php
// Configurações da Brevo API
return [
    'api_key' => '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', // Você precisará substituir isso pela sua chave API da Brevo
    'sender_email' => 'allanmouraoficial2@gmail.com',
    'sender_name' => 'TEEB Web',
    'template_id' => 2, // Template ID atualizado
    // Para encontrar o ID do template:
    // 1. Acesse brevo.com
    // 2. Vá em Email > Transactional > Templates
    // 3. Crie um template com {{params.codigo}} para o código
    // 4. Copie o ID do template criado
];
?> 