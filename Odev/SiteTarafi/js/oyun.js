/* --- 4. OYUN MOTORU --- */

function initGame(id, role) {
    activeId = id;
    myRole = role;
    showScreen('screen-game');
    gameState = "WAITING";

    if (gameLoop) clearInterval(gameLoop);
    gameLoop = setInterval(updateGame, 1000);

    document.title = "Oyun Başladı...";
    let chatInput = document.getElementById("chatInp");
    if (chatInput) {
        chatInput.onkeyup = function (e) {
            if (e.key === "Enter") sendChat();
        };
    }
}

function updateGame() {

    if (activeId === 0) return;

    fetch(`${API}?islem=durum_cek&id=${activeId}&rol=${myRole}`)
        .then(r => r.json())
        .then(d => {

            // 🔴 KRİTİK: id geçici yoksa OYUNU BİTİRME
            if (!d || typeof d.id === "undefined") {
                return;
            }

            let me = (myRole === "p1")
                ? { name: d.p1_ad, score: d.p1_puan, ammo: d.p1_mermi, move: d.p1_hamle, hist: d.p1_gecmis }
                : { name: d.p2_ad, score: d.p2_puan, ammo: d.p2_mermi, move: d.p2_hamle, hist: d.p2_gecmis };

            let op = (myRole === "p1")
                ? { name: d.p2_ad, score: d.p2_puan, ammo: d.p2_mermi }
                : { name: d.p1_ad, score: d.p1_puan, ammo: d.p1_mermi };

            let hasOpponent = d.p2_durum === 'dolu';

            document.getElementById("p1Name").innerText = me.name || "SEN";
            document.getElementById("p1Score").innerText = me.score || 0;
            document.getElementById("p1Ammo").innerText = me.ammo || 0;
            document.getElementById("p2Name").innerText = op.name || "BEKLENİYOR...";
            document.getElementById("p2Score").innerText = op.score || 0;
            document.getElementById("p2Ammo").innerText = op.ammo || 0;
            document.getElementById("targetDisplay").innerText = "HEDEF: " + d.hedef_puan;

            let statusText = d.son_mesaj || "Hazır";
            if (statusText === "OYUNCU_KATILDI") {
                statusText = "RAKİP KATILDI! BAŞLIYORUZ...";
            }
            document.getElementById("gameStatus").innerText = statusText;

            let btnInvite = document.getElementById("btnInvitePlayers");
            if (btnInvite) {
                if (myRole === "p1" && d.p2_durum === 'bos') {
                    btnInvite.style.display = "block";
                } else {
                    btnInvite.style.display = "none";
                }
            }

            if (gameState === "WAITING" && hasOpponent && d.son_mesaj === "OYUNCU_KATILDI") {
                startCountdown(op.name || "RAKİP");
            }

            if (d.kazanan && gameState !== "END") {
                endGame(d.kazanan);
            }

            if (gameState === "PLAYING") {
                let btnSarj = document.getElementById("btnSarj");
                let btnAtes = document.getElementById("btnAtes");
                let btnBlok = document.getElementById("btnBlok");
                let info = document.getElementById("timerInfo");

                if (me.move) {
                    document.title = "Rakip Bekleniyor...";
                    if (info) info.innerText = "Rakip hamlesi bekleniyor...";
                    [btnSarj, btnAtes, btnBlok].forEach(b => b && (b.disabled = true));
                } else {
                    document.title = "🔴 SIRA SENDE!";
                    if (info) info.innerText = "HAMLE SIRASI SENDE!";

                    let lastMove = "";
                    if (me.hist) {
                        let moves = me.hist.split(",").filter(x => x);
                        if (moves.length > 0) lastMove = moves[moves.length - 1];
                    }

                    if (btnSarj) btnSarj.disabled = (lastMove === "sarj");
                    if (btnBlok) btnBlok.disabled = (lastMove === "savunma");
                    if (btnAtes) btnAtes.disabled = (me.ammo <= 0 || lastMove === "ates");
                }
            }

            renderHist(d.p1_gecmis, d.p2_gecmis);

        })
        .catch(e => console.log(e));
}

