<?php
session_start(); // Oturumu başlat

// Tüm oturum verilerini temizle
session_unset();
session_destroy();

// Kullanıcıyı giriş sayfasına yönlendir
header("Location: kayit.php");
exit();
?>
