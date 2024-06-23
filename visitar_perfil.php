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

// Obtém dados do perfil do usuário atual
$perfil_atual = obter_dados($ma_usuario, $pdo);
$usuario_nome = $perfil_atual['nome'];
$usuario_categoria = $perfil_atual['cod_categoria'];
$caminho_imagem = obter_caminho_imagem($ma_usuario, $usuario_categoria, $pdo);

// Verifica se foi recebido um MA de perfil por GET (via URL)
if (isset($_GET['ma_perfil'])) {
    $ma_perfil = $_GET['ma_perfil']; // Obtém o MA do perfil visitado por GET

    // Obtém os dados do perfil visitado
    $perfil_usuario = obter_dados($ma_perfil, $pdo);

    if (!$perfil_usuario) {
        // Se não encontrou um usuário válido, redireciona para alguma página de erro ou tratamento adequado
        header("Location: erro.php");
        exit();
    }

    // A partir daqui, utilizamos $perfil_usuario para exibir os dados do perfil visitado
    $nome = $perfil_usuario['nome'];
    $perfil_usuario_genero = obter_genero($perfil_usuario['cod_genero'], $pdo);
    $categoria = obter_cargo($perfil_usuario['cod_categoria'], $perfil_usuario['cod_genero'], $pdo);
    $perfil_usuario_data_registro = $perfil_usuario['data_registro'];
    $data_formatada = date("d/m/Y", strtotime($perfil_usuario_data_registro)); // Formata a data de registro para o formato dd/mm/aaaa
    $perfil_imagem = obter_caminho_imagem($ma_perfil, $perfil_usuario['cod_categoria'], $pdo);
    $disciplinas = obter_disciplinas($ma_perfil, $perfil_usuario['cod_categoria'], $pdo);

} else {
    // Se não foi recebido um MA de perfil por GET, redireciona para algum tratamento adequado
    header("Location: erro.php");
    exit();
}
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
						<span class="user-name"><?php echo $usuario_nome; ?></span>
					</a>
					<div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
						<a class="dropdown-item" href="profile.php"><i class="dw dw-user1"></i> Perfil </a>
						<a class="dropdown-item" href="logout.php"><i class="dw dw-logout"></i> Sair </a>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php include "globlal_html_includes.php"; ?>

<?php
switch ($usuario_categoria) {
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
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Perfil</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Perfil</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            <div class="pd-20 card-box mb-30">
                <div class="profile-photo">
                    <?php echo '<img src="' . $perfil_imagem . '" alt="Imagem do Usuário" class="avatar-photo">'; ?>
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
                        </li>
                    </ul>
                </div>
                <div class="profile-info">
                    <h5 class="mb-20 h5 text-blue">Dados</h5>
                    <ul>
                        <li>
                            <span>CPF:</span>
                            <?php echo $perfil_usuario['cpf']; ?>
                        </li>
                        <li>
                            <span>Data de Nascimento:</span>
                            <?php echo $perfil_usuario['data_nascimento']; ?>
                        </li>
                        <li>
                            <span>Gênero:</span>
                            <?php echo $perfil_usuario_genero; ?>
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

                    <?php foreach ($disciplinas as $disciplina): ?>
                        <h6 class="mb-5 font-14"><?php echo htmlspecialchars($disciplina); ?></h6>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-wrap pd-20 mb-20 card-box">
        Engenharia da Computação - Lucas Ribeiro e Líbano Abboud
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
