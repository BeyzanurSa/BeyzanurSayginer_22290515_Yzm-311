<?php
include("baglanti.php");

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $sql = "DELETE FROM kitaplar WHERE id = ?";
    $stmt = $baglanti->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Hata: " . $baglanti->error;
    }
}
?>
