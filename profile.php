<?php
session_start();

include "functions.php"; // Inclui o arquivo com as funções necessárias

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    // Redireciona para a página de login caso não esteja logado
    header("Location: login.php");
    exit();
}

$ma_user = $_SESSION['user']; // Obtém o MA do usuário logado

// Obtém os dados do usuário a partir do MA
$usuario = obter_dados($ma_user, $pdo);

// Se não encontrou um usuário válido, redireciona para a página de login
if (!$usuario) {
    header("Location: login.php");
    exit();
}

$nome = $usuario['nome']; // Obtém o nome do usuário
$cod_categoria = $usuario['cod_categoria']; // Obtém o código da categoria do usuário

// Obtém o gênero do usuário com base no código de gênero
$genero = obter_genero($usuario['cod_genero'], $pdo);


// Obtém o cargo do usuário com base no código de categoria e de gênero
$categoria = obter_cargo($usuario['cod_categoria'], $usuario['cod_genero'], $pdo);

// Formata a data de registro para o formato dd/mm/aaaa
$data_registro = $usuario['data_registro'];
$data_formatada = date("d/m/Y", strtotime($data_registro));


$caminho_imagem = obter_caminho_imagem($ma_user, $cod_categoria,$pdo);

$disciplinas = obter_disciplinas($ma_user, $cod_categoria, $pdo);

$ocorrencias_usuario = listar_ocorrencias($ma_user,'1', $cod_categoria, $pdo);


?>



<!DOCTYPE html>
<html>
<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>Profile</title>

	<!-- Site favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="vendors/images/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="vendors/images/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="vendors/images/favicon-16x16.png">

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="vendors/styles/core.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/cropperjs/dist/cropper.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/style.css">

