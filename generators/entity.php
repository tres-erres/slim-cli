<?php

$adodb = db();
$tabla = isset($args[1]) ? $args[1] : ask("Tabla");
$nombreClass = ask("Nombre de la clase", ucfirst($tabla));
$resdb = $adodb->query('show columns from ' . $tabla);

$filename = $tabla . ".model.ts";

$header = "\n\n------------\t{$filename}\t--------------\n\n";

$output = "import { FormGroup, FormControl } from '@angular/forms';\n\n";

$output .= "export class " . $nombreClass . " {\n";
$output .= "\tconstructor(\n";
$anterior=false;
$delreset="";

while($datos = $resdb->fetchRow()){
	if ($anterior) $output .= $anterior;
	$como = substr($datos['Type'],0,strpos($datos['Type'],"("));
	if (!strlen($como)) $como=$datos['Type'];
	switch ($como) {
		case 'int':
		case 'decimal':
		case 'tinyint':
		case 'mediumint':
		case 'longint':
			$tipo="number";
			$tipo2="null";
		break;
		case 'date':
		case 'datetime':
		case 'varchar':
		case 'timestamp':
		case 'longtext':
		case 'longblob':
		case 'blob':
			$tipo="string";
			$tipo2="''";
		break;
		default:
			$tipo="string";
			$tipo2="''";
		break;
	}
	$anterior = "\t\tpublic " . $datos['Field'] . "?: " . $tipo . ",\n";
	$delreset.= "\t\t\t" . $datos['Field'] . ": new FormControl(null),\n";
}
$output .= substr($anterior,0,-2) . "\n\t) { }\n\n\tstatic form() {\n\t\treturn new FormGroup({\n{$delreset}\t\t});\n\t}\n}\n";

try {
	open_editor(save_temp($filename, $output));
} catch (Exception $e) {
	echo $header . $output;
}
