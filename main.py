import requests
from bs4 import BeautifulSoup
import os
import json
import re

import testgemini

host = 'https://ixdzs.tw/'
from_index = 208
to_index = 210

os.makedirs("downloads", exist_ok=True)
def sanitize_filename(name):
    # Loại bỏ ký tự không hợp lệ và cắt bớt nếu cần
    name = re.sub(r'[\\/*?:"<>|]', '', name)
    return name.strip()[:100]


for i in range(from_index, to_index + 1):
    page_url = f'{host}read/569138/p{i}.html'
    html = requests.get(page_url).text
    soup = BeautifulSoup(html, 'html.parser')

    content_div = soup.find('article', class_='page-content')
    if not content_div:
        print(f"[!] Không tìm thấy nội dung ở {page_url}")
        continue

    paragraphs = content_div.find_all('p')
    lines = [p.get_text(strip=True) for p in paragraphs if p.get_text(strip=True)]

    if not lines:
        print(f"[!] Không có nội dung ở {page_url}")
        continue

    # Ghi nội dung
    story_text = '\n'.join(lines)
    json_response = json.loads(testgemini.translate_chapter_with_gemini(story_text).replace("```", "").replace("json", "").strip())
    safe_name = sanitize_filename(json_response['chapter_name'])
    filename = f"translated/{safe_name}.txt"
    with open(filename, "w", encoding="utf-8") as f:
        f.write(json_response['translated'])

    print(f"✔ Đã lưu '{json_response['chapter_name']}' vào {filename}")
