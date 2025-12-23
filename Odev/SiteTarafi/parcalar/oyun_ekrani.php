<div id="screen-game" class="screen">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div id="gameStatus" class="status-bar" style="margin:0; flex:1;">BAĞLANTI KURULUYOR...</div>
        
        <button id="btnInvitePlayers" class="btn-dark" style="width:auto; font-size:11px; display:none; margin-left:10px; border:1px solid #00e5ff; color:#00e5ff;" onclick="openInviteModal()">
            + DAVET ET
        </button>
    </div>

    <div class="score-board">
        <div class="score-box p1">
            <span id="p1Name" style="font-size:12px;">BEN</span>
            <span class="big-score" id="p1Score">0</span>
            <div class="ammo-badge">MERMİ: <span id="p1Ammo">0</span></div>
        </div>
        <div style="display:flex; flex-direction:column; justify-content:center; text-align:center;">
            <span style="font-size:10px; color:#555; margin-bottom:5px;">VS</span>
            <span id="targetDisplay" style="color:#fff; font-size:11px; background:#222; padding:3px 8px; border-radius:4px;">HEDEF: 5</span>
        </div>
        <div class="score-box p2">
            <span id="p2Name" style="font-size:12px;">RAKİP</span>
            <span class="big-score" id="p2Score">0</span>
            <div class="ammo-badge">MERMİ: <span id="p2Ammo">0</span></div>
        </div>
    </div>

    <div class="controls">
        <button class="btn-big btn-sarj" id="btnSarj" onclick="sendMove('sarj')">⚡ ŞARJ ET</button>
        <button class="btn-big btn-blok" id="btnBlok" onclick="sendMove('savunma')">🛡️ SAVUN</button>
        <button class="btn-big btn-ates" id="btnAtes" onclick="sendMove('ates')">🔥 ATEŞ ET</button>
    </div>
    <div style="text-align:center; font-size:12px; color:#666; margin-top:10px;" id="timerInfo">Hamle bekleniyor...</div>

    <div class="history-wrap">
        <div class="h-col">
            <div style="color:#00e5ff; font-weight:bold; margin-bottom:5px; text-align:center;">BEN</div>
            <div id="histMy"></div>
        </div>
        <div class="h-col">
            <div style="color:#ff4081; font-weight:bold; margin-bottom:5px; text-align:center;">RAKİP</div>
            <div id="histOp"></div>
        </div>
    </div>

    <div class="chat-container">
        <div class="chat-box" id="chatBox"></div>
        <div class="chat-input-area">
            <input type="text" id="chatInp" placeholder="Mesaj yaz...">
            <button class="btn-dark" style="width:auto; padding:0 20px;" onclick="sendChat()">></button>
        </div>
    </div>
    
    <button onclick="confirmExit()" style="margin-top:20px; background:none; color:#555; font-size:11px;">ÇIKIŞ YAP (HÜKMEN MAĞLUP)</button>
</div>

<div id="overlay-join" class="overlay hidden">
    <div class="big-text" id="joinName">RAKİP KATILDI!</div>
    <div class="count-num" id="countDownNum">3</div>
    <div class="sub-text">HAZIR OL...</div>
</div>

<div id="overlay-win" class="overlay hidden">
    <div class="big-text" id="winnerText" style="color:#ffd700;">KAZANDIN!</div>
    <div class="sub-text" id="winSub">Mükemmel bir zaferdi ajan.</div>
    <div style="margin-top:30px; font-size:14px; color:#888;">Lobiye dönülüyor: <span id="lobbyTimer" style="color:#fff;">30</span></div>
    <button class="btn-green" style="width:200px; margin-top:20px;" onclick="location.reload()">HEMEN DÖN</button>
</div>

<div id="overlay-invite" class="overlay hidden" style="background:rgba(0,0,0,0.8);">
    <div class="card" style="width:300px; max-height:400px; display:flex; flex-direction:column;">
        <h3>DAVET GÖNDER</h3>
        <div id="inviteListArea" style="flex:1; overflow-y:auto; margin-bottom:10px; min-height:100px;">
            Yükleniyor...
        </div>
        <button class="btn-dark" onclick="document.getElementById('overlay-invite').classList.add('hidden')">KAPAT</button>
    </div>
</div>