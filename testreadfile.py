with open('summarize.txt', 'a', encoding='utf-8') as f:
    f.write('\nNội dung mới được thêm vào\n')

with open('summarize.txt', 'r', encoding='utf-8') as f:
    content = f.read()
    print(content)

