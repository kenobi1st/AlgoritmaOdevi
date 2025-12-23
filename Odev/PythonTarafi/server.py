import requests
import time
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
SITE_URL = "https://www.okanhoca.online/oyun_api.php" 

print("--- PROTOCOL 007 (LOGIC V2) ---")
print("Kurallar: Vuran +1 Puan alır. Ateş eden -1 Mermi harcar.")

session = requests.Session()
session.headers.update({"User-Agent": "Mozilla/5.0", "Connection": "keep-alive"})

while True:
    try:
        response = session.get(f"{SITE_URL}?islem=tum_odalari_cek", verify=False, timeout=5)
        
        if response.status_code == 200:
            data = response.json()
            if data:
                for oda in data:
                    oid = oda['id']
                    p1_ad = oda.get('p1_ad', 'P1')
                    p2_ad = oda.get('p2_ad', 'P2')
                    p1, p2 = oda['p1_hamle'], oda['p2_hamle']

                    if p1 and p2:
                        print(f"[ODA #{oid}] {p1} vs {p2}")
                        msg = ""
                        # m = mermi değişimi, p = puan değişimi
                        p1_m_add, p2_m_add = 0, 0
                        p1_p_add, p2_p_add = 0, 0

                        # --- YENİ MANTIK ---
                        
                        # 1. P1 ATEŞ ETTİ
                        if p1 == "ates":
                            p1_m_add = -1 # Mermi harca
                            if p2 == "sarj": 
                                msg = f"{p1_ad} VURDU! (+1 Puan)"
                                p1_p_add = 1 # Vurduğu için puan
                            elif p2 == "ates": 
                                p2_m_add = -1
                                msg = "KARŞILIKLI ÇATIŞMA (İki taraf da ıskaladı)"
                            else: # savunma
                                msg = f"{p2_ad} BLOKLADI! (Mermi boşa gitti)"

                        # 2. P1 ŞARJ ETTİ
                        elif p1 == "sarj":
                            p1_m_add = 1 # Mermi kazan
                            if p2 == "ates": 
                                p2_m_add = -1
                                msg = f"{p2_ad} VURDU! (+1 Puan)"
                                p2_p_add = 1 # P2 puan kazandı
                            elif p2 == "sarj": 
                                p2_m_add = 1
                                msg = "İKİ TARAF DA ŞARJ OLDU"
                            else: 
                                msg = f"{p1_ad} RAHATÇA ŞARJ OLDU"

                        # 3. P1 SAVUNMA YAPTI
                        elif p1 == "savunma":
                            if p2 == "ates": 
                                p2_m_add = -1
                                msg = f"{p1_ad} BAŞARIYLA BLOKLADI"
                            elif p2 == "sarj": 
                                p2_m_add = 1
                                msg = f"{p2_ad} RAHATÇA ŞARJ OLDU"
                            else: 
                                msg = "İKİ TARAF DA BEKLEMEDE"

                        safe_msg = msg.replace(" ", "%20")
                        
                        # API'ye yeni parametrelerle gönder
                        url = f"{SITE_URL}?islem=sonuc_yaz&id={oid}&msg={safe_msg}&p1_m_add={p1_m_add}&p2_m_add={p2_m_add}&p1_p_add={p1_p_add}&p2_p_add={p2_p_add}&m1={p1}&m2={p2}"
                        session.get(url, verify=False)
                        print(f"-> {msg}")
        
        time.sleep(1.5) 

    except Exception as e:
        print("Hata, tekrar deneniyor...")
        time.sleep(2)