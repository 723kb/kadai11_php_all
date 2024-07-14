function previewFile() {
  const fileInput = document.getElementById('picture');
  const preview = document.getElementById('preview');

  // 選択されたファイルを取得
  const file = fileInput.files[0];

  // FileReaderを使用して内容を読み込み
  if (file) {
    const reader = new FileReader();

    reader.onload = function (event) {
      preview.src = event.target.result;
      preview.classList.remove('hidden'); // プレビューを表示する
    }

    reader.readAsDataURL(file);
  } else {
    // ファイルが選択されていない場合、プレビューを隠す
    preview.src = ''; // プレビュー画像を空にする
    preview.classList.add('hidden'); // プレビューを隠す
  }
}

// ページ読み込み時に初期化するために呼び出し
window.addEventListener('load', initialize);

function initialize() {
  const preview = document.getElementById('preview');
  const idInput = document.querySelector('input[name="id"]');
  
    // ページ読み込み時にフォームのidをログに出力する
    console.log('ID:', idInput.value);

  if (preview.src !== '' && !preview.classList.contains('hidden')) {
    preview.classList.remove('hidden'); // プレビューを表示する
  } else {
    preview.classList.add('hidden'); // プレビューを隠す
  }
}

document.getElementById('picture').addEventListener('change', previewFile);

// メッセージの文字数を監視する処理
const messageTextarea = document.getElementById('message');
const messageError = document.getElementById('messageError');

messageTextarea.addEventListener('input', function () {
  if (this.value.length > 140) {
    messageError.classList.remove('hidden'); // 文字数が140文字を超えたらエラーメッセージを表示
  } else {
    messageError.classList.add('hidden'); // それ以外の場合は非表示
  }
  console.log('ID:', document.querySelector('input[name="id"]').value);

});