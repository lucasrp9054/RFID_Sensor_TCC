<?php
session_start();

include "functions.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    // Redireciona para a página de login caso não esteja logado
    header("Location: login.php");
    exit();
}

$ma_user = $_SESSION['user'];
$usuario = obter_dados($ma_user, $pdo);

if (!$usuario) {
    header("Location: login.php");
    exit();
}

$nome = $usuario['nome'];
$cod_categoria = $usuario['cod_categoria'];
$caminho_imagem = obter_caminho_imagem($ma_user, $cod_categoria,$pdo);



?>


<!DOCTYPE html>
<html>
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>Dashboard</title>

    <!-- Site favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="vendors/images/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="vendors/images/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="vendors/images/favicon-16x16.png">

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="vendors/styles/core.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/style.css">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

	<!-- Bootstrap CSS -->
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

	<!-- DataTables CSS -->
	<link rel="stylesheet" type="text/css" href="src/plugins/datatables/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/datatables/css/responsive.bootstrap4.min.css">

	<!-- FullCalendar CSS -->
	<link rel="stylesheet" type="text/css" href="src/plugins/fullcalendar/fullcalendar.css">

</head>

	<div class="pre-loader">
		<div class="pre-loader-box">
			<div class="loader-logo"><img src="vendors\images\alternative_logo_6.svg" alt=""></div> 
			<div class='loader-progress' id="progress_div">
				<div class='bar' id='bar1'></div>
			</div>
			<div class='percent' id='percent1'>0%</div>
			<div class="loading-text">
				Carregando...
			</div>
		</div>
	</div>

