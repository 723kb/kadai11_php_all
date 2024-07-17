  // 登録者一覧と投稿一覧の表示をクリックで切り替える処理
  function showUsers() {
    document.getElementById('userInfo').classList.remove('hidden');
    document.getElementById('postList').classList.add('hidden');
  }

  function showPosts() {
    document.getElementById('userInfo').classList.add('hidden');
    document.getElementById('postList').classList.remove('hidden');
  }

  // ハンバーガーメニューの切り替え処理
  menuButton.addEventListener('click', event => {
    toggleMenu();
  });

  // メニュー内の各ボタンがクリックされた時の処理
  document.querySelectorAll('#menu button').forEach(button => {
    button.addEventListener('click', event => {
      if (button.textContent.trim() !== 'ホーム') {
        closeMenu();
      }
    });
  });

  // メニューを開閉する関数
  function toggleMenu() {
    bars.classList.toggle('fa-bars');
    bars.classList.toggle('fa-times');
    menu.classList.toggle('translate-x-full');
  }

  // メニューを閉じる関数
  function closeMenu() {
    bars.classList.remove('fa-times');
    bars.classList.add('fa-bars');
    menu.classList.add('translate-x-full');
  }