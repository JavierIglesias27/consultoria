<?php
//areglar variabls pasandolas a otro php
date_default_timezone_set('Europe/Madrid');
define("RECAPTCHA_V3_SECRET_KEY", '6LepHlMgAAAAANAWwdPTbXISe5rKHLbQEno8tQV1');

$myObject = new stdClass();


/* sanitize =le ponemos strip_tags xa q solo coja texto aunque sea codigo */
function sanitize($texto)
{
    return htmlentities(strip_tags($texto), ENT_QUOTES, "UTF-8");
}

switch ($_POST['api']) {
    case "loginUser":
        checkCaptcha(sanitize($_POST['captcha']), $myObject);

        if (isset($myObject->success)) {
            loginUser(sanitize($_POST['email']), sanitize($_POST['password']), $myObject);
        }
        break;
    default:
        $myObject->error = "error en el switchcase";
        break;
}
echo json_encode($myObject);

function checkCaptcha($captcha, $myObject)
{

    $response = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" . RECAPTCHA_V3_SECRET_KEY . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']
    );
    $response = json_decode($response);
    if ($response->success === false) {
        //Do something with error
        $myObject->error = "NO recaptcha<br>";
    } else {
        if ($response->success == true && $response->score > 0.5) {
            $myObject->success = "Human";
        } else if ($response->success == true && $response->score <= 0.5) {
            //Do something to denied access
            $myObject->error = "Human?<br>";
        } else {
            $myObject->error = "NO<br>";
        }
    }
}
function loginUser($email, $password, $myObject)
{
    $usuario = new stdClass();
    $conn = new mysqli("sql4.freemysqlhosting.net", "sql4499631", "aVDL9RBswz", "sql4499631");
    $sql = "SELECT nombre FROM usuarios WHERE email= '" . $email . "'&& password='" . md5($password) . "';";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        while ($row = $result->fetch_assoc()) {

            $usuario->email = $email;
            $usuario->nombre = $row['nombre'];
            $usuario->token = md5(time() . "-" . $usuario->email);
            $sql = "UPDATE usuarios SET token='" . $usuario->token . "' WHERE email= '" . $email . "';";
            $result_a = $conn->query($sql);/* hacer if xa comprobar q entre */

            $myObject->success = json_encode($usuario);
            // $myObject->error = null;
            break;
        }
    } else {
        echo 'Error:usuario not found';
    }
    $conn->close();
}
