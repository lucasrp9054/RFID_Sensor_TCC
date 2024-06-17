<?php
// Inicia a sessão, se ainda não estiver iniciada
session_start();

// Destroi todas as variáveis de sessão
session_destroy();

// Redireciona o usuário para a página de login
header("Location: login.php");
exit; // Encerra o script após o redirecionamento

