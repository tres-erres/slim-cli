<?php

include('helpers.php');

$tabla = $args[1];
$adodb = db();
$attrTbl = $adodb->getAll('DESCRIBE '.$tabla);
$ide = $adodb->getAll('SHOW INDEX FROM '.$tabla);
$ide = $ide[0]['Column_name'];
$atributos = array();
$injAttrArr = array();
$parAttrArr = array();
$formAttrArr = array();
$camposAttrArr = array();
$hojeaAttrArr = array();
$indice = 1;
foreach ($attrTbl as $attr){ 
	$como = substr($attr['Type'],0,strpos($attr['Type'],"("));
	if (!strlen($como)) 
		$como=$attr['Type'];
	switch ($como) {
		case 'int': case 'decimal': case 'tinyint': case 'mediumint': case 'longint':
			$tipo="number";
			$elTipo="numero";
			$casteo = '.toString()';
			$tipo2="null";
			break;
		case 'date': case 'datetime': case 'varchar': case 'timestamp': case 'longtext': case 'longblob': case 'blob':
			$tipo="string";
			$elTipo="texto";
			$casteo = '';
			$tipo2="''";
			break;
		default:
			$tipo="string";
			$elTipo="texto";
			$casteo = '';
			$tipo2="''";
			break;
	}
	array_push($atributos,$attr['Field']);

	$elCampo = $attr['Field'];
	$linForm = "formData.append('".$attr['Field']."', this.".$attr['Field'].$casteo.");";
	$linInj = "this.".$attr['Field']." = data.".$attr['Field'].";";
	$linCampos = "\t\t\t<mat-card>\r\n\t\t\t\t<mat-card-content>\r\n\t\t\t\t\t<mat-form-field>\r\n\t\t\t\t\t\t<input trFocusNext=\"".$indice."\" matInput [(ngModel)]=\"".$elCampo."\" name=\"".$elCampo."\" placeholder=\"".$elCampo."\">\r\n\t\t\t\t\t</mat-form-field>\r\n\t\t\t\t</mat-card-content>\r\n\t\t\t</mat-card>";
	$linHojea = "\t\t\t\t{ ancho: 10, def:\"".$attr['Field']."\", nombre:\"".$attr['Field']."\", tipo: \"".$elTipo."\"},";
	array_push($formAttrArr,$linForm);
	array_push($injAttrArr,$linInj);
	array_push($camposAttrArr,$linCampos);
	array_push($parAttrArr,$attr['Field'].':'.$tipo.';');
	array_push($hojeaAttrArr, $linHojea);
	$indice++;
}
$formAttr = implode("\n", $formAttrArr);
$injAttr = implode("\n", $injAttrArr);
$parAttr = implode("\n", $parAttrArr);
$camposAttr = implode("\n", $camposAttrArr);
$hojeaAttr = implode("\n", $hojeaAttrArr);
$pathFront = ask("Path Front-End", "../frontend/src/app/layout/dashboard/$tabla");
$pathBack = ask("Path Back-End", "./");
$entidad = ask("Nombre de entidad", ucfirst($tabla));
if (!file_exists($pathFront)) {mkdir($pathFront, 0777, true);}
if (!file_exists($pathBack)) {mkdir($pathBack, 0777, true);}
/* Front end */
$copioModule = copy ('./templates/'.$args[0].'/componenteModule.txt' , $pathFront.'/'.$tabla.'.module.ts');
$copioHtml = copy ('./templates/'.$args[0].'/componenteHtml.txt' , $pathFront.'/'.$tabla.'.component.html');
$copioTs = copy ('./templates/'.$args[0].'/componenteTs.txt' , $pathFront.'/'.$tabla.'.component.ts');
$copioScss = copy ('./templates/'.$args[0].'/componenteScss.txt' , $pathFront.'/'.$tabla.'.component.scss');
$copioDialog = copy ('./templates/'.$args[0].'/componenteDialogo.txt' , $pathFront.'/dialogo-'.$tabla.'.html');
$copioModuloRutas = copy ('./templates/'.$args[0].'/moduloRutas.txt' , $pathFront.'/'.$tabla.'-routing.module.ts');
/* Back end */
$copioClase = copy ('./templates/'.$args[0].'/clase.txt' , $pathBack.'/app/'.ucfirst($tabla).'.php');
if ($copioModule && $copioHtml && $copioTs && $copioScss && $copioDialog && $copioClase && $copioModuloRutas){
	$filename=$pathFront.'/dialogo-'.$tabla.'.html';
	$string_to_replace="_min_";
	$replace_with=$tabla;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/dialogo-'.$tabla.'.html';
	$string_to_replace="_campos_";
	$replace_with=$camposAttr;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/'.$tabla.'.component.ts';
	$string_to_replace="_may_";
	$replace_with=$entidad;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/'.$tabla.'.component.ts';
	$string_to_replace="_colusHoj_";
	$replace_with=$hojeaAttr;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/'.$tabla.'.component.ts';
	$string_to_replace="_min_";
	$replace_with=$tabla;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/'.$tabla.'.component.ts';
	$string_to_replace="_injectParams_";
	$replace_with=$injAttr;
	replace_string_in_file($filename, $string_to_replace, $replace_with);$filename=$pathFront.'/'.$tabla.'.component.ts';
	$string_to_replace="_params_";
	$replace_with=$parAttr;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/'.$tabla.'.component.ts';
	$string_to_replace="_formDataParams_";
	$replace_with=$formAttr;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/'.$tabla.'-routing.module.ts';
	$string_to_replace="_may_";
	$replace_with=$entidad;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/'.$tabla.'-routing.module.ts';
	$string_to_replace="_min_";
	$replace_with=$entidad;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/'.$tabla.'.module.ts';
	$string_to_replace="_may_";
	$replace_with=$entidad;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/'.$tabla.'.module.ts';
	$string_to_replace="_min_";
	$replace_with=$tabla;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathFront.'/'.$tabla.'.component.scss';
	$string_to_replace="_min_";
	$replace_with=$tabla;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathBack.'/app/'.ucfirst($tabla).'.php';
	$string_to_replace="_min_";
	$replace_with=$tabla;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathBack.'/app/'.ucfirst($tabla).'.php';
	$string_to_replace="_may_";
	$replace_with=$entidad;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	$filename=$pathBack.'/app/'.ucfirst($tabla).'.php';
	$string_to_replace="_ide_";
	$replace_with=$ide;
	replace_string_in_file($filename, $string_to_replace, $replace_with);
	actualizoRutas($pathBack,$tabla);
	actualizoDependencias($pathBack,$tabla);
} else {
	echo "No pude copiar el archivo... NO ES MI CULPA";
}

