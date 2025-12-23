<?php

$action = $_GET["mode"] ?? "";

$kadi = $_GET["kadi"] ?? "";
$sifre = $_GET["sifre"] ?? "";

if ($action === "" || $kadi === "" || $sifre === "") {
    error("Eksik bilgi");
}

/* === KAYIT === */
if ($action === "kayit") {

    // Aynı isim var mı?
    $stmt = $pdo->prepare("SELECT id FROM oyuncular WHERE isim=?");
    $stmt->execute([$kadi]);

    if ($stmt->fetch()) {
        error("Bu kullanıcı adı zaten kayıtlı");
    }

    $hash = password_hash($sifre, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        "INSERT INTO oyuncular (isim, sifre) VALUES (?, ?)"
    );
    $stmt->execute([$kadi, $hash]);

    success();
}

/* === GİRİŞ === */
if ($action === "giris") {

    $stmt = $pdo->prepare(
        "SELECT id, sifre FROM oyuncular WHERE isim=?"
    );
    $stmt->execute([$kadi]);
    $u = $stmt->fetch();

    if (!$u) {
        error("Kullanıcı bulunamadı");
    }

    if (!password_verify($sifre, $u["sifre"])) {
        error("Şifre yanlış");
    }

    success([
        "oyuncu_id" => $u["id"]
    ]);
}

error("Geçersiz işlem");
