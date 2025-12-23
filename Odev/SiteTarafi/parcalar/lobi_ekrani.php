<div id="screen-lobby" class="screen" style="max-width: 900px;"> 
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div class="user-profile-bar">
            <span class="avatar-icon">👤</span>
            <span class="user-name-disp" id="lobbyUserName">AJAN</span>
            <span style="font-size:10px; color:#888; margin-left:5px;">(Çevrimiçi)</span>
        </div>
        <button class="btn-dark" style="width:auto; font-size:11px;" onclick="logOut()">ÇIKIŞ</button>
    </div>

    <h1 class="brand">GÖREV MERKEZİ</h1>
    
    <div class="lobby-container">
        <div class="lobby-left">
            <div class="card">
                <h3>YENİ OYUN KUR</h3>
                <input type="text" id="roomName" placeholder="Oda İsmi">
                <div style="display:flex; gap:10px;">
                    <select id="targetScore" style="flex:1;">
                        <option value="5">5 Puan</option>
                        <option value="10">10 Puan</option>
                        <option value="15">15 Puan</option>
                    </select>
                    <input type="text" id="roomPass" placeholder="Şifre" style="flex:1;">
                </div>
                <button class="btn-green" onclick="createRoom()">BAŞLAT</button>
            </div>

            <div class="card">
                <h3 style="display:flex; justify-content:space-between; align-items:center;">
                    <span>OPERASYONLAR</span>
                    <button 
                        class="btn-dark" 
                        style="font-size:10px; padding:3px 6px;"
                        onclick="refreshRooms()"
                        title="Odaları Yenile"
                    >
                        ⟳
                    </button>
                </h3>
                <div id="roomList"></div>
            </div>
            
            <div class="card">
                <h3>🏆 TOP 10 AJANLAR</h3>
                <div id="leaderboard" style="font-size:12px;">Yükleniyor...</div>
            </div>
        </div>

        <div class="lobby-right">
            <div class="card">
                <h3>AJAN AĞI (SOHBET)</h3>
                <div 
                    id="lobbyChatBox" 
                    style="height:250px; overflow-y:auto; background:#080808; padding:10px; border:1px solid #333; margin-bottom:10px; border-radius:8px;"
                ></div>
                <div style="display:flex; gap:5px;">
                    <input 
                        type="text" 
                        id="lobbyChatInput" 
                        placeholder="Mesaj..." 
                        style="margin:0;"
                        onkeyup="if(event.key === 'Enter') sendLobbyChat()"
                    >
                    <button class="btn-green" style="width:auto;" onclick="sendLobbyChat()">></button>
                </div>
            </div>

            <div class="card">
                <h3>AKTİF AJANLAR</h3>
                <div id="activeUserList" style="max-height:200px; overflow-y:auto;"></div>
            </div>
        </div>
    </div>
</div>
