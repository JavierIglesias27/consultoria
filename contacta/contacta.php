<?php
//INTRODUCIR DATOS DE SQLMY ADMIN
//https://www.freemysqlhosting.net/register/?action=register

//new mysqli("SERVER", "USERNAME", "PASSWORD", "NAME");
//new mysqli("localhost", "root", "", "pbd");

//bbdd mysqlhosting
//new mysqli("sql4.freemysqlhosting.net", "sql4501016", "LNnLKKSRBe", "sql4501016");

date_default_timezone_set('Europe/Madrid');

require_once $_SERVER["DOCUMENT_ROOT"] . "/conf/admin.php";


$myObject = new stdClass();


/* sanitize =le ponemos strip_tags xa q solo coja texto aunque sea codigo */
function sanitize($texto)
{
    return htmlentities(strip_tags($texto), ENT_QUOTES, "UTF-8");
}
switch ($_POST['api']) {
    case "checkEmail":
        $email = sanitize($_POST['email']);
        checkEmail($email, $myObject);
        if (isset($myObject->success)) {
            insertUser($email, sanitize($_POST['nombre']), sanitize($_POST['apellido']), sanitize($_POST['phone']), sanitize($_POST['asunto']), sanitize($_POST['textarea']), sanitize($_POST['captcha']), $myObject);
        }
        break;
    default:
        $myObject->error = "error en el switchcase";
        break;
}
echo json_encode($myObject);
function checkEmail($email, $myObject)
{
    /*trim quito espacios delante
    str_replace quieta espacios entre letras
    strtolower  se pone todo a minusculas */

    $email = strtolower(str_replace(" ", "", trim($email)));
    if ($email == "" || is_numeric($email)) {
        $myObject->error =  "PHP lado servidor: el mail no puede estar vacio o es valor numerico";
    }

    /*if(pattern ) */


    $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);

    $sql = "SELECT email FROM contacta WHERE email= '" . $email . "';";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        // print_r($result);
        while ($row = $result->fetch_assoc()) {
            $myObject->error = "YA EXISTE: vacio - " . $row['email'];
        }
    } else {
        $myObject->success = "PHP: lado servidor : mail is OK";
    }
    $conn->close();
}
function insertUser($email, $nombre, $apellido, $phone, $asunto, $textarea, $captcha, $myObject)
{
    // hacer regex
    // if($email==""){

    // }else{

    // }

    $response = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" . RECAPTCHA_V3_SECRET_KEY . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']
    );
    $response = json_decode($response);
    if ($response->success === false) {
        //Do something with error
    } else {
        if ($response->success == true && $response->score > 0.5) {
            guardarDB($email, $nombre, $apellido,  $phone, $asunto, $textarea, $myObject);
        } else if ($response->success == true && $response->score <= 0.5) {
            //Do something to denied access
            $myObject->error = "Human?<br>";
        } else {
            $myObject->error = "NO<br>";
        }
    }
}
function guardarDB($email, $nombre, $apellido, $phone, $asunto, $textarea, $myObject)
{
    $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
    $sql = "INSERT INTO contacta_temp(email,nombre,apellido,phone,asunto,textarea) VALUES('" . $email . "', '" . $nombre . "','" . $apellido . "', '" . $phone . "','" . $asunto . "','" . $textarea . "' )";
    if ($conn->multi_query($sql) === TRUE) {
        // echo "  insert  table \"pbd\"<br/>";
        $last_id = $conn->insert_id;
        // echo  "Id asociado: " . $last_id;
        enviarmail($email, $myObject);
    } else {
        // echo "Error al  insert table \"pbd\"<br/>" . $conn->error;
        $myObject->success = " ERROR Usuario NO guardado DB";
    }
    $conn->close();
}
function enviarmail($email, $myObject)
{
    $usuario = new stdClass();
    $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
    $sql = "SELECT * FROM contacta_temp WHERE email = '" . $email . "' ORDER BY id DESC LIMIT 1 ;";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {

        while ($row = $result->fetch_assoc()) {
            $usuario->id = $row['id'];
            $usuario->email = $row['email'];
            $usuario->nombre = $row['nombre'];
            $usuario->apellido = $row['apellido'];
            $usuario->phone = $row['phone'];
            $usuario->asunto = $row['asunto'];
            $usuario->textarea = $row['textarea'];
            $usuario->reg_date = $row['reg_date'];
        }
        $xstring = $usuario->id . "-" . $usuario->email . "-" . $usuario->nombre . "-" . $usuario->apellido . "-" . $usuario->phone . "-" . $usuario->asunto . "-" . $usuario->textarea . "-" . $usuario->reg_date;
        $sha1 = sha1($xstring);
        // echo $sha1;
        sendMail($usuario, $sha1, $myObject);
    } else {

        // echo "ERROR:<br>";
        // print_r($result);
        // echo "<br>" . $sql . "<br>";
    }
    $conn->close();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($usuario, $sha1, $myObject)
{
    //MAIL
    $HostSMTP = 'smtp.gmail.com'; // Set the SMTP server to send through
    $ContrasenaDelCorreo = 'glmhqdsxiibzpqak'; // SMTP password CONTRASEÑA APLICACIONGENERDA MAIL IMPORTANMTEEEEEE
    $SendFromEMAIL = 'javiCesi75@gmail.com'; // escribir mi correo electronico
    $QuienLoEnviaNAME = 'moderator';
    $SendFromEMAILreply = 'javiCesi75@gmail.com';
    $QuienResponderNAME = 'moderator';
    $PortSMTP = 465; // TCP port to connect to
    //$PortSMTP = 587; // TCP port to connect to
    //
    $SentToEmail = $usuario->email;/* este es el usuario q yo genere podria poner javi@hotmail.com */
    $Asunto = "ninguno";
    $BodyHTML = '<div
    style="background-color: rgb(178, 198, 243); border: 1px solid black"
    ><h1>Bienvenido <code>	
        &#128076;</code>   ' . $usuario->nombre . '</h1><p style="font-size:15px;"> Te has registrado correctamente<code>&#10004;</code></p><br/><div><p style="font-size:15px;">Click en el link para acceder:        <a href="http://' . $_SERVER['HTTP_HOST'] . '/contacta/nuevousuario.php?id=' . $usuario->id . '&clave=' . $sha1 . '"><b>' . $sha1 . '</b></a> </p></div></div>';

    $BodyNOHTML = "hola que tal?";




    //Load Composer's autoloader
    require realpath($_SERVER["DOCUMENT_ROOT"]) . '/vendor/autoload.php';

    //Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_CONNECTION;                      //Enable verbose debug output
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $HostSMTP;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $SendFromEMAIL;                     //SMTP username
        $mail->Password   = $ContrasenaDelCorreo;                               //SMTP password
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $PortSMTP;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom($SendFromEMAIL, $QuienLoEnviaNAME);
        //$mail->addAddress($SentToEmail, 'Joe User');     //Add a recipient
        $mail->addAddress($SentToEmail);               //Name is optional
        $mail->addReplyTo($SendFromEMAIL, $QuienLoEnviaNAME);
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $Asunto;
        $mail->Body    = $BodyHTML;
        $mail->AltBody = $BodyNOHTML;

        $mail->send();
        $myObject->success = "Message has been sent";
    } catch (Exception $e) {
        $myObject->error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
