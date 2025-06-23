import requests
from bs4 import BeautifulSoup

url = 'https://ixdzs.tw/read/569138/p100.html'
response = requests.get(url)
soup = BeautifulSoup(response.text, 'html.parser')

# Tìm thẻ <a> có class là 'chapter-paging chapter-next'
next_chapter = soup.find('a', class_='chapter-paging chapter-next')

if next_chapter:
    href = next_chapter['href']              # lấy giá trị href
    text = next_chapter.get_text(strip=True) # lấy nội dung văn bản
    print(f"Next chapter link: {href}")
    print(f"Text: {text}")
else:
    print("Không tìm thấy thẻ next chapter.")
