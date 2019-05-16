<?php

function save_temp($name, $content) {
	$tmpfile = tempnam(sys_get_temp_dir(), $name);
	$handle = fopen($tmpfile, "w");
	fwrite($handle, $content);
	fclose($handle);
	return $tmpfile;
}

function open_editor($filepath) {
	try {
		shell_exec("vscodium " . $filepath . '&');
	} catch (Exception $e) {
		try {
			shell_exec("code " . $filepath . '&');
		} catch (Exception $e) {
			try {
				shell_exec("subl " . $filepath . '&'); 
			} catch (Exception $e) {
				shell_exec("xdg-open " . $filepath . '&');
			}
		}
	}
}

function replace_string_in_file($filename, $string_to_replace, $replace_with){
	$content=file_get_contents($filename);
	$content_chunks=explode($string_to_replace, $content);
	$content=implode($replace_with, $content_chunks);
	file_put_contents($filename, $content);
}

/*************
 *	HELPERS
 ************/

/**
 * Función que pregunta por entrada de texto.
 * Se puede pasar valor por defecto.
 * 
 * Para aceptar valor vacío, enviar '' como $default
 */
function ask($text, $default = null) {
	echo $text . ($default !== null && strlen($default) > 0 ? '[' . $default . ']' : '') . ': ';
	$res = rtrim(fgets(STDIN));
	if (strlen($res) === 0 && $default === null) {
		echo "\n\n\e[1;31mERROR: no existe valor por defecto. Ingrese un valor. \e[0m\n\n";
		$res = ask($text);
	}
	return (strlen($res) === 0 && $default !== null ? $default : $res);
}

/**
 * Función para conectar a la DB
 */
function db() {
	if (!empty($app)) {
		$adodb = $app->getContainer()['adodb'];
	} else {
		echo "\n\e[1;31mNo se encontró container de ADOdb. Configure la conexión.\e[0m\n\n";
		
		$server = ask("Servidor", "localhost");
		$user = ask("Usuario", "root");
		$pass = ask("Contraseña", "root");
		$db = ask("Base de datos");

		$adodb = ADONewConnection('mysqli');
		while ($adodb->connect($server,$user,$pass,$db) === false) {
			echo "\n\n\e[1;31mCredenciales incorrectas.\e[0m\n\n";
			$user = ask("Usuario", $user);
			$pass = ask("Contraseña", $pass);
		}
		$adodb->Execute("SET @@lc_time_names = 'es_AR';");
		$adodb->cacheSecs = 10;
		$adodb->debug = false; 
		$adodb->setFetchMode(ADODB_FETCH_ASSOC);
		$adodb->SetCharSet('utf8');
		$adodb->execute("SET NAMES 'utf8'");
		$adodb->Execute("SET @@lc_time_names = 'es_AR';");
		$adodb->Execute("SET collation_connection = 'utf8_general_ci'");
	}
	return $adodb;
}

/** Navegación interactiva, retorna directorio seleccionado */
function navegar($directorio = null) {
	if ($directorio === null) {
		if (strpos(getcwd(), 'vendor') !== false) {
			chdir('../..');
		}
	} else {
		chdir($directorio);
	}
	$directorios = scandir(getcwd());
	$filterFn = function($item) {
		return !is_file($item) && $item !== '.';
	};
	$directorios = array_values(array_filter($directorios, $filterFn));
	for ($i = 0; $i < count($directorios); $i++) {
		echo "\n" . $i . ". " . $directorios[$i];
	}
	echo "\n\n";
	$directorio = ask("Navegar (ENTER para seleccionar actual)", "");
	if (strlen($directorio) === 0) {
		return getcwd();
	} else {
		return navegar($directorios[$directorio]);
	}
}

/** Retrocede un directorio y busca transversalmente */
/*function buscarDirectorio($arrSearch) {
	if (strpos(getcwd(), 'vendor') !== false) {
		chdir('../..');
	}
	chdir('..');
	$locations = scandir(getcwd());
	$filterFn = function($item) {
		return !is_file($item) && !in_array(needle, haystack);
	};
	$filteredLocations = array_values(array_filter($locations, $filterFn));
	return transverse($filteredLocations);
}

transverse($array) {
	for (i = 0; $i < count($pivot); $i++) { 
		if () {

		}
	}
}*/
