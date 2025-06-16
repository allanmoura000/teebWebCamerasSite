<?php
header('Content-Type: text/plain');

$domain = 'teebweb.com.br';

echo "Verificando configurações DNS para {$domain}\n\n";

// Verifica registros SPF
echo "Verificando registro SPF:\n";
$spf_records = dns_get_record($domain, DNS_TXT);
$spf_found = false;
foreach ($spf_records as $record) {
    if (isset($record['txt']) && strpos($record['txt'], 'v=spf1') !== false) {
        echo "SPF encontrado: " . $record['txt'] . "\n";
        $spf_found = true;
        break;
    }
}
if (!$spf_found) {
    echo "AVISO: Registro SPF não encontrado!\n";
    echo "Recomendação: Adicione um registro SPF como:\n";
    echo "v=spf1 ip4:191.7.32.16 include:_spf.google.com ~all\n\n";
}

// Verifica registros MX
echo "\nVerificando registros MX:\n";
$mx_records = dns_get_record($domain, DNS_MX);
if (empty($mx_records)) {
    echo "AVISO: Registros MX não encontrados!\n";
} else {
    foreach ($mx_records as $record) {
        echo "MX: " . $record['target'] . " (Prioridade: " . $record['pri'] . ")\n";
    }
}

// Verifica registros DKIM
echo "\nVerificando registros DKIM:\n";
$dkim_records = dns_get_record('default._domainkey.' . $domain, DNS_TXT);
if (empty($dkim_records)) {
    echo "AVISO: Registro DKIM não encontrado!\n";
    echo "Recomendação: Configure o DKIM para melhorar a entrega de emails\n\n";
} else {
    foreach ($dkim_records as $record) {
        if (isset($record['txt'])) {
            echo "DKIM encontrado: " . $record['txt'] . "\n";
        }
    }
}

// Verifica registros DMARC
echo "\nVerificando registro DMARC:\n";
$dmarc_records = dns_get_record('_dmarc.' . $domain, DNS_TXT);
if (empty($dmarc_records)) {
    echo "AVISO: Registro DMARC não encontrado!\n";
    echo "Recomendação: Adicione um registro DMARC como:\n";
    echo "v=DMARC1; p=none; rua=mailto:allanmoura@teebweb.com.br\n\n";
} else {
    foreach ($dmarc_records as $record) {
        if (isset($record['txt'])) {
            echo "DMARC encontrado: " . $record['txt'] . "\n";
        }
    }
}

// Verifica PTR (Reverse DNS)
echo "\nVerificando PTR (Reverse DNS):\n";
$ip = '191.7.32.16';
$ptr = gethostbyaddr($ip);
if ($ptr === $ip) {
    echo "AVISO: PTR não encontrado para {$ip}!\n";
    echo "Recomendação: Configure o PTR para apontar para {$domain}\n\n";
} else {
    echo "PTR encontrado: {$ptr}\n";
}

// Verifica se o IP está em listas negras
echo "\nVerificando listas negras:\n";
$blacklists = [
    'zen.spamhaus.org',
    'bl.spamcop.net',
    'cbl.abuseat.org',
    'dnsbl.sorbs.net'
];

foreach ($blacklists as $bl) {
    $lookup = $ip . '.' . $bl;
    $result = gethostbyname($lookup);
    if ($result === $lookup) {
        echo "{$bl}: OK\n";
    } else {
        echo "AVISO: IP listado em {$bl}!\n";
    }
}

echo "\nRecomendações finais:\n";
echo "1. Configure todos os registros DNS mencionados acima\n";
echo "2. Aguarde a propagação dos DNS (pode levar até 24 horas)\n";
echo "3. Use ferramentas como mxtoolbox.com para verificar a configuração completa\n";
echo "4. Considere usar um serviço de email transacional como SendGrid ou Amazon SES\n";
?>   