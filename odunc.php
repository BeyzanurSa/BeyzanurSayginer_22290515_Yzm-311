<?php
include("baglanti.php");
session_start();

if (!isset($_SESSION["kullanici"])) {
    header("Location: kayit.php");
    exit();
}

$kitap_id = intval($_GET["kitap_id"]);
$kullanici_adi = $_SESSION["kullanici"];

// Kullanıcı ID'sini al
$sql = "SELECT id FROM kullanicilar WHERE kullanici_adi = ?";
$stmt = $baglanti->prepare($sql);
$stmt->bind_param("s", $kullanici_adi);
$stmt->execute();
$result = $stmt->get_result();

// Kullanıcı bulunamadıysa hata mesajı
if ($result->num_rows === 0) {
    $_SESSION["mesaj"] = "Kullanıcı bulunamadı!";
    header("Location: admin.php");
    exit();
}

$kullanici = $result->fetch_assoc();
$kullanici_id = $kullanici["id"];
// Kitap zaten ödünç alınmış mı kontrol et
$sql = "SELECT * FROM odunc_alma 
        WHERE kitap_id = ?
        AND teslim_edildi = FALSE";
$stmt = $baglanti->prepare($sql);
$stmt->bind_param("i", $kitap_id);
$stmt->execute(); // Bu satır eksikti
$result = $stmt->get_result(); // Bu satır eksikti

// Eğer kitap ödünç alınmışsa, diğer kullanıcıya ödünç verilmesin
if ($result->num_rows > 0) {
    $odunc = $result->fetch_assoc();
    $formatli_tarih = date("d.m.Y", strtotime($odunc["teslim_tarihi"]));
    $_SESSION["mesaj"] = "Bu kitap " . $formatli_tarih . " tarihine kadar ödünç alınmıştır.";
    header("Location: admin.php");
    exit();
}

// Aynı kullanıcı aynı kitabı ödünç almış mı kontrol et
$sql = "SELECT * FROM odunc_alma 
        WHERE kitap_id = ? 
        AND kullanici_id = ? 
        AND teslim_edildi = FALSE";
$stmt = $baglanti->prepare($sql);
$stmt->bind_param("ii", $kitap_id, $kullanici_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION["mesaj"] = "Bu kitabı zaten ödünç aldınız.";
    header("Location: admin.php");
    exit();
}

// Ödünç alma işlemini ekle
$odunc_alma_tarihi = date("Y-m-d");
$teslim_tarihi = date("Y-m-d", strtotime("+15 days"));
// Varsayılan olarak teslim_edildi false olacak
$teslim_edildi = false;

$sql = "INSERT INTO odunc_alma (kullanici_id, kitap_id, odunc_alma_tarihi, teslim_tarihi, teslim_edildi) VALUES (?, ?, ?, ?, ?)";
$stmt = $baglanti->prepare($sql);
$stmt->bind_param("iissi", $kullanici_id, $kitap_id, $odunc_alma_tarihi, $teslim_tarihi, $teslim_edildi);

if ($stmt->execute()) {
    $formatli_tarih = date("d.m.Y", strtotime($teslim_tarihi));
    $_SESSION["mesaj"] = "Kitap başarıyla ödünç alındı. Teslim tarihi: " . $formatli_tarih;
    header("Location: admin.php");
    exit();
} else {
    $_SESSION["mesaj"] = "Hata: " . $baglanti->error;
    header("Location: admin.php");
    exit();
}
?>