<?php

$oda_id = $_POST["oda_id"]
       ?? $_GET["oda_id"]
       ?? 0;

$oyuncu_id = $_POST["oyuncu_id"]
          ?? $_GET["oyuncu_id"]
          ?? 0;

$hamle = $_POST["hamle"]
      ?? $_GET["hamle"]
      ?? "";

if (!$oda_id || !$oyuncu_id || $hamle === "") {
    error("Eksik veri");
}

$stmt = $pdo->prepare("SELECT * FROM oyuncular WHERE id=?");
$stmt->execute([$oyuncu_id]);
$oyuncu = $stmt->fetch();

if (!$oyuncu) {
    error("Oyuncu bulunamadı");
}

/* Aynı hamle kontrolü */
if ($oyuncu["son_hamle"] === $hamle) {
    error("Aynı hamle arka arkaya yapılamaz");
}

/* Mermi kontrolü */
if ($hamle === "ates" && $oyuncu["mermi"] <= 0) {
    error("Mermi yok");
}

/* Hamleyi kaydet */
$stmt = $pdo->prepare(
    "INSERT INTO hamleler (oda_id, oyuncu_id, hamle) VALUES (?,?,?)"
);
$stmt->execute([$oda_id, $oyuncu_id, $hamle]);

/* Oyuncu durum güncelle */
if ($hamle === "sarj") {
    $pdo->prepare("UPDATE oyuncular SET mermi=mermi+1 WHERE id=?")
        ->execute([$oyuncu_id]);
}

if ($hamle === "ates") {
    $pdo->prepare("UPDATE oyuncular SET mermi=mermi-1 WHERE id=?")
        ->execute([$oyuncu_id]);
}

$pdo->prepare("UPDATE oyuncular SET son_hamle=? WHERE id=?")
    ->execute([$hamle, $oyuncu_id]);

success([
    "hamle" => $hamle
]);
