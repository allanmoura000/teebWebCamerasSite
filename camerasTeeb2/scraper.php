<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$site_url = "https://www.ibiapaba24horas.com.br/";

// Função para buscar conteúdo da URL usando cURL
function getUrlContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Apenas se houver problemas com SSL
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

// Obtém o HTML do site
$html = getUrlContent($site_url);
if (!$html) {
    echo json_encode(["error" => "Falha ao acessar o site"]);
    exit;
}

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();
$xpath = new DOMXPath($dom);

// Seleciona os títulos das notícias
$titulos = $xpath->query("//h2/a | //h3/a");

$noticias = [];
$news_links = [];

foreach ($titulos as $titulo_element) {
    $titulo = trim($titulo_element->textContent);
    $link = $titulo_element->getAttribute("href");
    if (!str_starts_with($link, "http")) {
        $link = $site_url . ltrim($link, "/");
    }
    $news_links[] = ['titulo' => $titulo, 'link' => $link];
}

// Usa curl_multi para buscar as páginas das notícias simultaneamente
$mh = curl_multi_init();
$curl_handles = [];
$responses = [];

foreach ($news_links as $key => $news) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $news['link']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_multi_add_handle($mh, $ch);
    $curl_handles[$key] = $ch;
}

do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

foreach ($curl_handles as $key => $ch) {
    $responses[$key] = curl_multi_getcontent($ch);
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}
curl_multi_close($mh);

// Extrai a imagem de cada notícia
foreach ($news_links as $key => $news) {
    $imagem = "";
    $noticia_html = $responses[$key];
    if ($noticia_html) {
        $noticia_dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $noticia_dom->loadHTML($noticia_html);
        libxml_clear_errors();
        $noticia_xpath = new DOMXPath($noticia_dom);
        $imagem_element = $noticia_xpath->query("//meta[@property='og:image']");
        if ($imagem_element->length > 0) {
            $imagem = $imagem_element->item(0)->getAttribute("content");
        }
    }
    $noticias[] = [
        "titulo" => $news['titulo'],
        "link"   => $news['link'],
        "imagem" => $imagem
    ];
}

if (empty($noticias)) {
    echo json_encode(["error" => "Nenhuma notícia encontrada. Verifique os seletores."]);
} else {
    echo json_encode($noticias);
}
?>
