<?php

$oyuncu_id = $_POST["oyuncu_id"]
          ?? $_GET["oyuncu_id"]
          ?? 0;

if (!$oyuncu_id) {
    error("Oyuncu ID yok");
}

/* Bekleyen oda var mı? */
$stmt = $pdo->query("SELECT * FROM odalar WHERE durum='bekleniyor' LIMIT 1");
$oda = $stmt->fetch();

if ($oda) {
    // P2 olarak gir
    $stmt = $pdo->prepare("UPDATE odalar SET p2_id=?, durum='basladi' WHERE id=?");
    $stmt->execute([$oyuncu_id, $oda["id"]]);

    success([
        "oda_id" => $oda["id"],
        "rol" => "p2"
    ]);
} else {
    // Yeni oda oluştur, P1 ol
    $stmt = $pdo->prepare("INSERT INTO odalar (p1_id) VALUES (?)");
    $stmt->execute([$oyuncu_id]);

    success([
        "oda_id" => $pdo->lastInsertId(),
        "rol" => "p1"
    ]);
}
