<?php
require_once('../../../config/config.php');
$db = GCApp::getDB();
$dbSchema=DB_SCHEMA;

function to_xml(SimpleXMLElement $object, array $data)
{   
    foreach ($data as $key => $value){   
        if (is_array($value))
        {   
            $new_object = $object->addChild("segnalazione");
            to_xml($new_object, $value);
        }   
        else
        {   
            $object->addChild($key, $value);
        }   
    }   
} 

//$sql = "SELECT id,idbacino,bacino,istatcomune,comune,tipo,tipomanutenzione,stato,dataapertura,datachiusura from segnalazioni.segnalazioni WHERE id in (" . $_REQUEST["id"] . ");";
$sql = "SELECT bacino,comune,id,tipo,tipomanutenzione,stato,dataapertura,datachiusura from segnalazioni.segnalazioni WHERE id in (" . $_REQUEST["id"] . ") order by bacino,comune,dataapertura,datachiusura;";
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_NUM);

//$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
/* RAGGRUPPAMENTO VEDREMO!!!
$segnalazioni = array();
for($i=0;$i<count($rows);$i++){
    $sng = array(
        "id"=>$rows[$i]["id"],
        "tipo"=>$rows[$i]["tipo"],
        "manutenzione"=>$rows[$i]["tipomanutenzione"],
        "stato"=>$rows[$i]["stato"],
        "dataapertura"=>$rows[$i]["dataapertura"],
        "datachiusura"=>$rows[$i]["datachiusura"],
        "bacino"=>$rows[$i]["bacino"],
        "comune"=>$rows[$i]["comune"]
    );
    if(!is_array($segnalazioni[$rows[$i]["idbacino"]]))$segnalazioni[$rows[$i]["idbacino"]] = array();
    if(!is_array($segnalazioni[$rows[$i]["idbacino"]][$rows[$i]["istatcomune"]]))$segnalazioni[$rows[$i]["idbacino"]][$rows[$i]["istatcomune"]] = array();
    array_push($segnalazioni[$rows[$i]["idbacino"]][$rows[$i]["istatcomune"]], $sng);
     //   $segnalazioni[$rows[$i]["bacino"]][$rows[$i]["comune"]][] = 

};

$xml = new SimpleXMLElement('<segnalazioni/>');
foreach ($segnalazioni as $bacino => $comuni){
    $xml->addChild("comprensorio",$bacino);
    foreach ($comuni as $comune => $data){
        $xml->addChild("comune",$comune);
        to_xml($xml, $data);
    }
}  
*/
/*XML FOP --- da fare !!!
$xml = new SimpleXMLElement('<segnalazioni/>');
to_xml($xml, $rows);
file_put_contents('segnalazioni.xml', $xml->asXML());

*/

//MAPPA
$imgWidth = 800;
$imgHeight = 600;
$oMap=ms_newMapObj(ROOT_PATH."map/bonificamarche/consorziobonifica.map");
$oMap->setSize($imgWidth,$imgHeight);
$srs = strtolower($_REQUEST["srs"]);
if($srs == "epsg:900913") $srs = "epsg:3857";
$oMap->setProjection("init=".$srs);
$oMap->extent->setextent($_REQUEST['extent'][0], $_REQUEST['extent'][1], $_REQUEST['extent'][2], $_REQUEST['extent'][3]);

$oLayer = $oMap->getLayerByName("segnalazioni.segnalazioni");
$layerFilter = "gc_objid IN (" . $_REQUEST["id"] . ")";
if ($oLayer->getFilterString())
    $layerFilter = str_replace("\"", "", $oLayer->getFilterString()) . " AND " . $layerFilter;
$oLayer->setFilter($layerFilter);



$oLayer->set("status",MS_ON);

$oLayer = $oMap->getLayerByName("osm.osm-wms");$oLayer->set("status",MS_ON);
$oLayer = $oMap->getLayerByName("grp_bacini_principali.lay_bacini_principali");$oLayer->set("status",MS_ON);
$oLayer = $oMap->getLayerByName("grp_acque_pubbliche_marche.lay_acque_pubbliche_marche");$oLayer->set("status",MS_ON);
$oLayer = $oMap->getLayerByName("grp_ctr.lay_ctr_edifici");$oLayer->set("status",MS_ON);




ms_ResetErrorList();
$oMap->save('debug.map');

$oImage = $oMap->draw();
$filename = "segnalazioni_".rand(0,99999999);
$oImage->saveImage(GC_WEB_TMP_DIR.$filename.".png", $oMap);
$mapImage = GC_WEB_TMP_URL.$filename.".png";

$table = '<table border="1"><tr><th>Bacino</th><th>Comune</th><th>ID</th><th>Tipo segnalazione</th><th>Tipo manutenzione</th><th>Stato</th><th>Data apertura</th><th>Data chiusura</th></tr>';
for($i=0;$i<count($rows);$i++){
    $row = $rows[$i];
    $html = "<tr>";
    for ($j=0; $j < count($row); $j++) { 
        if(!$row[$j]) $row[$j]="-";
        $html .= "<td>".$row[$j]."</td>";
    }
    $html .= "</tr>";
    $table.=$html;
}
$table.="</table>";

$table.="<table><caption>Legenda</caption><tr><td><img src='".MAP_URL."/jquery/images/marker32_red.png'>Aperta - presa in carico dal tecnico - in fase di sopralluogo</td>
        <td><img src='".MAP_URL."/jquery/images/marker32_yel.png'>In fase di valutazione - richiesta di autorizzazioni - realizzazione intervento</td>
        <td><img src='".MAP_URL."/jquery/images/marker32_blu.png'>In attesa di verifica e controllo lavori eseguiti - verifica lavori da non fare proposti</td>
        <td><img src='".MAP_URL."/jquery/images/marker32_gre.png'>Chiusa con intervento eseguito</td>
        <td><img src='".MAP_URL."/jquery/images/marker32_bla.png'>Chiusa senza intervento</td></tr></table>";

$table.= "<div><img src=\"".$mapImage."\" width=\"" .$imgWidth. "\" heigh=\"".$imgHeight."\"></div>";

$html = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><title>Elenco segnalazioni</title></head><body>";
$html.=$table;
$html.= "</body></html>";

file_put_contents(GC_WEB_TMP_DIR.$filename.".html", $html);

$result=array("success"=>"ok","file"=>GC_WEB_TMP_URL.$filename.".html");
header("Content-Type: application/json");
die(json_encode($result));
