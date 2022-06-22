<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
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
                $usuario->estado = $row['estado'];
                $usuario->reg_date = $row['reg_date'];
            }
            // $xstring = $usuario->id . "-" . $usuario->email . "-" . $usuario->nombre . "-" . $usuario->apellido . "-" . $usuario->phone . "-" . $usuario->asunto . "-" . $usuario->textarea . "-" . $usuario->reg_date .  "-" . $usuario->estado;

            $xstring = $usuario->id . "-" . $usuario->email . "-" . $usuario->nombre . "-" . $usuario->apellido . "-" . $usuario->phone . "-" . $usuario->asunto . "-" . $usuario->textarea . "-" . $usuario->reg_date . '-0';
            $sha1 = sha1($xstring);
            echo '<br/>';
            echo 'AA+' . $usuario->email . '+AA';
            echo '<br/>';
            echo $sha1;
            echo '<br/>';

            echo '<br/>';
            if ($clave == $sha1) {
                echo '<h2> tu correo ha sido validado</h2>';
                // un avez hecho descomento insertuser
                //insertUser($usuario);
            } else {
                echo 'mal';
            }
        } else {
            echo 'Error:id not found';
        }
        $conn->close();
    }


    function insertUser($user)
    {
        $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
        $sql = 'UPDATE contacta_temp  SET estado=1 WHERE id="' . $user->id . '"';
        if ($conn->query($sql) === TRUE) {
            // copiar aqui tmb if
            echo '<h2> tu correo ha sido validado</h2>';
            // header('Location: login.html');
        } else {
            echo "<br>ERROR";
            //echo "Error: insert table \"usuarios\" " . $conn->error . " <br>" . $sql . "<br>";
        }
        $conn->close();
    }
    ?>
</body>

</html>