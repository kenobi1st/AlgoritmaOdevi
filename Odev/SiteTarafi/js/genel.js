/* =====================================================
   1. GENEL AYARLAR VE GLOBAL DEĞİŞKENLER
===================================================== */

const API = "https://okanhoca.online/oyun_api.php";

// Kullanıcı bilgileri
let myName = localStorage.getItem("007_username") || "";
let myRole = localStorage.getItem("007_role") || "";
let activeId = parseInt(localStorage.getItem("007_active_id") || "0");

// 🔴 KRİTİK: backend ile birebir giden ID
let oyuncuId = parseInt(localStorage.getItem("007_oyuncu_id") || "0");

// Döngüler
let gameLoop = null;
let lobbyLoop = null;

// Oyun durumu
let gameState = "WAITING";

// Sayaçlar
let lastLobbyMsgCount = 0;
let lastGameMsgCount = 0;

// Sesler
const soundBeep = new Audio("https://www.soundjay.com/buttons/sounds/button-19.mp3");
const soundJoin = new Audio("https://www.soundjay.com/buttons/sounds/button-37.mp3");

/* =====================================================
   2. YARDIMCI FONKSİYONLAR
===================================================== */

function timeAgo(dateString) {
    if (!dateString) return "";
    let date = new Date(dateString.replace(" ", "T"));
    let now = new Date();
    let diff = Math.floor((now - date) / 1000);

    if (diff < 60) return "şimdi";
    let m = Math.floor(diff / 60);
    if (m < 60) return m + "dk";
    let h = Math.floor(m / 60);
    if (h < 24) return h + "sa";
    let d = Math.floor(h / 24);
    return d + "g";
}

function showFullDate(dateString) {
    alert(new Date(dateString.replace(" ", "T")).toLocaleString("tr-TR"));
}

function showScreen(id) {
    document.querySelectorAll(".screen").forEach(s => {
        s.classList.remove("active-screen");
    });
    let el = document.getElementById(id);
    if (el) el.classList.add("active-screen");
}

/* =====================================================
   3. OTURUM YÖNETİMİ
===================================================== */

function saveSession(odaId, role) {
    activeId = odaId;
    myRole = role;
    localStorage.setItem("007_active_id", odaId);
    localStorage.setItem("007_role", role);
}

function clearSession() {
    activeId = 0;
    myRole = "";
    localStorage.removeItem("007_active_id");
    localStorage.removeItem("007_role");
}

function logOut() {
    if (oyuncuId > 0) {
        fetch(`${API}?islem=cikis&oyuncu_id=${oyuncuId}`);
    }
    localStorage.clear();
    location.reload();
}

/* =====================================================
   4. SAYFA YÜKLENİNCE
===================================================== */

window.onload = function () {
    if (myName && oyuncuId > 0) {
        let nameSpan = document.getElementById("lobbyUserName");
        if (nameSpan) nameSpan.innerText = myName;

        if (activeId > 0 && myRole) {
            initGame(activeId, myRole);
        } else {
            enterLobbyMode();
        }
    } else {
        showScreen("screen-login");
    }
};

/* =====================================================
   5. SAYFA GÖRÜNÜRLÜK
===================================================== */

document.addEventListener("visibilitychange", function () {
    if (document.visibilityState === "visible") {
        if (activeId > 0 && myRole) {
            fetch(`${API}?islem=durum_cek&id=${activeId}&rol=${myRole}`);
        }
    }
});
