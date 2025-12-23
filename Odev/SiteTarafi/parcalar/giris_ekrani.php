<div id="screen-login" class="screen active-screen">
    <h1 class="brand">
        Protocol 007 <br>
        <span style="font-size:12px; color:#fff;">TACTICAL WARFARE</span>
    </h1>

    <div class="card">
        <h3 id="loginTitle">AJAN GİRİŞİ</h3>
        <p style="font-size:12px; color:#aaa; margin-bottom:15px;">
            Operasyona katılmak için kimliğinizi doğrulayın.
        </p>

        <label>Kod Adı</label>
        <input type="text" id="loginName" placeholder="Örn: Viper">

        <label>Şifre</label>
        <input type="password" id="loginPass" placeholder="Gizli Şifreniz">

        <button class="btn-green" onclick="doLogin()">GİRİŞ YAP</button>
        <button
            class="btn-dark"
            style="margin-top:10px;"
            onclick="toggleRegister()"
            id="btnToggle"
        >
            Kayıt Ol
        </button>
    </div>
</div>

<!-- 🔑 GİRİŞ EKRANINA ÖZEL JS SADECE BURADA -->
<script src="js/giris.js"></script>

