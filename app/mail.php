<?php

$method = $_SERVER['REQUEST_METHOD'];

$project_name = "DreamTorg";
$admin_email  = "zaiavka@dreamtorg.kz, client@marketing-time.kz";
$server_email = "<zaiavka@dreamtorg.kz>";
$form_subject = "Заявка";

//Script Foreach
$c = true;
if ( $method === 'POST' ) {

	foreach ( $_POST as $key => $value ) {
		if ( $value != "") {
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
'From: '.$server_email. PHP_EOL .
'Reply-To: '.$admin_email. PHP_EOL;

mail($admin_email, adopt($form_subject), $message, $headers);

// добавление лид в bitrix
$data['LOGIN']='bdm1@marketing-time.kz'; //На кого рапаковывается
$data['PASSWORD']='passadoble1983'; //пароль
$subdomain='mtkz'; // поддомен битрикс24 mtkz.bitrix24.ru. Смотрите внизу

$block= $source.': '.$info;
$source = 'franchise.evodreamholding.com'; //Источник, Если что спрашивать у Кирилла

$ch=curl_init();
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_USERAGENT,'API-client/1.0');
curl_setopt($ch,CURLOPT_URL,'https://'.$subdomain.'.bitrix24.ru/crm/configs/import/lead.php'); //Сюда
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);

if(preg_match("|(.{1,}) (.{1,})|",$name,$names)){
  $data['LAST_NAME']=$names[2];
  $data['NAME']=$names[1];
}else
$data['NAME']=$_POST['name'];
$data['TITLE']="Лид ".$_POST['name'];
if($_POST['phone']!='')
  $data['PHONE_MOBILE']=preg_replace("/[^0-9]/", '', $_POST['phone']); 
if(isset($source))
  $data['SOURCE_ID']=$source; 
if($_POST['email']!='')
  $data['EMAIL_WORK']=$_POST['email']; 

//UF_CRM_1463741187 — ID произвольных полей в настройках полей в строке URL 

$data['UF_CRM_1463741187']="franchise.evodreamholding.com";

if($_POST['keyword'])
  $data['UF_CRM_1406282339']=$keyword;

if($_POST['utm_medium'])
  $data['UF_CRM_1463742682']=$_POST['utm_medium'];

if($_POST['utm_source'])
  $data['UF_CRM_1463742727']=$_POST['utm_source'];  

if($_POST['utm_campaign'])
  $data['UF_CRM_1463742749']=$_POST['utm_campaign'];        

if($_POST['utm_term'])
  $data['UF_CRM_1463742765']=$_POST['utm_term'];    

if($_POST['utm_content'])
  $data['UF_CRM_1463742810']=$_POST['utm_content'];     

if($block)
  $data['UF_CRM_1463742789']=$block;          

curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
$flogs=fopen("./files/logs.txt","a"); //Работа с файлами
if(!preg_match('|Лид добавлен|', curl_exec($ch))){  
  $d_ = array();
  foreach($data as $k=>$v){
    $d_[] = $k.' : '.$v;
  }
  fwrite($flogs,date("d-m-Y H:i:s")." Не удалось добавить контакт в BITRIX24\n".join(', ',$d_)."\n");
}else{
  $d_ = array();
  foreach($data as $k=>$v){
    $d_[] = $k.' : '.$v;
  }
  fwrite($flogs,date("d-m-Y H:i:s")." --- Добавлен контакт в BITRIX24\n".join(', ',$d_)."\n");
}

switch ($_POST['info']) {
	case 'Скачать презентацию':
		$file_subject = "Презентация";
		$file_name = "Презентация франшизы DreamТорг.pdf";

    $file_path = './files/'.$file_name;
    $file = fopen($file_path, "r"); //Открываем файл
    $text = fread($file, filesize($file_path)); //Считываем весь файл
    fclose($file); //Закрываем файл

    $file_content = chunk_split(base64_encode($text));
		$uid = md5(uniqid(time()));
		
		// header
		$file_header = "From: ".$server_email."\r\n";
		$file_header .= "Reply-To: ".$admin_email."\r\n";
		$file_header .= "MIME-Version: 1.0\r\n";
		$file_header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
		
		// message & attachment
		$file_message = "--".$uid."\r\n";
		$file_message .= "Content-type:text/plain; charset=iso-8859-1\r\n";
		$file_message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$file_message .= "--".$uid."\r\n";
		$file_message .= "Content-Type: application/octet-stream; name=\"".$file_name."\"\r\n";
		$file_message .= "Content-Transfer-Encoding: base64\r\n";
		$file_message .= "Content-Disposition: attachment; filename=\"".$file_name."\"\r\n\r\n";
		$file_message .= $file_content."\r\n\r\n";
		$file_message .= "--".$uid."--";

		mail($_POST['email'], $file_subject, $file_message, $file_header);

		header('Location: /test.html');
		break;
	case 'Получить подарки':
		header('Location: /result.html');
		break;
	default:
		break;
}
