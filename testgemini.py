import requests
import json

def translate_chapter_with_gemini(story_text: str) -> str:
    # Đọc file tóm tắt nhân vật
    with open('summarize.txt', 'r', encoding='utf-8') as f:
        summarize = f.read()

    # API key và endpoint
    API_KEY = 'AIzaSyAbobxuRAYZy7bfBTqgsLqUMRDsL12FDbc'
    url = f'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={API_KEY}'
    headers = {
        'Content-Type': 'application/json'
    }

    # Prompt gửi đi
    prompt_text = f'''
    tôi muốn dịch truyện 重生：开局逮到高冷校花超市偷窃
    đây là tóm tắt nội dung truyện và các nhân vật đã xuất hiện:
    {summarize}
    đây là nội dung chương
    {story_text}
    tôi muốn bạn đưa ra response dưới dạng json gồm 2 attribute toàn bộ dưới dạng tiếng việt
    chapter_name, translated
    chapter_name dạng như: 'chương 123: tôi phải làm gì bây giờ?'
    '''

    payload = {
        "contents": [
            {
                "parts": [
                    {
                        "text": prompt_text
                    }
                ]
            }
        ]
    }

    # Gửi request
    response = requests.post(url, headers=headers, data=json.dumps(payload))

    # Xử lý kết quả
    if response.status_code == 200:
        try:
            data = response.json()
            return data["candidates"][0]["content"]["parts"][0]["text"]
        except (KeyError, IndexError) as e:
            print("Unexpected response structure:", e)
            print(json.dumps(response.json(), indent=2, ensure_ascii=False))
            return ""
    else:
        print(f"Request failed with status code {response.status_code}")
        print(response.text)
        return ""
