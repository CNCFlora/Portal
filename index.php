<?php
session_start();

require 'vendor/autoload.php';
require 'Utils.php';
require 'View.php';
require 'Usuarios.php';
require 'Especies.php';
require 'Avaliacoes.php';

require_once '../includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$rest = new Rest\Server();

$rest->setAccept(array("*"));


$usuarios =new Usuarios();
$user = $usuarios->getCurrentUser();
$rest->setParameter("user",$user);
$hasUser = $user->uid > 0;
$rest->setParameter("logged",$hasUser);

$rest->addMap("GET","/portal/",function($r) {
    $intro_node = node_load('24');
    $intro = $intro_node->body['pt-br'][0]['value'];

    $news_nodes = node_load_multiple(array(),array('type'=>'noticia','status'=>1));
    usort($news_nodes,function($a,$b){
        return $a->created < $b->created;
    });
    $news_node = $news_nodes[0];
    $news = $news_node->body['pt-br'][0]['value'];
    $news = substr($news,0,250)."...";

    $rand = file_get_contents(PORTAL."plataforma2/rand.php");
    $rand = str_replace('src="plataforma2','src="../plataforma2',$rand);

    return new View('index.html',array("intro"=>$intro,"news"=>$news,"rand"=>$rand));
});

$rest->addMap("GET","/portal/pt-br/quem-somos",function($r){
    $node  = node_load('2');
    $html = $node->body['pt-br'][0]['value'];
    $html = nl2br($html);
    return new View('quem-somos.html',array("content"=>$html));
});

$rest->addMap("GET","/portal/pt-br/equipe/:type",function($r){
    $equipe_nodes = node_load_multiple(array(),array('type'=>'equipe','status'=>1));
    $type = $r->getRequest()->getParameter("type");

    $nodes = array();
    foreach($equipe_nodes as $n) {
        $papel = $n->field_equipe_papel['und'][0]['value'];
        if($type == "coordenacao" && $papel != "Coordenação") continue;
        if($type == "analise" && $papel != "Lista Vermelha") continue;
        if($type == "planos" && $papel != "Planos de Ação") continue;
        if($type == "colecoes" && $papel != "Coleções Botânicas") continue;
        if($type == "sistemas" && $papel != "Desenvolvimento e SIG") continue;
        if($type == "comunicacao" && $papel != "Comunicação") continue;
        if($type == "voluntario" && $papel != "Voluntário") continue;
        if($type == "exsitu" && $papel != "Ex-Situ") continue;
        $node = new StdClass;
        $node->body = nl2br($n->body['pt-br'][0]['value']);
        $node->title = $n->title;
        $node->picture = $n->field_equipe_foto['und'][0]['filename'];
        $node->lattes = $n->field_lattes['und'][0]['url'];
        $nodes[] = $node;
    }
    return new View('equipe.html',array("equipe"=>$nodes));
});

$rest->addMap("GET","/portal/pt-br/contato",function($r){
    return new View('contato.html');
});
$rest->addMap("GET","/portal/pt-br/contato-ok",function($r){
    return new View('contato-ok.html');
});

$rest->addMap("POST","/portal/pt-br/contato",function($r) {
    $p = $r->getRequesT()->getPost();
    $header = 'From: '.$p['name'].'<'.$p['email'].'>';
    $a = mail("diogo@cncflora.net",$p['subject'],$p['message'],$header);
    return new Rest\Controller\Redirect('/portal/pt-br/contato-ok');
});

$rest->addMap("GET","/portal/pt-br/livro",function($r) {
    $node = node_load(133711436);
    $content = $node->body['pt-br'][0]['value'];
    return new View('livro.html',array('html'=> $content));
});

$rest->addMap("GET","/portal/pt-br/redlisting",function($r) {
    $node = node_load(65);
    $content = $node->body['pt-br'][0]['value'];

    $db = new PDO("mysql:dbname=".MYSQL_DB.";host=".MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD);
    $q = $db->query("select distinct(familia) from especies where id in (select id from avaliacao where status = 'published') order by familia;");
    $families = array();
    while($f = $q->fetchColumn(0)) {
        $families[] = strtoupper(trim($f));
    }
    sort($families);
    return new View('redlisting.html',array('html'=> $content,'families'=>$families));
});

