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
    <title>Ocorrências</title>
    
    <link rel="apple-touch-icon" sizes="180x180" href="vendors/images/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="vendors/images/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="vendors/images/favicon-16x16.png">
    
    <!-- Meta tags necessárias para o funcionamento responsivo -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <!-- Estilos CSS -->
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
    <!-- Preloader -->
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

    <!-- Cabeçalho -->
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

    <!-- Menu Lateral -->
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
                
                <!-- Tabela de Alunos -->
                <div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Alunos</h4>
                            <p>Selecione para ir ao perfil do usuário.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="alunosTable" class="table hover">
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
                                    <tr class="linha-dados linha-aluno" data-ma="<?php echo htmlspecialchars($aluno['ma_aluno']); ?>">
                                        <td><?php echo htmlspecialchars($aluno['ma_aluno']); ?></td>
                                        <?php if ($cod_categoria == 3): ?>
                                            <td><?php echo htmlspecialchars($aluno['uid_rfid']); ?></td>
                                        <?php endif; ?>
                                        <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
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

                <!-- Tabela de Professores -->
                <div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Professores</h4>
                            <p>Selecione para ir ao perfil do usuário.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="professoresTable" class="table hover">
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

                <!-- Tabela de Coordenadores -->
                <div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Coordenadores</h4>
                            <p>Selecione para ir ao perfil do usuário.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="coordenadoresTable" class="table hover">
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
                                    <tr class="linha-dados linha-coordenador" data-ma="<?php echo htmlspecialchars($coordenador['ma']); ?>">
                                        <td><?php echo htmlspecialchars($coordenador['ma']); ?></td>
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

                <!-- Rodapé -->
                <div class="footer-wrap pd-20 mb-20 card-box">
                    Engenharia da Computação - Lucas Ribeiro e Líbano Abboud
                </div>
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
        $(document).ready(function() {
            // Opções padrão para DataTables
            var defaultTableOptions = {
                "order": [], // Ordenação inicial em nenhuma coluna
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json" // Tradução para PT-BR
                }
            };

            var alunosTableOptions = {
            ...defaultTableOptions,
            dom: 'Bfrtip',
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json" // Tradução para PT-BR
            },
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copiar',
                    title: function() {
                        return 'Lista de Alunos'; // Título personalizado para a tabela de Alunos
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV',
                    title: function() {
                        return 'Lista de Alunos'; // Título personalizado para a tabela de Alunos
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: function() {
                        return 'Lista de Alunos'; // Título personalizado para a tabela de Alunos
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    title: function() {
                        return 'Lista de Alunos'; // Título personalizado para a tabela de Alunos
                    }
                },
                {
                    extend: 'print',
                    text: 'Imprimir',
                    title: function() {
                        return 'Lista de Alunos'; // Título personalizado para a tabela de Alunos
                    }
                }
            ]
        };

        var professoresTableOptions = {
            ...defaultTableOptions,
            dom: 'Bfrtip',
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json" // Tradução para PT-BR
            },
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copiar',
                    title: function() {
                        return 'Lista de Professores'; // Título personalizado para a tabela de Professores
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV',
                    title: function() {
                        return 'Lista de Professores'; // Título personalizado para a tabela de Professores
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: function() {
                        return 'Lista de Professores'; // Título personalizado para a tabela de Professores
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    title: function() {
                        return 'Lista de Professores'; // Título personalizado para a tabela de Professores
                    }
                },
                {
                    extend: 'print',
                    text: 'Imprimir',
                    title: function() {
                        return 'Lista de Professores'; // Título personalizado para a tabela de Professores
                    }
                }
            ]
        };

        var coordenadoresTableOptions = {
            ...defaultTableOptions,
            dom: 'Bfrtip',
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json" // Tradução para PT-BR
            },
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copiar',
                    title: function() {
                        return 'Lista de Coordenadores'; // Título personalizado para a tabela de Coordenadores
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV',
                    title: function() {
                        return 'Lista de Coordenadores'; // Título personalizado para a tabela de Coordenadores
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: function() {
                        return 'Lista de Coordenadores'; // Título personalizado para a tabela de Coordenadores
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    title: function() {
                        return 'Lista de Coordenadores'; // Título personalizado para a tabela de Coordenadores
                    }
                },
                {
                    extend: 'print',
                    text: 'Imprimir',
                    title: function() {
                        return 'Lista de Coordenadores'; // Título personalizado para a tabela de Coordenadores
                    }
                }
            ]
        };

        // Inicializa os DataTables para cada tabela com as respectivas opções
        $(document).ready(function() {
            if (!$.fn.dataTable.isDataTable('#alunosTable')) {
                $('#alunosTable').DataTable(alunosTableOptions);
            }

            if (!$.fn.dataTable.isDataTable('#professoresTable')) {
                $('#professoresTable').DataTable(professoresTableOptions);
            }

            if (!$.fn.dataTable.isDataTable('#coordenadoresTable')) {
                $('#coordenadoresTable').DataTable(coordenadoresTableOptions);
            }

            // Captura o clique na linha das tabelas de alunos, professores e coordenadores
            $('#alunosTable, #professoresTable, #coordenadoresTable').on('click', 'tr.linha-dados', function() {
                var maPerfil = $(this).data('ma'); // Obtém o MA do perfil da linha clicada
                window.location.href = 'visitar_perfil.php?ma_perfil=' + encodeURIComponent(maPerfil); // Redireciona para o perfil
            });
        });


            // Captura o clique na linha das tabelas de alunos, professores e coordenadores
            $('#alunosTable, #professoresTable, #coordenadoresTable').on('click', 'tr.linha-dados', function() {
                var maPerfil = $(this).data('ma'); // Obtém o MA do perfil da linha clicada
                window.location.href = 'visitar_perfil.php?ma_perfil=' + encodeURIComponent(maPerfil); // Redireciona para o perfil
            });
        });
    </script>


</body>

</html>
