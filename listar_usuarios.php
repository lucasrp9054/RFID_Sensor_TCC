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

$alunos = listar_alunos($pdo);
$professores = listar_professores($pdo);
$coordenadores = listar_coordenadores($pdo);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>Lista</title>

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
    <style>
        /* Efeito de cursor ao passar sobre linhas da tabela */
        table.dataTable tbody tr:hover {
            cursor: pointer;
            background-color: #f5f5f5; /* Cor de fundo ao passar */
        }
    </style>

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
                                <h4>Listas</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Listas</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <!-- Default Basic Forms Start -->
                <div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Alunos</h4>
                            <p>Selecione para ir ao perfil do usuario.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table id="alunosTable" class="table table-striped">
                        <thead>
                            <tr>

                                <th>MA</th>    
                            <?php if ($cod_categoria == 3): ?>
                                <th>UID</th>
                            <?php endif; ?>
                                <th>Nome</th>
                                <th>Curso</th>
                            <?php if ($cod_categoria == 3): ?>
                                <th>Data Matrícula</th>
                            <?php endif; ?>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alunos as $aluno): ?>
                                <tr class="linha-dados linha-aluno" data-ma="<?php echo htmlspecialchars($aluno['ma_aluno']); ?>">                                <td><?php echo htmlspecialchars($aluno['ma_aluno']); ?></td>
                                <?php if ($cod_categoria == 3): ?>
                                    <td><?php echo htmlspecialchars($aluno['uid_rfid']); ?></td>
                                <?php endif; ?>                                <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['curso']); ?></td>
                                <?php if ($cod_categoria == 3): ?>
                                    <td><?php echo htmlspecialchars((new DateTime($aluno['data_registro']))->format('d/m/Y')); ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                <!-- Default Basic Forms Start -->
                <div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Professores</h4>
                            <p>Selecione para ir ao perfil do usuario.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table id="professoresTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>MA</th>    
                                <?php if ($cod_categoria == 3): ?>
                                    <th>UID</th>
                                <?php endif; ?>
                                    <th>Nome</th>
                                <?php if ($cod_categoria == 3): ?>
                                    <th>Data Registro</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($professores as $professor): ?>
                                <tr class="linha-dados linha-professor" data-ma="<?php echo htmlspecialchars($professor['ma']); ?>">
                                <td><?php echo htmlspecialchars($professor['ma']); ?></td>
                                <?php if ($cod_categoria == 3): ?>
                                    <td><?php echo htmlspecialchars($professor['uid_rfid']); ?></td>
                                <?php endif; ?>
                                <td><?php echo htmlspecialchars($professor['nome']); ?></td>
                                <?php if ($cod_categoria == 3): ?>
                                    <td><?php echo htmlspecialchars((new DateTime($professor['data_registro']))->format('d/m/Y')); ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                <!-- Default Basic Forms Start -->
                <div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Coordenadores</h4>
                            <p>Selecione para ir ao perfil do usuario.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table id="coordenadoresTable" class="table table-striped">
                        <thead>
                            <tr>
                            <th>MA</th>    
                                <?php if ($cod_categoria == 3): ?>
                                    <th>UID</th>
                                <?php endif; ?>
                                    <th>Nome</th>
                                <?php if ($cod_categoria == 3): ?>
                                    <th>Data Registro</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coordenadores as $coordenador): ?>
                                <tr class="linha-dados linha-coordenador" data-ma="<?php echo htmlspecialchars($coordenador['ma']); ?>">                                <td><?php echo htmlspecialchars($coordenador['ma']); ?></td>
                                <?php if ($cod_categoria == 3): ?>
                                    <td><?php echo htmlspecialchars($coordenador['uid_rfid']); ?></td>
                                <?php endif; ?>
                                <td><?php echo htmlspecialchars($coordenador['nome']); ?></td>
                                <?php if ($cod_categoria == 3): ?>
                                    <td><?php echo htmlspecialchars((new DateTime($coordenador['data_registro']))->format('d/m/Y')); ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

    <script>
    $(document).ready(function() {
        // Inicializa o DataTables para alunos
        $('#alunosTable').DataTable({
            "order": [], // Ordenação inicial em nenhuma coluna
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json" // Tradução para PT-BR
            }
        });

        // Inicializa o DataTables para professores
        $('#professoresTable').DataTable({
            "order": [], // Ordenação inicial em nenhuma coluna
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json"  // Tradução para PT-BR
            }
        });

        // Inicializa o DataTables para coordenadores
        $('#coordenadoresTable').DataTable({
            "order": [], // Nenhuma ordenação inicial
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json"
            }
        });

        // Captura o clique na linha das tabelas de alunos, professores e coordenadores
        $('#alunosTable, #professoresTable, #coordenadoresTable').on('click', 'tr.linha-dados', function() {
            var maPerfil = $(this).data('ma'); // Obtém o MA do perfil da linha clicada
            window.location.href = 'visitar_perfil.php?ma_perfil=' + encodeURIComponent(maPerfil); // Redireciona para o perfil
        });
    });
</script>

<style>
    /* Efeito de cursor ao passar sobre linhas da tabela */
    table.dataTable tbody tr:hover {
        cursor: pointer;
        background-color: #f5f5f5; /* Cor de fundo ao passar */
    }
</style>



</body>
</html>


