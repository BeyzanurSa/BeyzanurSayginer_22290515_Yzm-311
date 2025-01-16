<?php
include("baglanti.php");
session_start();

if (!isset($_SESSION["kullanici"])) {
    header("Location: kayit.php");
    exit();
}

if (isset($_GET["kitap_id"])) {
    $kitap_id = intval($_GET["kitap_id"]);
    $teslim_tarihi = date("Y-m-d");

    // Önce en son ödünç alma kaydını bul
    $sql = "UPDATE odunc_alma 
            SET teslim_edildi = TRUE, teslim_tarihi = ?
            WHERE kitap_id = ? 
            AND kullanici_id = (
            SELECT id FROM kullanicilar 
            WHERE kullanici_adi = ?
        )
        AND teslim_edildi = FALSE";
        
$stmt = $baglanti->prepare($sql);
$stmt->bind_param("sis",$teslim_tarihi, $kitap_id, $_SESSION["kullanici"]);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION["mesaj"] = "Kitap başarıyla teslim edildi.";
            header("Location: admin.php");
            exit();
        } else {
            $_SESSION["mesaj"] = "Bu kitabı siz almadınız.";
            header("Location: admin.php");
            exit();
        }
    } else {
        echo "Hata: " . $baglanti->error;
    }
}
?>