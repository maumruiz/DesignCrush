<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8"/>
    <title>Bootstrap File Upload</title>
        
	<link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
       
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/fileinput.min.js" type="text/javascript"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js" type="text/javascript"></script>
    
	</head>
    
	<body>
		<header>
    <!-- Navigation bar -->
    <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navegacion">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="home.html" class="navbar-brand">Diseñazo</a>
            </div>

            <!-- Inicia Menu -->
            <div class="collapse navbar-collapse" id="navegacion">
                <!-- Botones de barra de navegación derecha -->
                <ul id="navRightButtons" class="nav navbar-nav navbar-right">
                    <!-- Login / Register Button -->
                    <li>
                        <a href="login.html">Login / Register</a>
                    </li>
                    <!-- User dropdown -->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-user" aria-hidden="true"></span> User <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="userProfile.html">My Profile</a>
                            </li>
                            <li>
                                <a href="orderHistory.html">Order History</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="uploadDesign.html">Upload Design</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">Logout</a>
                            </li>
                        </ul>
                    </li>
                    <!-- Shopping Cart -->
                    <li id="shoppingCart">
                        <a href="cart.html"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Cart </a>
                    </li>
                </ul>
                <!-- Search bar -->
                <form action="" class="navbar-form navbar-right" role="search">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                </form> <!-- Search bar -->
            </div> <!-- navbar -->
        </div> <!-- Container -->
    </nav>
</header>
	<input id="archivos" name="imagenes[]" type="file" multiple=true class="file-loading">
	</body>
	<?php 	
	$directory = "img/designs/";      
	$images = glob($directory . "*.*");
	?>
	
	<script>
	$("#archivos").fileinput({
	uploadUrl: "upload.php", 
    uploadAsync: false,
    minFileCount: 1,
    maxFileCount: 20,
	showUpload: false, 
	showRemove: false,
	initialPreview: [
	<?php foreach($images as $image){?>
		"<img src='<?php echo $image; ?>' height='120px' class='file-preview-image'>",
	<?php } ?>],
    initialPreviewConfig: [<?php foreach($images as $image){ $infoImagenes=explode("/",$image);?>
	{caption: "<?php echo $infoImagenes[1];?>",  height: "120px", url: "borrar.php", key:"<?php echo $infoImagenes[1];?>"},
	<?php } ?>]
	}).on("filebatchselected", function(event, files) {
	
	$("#archivos").fileinput("upload");
	
	});
	
	</script>
</html>