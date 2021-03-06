<?php 

include "../helpers/autorizado.php";
include "../layout/layout.php";
include '../../helpers/utilities.php';
include '../../helpers/FileHandler/IFileHandler.php';
include '../../helpers/FileHandler/JsonFileHandler.php';
include '../../database/SADVContext.php';
include 'Ciudadano.php';
include '../../database/repository/IRepository.php';
include '../../database/repository/RepositoryBase.php';
include '../../database/repository/RepositoryCiudadano.php';
include 'CiudadanoService.php';

$layout = new layout(true,"ciudadanos",true);
$utilities = new Utilities();
$service = new CiudadanoService("../../database");

$containId = isset($_GET['id']);
$element = null;
if ($containId) {
    $id = $_GET['id'];
    $element = $service->GetById($id);
    $selectedActivo=($element->estado == "1") ? "selected" : ""; 
    $selectedInactivo=($element->estado == "0") ? "selected" : ""; 
}

$listaCiudadanos = $service->GetAll();
$documentosIdentidad = array();

// Validacion de POST

if(isset($_POST['nombre']) && isset($_POST['apellido']) && isset($_POST['email']) && isset($_POST['documentoIdentidad'])){
    
    foreach($listaCiudadanos as $ciudadano){
        if($ciudadano->documentoIdentidad != $element->documentoIdentidad){
            $documentosIdentidad[] = $ciudadano->documentoIdentidad;
        }
    }  

    foreach($documentosIdentidad as $documento){
      if($_POST['documentoIdentidad']==$documento){
          $_SESSION['mensajeExiste'] = "El ciudadano ya existe";
          header("location:editar.php?id={$element->id}");
          exit();
      }
    }
    $updateEntity = new Ciudadano();
    $updateEntity->InitializeData($id,$_POST['documentoIdentidad'],$_POST['nombre'], $_POST['apellido'],$_POST['email'],true);
    $service->Update($updateEntity);
    header("Location: listaCiudadanos.php"); 
    exit(); 
}

$mensaje="";
if(isset($_SESSION['mensajeExiste'])){
   $mensaje = $_SESSION['mensajeExiste'];
}
$_SESSION['mensajeExiste']="";

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Custom fonts for this template-->
    <link href="../../styles/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Page level plugin CSS-->
    <link href="../../styles/vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../styles/css/sb-admin.css" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ciudadanos</title>
</head>
<body  id="page-top">
<?php $layout->mostrarHeader();?>

<div id="content-wrapper">

    <div class="container-fluid">

    <?php if($mensaje!=""){echo "<script type='text/javascript'>alert('$mensaje');</script>";}?>

    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
        <a href="listaCiudadanos.php">Ciudadanos</a>
        </li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>
    <!--Formulario-->
    <div class="card mb-3">
        <div class="card-header">
        <i class="fas fa-user-cog"></i> Edicion del Ciudadano <strong><?php echo $element->nombre;?></strong>      
        </div>

        <div class="card-body">

        <!-- Formulario -->

        <form class="needs-validation" method="POST" action= "editar.php?id=<?php echo $element->id; ?>" novalidate>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <h6><label for="nombre" class="col-form-label-lg col-form-label">Nombre</label></h6>
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupPrepend"><i class="fa fa-user" aria-hidden="true"></i></span>
                        </div>
                        <input value="<?php echo $element->nombre;?>" type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre del ciudadano" aria-describedby="inputGroupPrepend" required>
                        <div class="invalid-feedback">
                        Digite un nombre valido
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mb-3">
                    <h6><label for="apellido" class="col-form-label-lg col-form-label">Apellido</label></h6>
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupPrepend"><i class="fa fa-male" aria-hidden="true"></i></span>
                        </div>
                        <input value="<?php echo $element->apellido;?>" type="text" class="form-control" name="apellido" id="apellido" placeholder="Apellido del ciudadano" aria-describedby="inputGroupPrepend" required>
                        <div class="invalid-feedback">
                        Digite un apellido valido
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-5 mb-3">
                    <h6><label for="email" class="col-form-label-lg col-form-label">Email</label></h6>
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupPrepend"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                        </div>
                        <input value="<?php echo $element->email;?>" name="email" type="email" class="form-control" id="email"value="<?php echo $element->email;?>" placeholder="Email del Ciudadano" aria-describedby="inputGroupPrepend" required>
                        <div class="invalid-feedback">
                        Digite un email valido
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <h6><label for="DocIdentidad" class="col-form-label-lg col-form-label">Documento de Identidad</label></h6>
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupPrepend"><i class="fa fa-id-card" aria-hidden="true"></i></span>
                        </div>
                        <input value="<?php echo $element->documentoIdentidad;?>" type="text" class="form-control" name="documentoIdentidad" id="documentoIdentidad" placeholder="Documento de Identidad" aria-describedby="inputGroupPrepend" required>
                        <div class="invalid-feedback">
                        Digite un documento de identidad valido
                        </div>
                    </div>
                </div>
            </div>
            <h6><label class="col-form-label-lg col-form-label">Estado</label></h6>
            <div class="custom-control custom-radio">
            <select name="estado" class="form-control" id="CheckStatus">
            <option <?php echo $selectedActivo; ?> value="1">Activo</option>
            <option <?php echo $selectedInactivo; ?> value="0">Inactivo</option>
            </select>
            </div>
            <br>
            <button class="btn btn-primary" type="submit">Editar</button>
        </form>
<script>
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
</script>
        <!-- /Formulario -->



        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php $layout->mostrarFooter();?>
</body>
</html>