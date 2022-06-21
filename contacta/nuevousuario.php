<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/conf/admin.php";

if (isset($_GET['id']) && isset($_GET['clave'])) {
    $id = $_GET['id'];
    $clave = $_GET['clave'];

    echo "id=" . $id . "<br />";

    echo "clave=" . $clave;

    $usuario = new stdClass();
    $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
    $sql = "SELECT * FROM contacta_temp WHERE id= '" . $id . "';";
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
        if ($clave == $sha1) {
            insertUser($usuario);
        }
    } else {
        echo 'Error:id not found';
    }
    $conn->close();
}


function insertUser($user)
{
    $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
    $sql = "INSERT INTO contacta (email,nombre,apellido,phone,asunto,textarea,reg_date) VALUES ('" . $user->email . "','" . $user->nombre . "','" . $user->apellido . "'," . $user->phone . ",'" . $user->asunto . "','" . $user->textarea . "','" . date("Y-m-d H:i:s") . "');";
    if ($conn->query($sql) === TRUE) {
        echo "<br>OK";
        $sql_a = "DELETE FROM usuarios_temp WHERE email='" . $user->email . "' || reg_date <= NOW() - INTERVAL 1 DAY;";
        $conn->query($sql_a);
        header('Location: login.html');
    } else {
        echo "<br>ERROR";
        //echo "Error: insert table \"usuarios\" " . $conn->error . " <br>" . $sql . "<br>";
    }
    $conn->close();
}
