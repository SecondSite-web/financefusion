 <?php
/*
 *  Template Controller page
 *  Author: Gregory Schoeman
*/
 require_once __DIR__ .'/../dash-loader.php';
 defined('ROOT_PATH') || exit;
 require_once FUNCTIONS_URL.'/lock.php';

 use Dash\Cf;

 $cf = new Cf($pdo);

 $template = "sb_cf_table.twig";
 $tbody = $cf->getAll();
 $thead = $cf->getColumnNames();

 $values = array(
    'page' => array(
        'title'         => "Contact form submissions",
        'description'   => "Tabled view of all contact form submissions",
        'class'         => "cf-tables",
        'pic'           => ""
    ),
    'tbody'             => $tbody,
    'thead'             => $thead
 );
 echo $twig->render($template, $values);
