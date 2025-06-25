<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Truyện chữ</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      line-height: 1.6;
      max-width: 800px;
      margin: auto;
      padding: 20px;
      background: #fefefe;
      color: #333;
    }
    #controls, #bottomControls {
      margin: 20px 0;
      text-align: center;
    }
    select, button {
      font-size: 16px;
      padding: 6px 12px;
      margin: 5px;
    }
    pre {
      white-space: pre-wrap;
      background: #f4f4f4;
      padding: 20px;
      border-radius: 5px;
      overflow-x: auto;
    }
    #backToTop {
      display: inline-block;
      margin-top: 10px;
      font-size: 14px;
      color: #007bff;
      cursor: pointer;
      text-decoration: underline;
    }
  </style>
</head>
<body>

<h1>Truyện chữ - Trình đọc</h1>

<div id="controls">
  <button onclick="prevChapter()">← Chương trước</button>
  <button onclick="nextChapter()">Chương tiếp →</button>
  <select id="chapterSelect" onchange="selectChapter()"></select>
</div>

<h2 id="chapterTitle"></h2>
<pre id="chapterContent">Đang tải...</pre>

<div id="bottomControls">
  <button onclick="prevChapter()">← Chương trước</button>
  <button onclick="nextChapter()">Chương tiếp →</button>
  <select id="chapterSelectBottom" onchange="selectChapter(this.value)"></select>
  <div id="backToTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">⬆ Quay lại đầu trang</div>
</div>

<?php
$dir = "translated/";
$files = scandir($dir);
$chapters = [];

foreach ($files as $file) {
    if (preg_match('/chương\s*(\d+)/iu', $file, $matches)) {
        $num = (int)$matches[1];
        $chapters[$num] = $dir . $file;
    }
}
ksort($chapters);
?>

<script>
  const chapters = <?php echo json_encode(array_values($chapters)); ?>;
  const chapterNumbers = <?php echo json_encode(array_keys($chapters)); ?>;
  let currentIndex = 0;

  const chapterTitle = document.getElementById('chapterTitle');
  const chapterContent = document.getElementById('chapterContent');
  const chapterSelect = document.getElementById('chapterSelect');
  const chapterSelectBottom = document.getElementById('chapterSelectBottom');

  // Populate both dropdowns
  function populateDropdowns() {
    chapterNumbers.forEach((chNum, idx) => {
      const opt1 = new Option('Chương ' + chNum, idx);
      const opt2 = new Option('Chương ' + chNum, idx);
      chapterSelect.appendChild(opt1);
      chapterSelectBottom.appendChild(opt2);
    });
  }

  function loadChapter(index) {
    if (index < 0 || index >= chapters.length) return;
    fetch(chapters[index])
      .then(res => {
        if (!res.ok) throw new Error("Không thể tải chương");
        return res.text();
      })
      .then(text => {
        currentIndex = index;
        chapterTitle.textContent = chapterSelect.options[index].text;
        chapterContent.textContent = text;
        chapterSelect.value = index;
        chapterSelectBottom.value = index;
        document.cookie = `lastChapter=${index}; path=/; max-age=31536000`;
        window.scrollTo(0, 0);
      })
      .catch(err => {
        chapterTitle.textContent = '';
        chapterContent.textContent = 'Không thể tải chương.';
      });
  }

  function prevChapter() {
    if (currentIndex > 0) loadChapter(currentIndex - 1);
  }

  function nextChapter() {
    if (currentIndex < chapters.length - 1) loadChapter(currentIndex + 1);
  }

  function selectChapter(value) {
    const selected = parseInt(value ?? chapterSelect.value, 10);
    loadChapter(selected);
  }

  function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
  }

  function initKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowLeft') prevChapter();
      if (e.key === 'ArrowRight') nextChapter();
    });
  }

  // INIT
  window.onload = () => {
    populateDropdowns();
    initKeyboardShortcuts();
    const saved = getCookie('lastChapter');
    loadChapter(saved ? parseInt(saved, 10) : 0);
  };
</script>

</body>
</html>
