<?php

$class = isset($args[1]) ? ucfirst(strtolower($args[1])) : ucfirst(ask("Nombre de la clase"));
$filename = strtolower($class) . ".form.ts";

$header = "\n\n------------\t{$filename}\t--------------\n\n";

$form = "import { FormGroup, FormArray } from '@angular/forms';";
$form .= "\n";
$form .= "\nexport function form() {";
$form .= "\n\treturn new FormGroup({";
$form .= "\n\t});";
$form .= "\n}";
$form .= "\n";
$form .= "\nexport interface {$class}Form {";
$form .= "\n}";

try {
	open_editor(save_temp($filename, $form));
} catch (Exception $e) {
	echo $header . $form;
}
