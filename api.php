<?php

include "xml2json.php";

//comune (for debug)
$comune="Prato";

//get_meteo($comune);
print_r('----------------------');
get_rischio($comune);

/**
 * Genera i dati JSON dato un nome di un comune per cui sono previsti i dati
 * data source http://dati.toscana.it/
 * @param comune il nome del comune di cui si vuol sapere il meteo
 * @return $json data (null if data are not available)
 * @author Matteo Tempestini 
 */
 
function get_meteo($comune)
{
	//lista comuni disponibili
	$meteo_list_url="http://www.lamma.rete.toscana.it/previ/ita/xml/lista_comuni.xml";

	//url base di archivio dati
	$url_base_meteo="http://www.lamma.rete.toscana.it/previ/ita/xml/comuni_web/dati/";

	//cerca il nodo e preleva l'url
	$xmlNode = simplexml_load_file($meteo_list_url);
	$node = $xmlNode->xpath("//link[contains(title,'$comune')]");
	
	//print_r($node);
	$url_node=$node[0]->url;

	//carica il file XML e restituisce il JSON corrispondente
	$url_meteo=$url_base_meteo. $url_node  .'.xml';
	$xmlNode = simplexml_load_file($url_meteo);
	if ($xmlNode==false)
	{
		print("Errore nella ricerca del file relativo al rischio");
		$json=null;
	}else
	{
		$arrayData = xmlToArray($xmlNode);
		$tag='comune';
		$arrayData[] = array($tag=>$comune);
		$json=json_encode($arrayData);
	}
	print_r($json);
	return $json;
}

/**
 * Genera i dati JSON dato un nome di un comune per cui sono previsti i dati
 * data source http://www.sir.toscana.it/
 * @param comune il nome del comune di cui si vuol sapere il rischio
 * @return $json data (null if data are not available)
 * -------rischi oggi
 * --idrogeologico
 * --idraulico
 * --vento
 * --mareggiate
 * --neve
 * --ghiaccio
 * --temporali
 * --------rischi domani
 * --idrogeologico
 * --idraulico
 * --vento
 * --mareggiate
 * --neve
 * --ghiaccio
 * --temporali
 * @author Matteo Tempestini 
 */
 
function get_rischio($comune)
{
	date_default_timezone_set('UTC');
	$today = date("Ymd");   

	//Gestione Rischio Centro Funzionale Regione Toscana
	$xmlrisk=simplexml_load_file("http://www.sir.toscana.it/supports/xml/risks_395/".$today.".xml"); 
	if ($xmlrisk==false)
	{
		print("Errore nella ricerca del file relativo al rischio");
		$json=null;
	}else
	{

		//cerca il nodo dell'area di rischio
		$node = $xmlrisk->xpath("//area[contains(comuni,'$comune')]");
		$node=$node[0];
		$area=$node[@name];
		
		//$tags = $xmlrisk->xpath("rischio[@name]");
		$rischi = $xmlrisk->xpath("//rischi/rischio/area[contains(@name,'$area')]");
		//$arrayData = xmlToArray($rischi);
		$tag='comune';
		$rischi[] = array($tag=>$comune);
		$json=json_encode($rischi);
	}
	print_r($json);
	return $json;
}

?>