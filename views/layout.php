<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>Sistema de Ventas</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <a class="navbar-brand" href="/carrito_pmlx/">
            <img src="<?= asset('./images/cit.png') ?>" width="35px'" alt="cit" >
            Sistema de Ventas
        </a>
        
        <div class="collapse navbar-collapse" id="navbarToggler">
            
            <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="margin: 0;">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/carrito_pmlx/ventas">
                        <i class="bi bi-house-fill me-2"></i>Inicio
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/carrito_pmlx/ventas">
                        <i class="bi bi-cart-plus me-2"></i>Ventas
                    </a>
                </li>

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-gear me-2"></i>Gestión
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" style="margin: 0;">
                        <li>
                            <a class="dropdown-item nav-link text-white" href="/carrito_pmlx/productos">
                                <i class="bi bi-box-seam me-2"></i>Productos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item nav-link text-white" href="/carrito_pmlx/usuario">
                                <i class="bi bi-people me-2"></i>Clientes
                            </a>
                        </li>
                    </ul>
                </div>
            </ul> 
            
            <div class="dropdown me-3">
                <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-1"></i> Usuario
                </a>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-gear me-2"></i>Configuración
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="col-lg-1 d-grid mb-lg-0 mb-2">
                <a href="/menu/" class="btn btn-outline-light">
                    <i class="bi bi-arrow-bar-left me-1"></i>MENÚ
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="progress fixed-bottom" style="height: 6px;">
    <div class="progress-bar progress-bar-animated bg-primary" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
</div>

<div class="container-fluid pt-3 mb-4" style="min-height: 85vh">
    <?php echo $contenido; ?>
</div>

<div class="container-fluid">
    <div class="row justify-content-center text-center">
        <div class="col-12">
            <p style="font-size:xx-small; font-weight: bold;">
                Sistema de Ventas - Comando de Informática y Tecnología, <?= date('Y') ?> &copy;
            </p>
        </div>
    </div>
</div>

</body>
</html>