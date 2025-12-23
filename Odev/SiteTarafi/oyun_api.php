<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/api/db.php";

function out($arr, $code = 200) {
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

function now() {
    return date("Y-m-d H:i:s");
}

$islem = $_GET["islem"] ?? "";

/* ======================================================
   KAYIT OL
====================================================== */
if ($islem === "kayit_ol") {

    $kadi  = trim($_GET["kadi"] ?? "");
    $sifre = trim($_GET["sifre"] ?? "");

    if ($kadi === "" || $sifre === "") {
        out(["durum"=>"hata","mesaj"=>"Alanlar boş"],400);
    }

    $q = $pdo->prepare("SELECT id FROM oyuncular WHERE isim=?");
    $q->execute([$kadi]);
    if ($q->fetch()) {
        out(["durum"=>"hata","mesaj"=>"Kullanıcı var"],400);
    }

    $pdo->prepare("INSERT INTO oyuncular (isim, sifre) VALUES (?, ?)")
        ->execute([$kadi, password_hash($sifre, PASSWORD_DEFAULT)]);

    out(["durum"=>"basarili"]);
}

/* ======================================================
   GİRİŞ YAP
====================================================== */
if ($islem === "giris_yap") {

    $kadi  = trim($_GET["kadi"] ?? "");
    $sifre = trim($_GET["sifre"] ?? "");

    if ($kadi === "" || $sifre === "") {
        out(["durum"=>"hata","mesaj"=>"Alanlar boş"],400);
    }

    $q = $pdo->prepare("SELECT id, sifre FROM oyuncular WHERE isim=?");
    $q->execute([$kadi]);
    $u = $q->fetch(PDO::FETCH_ASSOC);

    if (!$u || !password_verify($sifre, $u["sifre"])) {
        out(["durum"=>"hata","mesaj"=>"Kullanıcı bulunamadı"],400);
    }

    out([
        "durum"=>"basarili",
        "veri"=>[
            "oyuncu_id"=>$u["id"],
            "kadi"=>$kadi
        ]
    ]);
}

/* ======================================================
   ODA KUR (P1)
====================================================== */
if ($islem === "oda_kur") {

    $oyuncu_id = intval($_GET["oyuncu_id"] ?? 0);
    $oda_adi   = trim($_GET["oda_adi"] ?? "");
    $hedef     = intval($_GET["hedef"] ?? 5);

    if ($oyuncu_id <= 0 || $oda_adi === "") {
        out(["durum"=>"hata","mesaj"=>"Eksik bilgi"],400);
    }

    $q = $pdo->prepare("SELECT isim FROM oyuncular WHERE id=?");
    $q->execute([$oyuncu_id]);
    $u = $q->fetch(PDO::FETCH_ASSOC);

    $oda_id = time();

    $pdo->prepare("
        INSERT INTO oyunlar
        (
            oda_id, oda_adi,
            p1_id, p1_ad, p1_mermi, p1_puan, p1_hamle,
            hedef_puan, durum, son_mesaj,
            created_at, updated_at
        )
        VALUES (?, ?, ?, ?, 0, 0, NULL, ?, 'bekleniyor', 'ODA_KURULDU', ?, ?)
    ")->execute([
        $oda_id,
        $oda_adi,
        $oyuncu_id,
        $u["isim"],
        $hedef,
        now(),
        now()
    ]);

    out([
        "status"=>"ok",
        "data"=>[
            "oda_id"=>$oda_id,
            "rol"=>"p1"
        ]
    ]);
}

