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
$caminho_imagem = obter_caminho_imagem($ma_user, $cod_categoria, $pdo);

$graduacoes = listar_graduacoes($pdo);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>cadastro</title>

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
<body>

    <div class="pre-loader">
        <div class="pre-loader-box">
            <div class="loader-logo"><img src="vendors/images/alternative_logo_6.svg" alt=""></div>
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
                            <img src="<?php echo $caminho_imagem; ?>" alt="Imagem do Usuário" class="avatar-photo">
                        </span>
                        <span class="user-name"><?php echo htmlspecialchars($nome); ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                        <a class="dropdown-item" href="profile.php"><i class="dw dw-user1"></i> Perfil</a>
                        <a class="dropdown-item" href="logout.php"><i class="dw dw-logout"></i> Sair</a>
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
        default:
            // Código adicional para lidar com categorias desconhecidas
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
                                <h4>Form</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Form Basic</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <!-- Default Basic Forms Start -->
                <div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Cadastrar novo aluno</h4>
                            <p class="mb-30">Preencha todos os campos</p>
                        </div>
                    </div>
                    <form method="POST" action="processar_novo_aluno.php">
                        <div class="form-group row">
                            <label class="col-sm-12 col-md-2 col-form-label">Nome</label>
                            <div class="col-sm-12 col-md-10">
                                <input class="form-control" type="text" name="nome" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-12 col-md-2 col-form-label">Id do cartão</label>
                            <div class="col-sm-12 col-md-10">
                                <input class="form-control" placeholder="xxxxxxxx" type="text" name="uid_rfid" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-12 col-md-2 col-form-label">Curso</label>
                            <div class="col-sm-12 col-md-10">
                                <select name="cod_curso" class="custom-select col-12" required>
                                    <option selected>Escolha...</option>
                                    <?php foreach ($graduacoes as $graduacao): ?>
                                        <option value="<?php echo htmlspecialchars($graduacao['cod_graduacao']); ?>">
                                            <?php echo htmlspecialchars($graduacao['graduacao']); ?>
                                        </option>
										<?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <input type="submit" class="btn btn-primary" value="Salvar">
                        </div>
                    </form>
                </div>
				<div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Cadastrar novo professor</h4>
                            <p class="mb-30">Preencha todos os campos</p>
                        </div>
                    </div>
                    <form method="POST" action="processar_novo_professor.php">
                        <div class="form-group row">
                            <label class="col-sm-12 col-md-2 col-form-label">Nome</label>
                            <div class="col-sm-12 col-md-10">
                                <input class="form-control" type="text" name="nome" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-12 col-md-2 col-form-label">Id do cartão</label>
                            <div class="col-sm-12 col-md-10">
                                <input class="form-control" placeholder="xxxxxxxx" type="text" name="uid_rfid" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-12 col-md-2 col-form-label">Data de Nascimento</label>
                            <div class="col-sm-12 col-md-10">
                                <input class="form-control" type="date" name="data_nasc" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label  class="col-sm-12 col-md-2 col-form-label">CPF</label>
                            <div class="col-sm-12 col-md-10">
                                <input id="cpf" class="form-control" type="text" name="cpf" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-12 col-md-2 col-form-label">E-mail</label>
                            <div class="col-sm-12 col-md-10">
                                <input class="form-control" type="email" name="email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label  class="col-sm-12 col-md-2 col-form-label">Telefone</label>
                            <div class="col-sm-12 col-md-10">
                                <input id="telefone" class="form-control" type="tel" name="telefone" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-12 col-md-2 col-form-label">Gênero</label>
                            <div class="col-sm-12 col-md-10">
                                <select name="cod_genero" class="custom-select col-12" required>
                                    <option selected>Escolha...</option>
                                    <option value="1">Masculino</option>
                                    <option value="2">Feminino</option>
                                    <option value="3">Outro</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <input type="submit" class="btn btn-primary" value="Salvar">
                        </div>
                    </form>

                </div>
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

    <script>
    document.getElementById('telefone').addEventListener('input', function (e) {
      var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
      e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
    });
  </script>

<script>
        $(document).ready(function(){
            $('#cpf').on('input', function(){
                let cpf = $(this).val();
                cpf = cpf.replace(/\D/g, ''); // Remove caracteres não numéricos
                cpf = cpf.replace(/^(\d{3})(\d)/, '$1.$2'); // Adiciona o primeiro ponto
                cpf = cpf.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3'); // Adiciona o segundo ponto
                cpf = cpf.replace(/\.(\d{3})(\d)/, '.$1-$2'); // Adiciona o hífen
                $(this).val(cpf);
            });
        });
    </script>
</body>
</html>
