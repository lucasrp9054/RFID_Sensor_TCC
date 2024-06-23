<?php
session_start();

include "functions.php"; // Inclui o arquivo com as funções necessárias

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    // Redireciona para a página de login caso não esteja logado
    header("Location: login.php");
    exit();
}

$ma_usuario = $_SESSION['user']; // Obtém o MA do usuário logado na sessão

// Verifica se foi recebido um MA de perfil por POST
if (isset($_POST['ma_perfil'])) {
    $ma_perfil = $_POST['ma_perfil']; // Obtém o MA do perfil visitado por POST

    // Obtém os dados do perfil visitado
    $perfil_usuario = obter_dados($ma_perfil, $pdo);

    if (!$perfil_usuario) {
        // Se não encontrou um usuário válido, redireciona para alguma página de erro ou tratamento adequado
        header("Location: erro.php");
        exit();
    }

} else {
    // Se não foi recebido um MA de perfil por POST, redireciona para algum tratamento adequado
    header("Location: erro.php");
    exit();
}

// A partir daqui, o código continua como antes, utilizando $usuario para acessar os dados do perfil visitado
$nome = $perfil_usuario['nome']; // Obtém o nome do usuário

// Obtém o gênero do usuário com base no código de gênero
$genero = obter_genero($perfil_usuario['cod_genero'], $pdo);

// Obtém o cargo do usuário com base no código de categoria e de gênero
$categoria = obter_cargo($perfil_usuario['cod_categoria'], $perfil_usuario['cod_genero'], $pdo);

$data_registro = $perfil_usuario['data_registro'];
$data_formatada = date("d/m/Y", strtotime($data_registro)); // Formata a data de registro para o formato dd/mm/aaaa

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

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="vendors/styles/core.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/cropperjs/dist/cropper.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/style.css">

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-119386393-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-119386393-1');
	</script>
