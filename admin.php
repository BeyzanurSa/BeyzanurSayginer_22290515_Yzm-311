<?php
include("baglanti.php");
session_start();

// Kullanıcı giriş kontrolü
if (!isset($_SESSION["kullanici"])) {
    header("Location: kayit.php");
    exit();
}

// Kitapları veritabanından çek
$sql = "SELECT * FROM kitaplar";
$result = $baglanti->query($sql);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Kitap Yönetim Paneli</h1>
            <div>
                <a href="ekle.php" class="btn btn-success">Yeni Kitap Ekle</a>
                <a href="cikis.php" class="btn btn-danger ms-2">Çıkış Yap</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>ISBN</th>
                        <th>Kitap Adı</th>
                        <th>Baskı Yılı</th>
                        <th>Yazar</th>
                        <th>Kategori</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { 
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["id"]); ?></td>
                            <td><?php echo htmlspecialchars($row["isbn"]); ?></td>
                            <td><?php echo htmlspecialchars($row["kitap_adi"]); ?></td>
                            <td><?php echo htmlspecialchars($row["basim_yili"]); ?></td>
                            <td><?php echo htmlspecialchars($row["yazar"]); ?></td>
                            <td><?php echo htmlspecialchars($row["kategori"]); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="duzenle.php?id=<?php echo $row["id"]; ?>" 
                                       class="btn btn-warning btn-sm">
                                        Düzenle
                                    </a>
                                    <a href="sil.php?id=<?php echo $row["id"]; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bu kitabı silmek istediğinizden emin misiniz?')">
                                        Sil
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="7" class="text-center">Henüz kitap eklenmemiş.</td>
                        </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>