<div class="header">
		<div class="header-left">
			<div class="menu-icon dw dw-menu"></div>
			<div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div>
			<div class="header-search">
				<form>
					<div class="form-group mb-0">
						<i class="dw dw-search2 search-icon"></i>
						<input type="text" class="form-control search-input" placeholder="Search Here">
						<div class="dropdown">
							<a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
								<i class="ion-arrow-down-c"></i>
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<div class="form-group row">
									<label class="col-sm-12 col-md-2 col-form-label">From</label>
									<div class="col-sm-12 col-md-10">
										<input class="form-control form-control-sm form-control-line" type="text">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-12 col-md-2 col-form-label">To</label>
									<div class="col-sm-12 col-md-10">
										<input class="form-control form-control-sm form-control-line" type="text">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-12 col-md-2 col-form-label">Subject</label>
									<div class="col-sm-12 col-md-10">
										<input class="form-control form-control-sm form-control-line" type="text">
									</div>
								</div>
								<div class="text-right">
									<button class="btn btn-primary">Search</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="header-right">
			<div class="dashboard-setting user-notification">
				<div class="dropdown">
					<a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
						<i class="dw dw-settings2"></i>
					</a>
				</div>
			</div>
			<div class="user-info-dropdown">
				<div class="dropdown">
					<a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
						<span class="user-icon">
						<?php echo '<img src="' . $caminho_imagem . '" alt="Imagem do Usuário" class="avatar-photo">';?>
						</span>
						<span class="user-name"><?php echo $nome; ?></span>
					</a>
					<div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
						<a class="dropdown-item" href="profile.php"><i class="dw dw-user1"></i> Perfil </a>
						<a class="dropdown-item" href="logout.php"><i class="dw dw-logout"></i> Sair </a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include "config_layout_menu.php"; ?>

	<?php

	switch ($cod_categoria) {
	case 1:
		include "menu_lateral_aluno.php";
		break;
	case 2:
		include "menu_lateral_professor.php";
		break;
	case 3:
		include "menu_lateral_coordenacao.php";
		break;
	}

	?>




	<div class="mobile-menu-overlay"></div>
	<div class="main-container">
		<div class="xs-pd-20-10 pd-ltr-20">
			<div class="page-header">
				<div class="row">
					<div class="col-md-6 col-sm-12">
						<div class="title">
							<h4>Dashboard</h4>
						</div>
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.html">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
							</ol>
						</nav>
					</div>
					<div class="col-md-6 col-sm-12 text-right">
						<div class="dropdown">
							<a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
								January 2018
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="#">Export List</a>
								<a class="dropdown-item" href="#">Policies</a>
								<a class="dropdown-item" href="#">View Assets</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row clearfix progress-box">
				<div class="col-lg-3 col-md-6 col-sm-12 mb-30">
					<div class="card-box pd-30 height-100-p">
						<div class="progress-box text-center">
							 <input type="text" class="knob dial1" value="80" data-width="120" data-height="120" data-linecap="round" data-thickness="0.12" data-bgColor="#fff" data-fgColor="#1b00ff" data-angleOffset="180" readonly>
							<h5 class="text-blue padding-top-10 h5">My Earnings</h5>
							<span class="d-block">80% Average <i class="fa fa-line-chart text-blue"></i></span>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-12 mb-30">
					<div class="card-box pd-30 height-100-p">
						<div class="progress-box text-center">
							 <input type="text" class="knob dial2" value="70" data-width="120" data-height="120" data-linecap="round" data-thickness="0.12" data-bgColor="#fff" data-fgColor="#00e091" data-angleOffset="180" readonly>
							<h5 class="text-light-green padding-top-10 h5">Business Captured</h5>
							<span class="d-block">75% Average <i class="fa text-light-green fa-line-chart"></i></span>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-12 mb-30">
					<div class="card-box pd-30 height-100-p">
						<div class="progress-box text-center">
							 <input type="text" class="knob dial3" value="90" data-width="120" data-height="120" data-linecap="round" data-thickness="0.12" data-bgColor="#fff" data-fgColor="#f56767" data-angleOffset="180" readonly>
							<h5 class="text-light-orange padding-top-10 h5">Projects Speed</h5>
							<span class="d-block">90% Average <i class="fa text-light-orange fa-line-chart"></i></span>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-12 mb-30">
					<div class="card-box pd-30 height-100-p">
						<div class="progress-box text-center">
							 <input type="text" class="knob dial4" value="65" data-width="120" data-height="120" data-linecap="round" data-thickness="0.12" data-bgColor="#fff" data-fgColor="#a683eb" data-angleOffset="180" readonly>
							<h5 class="text-light-purple padding-top-10 h5">Panding Orders</h5>
							<span class="d-block">65% Average <i class="fa text-light-purple fa-line-chart"></i></span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-4 col-md-6 col-sm-12 mb-30">
					<div class="card-box pd-30 pt-10 height-100-p">
						<h2 class="mb-30 h4">Browser Visit</h2>
						<div class="browser-visits">
							<ul>
								<li class="d-flex flex-wrap align-items-center">
									<div class="icon"><img src="vendors/images/chrome.png" alt=""></div>
									<div class="browser-name">Google Chrome</div>
									<div class="visit"><span class="badge badge-pill badge-primary">50%</span></div>
								</li>
								<li class="d-flex flex-wrap align-items-center">
									<div class="icon"><img src="vendors/images/firefox.png" alt=""></div>
									<div class="browser-name">Mozilla Firefox</div>
									<div class="visit"><span class="badge badge-pill badge-secondary">40%</span></div>
								</li>
								<li class="d-flex flex-wrap align-items-center">
									<div class="icon"><img src="vendors/images/safari.png" alt=""></div>
									<div class="browser-name">Safari</div>
									<div class="visit"><span class="badge badge-pill badge-success">40%</span></div>
								</li>
								<li class="d-flex flex-wrap align-items-center">
									<div class="icon"><img src="vendors/images/edge.png" alt=""></div>
									<div class="browser-name">Microsoft Edge</div>
									<div class="visit"><span class="badge badge-pill badge-warning">20%</span></div>
								</li>
								<li class="d-flex flex-wrap align-items-center">
									<div class="icon"><img src="vendors/images/opera.png" alt=""></div>
									<div class="browser-name">Opera Mini</div>
									<div class="visit"><span class="badge badge-pill badge-info">20%</span></div>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-lg-8 col-md-6 col-sm-12 mb-30">
					<div class="card-box pd-30 pt-10 height-100-p">
						<h2 class="mb-30 h4">World Map</h2>
						<div id="browservisit" style="width:100%!important; height:380px"></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-7 col-md-12 col-sm-12 mb-30">
					<div class="card-box pd-30 height-100-p">
						<h4 class="mb-30 h4">Compliance Trend</h4>
						<div id="compliance-trend" class="compliance-trend"></div>
					</div>
				</div>
				<div class="col-lg-5 col-md-12 col-sm-12 mb-30">
					<div class="card-box pd-30 height-100-p">
						<h4 class="mb-30 h4">Records</h4>
						<div id="chart" class="chart"></div>
					</div>
				</div>
			</div>
			<div class="footer-wrap pd-20 mb-20 card-box">
				DeskApp - Bootstrap 4 Admin Template By <a href="https://github.com/dropways" target="_blank">Ankit Hingarajiya</a>
			</div>
		</div>
	</div>

	
	<!-- js -->
	<script src="vendors/scripts/core.js"></script>
	<script src="vendors/scripts/script.min.js"></script>
	<script src="vendors/scripts/process.js"></script>
	<script src="vendors/scripts/layout-settings.js"></script>
	<script src="src/plugins/apexcharts/apexcharts.min.js"></script>
	<script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
	<script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
	<script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
	<script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
	<script src="vendors/scripts/dashboard.js"></script>
</body>
</html>