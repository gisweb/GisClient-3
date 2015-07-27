<?php

require_once "../../../config/config.php";


function toJSON($result){

	header("Content-Type: application/json");

	if($_REQUEST["callback"])
		die($_REQUEST["callback"]."(".json_encode($result).")");
	else
		die(json_encode($result));

}

if(empty($_REQUEST["request"])){
	die("Manca il parametro request");
}

$dbSchema=DB_SCHEMA;
$transform = defined('POSTGIS_TRANSFORM_GEOMETRY')?POSTGIS_TRANSFORM_GEOMETRY:'postgis_transform_Geometry';
// Setto qui i parametri di trasformazione... troppo casino ricavarli dal progetto corrente
$SRS = array(
	'3003'=>'+proj=tmerc +lat_0=0 +lon_0=9 +k=0.999600 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs +towgs84=-104.1,-49.1,-9.9,0.971,-2.917,0.714,-11.68',
	'3004'=>'+proj=tmerc +lat_0=0 +lon_0=15 +k=0.9996 +x_0=2520000 +y_0=0 +ellps=intl +units=m +no_defs +towgs84=-104.1,-49.1,-9.9,0.971,-2.917,0.714,-11.68',
	'900913'=>'+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +towgs84=0,0,0 +no_defs',
	'4326'=>'+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs'
);
$geom = $transform."(the_geom,'".$SRS["3004"]."','".$SRS["4326"]."',4326)";
$db = GCApp::getDB();

if($_REQUEST["request"]=="comuni"){

	$sql = "select codice_belfiore,comune,provincia,round(xmin(box2d(".$geom."))::numeric,6)||','||round(ymin(box2d(".$geom."))::numeric,6)||','||round(xmax(box2d(".$geom."))::numeric,6)||','||round(ymax(box2d(".$geom."))::numeric,6) as extent  from  piano_di_classifica.confini_comunali order by 2;";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetchAll(PDO::FETCH_NUM);
	$result=array("success"=>"ok","results"=>$row);
	toJSON($result);

}

if($_REQUEST["request"]=="fogli"){

	$sql = "SELECT id, foglio || coalesce (' '||sezione,'') as foglio, round(xmin(box2d(".$geom."))::numeric,6)||','||round(ymin(box2d(".$geom."))::numeric,6)||','||round(xmax(box2d(".$geom."))::numeric,6)||','||round(ymax(box2d(".$geom."))::numeric,6) as extent FROM nct.qufogli where comune=:comune_codice ORDER BY foglio::int";
	$stmt = $db->prepare($sql);
	$stmt->execute(array("comune_codice"=>$_REQUEST["comune"]));
	$row = $stmt->fetchAll(PDO::FETCH_NUM);
	$result=array("success"=>"ok","results"=>$row);
	toJSON($result);

}

elseif($_REQUEST["request"]=="localita"){

	$sql = "select text, round(y(".$geom.")::numeric,6) as lat, round(x(".$geom.")::numeric,6) as lng from toponomastica.localita_ctr where comune=:comune_codice";
	$stmt = $db->prepare($sql);
	$stmt->execute(array("comune_codice"=>$_REQUEST["comune"]));
	$row = $stmt->fetchAll(PDO::FETCH_NUM);
	$result=array("success"=>"ok","results"=>$row);
	toJSON($result);

}

elseif($_REQUEST["request"]=="infoposizione"){

	if(empty($_REQUEST["lat"]) || empty($_REQUEST["lng"])){
		die("Mancano le coordinate");
	}
	$point = "ST_SetSRID(ST_MakePoint(".$_REQUEST["lng"].",".$_REQUEST["lat"]."),4326)";
	$point = $transform."(".$point.",'".$SRS["4326"]."','".$SRS["3004"]."',3004)";

	$ret = array();

	//Snap su rete idrografica
	$sql = "WITH nearest AS (
	  select gid as id, nome, ".$point." as mypoint, the_geom as myline from piano_di_classifica.reticolo_idrografico order by distance(".$point.",the_geom) limit 1
	  ), 
	  newpoint as (
		SELECT id, nome, ST_Line_Interpolate_Point(myline,ST_Line_Locate_Point(myline,mypoint)) as point from nearest
		)
	SELECT id, nome, Y(".$transform."(point,'".$SRS["3004"]."','".$SRS["4326"]."',4326)) as lat, X(".$transform."(point,'".$SRS["3004"]."','".$SRS["4326"]."',4326)) as lng, point from newpoint;";

	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$newPoint = $row["point"];
	$idReticolo = $row["id"];
	$astaReticolo = $row["nome"];
	$ret["Lat"] = round($row["lat"],6);
	$ret["Lng"] = round($row["lng"],6);

	//Comune e dato catastale (query da sostituire con quella in join con il catasto)
	//SOSTITUITA CON SOLO FOGLI IN ATTESA DI CATASTO COMPLETO
	//$sql = "select confini_comunali.comune, istat, codice_belfiore, coalesce(sezione,'') as sezione, foglio, mappale as numero from piano_di_classifica.confini_comunali inner join nct.particelle on (confini_comunali.codice_belfiore=particelle.comune) WHERE contains(".$transform."(bordo_gb,'".$SRS["3004"]."','".$SRS["4326"]."',4326),".$point.");";
	//$sql = "select confini_comunali.comune, istat, codice_belfiore, coalesce(sezione,'') as sezione, foglio, mappale as numero from piano_di_classifica.confini_comunali left join nct.particelle on (confini_comunali.codice_belfiore=particelle.comune) order by distance(bordo_gb,'".$newPoint."') limit 1;";
	//$sql = "select confini_comunali.comune, istat, codice_belfiore from piano_di_classifica.confini_comunali where contains(the_geom,'".$newPoint."');";
	$sql = "SELECT st_distance(particelle.geom,'".$newPoint."') as dist, confini_comunali.comune, istat, codice_belfiore, sezi_cens as sezione, nume_fogl as foglio, ltrim(nume_part,'0') as numero FROM piano_di_classifica.confini_comunali INNER JOIN catasto.particelle ON (confini_comunali.codice_belfiore=particelle.codi_fisc_) ORDER BY 1 LIMIT 1;";

	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$ret["codIstat"] = $row["istat"];
	$ret["DescComune"] = $row["comune"];
	$ret["Sezione"] = $row["sezione"];
	$ret["Foglio"] = $row["foglio"];
	$ret["Numero"] = $row["numero"];

	//Località
	//$sql = "select text, distance(".$point.",".$geom.") from toponomastica.localita_ctr order by 2 limit 1";
	$sql = "select text, distance('".$newPoint."',the_geom) from toponomastica.localita_ctr order by 2 limit 1";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$ret["Localita"] = $row["text"];

	//Nome Asta reticolo
	//$sql = "select nome, distance(".$point.",".$geom.") from piano_di_classifica.reticolo_idrografico order by 2 limit 1";
