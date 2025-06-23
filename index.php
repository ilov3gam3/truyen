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
    #controls {
      margin-bottom: 20px;
    }
    select, button {
      font-size: 16px;
      padding: 6px 12px;
      margin-right: 10px;
    }
    pre {
      white-space: pre-wrap;
      background: #f4f4f4;
      padding: 20px;
      border-radius: 5px;
      overflow-x: auto;
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

<div id="controls">
  <button onclick="prevChapter()">← Chương trước</button>
  <button onclick="nextChapter()">Chương tiếp →</button>
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

  // Populate dropdown
  chapterNumbers.forEach((chNum, idx) => {
    const option = document.createElement('option');
    option.value = idx;
    option.textContent = 'Chương ' + chNum;
    chapterSelect.appendChild(option);
  });

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

  function selectChapter() {
    const selected = parseInt(chapterSelect.value, 10);
    loadChapter(selected);
  }

  window.onload = () => loadChapter(0);
</script>

</body>
</html>