</head>
<body>

	<?php include "globlal_html_includes.php";?>

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
								<a href="modal" data-toggle="modal" data-target="#modal" class="edit-avatar"><i class="fa fa-pencil"></i></a>
								<img src="vendors/images/photo1.jpg" alt="" class="avatar-photo">
								<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-body pd-5">
												<div class="img-container">
													<img id="image" src="vendors/images/photo2.jpg" alt="Picture">
												</div>
											</div>
											<div class="modal-footer">
												<input type="submit" value="Update" class="btn btn-primary">
												<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h5 class="text-center h5 mb-0"><?php echo $nome; ?></h5>
							<p class="text-center text-muted font-14"><?php echo $categoria; ?></p>
							<div class="profile-info">
								<h5 class="mb-20 h5 text-blue">Informação de Contato</h5>
								<ul>
									<li>
										<span>E-mail:</span>
										<?php echo $perfil_usuario['email']; ?>
									</li>
									<li>
										<span>Telefone:</span>
										<?php echo $perfil_usuario['telefone']; ?>
                                        <span>  </span>
                                        <span>  </span>
									</li>
								</ul>
                                <h5 class="mb-20 h5 text-blue">Dados</h5>
								<ul>
									<li>
										<span>Data de Nascimento:</span>
										<?php echo $perfil_usuario['data_nascimento']; ?>
									</li>
									<li>
										<span>Gênero:</span>
										<?php echo $perfil_usuario['cod_genero']; ?>
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
                                if ($perfil_usuario['cod_categoria'] == 1) {
                                    echo "Matriculado em:";
                                } elseif ($perfil_usuario['cod_categoria'] == 2) {
                                    echo "Lecionando:";
                                } elseif ($perfil_usuario['cod_categoria'] == 3) {
                                    echo "Coordenando:";
                                } else {
                                    echo "Atuando em";
                                }
                                ?>
                            </h5>
								<h6 class="mb-5 font-14">HTML</h6>
								
								<h6 class="mb-5 font-14">Css</h6>
								
								<h6 class="mb-5 font-14">jQuery</h6>
								
								<h6 class="mb-5 font-14">Bootstrap</h6>
								
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
											<a class="nav-link" data-toggle="tab" href="#tasks" role="tab">Tasks</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#setting" role="tab">Configurações</a>
										</li>
									</ul>
									<div class="tab-content">
										<!-- Timeline Tab start -->
										<div class="tab-pane fade show active" id="timeline" role="tabpanel">
											<div class="pd-20">
												<div class="profile-timeline">
													<div class="timeline-month">
														<h5>August, 2020</h5>
													</div>
													<div class="profile-timeline-list">
														<ul>
															<li>
																<div class="date">12 Aug</div>
																<div class="task-name"><i class="ion-android-alarm-clock"></i> Task Added</div>
																<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
																<div class="task-time">09:30 am</div>
															</li>
															<li>
																<div class="date">10 Aug</div>
																<div class="task-name"><i class="ion-ios-chatboxes"></i> Task Added</div>
																<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
																<div class="task-time">09:30 am</div>
															</li>
															<li>
																<div class="date">10 Aug</div>
																<div class="task-name"><i class="ion-ios-clock"></i> Event Added</div>
																<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
																<div class="task-time">09:30 am</div>
															</li>
															<li>
																<div class="date">10 Aug</div>
																<div class="task-name"><i class="ion-ios-clock"></i> Event Added</div>
																<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
																<div class="task-time">09:30 am</div>
															</li>
														</ul>
													</div>
													<div class="timeline-month">
														<h5>July, 2020</h5>
													</div>
													<div class="profile-timeline-list">
														<ul>
															<li>
																<div class="date">12 July</div>
																<div class="task-name"><i class="ion-android-alarm-clock"></i> Task Added</div>
																<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
																<div class="task-time">09:30 am</div>
															</li>
															<li>
																<div class="date">10 July</div>
																<div class="task-name"><i class="ion-ios-chatboxes"></i> Task Added</div>
																<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
																<div class="task-time">09:30 am</div>
															</li>
														</ul>
													</div>
													<div class="timeline-month">
														<h5>June, 2020</h5>
													</div>
													<div class="profile-timeline-list">
														<ul>
															<li>
																<div class="date">12 June</div>
																<div class="task-name"><i class="ion-android-alarm-clock"></i> Task Added</div>
																<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
																<div class="task-time">09:30 am</div>
															</li>
															<li>
																<div class="date">10 June</div>
																<div class="task-name"><i class="ion-ios-chatboxes"></i> Task Added</div>
																<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
																<div class="task-time">09:30 am</div>
															</li>
															<li>
																<div class="date">10 June</div>
																<div class="task-name"><i class="ion-ios-clock"></i> Event Added</div>
																<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
																<div class="task-time">09:30 am</div>
															</li>
														</ul>
													</div>
												</div>
											</div>
										</div>
										<!-- Timeline Tab End -->
										<!-- Tasks Tab start -->
										<div class="tab-pane fade" id="tasks" role="tabpanel">
											<div class="pd-20 profile-task-wrap">
												<div class="container pd-0">
													<!-- Open Task start -->
													<div class="task-title row align-items-center">
														<div class="col-md-8 col-sm-12">
															<h5>Open Tasks (4 Left)</h5>
														</div>
														<div class="col-md-4 col-sm-12 text-right">
															<a href="task-add" data-toggle="modal" data-target="#task-add" class="bg-light-blue btn text-blue weight-500"><i class="ion-plus-round"></i> Add</a>
														</div>
													</div>
													<div class="profile-task-list pb-30">
														<ul>
															<li>
																<div class="custom-control custom-checkbox mb-5">
																	<input type="checkbox" class="custom-control-input" id="task-1">
																	<label class="custom-control-label" for="task-1"></label>
																</div>
																<div class="task-type">Email</div>
																Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id ea earum.
																<div class="task-assign">Assigned to Ferdinand M. <div class="due-date">due date <span>22 February 2019</span></div></div>
															</li>
															
														</ul>
													</div>
													<!-- Open Task End -->
													<!-- Close Task start -->
													<div class="task-title row align-items-center">
														<div class="col-md-12 col-sm-12">
															<h5>Closed Tasks</h5>
														</div>
													</div>
													<div class="profile-task-list close-tasks">
														<ul>
															<li>
																<div class="custom-control custom-checkbox mb-5">
																	<input type="checkbox" class="custom-control-input" id="task-close-1" checked="" disabled="">
																	<label class="custom-control-label" for="task-close-1"></label>
																</div>
																<div class="task-type">Email</div>
																Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id ea earum.
																<div class="task-assign">Assigned to Ferdinand M. <div class="due-date">due date <span>22 February 2018</span></div></div>
															</li>
											
														</ul>
													</div>
													<!-- Close Task start -->
													<!-- add task popup start -->
													<div class="modal fade customscroll" id="task-add" tabindex="-1" role="dialog">
														<div class="modal-dialog modal-dialog-centered" role="document">
															<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title" id="exampleModalLongTitle">Tasks Add</h5>
																	<button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Close Modal">
																		<span aria-hidden="true">&times;</span>
																	</button>
																</div>
																<div class="modal-body pd-0">
																	<div class="task-list-form">
																		<ul>
																			<li>
																				<form>
																					<div class="form-group row">
																						<label class="col-md-4">Task Type</label>
																						<div class="col-md-8">
																							<input type="text" class="form-control">
																						</div>
																					</div>
																					<div class="form-group row">
																						<label class="col-md-4">Task Message</label>
																						<div class="col-md-8">
																							<textarea class="form-control"></textarea>
																						</div>
																					</div>
																					<div class="form-group row">
																						<label class="col-md-4">Assigned to</label>
																						<div class="col-md-8">
																							<select class="selectpicker form-control" data-style="btn-outline-primary" title="Not Chosen" multiple="" data-selected-text-format="count" data-count-selected-text= "{0} people selected">
																								<option>Ferdinand M.</option>
																								<option>Don H. Rabon</option>
																								<option>Ann P. Harris</option>
																								<option>Katie D. Verdin</option>
																								<option>Christopher S. Fulghum</option>
																								<option>Matthew C. Porter</option>
																							</select>
																						</div>
																					</div>
																					<div class="form-group row mb-0">
																						<label class="col-md-4">Due Date</label>
																						<div class="col-md-8">
																							<input type="text" class="form-control date-picker">
																						</div>
																					</div>
																				</form>
																			</li>
																			<li>
																				<a href="javascript:;" class="remove-task"  data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Remove Task"><i class="ion-minus-circled"></i></a>
																				<form>
																					<div class="form-group row">
																						<label class="col-md-4">Task Type</label>
																						<div class="col-md-8">
																							<input type="text" class="form-control">
																						</div>
																					</div>
																					<div class="form-group row">
																						<label class="col-md-4">Task Message</label>
																						<div class="col-md-8">
																							<textarea class="form-control"></textarea>
																						</div>
																					</div>
																					<div class="form-group row">
																						<label class="col-md-4">Assigned to</label>
																						<div class="col-md-8">
																							<select class="selectpicker form-control" data-style="btn-outline-primary" title="Not Chosen" multiple="" data-selected-text-format="count" data-count-selected-text= "{0} people selected">
																								<option>Ferdinand M.</option>
																								<option>Don H. Rabon</option>
																								<option>Ann P. Harris</option>
																								<option>Katie D. Verdin</option>
																								<option>Christopher S. Fulghum</option>
																								<option>Matthew C. Porter</option>
																							</select>
																						</div>
																					</div>
																					<div class="form-group row mb-0">
																						<label class="col-md-4">Due Date</label>
																						<div class="col-md-8">
																							<input type="text" class="form-control date-picker">
																						</div>
																					</div>
																				</form>
																			</li>
																		</ul>
																	</div>
																	<div class="add-more-task">
																		<a href="#" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Add Task"><i class="ion-plus-circled"></i> Add More Task</a>
																	</div>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-primary">Add</button>
																	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
																</div>
															</div>
														</div>
													</div>
													<!-- add task popup End -->
												</div>
											</div>
										</div>
										<!-- Tasks Tab End -->
										<!-- Setting Tab start -->
										<div class="tab-pane fade height-100-p" id="setting" role="tabpanel">
											<div class="profile-setting">
												<form>
													<ul class="profile-edit-list row">
														<li class="weight-500 col-md-6">
															<h4 class="text-blue h5 mb-20">Edit Your Personal Setting</h4>
															<div class="form-group">
																<label>Full Name</label>
																<input class="form-control form-control-lg" type="text">
															</div>
															
															<div class="form-group">
																<label>Email</label>
																<input class="form-control form-control-lg" type="email">
															</div>
															<div class="form-group">
																<label>Date of birth</label>
																<input class="form-control form-control-lg date-picker" type="text">
															</div>
															<div class="form-group">
																<label>Gender</label>
																<div class="d-flex">
																<div class="custom-control custom-radio mb-5 mr-20">
																	<input type="radio" id="customRadio4" name="customRadio" class="custom-control-input">
																	<label class="custom-control-label weight-400" for="customRadio4">Masculino</label>
																</div>
																<div class="custom-control custom-radio mb-5">
																	<input type="radio" id="customRadio5" name="customRadio" class="custom-control-input">
																	<label class="custom-control-label weight-400" for="customRadio5">Feminino</label>
																</div>
																</div>
															</div>
															<div class="form-group mb-0">
																<input type="submit" class="btn btn-primary" value="Update Information">
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
		</div>
	</div>
	<!-- js -->
	<script src="vendors/scripts/core.js"></script>
	<script src="vendors/scripts/script.min.js"></script>
	<script src="vendors/scripts/process.js"></script>
	<script src="vendors/scripts/layout-settings.js"></script>
	<script src="src/plugins/cropperjs/dist/cropper.js"></script>
</body>
</html>