<?php
include("baglanti.php");
session_start();

// Formun POST isteğiyle gönderildiğini kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Hangi işlemin yapıldığını kontrol et: login mi signup mı?
    if (isset($_POST["login"])) {
        // Giriş Yap (Login)
        $kullaniciadi = trim($_POST["kullaniciadi"]);
        $parola = trim($_POST["parola"]);

        // Kullanıcı adı ve şifreyi kontrol et
        $kontrol = "SELECT * FROM kullanicilar WHERE kullanici_adi = ?";
        $stmt = $baglanti->prepare($kontrol);
        $stmt->bind_param("s", $kullaniciadi);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $kullanici = $result->fetch_assoc();

            // Şifreyi doğrula
            if (password_verify($parola, $kullanici["parola"])) {
                $_SESSION["kullanici"] = $kullaniciadi;
                header("Location: http://localhost/PhP/admin.php"); // Başarılı girişte admin paneline yönlendir
                exit();
            } else {
                echo '<div class="alert alert-danger" role="alert">Şifre yanlış!</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Kullanıcı bulunamadı!</div>';
        }
    } elseif (isset($_POST["signup"])) {
        // Kayıt Ol (Sign Up)
        $kullaniciadi = trim($_POST["kullaniciadi"]);
        $email = trim($_POST["email"]);
        $parola = trim($_POST["parola"]);

        // Kullanıcı adı veya email zaten var mı kontrol et
        $kontrol = "SELECT * FROM kullanicilar WHERE kullanici_adi = ? OR email = ?";
        $stmt = $baglanti->prepare($kontrol);
        $stmt->bind_param("ss", $kullaniciadi, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<div class="alert alert-danger" role="alert">Kullanıcı adı veya email zaten kayıtlı!</div>';
        } else {
            // Şifreyi güvenli hale getir
            $hashed_parola = password_hash($parola, PASSWORD_BCRYPT);

            // Yeni kullanıcıyı ekle
            $ekle = "INSERT INTO kullanicilar (kullanici_adi, email, parola) VALUES (?, ?, ?)";
            $stmt = $baglanti->prepare($ekle);
            $stmt->bind_param("sss", $kullaniciadi, $email, $hashed_parola);

            if ($stmt->execute()) {
                echo '<div class="alert alert-success" role="alert">Kayıt başarılı! Şimdi giriş yapabilirsiniz.</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Kayıt sırasında bir hata oluştu.</div>';
            }
        }
    }
}
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kullanıcı Girişi ve Kaydı</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container p-5">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Giriş Yap</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup" type="button" role="tab" aria-controls="signup" aria-selected="false">Kayıt Ol</button>
          </li>
        </ul>
        <div class="tab-content" id="myTabContent">
          <!-- Giriş Yap -->
          <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
            <form action="kayit.php" method="POST" class="mt-4">
                <div class="mb-3">
                    <label for="loginKullaniciAdi" class="form-label">Kullanıcı Adı</label>
                    <input type="text" class="form-control" id="loginKullaniciAdi" name="kullaniciadi" required>
                </div>
                <div class="mb-3">
                    <label for="loginParola" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="loginParola" name="parola" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary">Giriş Yap</button>
            </form>
          </div>
          <!-- Kayıt Ol -->
          <div class="tab-pane fade" id="signup" role="tabpanel" aria-labelledby="signup-tab">
            <form action="kayit.php" method="POST" class="mt-4">
                <div class="mb-3">
                    <label for="signupKullaniciAdi" class="form-label">Kullanıcı Adı</label>
                    <input type="text" class="form-control" id="signupKullaniciAdi" name="kullaniciadi" required>
                </div>
                <div class="mb-3">
                    <label for="signupEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="signupEmail" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="signupParola" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="signupParola" name="parola" required>
                </div>
                <button type="submit" name="signup" class="btn btn-success">Kayıt Ol</button>
            </form>
          </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
