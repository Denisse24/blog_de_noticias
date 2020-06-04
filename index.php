<?php

//traer los datos de las APIs
$noticasApi = file_get_contents('http://newsapi.org/v2/top-headlines?country=mx&category=technology&apiKey=f12a53eec4a946539e3e972333111d5f');
$noticias   = json_decode($noticasApi, true);
$articulos  = $noticias['articles'];

$autoresApi = file_get_contents('https://randomuser.me/api/?results='.count($articulos).'&nat=es&?format=json');
$autores    = json_decode($autoresApi, true);


//se agregan los autores al arreglo de noticias
$i = 0;
foreach ($autores['results'] as $autor){

    $encontrarNombre = array_search($autor['name']['first']. " " . $autor['name']['last'], $articulos);
    if(!$encontrarNombre){
        $articulos[$i]['autor'] = $autor['name']['first']. " " . $autor['name']['last'];
        $i++;
    }
}


//----- CODIGO DE PAGINACION -----

//numero de noticias por pagina
$noticiasPagina = 10;

//numero de paginas que habra
$paginasTotal = ceil(count($articulos) / $noticiasPagina);

//Para empezar siempre en la pagina 1
if(!$_GET){
    header('Location: index.php?pagina=1');
}

//Si se coloca en la url un numero de pagina que no existe se redirecciona a la primera pagina
if($_GET['pagina'] > $paginasTotal || $_GET['pagina'] <= 0){
    header('Location: index.php?pagina=1');
}

//numero de la primera noticia de la pagina
$noticiaInicialPagina = ($_GET['pagina'] - 1) * $noticiasPagina;

//numero de la ultima noticia de la pagina
$noticiaFinalPagina   = ($_GET['pagina'] - 1) * $noticiasPagina + ($noticiasPagina - 1);

//nuevo arreglo que solo tendra las noticias de la pagina actual
$articulosPorPagina   = [];

//para colocar en el nuevo el numero de noticias que corresponda segun la pagina seleccionada
for($i=$noticiaInicialPagina; $i <= $noticiaFinalPagina; $i++){

    array_push($articulosPorPagina,  $articulos[$i]);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/all.min.js"></script>
    <title>Document</title>
</head>
<body class="body-color">
    <header class="header-margin">
        <nav class="navbar navbar-light nav-color">
            <a class="navbar-brand nav-titulo" href="/">
                <i class="far fa-newspaper" width="30" height="30" class="d-inline-block align-top" ></i>
                Noticias de tecnología en México
            </a>
        </nav>
    </header>
    <div class="container">
        <div class="row row-cols-1 row-cols-md-2" id="recargar">
        <?php foreach ($articulosPorPagina as $articulo): ?>
            <div class="col mb-4">
                <a href="<?php echo $articulo['url'] ?>" target="_blank" id="tarjetaLink">
                    <div class="card h-100 noticia">

                            <img src="<?php echo $articulo['urlToImage'] ?>" class="card-img-top img-height" alt="...">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $articulo['title'] ?></h5>
                                <p class="card-text"><?php echo $articulo['description'] ?></p>
                            </div>
                            <div class="card-footer">
                                <small class="text-muted">
                                    Por: <?php echo $articulo['autor'] ?>
                                    <i class="far fa-calendar-alt"></i> <?php echo date("d-m-Y", strtotime($articulo['publishedAt']))?>
                                    <i class="far fa-clock"></i> <?php echo date("H:i", strtotime($articulo['publishedAt']))?></small>
                            </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
        </div>
        <nav>
            <ul class="pagination">
                <li class="page-item <?php echo $_GET['pagina'] <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?pagina=<?php echo $_GET['pagina'] - 1 ?>">Anterior</a>
                </li>
                <?php for($i=0; $i < $paginasTotal; $i++): ?>
                    <li class="page-item <?php echo $_GET['pagina'] == $i + 1 ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?pagina=<?php echo $i + 1?>"><?php echo $i + 1?></a>
                    </li>
                <?php endfor;?>
                <li class="page-item <?php echo $_GET['pagina'] >= $paginasTotal ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?pagina=<?php echo $_GET['pagina'] + 1 ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>

</body>
</html>

