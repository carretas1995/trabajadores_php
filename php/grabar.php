<?php
    if(isset($_POST["funcion"])) { // Se pasa una acción
		$funcion = $_POST['funcion'];
		switch($funcion) {
            case 1:
                grabar();
                break;
            case 2:
                listar();
                break;
            case 3:
                buscar();
                break;
            case 4:
                actualizar();
                break;
            case 5:
                borrar();
				break;
            default:
                echo "Error: Falta una acción";
        }
	}

    /*abre la conexion con la base de datos*/
	function conexion(){
		include('db_acceso.php');
		//conexion con el servidor mysql y seleccion de la base de datos:
		$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_database);
		$mysqli->set_charset("utf8");

		#compruebo la conexion, en caso de error salgo
		if ($mysqli->connect_errno) {
   			printf("Connect error:".$mysqli->connect_error);
   			exit();
		}
		return $mysqli;
	}

/*------Funcion para grabar ------------*/    
	function grabar(){
        $mysqli =conexion();
        $stmt = $mysqli->prepare("insert into plantilla (nombre,apellidos,telefono,otros,departamento) values(?,?,?,?,?)");  
        //los valores recogidos sol los que paso por ajax       
        $stmt->bind_param("ssiss",$_POST['nombre'],$_POST['apellidos'],$_POST['telefono'],$_POST['otros'],$_POST['departamento']);
        //ejecutamos, en caso de que no se ejecute devolvemos error
        if ($stmt && $stmt->execute()){
            $msg= "1"; //guardado
            // Cerramos la sentencia preparada.
            $stmt -> close();
            echo "$msg";
        } else {
            $msg= "0"; //error
            echo "$msg";
        }
        // Cerramos la conexión.
        $mysqli->close();
    }

/*------Funcion para listar los trabajadores------------*/
    function listar(){
        $select=$_POST['departamentoListar'];
        $mysqli =conexion();
    /*--Compruebo la opcion elegida en el desplegable--*/
        if($select=="todos"){ //en caso de seleccionar todos
            $consulta="SELECT * FROM plantilla";
            /*--ejecuto la consulta, true=muestra el resultado--*/
            if ($resultado = $mysqli->query($consulta)) {
                echo '<TABLE BORDER="0" CELLPADDING="5" CELLSPACING="20">';
                    echo '<TR ALIGN="center" BGCOLOR="#333" id="tituloTabla">';
                        echo"<td id='borrar'><input type='checkbox'  id='borrar_todo'></td>";
                        echo '<TD>Id</TD>';
                        echo '<TD>Nombre</TD>';
                        echo '<TD>Apellidos</TD>';
                        echo '<TD>Telefono</TD>';
                        echo '<TD>Otros</TD>';
                        echo '<TD>Departamento</TD>';
                    echo '</tr>';
                /* obtener el array de objetos y saco los valoes de la bd */
                while ($fila = $resultado->fetch_assoc()) {
                    echo '<TR ALIGN="center" BGCOLOR="">';
                        echo"<td id='borrar'><input type='checkbox' class='case' value='".$fila["id"]."'></td>";
                        echo"<td id='id'>".$fila["id"]."</td>"; 
                        echo"<td>".$fila["nombre"]."</td>";
                        echo"<td>".$fila["apellidos"]."</td>";
                        echo"<td>".$fila["telefono"]."</td>";
                        echo"<td>".$fila["otros"]."</td>";
                        echo"<td>".$fila["departamento"]."</td>";
                        echo '</tr>';                  
                }
                echo '</table>';
                $resultado->close();
            }else{
                echo"<p>Error en la consulta!!</p>".$mysqli->error;
            }
        }else{ //en caso de seleccionar un depart. en concreto
            $consulta="SELECT * FROM plantilla WHERE departamento='$select'";
            /*--ejecuto la consulta, true=muestra el resultado--*/
            if ($resultado = $mysqli->query($consulta)) {
                echo '<TABLE BORDER="0" CELLPADDING="5" CELLSPACING="20">';
                    echo '<TR ALIGN="center" BGCOLOR="#333" id="tituloTabla">';
                        echo"<td id='borrar'><input type='checkbox'  id='borrar_todo'></td>";
                        echo '<TD>Id</TD>';
                        echo '<TD>Nombre</TD>';
                        echo '<TD>Apellidos</TD>';
                        echo '<TD>Telefono</TD>';
                        echo '<TD>Otros</TD>';
                        echo '<TD>Departamento</TD>';
                    echo '</tr>';
                /* obtener el array de objetos y saco los valoes de la bd */
                while ($fila = $resultado->fetch_assoc()) {
                    echo '<TR ALIGN="center" BGCOLOR="">';
                        echo"<td id='borrar'><input type='checkbox' class='case'  value='".$fila["id"]."'></td>";
                        echo"<td id='id'>".$fila["id"]."</td>"; 
                        echo"<td>".$fila["nombre"]."</td>";
                        echo"<td>".$fila["apellidos"]."</td>";
                        echo"<td>".$fila["telefono"]."</td>";
                        echo"<td>".$fila["otros"]."</td>";
                        echo"<td>".$fila["departamento"]."</td>";
                        echo '</tr>';                  
                }
                echo '</table>';
                $resultado->close();
            }else{
                echo"<p>Error en la consulta!!</p>".$mysqli->error;
            }
        }
    }

/*------Funcion para buscar un trabajador por id, devuelve un array en json------------*/
    function buscar(){
        $id=$_POST['id'];
        $mysqli =conexion();
        $consulta="SELECT * FROM plantilla WHERE id='$id'";
        //ejecuto la consulta
        if ($resultado = $mysqli->query($consulta)) {
            echo json_encode($resultado->fetch_assoc());  //codifico el array en json para manejarlo en javascript           
        }else{
            echo $mysqli->error;
        } 
    }
 
/*------Funcion para actualizar los datos de un trabajador------------*/
    function actualizar(){
        $mysqli=conexion();
        $stmt = $mysqli->prepare("UPDATE plantilla set nombre=?,apellidos=?,telefono=?,otros=?,departamento=?  WHERE id=? ");
        $stmt->bind_param("ssissi",$_POST['nombre_update'],$_POST['apellidos_update'],$_POST['telefono_update'],$_POST['otros_update'],$_POST['departamento_update'],$_POST['id_update']);
        if ($stmt && $stmt->execute()){
            // Cerramos la sentencia preparada.
            $stmt -> close();
            echo "Actuaizado correctamente";
        } else {
            echo "Error al actualizar".$mysqli->error;
        }
        // Cerramos la conexión.
        $mysqli->close();
    }

/*------Funcion para borrar trabajadores------------*/
    function borrar(){
        $mysqli=conexion();
        $stmt = $mysqli->prepare("DELETE FROM plantilla WHERE id=?");
        $stmt->bind_param("i", $_POST['id_borrar']);
        //ejecutamos
        if ($stmt && $stmt->execute()){
            // Cerramos la sentencia preparada.
            $stmt -> close();
            echo true;
        } else {
            echo false.$mysqli->error;
        }
        // Cerramos la conexión.
        $mysqli->close();
    }

?>
