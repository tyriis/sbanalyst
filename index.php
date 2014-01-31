<?
include_once('includes/classes/Core.class.php');
new Core();

include_once HOME.DS.'lib/simpleHTMLDOM/simple_html_dom.php';
$url = 'http://www.wohnungssuche.at/neu'; // this is not the real url ;)
$html = file_get_html($url);
$data = array();
$count = 0;
$config = array(
    'minChambers' => 3,
	'city' => 'Wien'
);

foreach($html->find('table.contenttable tbody tr') as $element){
    if($count == 0){
        $count++;
        continue;
    }
    $cells = $element->find('td');
    $cell = $cells[1]->find('p');
    $chambers = $cell[0]->innertext;
    $links = $element->find('a');
    $name = $links[0]->innertext;
    $link = $links[0]->href;
    if(preg_match("/\b".$config['city']."\b/",$name) && (int)$chambers >= $config['minChambers']){
        $data[] = array(
            'name' => $name,
            'link' => $url.'/'.$link,
            'chambers' => $chambers,
            'checksum' => md5($link.$name.$chambers)
        );
    }
    $count++;
}

foreach($data as $item){
    $checksum = $item['checksum'];
    $hit = new Hit();
    if(!$hit->getByChecksum($item['checksum'])){
        $hit->create((object)array('checksum' => $checksum));
        $body = str_replace(array('%link%','%chambers%'),array('<a href="'.$item['link'].'">'.$item['name'].'</a>',$item['chambers']), file_get_contents(HOME.DS.'templates'.DS.'mail.template.html'));
        $data = array(
            'email' => 'john.doe@gmail.com',
            'name' => 'John Doe',
            'body' => $body
        );
        $mail = new Mail($data);
        $mail->send();
    }
}
?>
