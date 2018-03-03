<?php

$method = $_SERVER['REQUEST_METHOD'];

$project_name = "DreamTorg";
$admin_email  = "info@avtomatizacia.kz, client@marketing-time.kz";
$form_subject = "Заявка";

//Script Foreach
$c = true;
if ( $method === 'POST' ) {

	foreach ( $_POST as $key => $value ) {
		if ( $value != "" && $key != "info") {
			$message .= "
			" . ( ($c = !$c) ? '<tr>':'<tr style="background-color: #f8f8f8;">' ) . "
				<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>$key</b></td>
				<td style='padding: 10px; border: #e9e9e9 1px solid;'>$value</td>
			</tr>
			";
		}
	}
} 

$message = "<table style='width: 100%;'>$message</table>";

function adopt($text) {
	return '=?UTF-8?B?'.Base64_encode($text).'?=';
}

$headers = "MIME-Version: 1.0" . PHP_EOL .
"Content-Type: text/html; charset=utf-8" . PHP_EOL .
'From: '.$admin_email. PHP_EOL .
'Reply-To: info@avtomatizacia.kz' . PHP_EOL;

mail($admin_email, adopt($form_subject), $message, $headers);

switch ($_POST['info']) {
	case 'Скачать презентацию':
		header('Location: /franchise/test.html');
		break;
	case 'Получить подарки':
		header('Location: /franchise/result.html');
		break;
	default:
		break;
}