/*	$sql = "select nome from piano_di_classifica.reticolo_idrografico where gid = " . $idReticolo;
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$ret["NomeAsta"] = $row["nome"];*/
	$ret["NomeAsta"] = $astaReticolo;

	//Sub-Bacino e IAP
	$sql = "select gid, bacino, iap from piano_di_classifica.sub_bacini where contains(the_geom,'".$newPoint."');";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$ret["CodSubBacino"] = $row["gid"];
	$ret["DescSubBacino"] = $row["bacino"];
	$ret["CodIAP"] = $row["iap"];

	//Acqua Pubblica
	$sql = "select coalesce(nome_elenco,'senza nome') as nome from piano_di_classifica.acque_pubbliche_marche where intersects(the_geom,buffer('".$newPoint."',1));";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$ret["DescAcquaPubb"] = $row["nome"]?true:false;

	//Aree di gestione
	$sql = "select id_gestint,id1,comp from areegestione.areegestione where contains(geom,'".$newPoint."');";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$ret["IdAreaGestione"] = $row["id_gestint"];

	//Area protetta
	$sql = "select gid as id, nome,tipo from aree_protette.aree_protette where contains(the_geom,'".$newPoint."');";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$ret["IdAreaProtetta"] = $row["id"];
	$ret["DescAreaProtetta"] = $row["nome"];
	$ret["TipoAreaProtetta"] = $row["tipo"];

	$result=array("success"=>"ok","results"=>$ret);
	toJSON($result);


}

elseif($_REQUEST["request"]=="elencotest"){
	$sql="select gid as id, text,'icon_'||color as tipo,x(".$geom."),y(".$geom.") from  toponomastica.localita_ctr limit 200;";
	$stmt = $db->prepare($sql);
	$stmt->execute();
}

elseif($_REQUEST["request"]=="getdata"){
	$sql = "select colore,st_astext(the_geom),descrizione from segnalazioni_test.annotazioni where segnalazione=".$_REQUEST["id"].";";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetchAll(PDO::FETCH_NUM);
	$result=array("success"=>"ok","results"=>$row);
	toJSON($result);

}

elseif($_REQUEST["request"]=="savedata"){
	
	$sql = "update segnalazioni_test.segnalazioni set the_geom = :geom where id=:id;";
	$stmt = $db->prepare($sql);
	$res = $stmt->execute(array(':geom'=>$_REQUEST["geom"],":id"=>$_REQUEST["id"])); 
	if($stmt->rowCount() == 0){
		$sql = "insert into segnalazioni_test.segnalazioni (idstatosegnalazione,id,ogc_fid,the_geom) values ('1',:id,:fid,:geom);";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':geom'=>$_REQUEST["geom"],":id"=>$_REQUEST["id"],":fid"=>$_REQUEST["id"])); 
	}

	if(isset($_REQUEST["overlays"])){
		$sql = "delete from segnalazioni_test.annotazioni where segnalazione=".$_REQUEST["id"].";";
		$stmt = $db->prepare($sql);
		$stmt->execute(); 
		for($i=0;$i<count($_REQUEST["overlays"]);$i++){
			$overlay = $_REQUEST["overlays"][$i];
					if(!isset($overlay["description"])) $overlay["description"] = '';
			$sql = "insert into segnalazioni_test.annotazioni (segnalazione,colore,descrizione,the_geom) values (".$_REQUEST["id"].",'".$overlay["color"]."','".str_replace("'", "''", $overlay["description"])."','".$overlay["geom"]."');";
			$stmt = $db->prepare($sql);
			$stmt->execute(); 
		}
	}
	

	$result=array("success"=>"ok");
	toJSON($result);

}

else{
	$result=array("success"=>"ko","message"=>"Richiesta non prevista");
	toJSON($result);
}
