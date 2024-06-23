<?php
session_start(); // Iniciar a sessão
$ma = isset($_SESSION['ma']) ? $_SESSION['ma'] : '';
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>1Acesso - Aluno</title>

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
</head>
<body class="login-page">
    <div class="login-header box-shadow">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <a href="login.php">
                    <img src="vendors\images\alternative_logo_4.svg" alt="DeskApp Logo">
                </a>
            </div>
        </div>
    </div>
    <div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-7">
                    <img src="vendors/images/login-page-img.png" alt="Login Page Image">
                </div>
                <div class="col-md-6 col-lg-5">
                    <div class="login-box bg-white box-shadow border-radius-10">
                        <div class="login-title">
                            <h2 class="text-center text-primary">Preencha seus Dados</h2>
                        </div>
                        
                        <form action="processar_dados_aluno.php" method="post">
                            <!-- Campo oculto para MA (Matrícula Acadêmica) -->
                            <input type="hidden" name="ma" value="<?php echo htmlspecialchars($ma); ?>">

                            <!-- Campo para CPF -->
                            <div class="input-group custom">
                                <input type="text" id="cpf" name="cpf" class="form-control form-control-lg text-center" placeholder="CPF" required>
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="dw dw-user1"></i></span>
                                </div>
                            </div>

                            <!-- Campo para Telefone -->
                            <div class="input-group custom">
                                <input id="telefone" name="telefone" type="tel" class="form-control form-control-lg text-center" placeholder="(xx) xxxxx-xxxx" required>
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="dw dw-phone"></i></span>
                                </div>
                            </div>

                            <!-- Campo para Data de Nascimento -->
                            <div class="input-group custom">
                                <input type="date" name="data_nascimento" class="form-control form-control-lg text-center" placeholder="Data de Nascimento" required>
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="dw dw-calendar1"></i></span>
                                </div>
                            </div>

                            <!-- Campo para E-mail -->
                            <div class="input-group custom">
                                <input type="email" name="email" class="form-control form-control-lg text-center" placeholder="E-mail" required>
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="dw dw-email1"></i></span>
                                </div>
                            </div>

                            <!-- Campo para Senha -->
                            <div class="input-group custom">
                                <input type="password" name="senha" class="form-control form-control-lg text-center" placeholder="Senha" required>
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
                                </div>
                            </div>

                            <!-- Campo para Gênero -->
                            <div class="form-group">
                                <label>Gênero</label>
                                <div class="d-flex">
                                    <div class="custom-control custom-radio mb-5 mr-20">
                                        <input type="radio" id="customRadio4" name="genero" class="custom-control-input" value="1" required>
                                        <label class="custom-control-label weight-400" for="customRadio4">Masculino</label>
                                    </div>
                                    <div class="custom-control custom-radio mb-5">
                                        <input type="radio" id="customRadio5" name="genero" class="custom-control-input" value="2" required>
                                        <label class="custom-control-label weight-400" for="customRadio5">Feminino</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Botão de envio -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="input-group mb-0">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">Salvar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
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