/* ======================================================
   ODA GİR (P2)
====================================================== */
if ($islem === "oda_gir") {

    $oda_id    = intval($_GET["oda_id"] ?? 0);
    $oyuncu_id = intval($_GET["oyuncu_id"] ?? 0);

    $q = $pdo->prepare("SELECT * FROM oyunlar WHERE oda_id=?");
    $q->execute([$oda_id]);
    $o = $q->fetch(PDO::FETCH_ASSOC);

    if (!$o || $o["p2_id"]) {
        out(["durum"=>"hata","mesaj"=>"Oda dolu"],400);
    }

    $q = $pdo->prepare("SELECT isim FROM oyuncular WHERE id=?");
    $q->execute([$oyuncu_id]);
    $u = $q->fetch(PDO::FETCH_ASSOC);

    $pdo->prepare("
        UPDATE oyunlar SET
            p2_id=?,
            p2_ad=?,
            p2_mermi=0,
            p2_puan=0,
            p2_hamle=NULL,
            durum='oyunda',
            son_mesaj='OYUNCU_KATILDI',
            updated_at=?
        WHERE oda_id=?
    ")->execute([
        $oyuncu_id,
        $u["isim"],
        now(),
        $oda_id
    ]);

    out(["durum"=>"basarili","rol"=>"p2"]);
}

/* ======================================================
   HAMLE YAP
====================================================== */
if ($islem === "hamle_yap") {

    $oda_id = intval($_GET["id"] ?? 0);
    $rol    = $_GET["rol"] ?? "";
    $hamle  = $_GET["hamle"] ?? "";

    $q = $pdo->prepare("SELECT * FROM oyunlar WHERE oda_id=?");
    $q->execute([$oda_id]);
    $o = $q->fetch(PDO::FETCH_ASSOC);

    if (!$o || $o["durum"] !== "oyunda") out(["durum"=>"hata"]);

    if ($o[$rol."_hamle"]) out(["durum"=>"bekle"]);

    $mermi = $o[$rol."_mermi"];
    if ($hamle === "sarj") $mermi++;
    if ($hamle === "ates" && $mermi > 0) $mermi--;

    $pdo->prepare("
        UPDATE oyunlar SET
            {$rol}_hamle=?,
            {$rol}_mermi=?,
            updated_at=?
        WHERE oda_id=?
    ")->execute([$hamle, $mermi, now(), $oda_id]);

    out(["durum"=>"ok"]);
}

/* ======================================================
   DURUM ÇEK
====================================================== */
if ($islem === "durum_cek") {

    $oda_id = intval($_GET["id"] ?? 0);

    $q = $pdo->prepare("SELECT * FROM oyunlar WHERE oda_id=?");
    $q->execute([$oda_id]);
    $o = $q->fetch(PDO::FETCH_ASSOC);

    if (!$o) out(["id"=>0]);

    out([
        "id"=>$o["oda_id"],
        "p1_ad"=>$o["p1_ad"],
        "p2_ad"=>$o["p2_ad"],
        "p1_mermi"=>$o["p1_mermi"],
        "p2_mermi"=>$o["p2_mermi"],
        "p1_puan"=>$o["p1_puan"],
        "p2_puan"=>$o["p2_puan"],
        "p1_hamle"=>$o["p1_hamle"],
        "p2_hamle"=>$o["p2_hamle"],
        "p1_gecmis"=>$o["p1_gecmis"],
        "p2_gecmis"=>$o["p2_gecmis"],
        "p2_durum"=>$o["p2_id"] ? "dolu" : "bos",
        "hedef_puan"=>$o["hedef_puan"],
        "son_mesaj"=>$o["son_mesaj"],
        "kazanan"=>$o["kazanan"],
        "gecmis_log"=>""
    ]);
}

/* ======================================================
   ODA LİSTELE
====================================================== */
if ($islem === "oda_listele") {

    $q = $pdo->query("
        SELECT
            oda_id,
            oda_adi,
            p1_ad AS kurucu,
            hedef_puan
        FROM oyunlar
        WHERE durum = 'bekleniyor'
        ORDER BY created_at DESC
    ");

    $rooms = $q->fetchAll(PDO::FETCH_ASSOC);

    out($rooms);
}


/* ======================================================
   FALLBACK
====================================================== */
out(["durum"=>"hata","mesaj"=>"Geçersiz API çağrısı"],400);