</head>
<body>
	
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
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title">
								<h4>Profile</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.html">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Profile</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
						<div class="pd-20 card-box height-100-p">
							<div class="profile-photo">

								<?php echo '<img src="' . $caminho_imagem . '" alt="Imagem do Usuário" class="avatar-photo">';?>

							</div>
							<h5 class="text-center h5 mb-0"><?php echo $nome; ?></h5>
							<p class="text-center text-muted font-14"><?php echo $categoria; ?></p>
							<div class="profile-info">
								<h5 class="mb-20 h5 text-blue">Informação de Contato</h5>
								<ul>
									<li>
										<span>E-mail:</span>
										<?php echo $usuario['email']; ?>
									</li>
									<li>
										<span>Telefone:</span>
										<?php echo $usuario['telefone']; ?>
                                        <span>  </span>
                                        <span>  </span>
									</li>
								</ul>
                                <h5 class="mb-20 h5 text-blue">Dados</h5>
								<ul>
									<li>
										<span>CPF:</span>
										<?php echo $usuario['cpf']; ?>
									</li>
									<li>
										<span>Data de Nascimento:</span>
										<?php echo $usuario['data_nascimento']; ?>
									</li>
									<li>
										<span>Gênero:</span>
										<?php echo $genero; ?>
									</li>
									<li>
										<span>Membro desde:</span>
										<?php echo $data_formatada; ?>
									</li>
								</ul>
							</div>
							<div class="profile-social">
								<h5 class="mb-20 h5 text-blue">Links Sociais</h5>
								<ul class="clearfix">
									<li><a href="#" class="btn" data-bgcolor="#007bb5" data-color="#ffffff"><i class="fa fa-linkedin"></i></a></li>
									<li><a href="#" class="btn" data-bgcolor="#bd081c" data-color="#ffffff"><i class="fa fa-envelope"></i></a></li>
								</ul>
							</div>
							<div class="profile-skills">
								<h5 class="mb-20 h5 text-blue">
									<?php
									// Condição para verificar a categoria do usuário
									if ($usuario['cod_categoria'] == 1) {
										echo "Matriculado em:";
									} elseif ($usuario['cod_categoria'] == 2) {
										echo "Lecionando:";
									} elseif ($usuario['cod_categoria'] == 3) {
										echo "Coordenando:";
									} else {
										echo "Atuando em";
									}
									?>
								</h5>

								<?php foreach ($disciplinas as $disciplina): ?>
									<h6 class="mb-5 font-14"><?php echo htmlspecialchars($disciplina); ?></h6>
								<?php endforeach; ?>
								
							</div>
						</div>
					</div>
					<div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
						<div class="card-box height-100-p overflow-hidden">
							<div class="profile-tab height-100-p">
								<div class="tab height-100-p">
									<ul class="nav nav-tabs customtab" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" data-toggle="tab" href="#timeline" role="tab">Ocorrências</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#setting" role="tab">Configurações</a>
										</li>
									</ul>
									<div class="tab-content">
										<!-- Timeline Tab start -->
										<div class="tab-pane fade show active" id="timeline" role="tabpanel">
											<div class="pd-20">
											<div class="pd-20 card-box mb-30">
												<div class="clearfix">
												</div>
												<div class="table-responsive">
													<table id="ocorrenciaUsuarioTable" class="table hover">
													<thead>
															<tr>
																<th>ID Grade</th>
																<th>Disciplina</th>
																<th>Data</th>
																<th>Horário</th>
																<th>Ocorrência</th>
															</tr>
														</thead>
														<tbody>
															<?php foreach ($ocorrencias_usuario as $ocorrencia): ?>
																<tr class="linha-dados linha-professor">
																	
																	<td><?php echo htmlspecialchars($ocorrencia['id_grade_horaria']); ?></td>
																	<td><?php echo htmlspecialchars($ocorrencia['disciplina']); ?></td>
																	<td><?php echo htmlspecialchars($ocorrencia['data_ocorrencia']); ?></td>
																	<td><?php echo htmlspecialchars($ocorrencia['hora_ocorrencia']); ?></td>
																	<td><?php echo htmlspecialchars($ocorrencia['ocorrencia']); ?></td>
														
																</tr>
															<?php endforeach; ?>
														</tbody>
													</table>
												</div>
											</div>
											</div>
										</div>
										<!-- Timeline Tab End -->
										<!-- Setting Tab start -->
										<div class="tab-pane fade height-100-p" id="setting" role="tabpanel">
											<div class="profile-setting">
												<form action="process_personal_info.php" method="post">
												<input type="hidden" name="ma" value="<?php echo htmlspecialchars($ma_user); ?>">
												<input type="hidden" name="tipo_usuario" value="<?php echo htmlspecialchars($cod_categoria); ?>"> 

													<ul class="profile-edit-list row">
														<li class="weight-500 col-md-6">
															<h4 class="text-blue h5 mb-20">Atualizar dados pessoais</h4>
															<label>Preencha os campos que quer alterar</label>
															<div class="form-group">
																<label>Telefone</label>
																<input id="telefone" name="telefone" placeholder="(xx) xxxxx-xxxx" class="form-control form-control-lg" pattern="\(\d{2}\) \d{4,5}-\d{4}" type="tel" required>															</div>
															<div class="form-group">
																<label>Email</label>
																<input name="email" class="form-control form-control-lg" type="email" required>
															</div>
															<div class="form-group mb-0">
																<input type="submit" class="btn btn-primary" value="Salvar">
															</div>
															
														</li>
													</ul>
												</form>
												<form action="processar_profile_imagem.php" method="post" enctype="multipart/form-data">

													<input type="hidden" name="ma" value="<?php echo htmlspecialchars($ma_user); ?>">
													<input type="hidden" name="tipo_usuario" value="<?php echo htmlspecialchars($cod_categoria); ?>"> 
													
													<ul class="profile-edit-list row">
														<li class="weight-500 col-md-6">
															<h4 class="text-blue h5 mb-20">Alterar Foto de Perfil</h4>
															<div class="form-group">
																
																<input required type="file" name="arquivo">
															</div>
															<div class="form-group mb-0">
																<input type="submit" class="btn btn-primary" value="Salvar">
															</div>
														</li>
													</ul>
												</form>
											</div>
										</div>
										<!-- Setting Tab End -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="footer-wrap pd-20 mb-20 card-box">
                    Engenharia da Computação - Lucas Ribeiro e Líbano Abboud
                </div>
		</div>
	</div>
	 <!-- JavaScripts -->
	 <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
    <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
    <script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
    <!-- DataTables Export Buttons -->
    <script src="src/plugins/datatables/js/dataTables.buttons.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.print.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.html5.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.flash.min.js"></script>
    <script src="src/plugins/datatables/js/pdfmake.min.js"></script>
    <script src="src/plugins/datatables/js/vfs_fonts.js"></script>
    <!-- Configurações do DataTables -->

	<script src="vendors/scripts/datatable-setting.js"></script>
	<script>
	document.getElementById('telefone').addEventListener('input', function (e) {
		var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
		e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
	});
	</script>




  <script>
    // Função executada quando o documento HTML estiver completamente carregado
    $(document).ready(function() {
			// Define opções padrão para DataTables
			var defaultTableOptions = {
				"order": [], // Nenhuma coluna é ordenada inicialmente
				"language": {
					"url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json" // Tradução para PT-BR
				}
			};

			// Inicializa o DataTables para a tabela com ID 'alunosTable' se ainda não estiver inicializada
			if (!$.fn.dataTable.isDataTable('#ocorrenciaUsuarioTable')) {
				$('#ocorrenciaUsuarioTable').DataTable(defaultTableOptions);
			}

			// Inicializa o DataTables para a tabela com ID 'professoresTable' se ainda não estiver inicializada
			if (!$.fn.dataTable.isDataTable('#professoresTable')) {
				$('#professoresTable').DataTable(defaultTableOptions);
			}
		});
	</script>


</body>
</html>