<?php

include "xml2json.php";

//nome comune (for debug)
$comune="Firenze";

/**
 * Genera i dati JSON dato un nome di un comune per cui sono previsti i dati
 * data source http://dati.toscana.it/
 * @param comune il nome del comune di cui si vuol sapere il meteo
 * @return $json data
 * @author Matteo Tempestini 
 */
 
function get_meteo_comune($comune)
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
	$arrayData = xmlToArray($xmlNode);
	$json=json_encode($arrayData);
	return $json;
}

?>