// --- DAVET MODALI AÇMA VE GÖNDERME ---
function openInviteModal() {
    document.getElementById("overlay-invite").classList.remove("hidden");
    document.getElementById("inviteListArea").innerHTML = "Ajanlar aranıyor...";

    fetch(`${API}?islem=lobi_guncelle&ben=${myName}`)
        .then(r => r.json())
        .then(d => {
            let html = "";
            let count = 0;
            d.kullanicilar.forEach(u => {
                if (u !== myName) {
                    html += `<div class="invite-row">
                        <span>${u}</span>
                        <button class="btn-invite-send" onclick="sendInvite('${u}')">DAVET ET</button>
                    </div>`;
                    count++;
                }
            });
            if (count === 0) {
                html = "<div style='color:#888; text-align:center; padding:20px;'>Şu an lobide boşta kimse yok.</div>";
            }
            document.getElementById("inviteListArea").innerHTML = html;
        });
}

function sendInvite(target) {
    fetch(`${API}?islem=davet_et&kimden=${myName}&kime=${target}&oda_id=${activeId}`)
        .then(r => r.json())
        .then(() => {
            alert(target + " adlı ajana davet gönderildi.");
            document.getElementById("overlay-invite").classList.add("hidden");
        });
}

// ... (Diğer fonksiyonlar aynen devam ediyor) ...

function startCountdown(opName) {
    gameState = "COUNTDOWN";
    document.getElementById("overlay-invite").classList.add("hidden");
    let overlay = document.getElementById("overlay-join");
    let num = document.getElementById("countDownNum");
    document.getElementById("joinName").innerText = opName.toUpperCase() + " KATILDI!";
    overlay.classList.remove("hidden");
    soundJoin.play().catch(e => { });
    let count = 3;
    num.innerText = count;
    let iv = setInterval(() => {
        count--;
        if (count > 0) {
            num.innerText = count;
            soundBeep.play().catch(e => { });
        } else {
            clearInterval(iv);
            overlay.classList.add("hidden");
            gameState = "PLAYING";
            soundBeep.play().catch(e => { });
        }
    }, 1000);
}

function endGame(winnerName) {
    gameState = "END";
    clearInterval(gameLoop);
    clearSession();
    alert("Oyun bitti. Kazanan: " + winnerName);
    location.reload();
}

function sendMove(m) {
    document.querySelectorAll(".controls button").forEach(b => b.disabled = true);

    fetch(`${API}?islem=hamle_yap&id=${activeId}&rol=${myRole}&hamle=${m}`)
        .then(() => {
            document.querySelectorAll(".controls button").forEach(b => b.disabled = false);
        })
        .catch(() => {
            document.querySelectorAll(".controls button").forEach(b => b.disabled = false);
        });
}

function sendChat() {
    let i = document.getElementById("chatInp");
    if (i.value) {
        fetch(`${API}?islem=mesaj_gonder&id=${activeId}&kim=${myName}&mesaj=${i.value}`);
        i.value = "";
    }
}

function renderHist(h1, h2) {
    let f = (s) =>
        s ? s.split(",").filter(x => x).reverse().map(x =>
            `<div class='h-item'>${x.toUpperCase()
                .replace("ATES", "ATEŞ")
                .replace("SARJ", "ŞARJ")
                .replace("SAVUNMA", "BLOK")}</div>`
        ).join("") : "";

    let mH = (myRole === "p1") ? h1 : h2;
    let oH = (myRole === "p1") ? h2 : h1;
    document.getElementById("histMy").innerHTML = f(mH);
    document.getElementById("histOp").innerHTML = f(oH);
}

function confirmExit() {
    if (confirm("Mağlup sayılacaksın. Emin misin?")) {
        fetch(`${API}?islem=oyundan_cik&id=${activeId}&kim=${myRole}`);
        clearSession();
        setTimeout(() => { location.reload(); }, 300);
    }
}
