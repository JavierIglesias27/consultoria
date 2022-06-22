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
            // echo '<br/>';
            // echo 'AA+' . $usuario->email . '+AA';
            // echo '<br/>';
            // echo $sha1;
            // echo '<br/>';

            echo '<br/>';
            if ($clave == $sha1) {
                //  
                // un avez hecho descomento insertuser
                insertUser($usuario);
            } else {
                echo 'mal';
            }
        } else {
            echo 'Error:id not found';
        }
        $conn->close();
    }


    function insertUser($usuario)
    {
        $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
        $sql = 'UPDATE contacta_temp  SET estado=1 WHERE id="' . $usuario->id . '"';
        if ($conn->query($sql) === TRUE) {
            // copiar aqui tmb if
            echo '<div style="background-color: grey; border: 2px solid black">
            <h1>Te damos la bienvenida a Consulting S.A</h1>
            <p>
                Sr/a' . "  " . '<i><b>' . $usuario->nombre . "  " .  $usuario->apellido . "  " . '</i></b>le confirmamos que su consulta a sido recibida
                correctamente.<br /> Nuestros especialista darán una solución lo más
                rápidamente posible poniendose en contacto con usted mediante su correo:' . "  " . '<i><b>' .
                $usuario->email . '</i></b>  o su número de telefono:' . "  " . '<i><b>' . $usuario->phone . '
            <i><b> </p>
            <div style="background-color: white; width:50%;margin:auto; padding-left:5px; border: 2px solid black">
            id=' . $usuario->id . '<br/> Asunto: ' . $usuario->asunto . '<br/> Fecha:' . $usuario->reg_date . '<br/>
            Texto: ' . $usuario->textarea . '
            
            </div>
            <p>En caso de que los datos sean erroneos pongasé en contacto mediante el correo: <a href="#">consultingAsesores@gmail.com</a></p>
            <h3>Atentamente la dirección de Consuting S.A</h3>
        </div>';

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