$rest->addMap("GET","/portal/pt-br/redlisting/family/:family",function($r) {
    $family = $r->getRequest()->getParameter("family");

    $db = new PDO("mysql:dbname=".MYSQL_DB.";host=".MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD);
    $q = $db->query("select distinct(familia) from especies where id in (select id from avaliacao where status = 'published') order by familia;");
    $families = array();
    while($f = $q->fetchColumn(0)) {
        $families[] = strtoupper(trim($f));
    }
    sort($families);

    $ids = json_decode(file_get_contents('demo.json'));
    $repo = new Avaliacoes();
    $spps = $repo->getFamiliaEtapa($family,'published',0,9999);
    $species = array();
    foreach($spps as $spp) {
        foreach($ids as $id) {
            if($spp->Id == $id) {
                $spp->ok = true;
            }
        }
        $species[] = $spp;
    }
    $content = '';

    return new View('redlisting.html',array('html'=> $content,'families'=>$families,'species'=>$species,"family"=>$family));
});

$rest->addMap("GET","/portal/pt-br/conservacao/:page",function($r){
    $page = $r->getREquesT()->getParameter("page");
    if($page == 'acoes') $node = node_load('22');
    else if($page == 'redlist') $node = node_load('5');
    else if($page == 'lista') $node = node_load('4');
    else if($page == 'ameacas') $node = node_load('6');
    else if($page == 'areas') $node = node_load('10');
    else if($page == 'biodiversidade') $node = node_load('3');
    else if($page == 'politicas') $node = node_load('21');

    $html = $node->body['pt-br'][0]['value'];

    return new View('conservacao.html',array("content"=>$html));
});

$rest->addMap("GET","/portal/pt-br/expedicoes/:page",function($r){
    $page = $r->getREquesT()->getParameter("page");
    if($page == 'intro') $mynode = node_load('18');
    else $mynode = node_load($page);

    $html = nl2br( $mynode->body['pt-br'][0]['value'] );

    $exps = node_load_multiple(array(),array('type'=>'expedicao','status'=>1));
    $nodes =array();
    foreach($exps as $n) {
        $node = new StdClass;
        $node->titulo = trim( $n->field_t_tulo_para_menu['pt-br'][0]['value'] );
        $node->nid = $n->nid;
        $node->marked = ($n->nid == $page)?'active':'';
        $node->biomas = array();
        foreach($n->field_biomas['und'] as $b) {
            $node->biomas[] = strtolower( str_replace(" ","-", $b['value'] ) );
        }
        $node->biomas = implode(' ',$node->biomas);
        $nodes[] = $node;
    }

    usort($nodes,function($a,$b){
        return $a->titulo < $b->titulo;
    });

    $photos = array();
    if($page !== 'intro') {
        foreach($mynode->field_expedicao_fotos['und'] as $p) {
            $photo = new StdClass;
            $photo->url = "sites/default/files/".substr( $p[ 'uri' ],9 );
            $photo->thumb = "sites/default/files/styles/thumbnail/public/".substr( $p[ 'uri' ],9 );
            $photos[] = $photo;
        }
    }

    return new View('expedicoes.html',array("content"=>$html,"nodes"=>$nodes,"photos"=>$photos));
});

$rest->addMap("GET","/portal/pt-br/publicacoes/:type",function($r){
    $docs_nodes = node_load_multiple(array(),array('type'=>'publicacao','status'=>1));
    $type = $r->getRequest()->getParameter("type");

    $nodes = array();
    foreach($docs_nodes as $n) {
        if($n->field_pub_tipo['und'][0]['value'] !== $type) continue;
        $node = new StdClass;
        if(count($n->field_arquivo['und'])==1) {
            $node->title = substr( $n->field_arquivo['und'][0]['filename'],0,-3 );
            $node->url = $n->field_arquivo['und'][0]['uri'];
            $node->url = 'sites/default/files/'.substr($node->url,9);
        } else if(count($n->field_fonte['und']) == 1){
            $node->title = $n->field_fonte['und'][0]['title'];
            $node->url = $n->field_fonte['und'][0]['url'];
        } else {
            continue;
        }
        $node->thumb = $n->field_capa['und'][0]['uri'];
        $node->thumb = 'sites/default/files/styles/medium/public/'.substr($node->thumb,9);
        $nodes[] = $node;
    }


    $node = node_load(105);
    $html = $node->body['pt-br'][0]['value'];
    return new View('documentos.html',array('docs'=>$nodes,'intro'=>$html));
});

$rest->addMap("GET","/portal/pt-br/legislacao",function($r){
    $node = node_load(106);
    $intro = $node->body['pt-br'][0]['value'];
    $node = node_load(12);
    $content = $node->body['pt-br'][0]['value'];
    return new View('legislacao.html',array('content'=>$content,'intro'=>$intro));
});

