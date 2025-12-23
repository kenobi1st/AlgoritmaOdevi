/* --- 2. GİRİŞ VE HESAP İŞLEMLERİ --- */

let isRegisterMode = false;

function toggleRegister() {
    isRegisterMode = !isRegisterMode;
    document.getElementById("loginTitle").innerText =
        isRegisterMode ? "YENİ AJAN KAYDI" : "AJAN GİRİŞİ";
    document.querySelector("#screen-login .btn-green").innerText =
        isRegisterMode ? "KAYIT OL" : "GİRİŞ YAP";
    document.getElementById("btnToggle").innerText =
        isRegisterMode ? "Giriş Yap" : "Kayıt Ol";
}

function doLogin() {
    let kadi = document.getElementById("loginName").value.trim();
    let sifre = document.getElementById("loginPass").value.trim();
    if (!kadi || !sifre) return alert("Lütfen alanları doldurun!");

    let islem = isRegisterMode ? "kayit_ol" : "giris_yap";

    fetch(`${API}?islem=${islem}&kadi=${kadi}&sifre=${sifre}`)
        .then(r => r.json())
        .then(d => {
            if (d.durum === "basarili") {

                if (isRegisterMode) {
                    alert("Kayıt başarılı! Şimdi giriş yapabilirsiniz.");
                    toggleRegister();
                    return;
                }

                myName = d.veri.kadi;
                oyuncuId = d.veri.oyuncu_id;

                localStorage.setItem("007_username", myName);
                localStorage.setItem("007_oyuncu_id", oyuncuId);

                document.getElementById("lobbyUserName").innerText = myName;
                enterLobbyMode();

            } else {
                alert(d.mesaj);
            }
        })
        .catch(() => alert("Bağlantı hatası"));
}
