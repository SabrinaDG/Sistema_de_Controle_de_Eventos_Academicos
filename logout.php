<?php
session_start();
session_unset();  // Remove todas as variáveis de sessão
session_destroy();
header("Location: index.html");
exit();
?>