$rest->addMap("GET","/portal/pt-br/noticias",function($r){
    $docs_nodes = node_load_multiple(array(),array('type'=>'noticia','status'=>1));
    $oldLocale = setlocale(LC_TIME, 'pt_BR');

    $nodes = array();
    foreach($docs_nodes as $n) {
        if(strlen($n->field_tipo['und'][0]['value']) < 3) continue;
        $node = new StdClass;
        $node->title = $n->title;
        $node->nid  = $n->nid;
        $node->date = utf8_encode( strftime("%A, %d de %B de %Y",(int)$n->created) );
        $node->created = $n->created;
        $nodes[] = $node;
    }

    usort($nodes,function($a,$b) {
        return $a->created < $b->created;
    });

    setlocale(LC_TIME, $oldLocale);
    return new View('noticias.html',array('noticias'=>$nodes));
});

$rest->addMap("GET","/portal/pt-br/noticia/:nid",function($r) {
    $node = node_load($r->getRequest()->getParameter("nid"));
    $html = $node->body['pt-br'][0]['value'];
    if($html == null) {
        $html = $node->body['und'][0]['value'];
    }

    $oldLocale = setlocale(LC_TIME, 'pt_BR');
    $date = utf8_encode( strftime("%A, %d de %B de %Y",(int)$node->created) );
    setlocale(LC_TIME, $oldLocale);

    $photos = array();
    foreach($node->field_imagens['und'] as $p) {
        $photo = new StdClass;
        $photo->url = "sites/default/files/".substr( $p[ 'uri' ],9 );
        $photo->thumb = "sites/default/files/styles/thumbnail/public/".substr( $p[ 'uri' ],9 );
        $photos[] = $photo;
    }

    return new View('noticia.html',array('content'=>$html,'date'=>$date,'photos'=>$photos));
});

$rest->addMap("GET","/portal/plataforma",function($r){
    $user = $r->getParameter("user");
    if($user->uid < 1) {
        return new Rest\Controller\Redirect('/portal/login');
    }
    ob_start();
    $base = PORTAL.'plataforma2/';
    include '../plataforma2/index.php';
    $html = ob_get_contents();
    ob_end_clean();
    return new View('plataforma.html',array('plataforma'=>$html));
});

$rest->addMap("GET","/portal/sistema",function($r){
    // TODO FIXME HELP PLEASE
    $user = $r->getParameter("user");
    if($user->uid < 1) {
        //return new Rest\Controller\Redirect('/portal/login');
    }

    $intro = "";
    $intro = "<h2>Bem vindo, {$user->name}.</h2>";
    if(in_array("Taxonomista",$user->roles)) {
        $intro = "<p>Respons&aacute;vel pela consolida&ccedil;&atilde;o taxon&ocirc;mica.</p>";
    }
    if(in_array("Analista",$user->roles)) {
        $intro = "<p>Voc&ecirc; est&aacute; respons&aacute;vel pela Consolida&ccedil;&atilde;o e An&aacute;lise das seguintes familias: ".implode(" , ",$user->familiasAnalise).".</p>";
    }
    if(in_array("Analista SIG",$user->roles)) {
        $intro = "<p>Voc&ecirc; est&aacute; respons&aacute;vel pela Consolida&ccedil;&atilde;o SIG.</p>";
    }
    if(in_array("Validador",$user->roles)) {
        $intro = "<p>Voc&ecirc; est&aacute; respons&aacute;vel pela valida&ccedil;&atilde;o das seguintes familias: ".implode(" , ",$user->familiasValidacao).".</p>";
    }
    if(in_array("Avaliador",$user->roles)) {
        $intro = "<p>Voc&ecirc; est&aacute; respons&aacute;vel pela avalia&ccedil;&atilde;o das seguintes familias: ".implode(" , ",$user->familiasAvaliacao).".</p>";
        $intro = "<p>Voc&ecirc; est&aacute; respons&aacute;vel pela revis&atilde;o da avalia&ccedil;&atilde;o das seguintes familias: ".implode(" , ",$user->familiasAvaliacaoRevisao).".</p>";
    }


    if($user->uid == 1) $user->uid = 13;
    $_GET["uid"] = $user->uid;
    $graph1 = (PORTAL."plataforma2/relatorios/relatorio_pagina_inicial_highchart.php?uid=".$user->uid);
    $graph2 = (PORTAL."plataforma2/relatorios/relatorio_pagina_inicial_avaliacoes_highchart.php?uid=".$user->uid);
    return new View('sistema.html',array('intro'=>$intro,'graph1'=>$graph1,'graph2'=>$graph2));
});


$rest->addMap("GET","/portal/login",function($r) {
    return new Rest\Controller\Redirect(PORTAL."?q=user");
});

$rest->addMap("GET","/portal/logout",function($r) {
    return new Rest\Controller\Redirect(PORTAL."user/logout");
});

/** The End */
$rest->execute();




