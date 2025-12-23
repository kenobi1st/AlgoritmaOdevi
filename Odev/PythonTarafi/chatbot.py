import google.generativeai as genai
import time

# API Anahtarını buraya yapıştır
genai.configure(api_key="AIzaSyC2r2-uRyxygPbOBnFvwaC9PVAJEUe3lyw")

# DİKKAT: Senin listende "models/" ile başlayan isimler vardı.
# En güvenli yöntem tam ismi kullanmak veya 'gemini-flash-latest' demektir.
model = genai.GenerativeModel('gemini-flash-latest')

chat = model.start_chat(history=[])

print("Yapay Zeka ile Sohbet Başladı! (Çıkmak için 'q' yazabilirsin)")
print("-" * 50)

while True:
    user_input = input("Sen: ")
    
    if user_input.lower() == 'q':
        print("Sohbet sonlandırıldı.")
        break
    
    try:
        response = chat.send_message(user_input)
        print(f"Yapay Zeka: {response.text}")
        print("-" * 50)
    except Exception as e:
        print(f"Hata: {e}")
        print("Lütfen 10 saniye bekleyip tekrar dene.")
        time.sleep(10)