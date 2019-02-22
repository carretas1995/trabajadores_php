/*Gabriel 2019*/
$(document).ready(function () {
/*-------------guardo un nuevo trabajador al pulsar el boton----------------------------------- */
    $("#grabar").click(function () {
        if(comprobar_vacio()){//compruebo si hay campos obligatorios nulos
            $.ajax({
                url: "php/grabar.php",
                method: "POST",
                async: false,
                data: {
                    funcion: 1, //valor para ejecutar accion del switch
                    //paso los valores del formulario
                    nombre: $("#nombre").val(),
                    apellidos: $("#apellidos").val(),
                    telefono: $("#telefono").val(),
                    otros: $("#otros").val(),
                    departamento: $("#departamento").val()
                },
                success: function(response) {
                    if(response==0){
                        $("#respuesta").html("Error 524");
                    }else{
                        $("#respuesta").html("Trabajador guardado").toggleClass('respuesta');
                    }
                }
            });
        }
    });

/*---------------boton para listar, llama a una funcion listar trabajadores--------------------------------- */
    $("#listar").click(function () {
            listar_trabajadores();
            //muestro el boton de borrar
            $(".tmp").css('display', 'block');
    });

    /*funcion que segun el departamento seleccionado lista en pantalla*/
    function listar_trabajadores(){
        $.ajax({
            url: "php/grabar.php",
            method: "POST",
            async: false,
            data: {
                funcion: 2, //valor para ejecutar accion del switch
                //paso el valor del select
                departamentoListar: $("#departamentoListar").val()
            },
            success: function(response) {
                $("#respuestaListar").html(response);
            }
        });
    }

/*-------------modificar trabajador clicando dos veces en el id de la tabla----------------------------------- */
//devido a la escucha del boton listar, es necesario enlazar con el elemento padre
    //antes estaba con document
    $("#respuestaListar").on("dblclick","#id",function() {
    /*-----recojo el id DEL ELEMENTO CLICADO------- */
        var s=parseInt($(this).text()); 
        $('#desplegable_modi').modal('show'); //despliego el modal center 

    /*-----Relleno el modal con los datos del trabajador seleccionado------- */
       $.ajax({
        url: "php/grabar.php",
        method: "POST",
        async: false,
        data: {
            funcion: 3, //valor para ejecutar accion del switch
            //paso el id a la consulta php
            id: s
        },
        success: function(response) { 
            var items = JSON.parse(response);  //recojo el array devuelto y paso el json 
            //escrivo los valores en los input del desplegable
            $("#nombre_mod").val(items.nombre);
            $("#apellidos_mod").val(items.apellidos);
            $("#telefono_mod").val(items.telefono);
            $("#otros_mod").val(items.otros);
            $("#departamento_mod").val(items.departamento);

        }
        });

    /*-----update a la bd al pulsar btn modificar------- */    
        $(".modal-footer").on("click",".btn-primary",function() {
            $.ajax({
                url: "php/grabar.php",
                method: "POST",
                async: false,
                data: {
                    funcion: 4, 
                    id_update:s,
                    nombre_update:$("#nombre_mod").val(),
                    apellidos_update:$("#apellidos_mod").val(),
                    telefono_update:parseInt($("#telefono_mod").val()),
                    otros_update:$("#otros_mod").val(),
                    departamento_update:$("#departamento_mod").val()
                },
                success: function(response) {
                  $('#desplegable_modi').modal('hide'); //cierro el modal (ventana desplegada)
                  listar_trabajadores(); //actualizo la tabla trabajadores
                }
            });
        });
    });

/*-------------Selecciono todos los checkbos de la lista al pulsar la opcion----------------------------------- */
    $("#respuestaListar").on("click","#borrar_todo", function() {
        $(".case").attr("checked", this.checked);
    });

/*-------------Borra un trabajador por su id, error: muestra alert----------------------------------- */
    var borrar_trab=function(id_trabajador){
        $.ajax({
            url: "php/grabar.php",
            method: "POST",
            async: false,
            data: {
                funcion: 5, 
                id_borrar:id_trabajador
            },
            success: function(response) {
                if(response!=true){
                    alert(response);
                }else{
                    listar_trabajadores(); //actualizo la tabla trabajadores
                }
            }
        });
    }

/*-------------Compruebo los checkbos marcados y elemino dicho trabajador----------------------------------- */
    $(".tmp").on("click","#borrar", function() {
        //bucle que coge los checkbox de clase case checados, y llama a borrar pasando el valor(id del trabajador)
        $("input:checkbox[class=case]:checked").each(function(){
            borrar_trab(parseInt($(this).val()));
        });
    });

    

/*-------------compruebo si hay campos obligatorios, si=no ejecuta y graba los datos----------------------------------- */
    function comprobar_vacio(){
        var campos=$(".no_null"); //array de campos 
        var bandera=true;
        for(var i=0;i<campos.length;i++){
            if(campos[i].value==""){
                bandera=false;
            }
        }
        return bandera;
    }


});
