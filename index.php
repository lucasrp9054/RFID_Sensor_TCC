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


$horarios = obter_grade_horaria($ma_user, $cod_categoria, $pdo);


//Preencher os boxes da index
if($cod_categoria == 1)
{
	$box_1 = contar_dados_index($ma_user, $cod_categoria, '1', $pdo);
	$box_2 = contar_dados_index($ma_user, $cod_categoria, '2', $pdo);

	if($box_2 == '')
	{
		$box_2 = 'Vazio';
	}

	$box_3 = contar_dados_index($ma_user, $cod_categoria, '3', $pdo);
	$box_4 = $usuario['data_registro'];

	$data_obj = new DateTime($box_4);
    $box_4 = $data_obj->format('d/m/Y');
}
elseif($cod_categoria == 2)
{
	$box_1 = contar_dados_index($ma_user, $cod_categoria, '1', $pdo);
	$box_2 = contar_dados_index($ma_user, $cod_categoria, '2', $pdo);
	$box_3 = contar_dados_index($ma_user, $cod_categoria, '3', $pdo);
	$box_4 = contar_dados_index($ma_user, $cod_categoria, '4', $pdo);
}
else
{
	$box_1 = contar_dados_index($ma_user, $cod_categoria, '1', $pdo);
	$box_2 = contar_dados_index($ma_user, $cod_categoria, '2', $pdo);
	$box_3 = contar_dados_index($ma_user, $cod_categoria, '3', $pdo);
	$box_4 = contar_dados_index($ma_user, $cod_categoria, '4', $pdo);
}




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
		<div class="pd-ltr-20">
			<div class="card-box pd-20 height-100-p mb-30">
				<div class="row align-items-center">
					<div class="col-md-4">
						<img src="vendors/images/banner-img.png" alt="">
					</div>
					<div class="col-md-8">
						<h4 class="font-20 weight-500 mb-10 text-capitalize">

							Olá! <div class="weight-600 font-30 text-blue"><?php echo $nome; ?></div>
						</h4>

<!-- Alterar -->		<p class="font-18 max-width-600">Boa sorte!</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xl-3 mb-30">
					<div class="card-box height-100-p widget-style1">
						<div class="d-flex flex-wrap align-items-center">
							
							<div class="widget-data">
								
								<div class="h6 mb-0">
								<?php
									// Condição para verificar a categoria do usuário
									if ($usuario['cod_categoria'] == 1) {
										echo "N°. Disciplinas";
									} elseif ($usuario['cod_categoria'] == 2) {
										echo "N°. Disciplinas";
									} elseif ($usuario['cod_categoria'] == 3) {
										echo "Área Coordenada";
									} else {
										echo "Atuando em";
									}
								?>
								</div>
								<div class="h5 mb-0"> <?php echo $box_1; ?> </div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 mb-30">
					<div class="card-box height-100-p widget-style1">
						<div class="d-flex flex-wrap align-items-center">
							<div class="widget-data">
								<div class="h6 mb-0">
								<?php
									// Condição para verificar a categoria do usuário
									if ($usuario['cod_categoria'] == 1) {
										echo "Média Geral";
									} elseif ($usuario['cod_categoria'] == 2) {
										echo "Área de Ensino";
									} elseif ($usuario['cod_categoria'] == 3) {
										echo "N°. Prof. Coord.";
									} else {
										echo "Atuando em";
									}
								?>
								</div>
								<div class="h5 mb-0"><?php echo $box_2; ?></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 mb-30">
					<div class="card-box height-100-p widget-style1">
						<div class="d-flex flex-wrap align-items-center">
							<div class="widget-data">
								<div class="h6 mb-0">
								<?php
									// Condição para verificar a categoria do usuário
									if ($usuario['cod_categoria'] == 1) {
										echo "Nº Registros";
									} elseif ($usuario['cod_categoria'] == 2) {
										echo "Dias Atuando";
									} elseif ($usuario['cod_categoria'] == 3) {
										echo "Nº Coordenadores";
									} else {
										echo "Atuando em";
									}
								?>
								</div>
								<div class="h5 mb-0"><?php echo $box_3; ?></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 mb-30">
					<div class="card-box height-100-p widget-style1">
						<div class="d-flex flex-wrap align-items-center">
							<div class="widget-data">
								<div class="h6 mb-0">
									<?php
										// Condição para verificar a categoria do usuário
										if ($usuario['cod_categoria'] == 1) {
											echo "Data Matrícula";
										} elseif ($usuario['cod_categoria'] == 2) {
											echo "Nº Ocorrências";
										} elseif ($usuario['cod_categoria'] == 3) {
											echo "Dias Atuando";
										} else {
											echo "Atuando em";
										}
									?>
								</div>

								<div class="h5 mb-0"><?php echo $box_4; ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Grade Horária</h4>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
								<tr>
									<th>ID Grade Horária</th>
                                    <th>Disciplina</th>
                                    <th>Dia Útil</th>
                                    <th>Inicia</th>
                                    <th>Encerra</th>
									<th>Sala</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($horarios as $horario): ?>
                                    <tr class="linha-dados linha-professor" data-ma="<?php echo htmlspecialchars($ocorrencias_professor['ma_aluno']); ?>">
										<td><?php echo htmlspecialchars($horario['id_grade_horaria']); ?></td>
										<td><?php echo htmlspecialchars($horario['disciplina']); ?></td>                            
										<td><?php echo htmlspecialchars($horario['dia_semana']); ?></td>
                                        <td><?php echo htmlspecialchars($horario['hora_inicio']); ?></td>
                                        <td><?php echo htmlspecialchars($horario['hora_fim']); ?></td>
                                        <td><?php echo htmlspecialchars($horario['sala']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
			<div class="footer-wrap pd-20 mb-20 card-box">
				Engenharia da Computação - Lucas Ribeiro e Líbano Abboud
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












