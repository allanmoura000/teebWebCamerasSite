<?php
require __DIR__ . '/vendor/autoload.php';  // ajuste o caminho se necessário
use PHPMailer\PHPMailer\PHPMailer;

ini_set('display_errors',1);
error_reporting(E_ALL);

$m = new PHPMailer(true);
$m->isSMTP();
$m->SMTPDebug   = 2;              // mostra debug completo no browser
$m->Debugoutput = 'html';         // formata em HTML
$m->Host        = 'smtp.gmail.com';
$m->SMTPAuth    = true;
$m->Username    = 'allanmouraoficial2@gmail.com';
$m->Password    = 'zttjmzejzvcvdgps';    // sem espaços
$m->SMTPSecure  = 'tls';
$m->Port        = 587;

$m->setFrom('allanmouraoficial2@gmail.com','Teste');
$m->addAddress('destino@exemplo.com');
$m->Subject = 'Teste SMTP';
$m->Body    = 'Se você vê isso, SMTP está OK.';

$m->send();
echo 'E-mail enviado com sucesso!';