function actualizoRutas($pathBack, $tabla){
	$directorio = $pathBack.'src/routes.php';
	$f = fopen($directorio, "r+");
	$original = file_get_contents($directorio);
	$agregaHojeador = "\r \n \t\$app->get('/".$tabla."', '".$tabla.":hojeador');";
	$agregaABM = "\r \n \t\$app->post('/".$tabla."', '".$tabla.":guardar');";
	$agregaABMDel = "\r \n \t\$app->post('/".$tabla."/eliminar/', '".$tabla.":eliminar');";
	$lineaHojeador = "\$app->group('/hojeador', function () use (\$app) {";
	$lineaAbm = "\$app->group('/abm', function () use (\$app) {";
	$patronH = preg_quote($agregaHojeador, '/');
	$patronH= "/^.*$patronH.*\$/m";
	if (!preg_match_all($patronH, $original, $matches)) {
		while (($buffer = fgets($f)) !== false) {
			if (strpos($buffer, $lineaHojeador) !== false) {
				$pos = ftell($f); 
				$newstr = substr_replace($original, $agregaHojeador, $pos, 0);
				file_put_contents($directorio, $newstr);
				break;
			}
		}
	}
	fclose($f);
	$f = fopen($directorio, "r+");
	$patronA = preg_quote($agregaABM, '/');
	$patronA = "/^.*$patronA.*\$/m";
	if (!preg_match_all($patronA, $original, $matches)) {
		while (($buffer = fgets($f)) !== false) {
			if (strpos($buffer, $lineaAbm) !== false) {
				$pos = ftell($f); 
				$newstr = substr_replace($original, $agregaABM, $pos, 0);
				file_put_contents($directorio, $newstr);
				break;
			}
		}
	}
	fclose($f);
	$f = fopen($directorio, "r+");
	$patronE = preg_quote($agregaABMDel, '/');
	$patronE = "/^.*$patronE.*\$/m";
	if (!preg_match_all($patronE, $original, $matches)){
		while (($buffer = fgets($f)) !== false) {
			if (strpos($buffer, $lineaAbm) !== false) {
				$pos = ftell($f); 
				$newstr = substr_replace($original, $agregaABMDel, $pos, 0);
				file_put_contents($directorio, $newstr);
				break;
			}
		}
	}
	fclose($f);
}

function actualizoDependencias($pathBack, $tabla){
	$directorio = $pathBack.'src/dependencies.php';
	$laTabla = ucfirst($tabla);
	$dependencia_nueva = "\r \n\$container['$tabla'] = function (\$c) { \r\n\t\$tabla = new App\\$laTabla(\$c['logger']);\r\n\t return \$tabla; };";
	$actual = file_get_contents($directorio);
	$actual .= $dependencia_nueva;
	file_put_contents($directorio, $actual);
}