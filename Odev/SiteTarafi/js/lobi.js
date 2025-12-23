/* =====================================================
   LOBİ YÖNETİMİ
===================================================== */

function enterLobbyMode() {
    showScreen("screen-lobby");

    updateLobby();
    updateRooms();

    if (lobbyLoop) clearInterval(lobbyLoop);

    lobbyLoop = setInterval(() => {
        updateLobby();
        updateRooms();
    }, 10000);

    let chatInp = document.getElementById("lobbyChatInput");
    if (chatInp) {
        chatInp.onkeyup = function (e) {
            if (e.key === "Enter") sendLobbyChat();
        };
    }
}

/* =====================================================
   AKTİF AJANLAR
===================================================== */

function updateLobby() {
    fetch(`${API}?islem=lobi_guncelle&oyuncu_id=${oyuncuId}`)
        .then(r => r.json())
        .then(d => {
            let html = "";

            if (d.kullanicilar && Array.isArray(d.kullanicilar)) {
                d.kullanicilar.forEach(u => {
                    if (u === myName) {
                        html += `<div class="active-user-item" style="color:#00ff41;">👤 ${u} (Sen)</div>`;
                    } else {
                        html += `<div class="active-user-item">👤 ${u}</div>`;
                    }
                });
            }

            document.getElementById("activeUserList").innerHTML =
                html || "<div style='color:#666'>Aktif ajan yok</div>";
        })
        .catch(() => {
            document.getElementById("activeUserList").innerHTML =
                "<div style='color:#666'>Bağlantı hatası</div>";
        });
}

/* =====================================================
   AKTİF ODALAR
===================================================== */

function updateRooms() {
    fetch(`${API}?islem=oda_listele`)
        .then(r => r.json())
        .then(list => {
            let html = "";

            if (Array.isArray(list) && list.length > 0) {
                list.forEach(r => {
                    html += `
                    <div style="
                        background:#111;
                        padding:10px;
                        margin-bottom:6px;
                        border:1px solid #333;
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        font-size:12px;
                    ">
                        <div>
                            <div style="font-weight:bold; color:#fff;">
                                🎯 ${r.oda_adi}
                            </div>
                            <div style="color:#888; font-size:11px;">
                                Kurucu: ${r.kurucu} | Hedef: ${r.hedef_puan}
                            </div>
                        </div>
                        <button class="btn-green"
                            style="width:auto; padding:4px 10px; font-size:11px;"
                            onclick="joinRoom(${r.oda_id})">
                            KATIL
                        </button>
                    </div>`;
                });
            } else {
                html = "<div style='color:#666; text-align:center;'>Aktif oda yok</div>";
            }

            document.getElementById("roomList").innerHTML = html;
        })
        .catch(() => {
            document.getElementById("roomList").innerHTML =
                "<div style='color:#666'>Oda listesi alınamadı</div>";
        });
}

/* =====================================================
   ODA KUR
===================================================== */

function createRoom() {
    let hedef = document.getElementById("targetScore").value;
    let odaAdi = document.getElementById("roomName").value.trim();

    if (!odaAdi) {
        alert("Oda adı girilmelidir");
        return;
    }

    if (!oyuncuId || oyuncuId <= 0) {
        alert("Oyuncu ID bulunamadı. Çıkış yapıp tekrar gir.");
        return;
    }

    fetch(
        `${API}?islem=oda_kur` +
        `&oyuncu_id=${oyuncuId}` +
        `&oda_adi=${encodeURIComponent(odaAdi)}` +
        `&hedef=${hedef}`
    )
        .then(r => r.json())
        .then(d => {
            if (d.status === "ok") {
                saveSession(d.data.oda_id, d.data.rol);
                initGame(d.data.oda_id, d.data.rol);
            } else {
                alert(d.mesaj || "Oda kurulamadı");
            }
        })
        .catch(() => alert("Bağlantı hatası"));
}

/* =====================================================
   ODAYA KATIL
===================================================== */

function joinRoom(odaId) {
    saveSession(odaId, "p2");
    initGame(odaId, "p2");
}

/* =====================================================
   LOBİ SOHBET
===================================================== */

function sendLobbyChat() {
    let inp = document.getElementById("lobbyChatInput");
    if (!inp || !inp.value.trim()) return;
    inp.value = "";
}
