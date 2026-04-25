<?php
session_start();
session_destroy(); // Apaga todas as variáveis de login
header("Location: index.php"); // Manda de volta para a tela inicial
exit();
?>