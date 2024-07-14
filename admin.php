<?php
session_start();  // セッション開始
include 'head.php';  // Header

require_once('db_conn.php');  // 関数群の呼び出し
require_once('funcs.php');
loginCheck();  // ログインチェック

// DB接続
$pdo = db_conn();

// データベースからユーザー一覧を取得 prepareでなくqueryメソッドでも可だが、prepareの方がセキュリティ的には良い
$stmtUsers = $pdo->prepare('SELECT * FROM kadai11_users_table');
$stmtUsers->execute();
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// データベースから投稿一覧を取得
$stmtPosts = $pdo->prepare('SELECT * FROM kadai11_msgs_table');
$stmtPosts->execute();
$posts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hamburger menu -->
<nav>
  <button id="button" type="button" class="fixed top-3 right-6 z-10 text-slate-600 hover:bg-white transition-colors duration-300 p-1 rounded-md">
    <i id="bars" class="fa-solid fa-bars fa-2x"></i>
  </button>
  <ul id="menu" class="fixed top-0 left-0 z-0 w-full h-screen md:h-20 translate-x-full bg-[#8DB1CF] bg-opacity-90 md:bg-opacity-100 text-center text-xl font-bold text-white transition-all ease-linear flex flex-col justify-center items-center md:flex-row md:px-20">
    <button onclick="showUsers()" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1] text-slate-600 hover:text-slate-600 transition-colors duration-300 p-2 m-2">登録者一覧</button>
    <button onclick="showPosts()" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1] text-slate-600 hover:text-slate-600 transition-colors duration-300 p-2 m-2">投稿一覧</button>
    <button onclick="location.href='index.php'" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1]  text-slate-600 hover:text-slate-600 transition-colors duration-300 p-2 m-2">ホーム</button>
  </ul>
</nav>

<!-- Main[Start] -->
<div class="w-[90vw] min-h-screen flex flex-col sm:flex-row bg-white rounded-lg">
  <!-- Display area[Start] -->
  <div id="contentArea" class="w-full border-t sm:border">
    <!-- 登録者一覧 管理画面表示時に最初から見せておく -->
    <div id="userList">
      <form id="deleteUserForm" method="POST" action="delete_multiple.php" onsubmit="return confirm('選択したユーザーを削除しますか？');">
        <h2 class="text-center text-xl mx-auto mt-4 sm:mb-4">登録者一覧</h2>
        <div class="flex justify-center mt-4">
          <!-- 管理者だった場合に表示(当たり前だが) -->
          <?php if ($_SESSION['kanri'] === 1) : ?>
            <button type="submit" class="w-2/3 md:w-1/4 border-2 rounded-md border-[#B33030] text-white md:text-[#B33030]  bg-[#B33030] md:bg-transparent md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 mb-4 p-2 sm:m-2"><i class="fas fa-trash-alt"></i> 選択した項目を削除</button>
          <?php endif; ?>
        </div>
        <ul>
          <!-- 取得したデータの表示 -->
          <?php foreach ($users as $user) : ?>
            <label class="block border-t sm:border-b sm:border-t-0 p-4 mb-4 cursor-pointer">
              <input type="checkbox" name="delete_ids[]" value="<?= $user['id'] ?>" class="mr-2">
              <div>
                <strong>ID: </strong><?php echo h($user['id']); ?><br>
                <strong>ログインID: </strong><?php echo h($user['lid']); ?><br>
                <strong>ユーザー名: </strong><?php echo h($user['username']); ?><br>
                <strong>EMAIL: </strong><?php echo h($user['email']); ?><br>
                <strong>ユーザータイプ: </strong><?php echo h($user['kanri_flg'] == 1 ? '管理者' : '一般ユーザー'); ?><br>
                <strong>登録日時: </strong><?php echo h($user['indate']); ?><br>
              </div>
            </label>
          <?php endforeach; ?>
        </ul>
      </form>
    </div>
    <!-- 投稿一覧 ボタンクリックで表示を切り替える -->
    <div id="postList" class="hidden">
      <h2 class="text-center text-xl mx-auto mt-4 sm:mb-4">投稿一覧</h2>
      <form id="deletePostForm" method="POST" action="delete_multiple.php" onsubmit="return confirm('選択した投稿を削除しますか？');">
        <div class="flex justify-center mt-4">
          <?php if ($_SESSION['kanri'] === 1) : ?>
            <button id="delete" type="submit" class="w-2/3 md:w-1/4 border-2 rounded-md border-[#B33030] text-white md:text-[#B33030]  bg-[#B33030] md:bg-transparent md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 mb-4 p-2 sm:m-2"><i class="fas fa-trash-alt"></i> 選択した項目を削除</button>
          <?php endif; ?>
        </div>
        <ul>
          <!-- 取得したデータの表示 -->
          <?php foreach ($posts as $post) : ?>
            <label class="block border-t sm:border-b sm:border-t-0 p-4 mb-4 cursor-pointer">
              <input type="checkbox" name="delete_ids[]" value="<?= $post['id'] ?>" class="mr-2">
              <div>
                <strong>ユーザー名: </strong><?php echo h($post['name']); ?><br>
                <strong>内容: </strong><?php echo h($post['message']); ?><br>
                <?php if (isset($post['picture_path'])) : ?>
                  <strong>画像: </strong><br>
                  <img src="<?php echo h($post['picture_path']); ?>" alt="写真" class="max-w-100% max-h-[300px] my-2">
                <?php endif; ?>
                <strong>投稿日時: </strong><?php echo h($post['date']); ?><br>
                <?php if (isset($post['updated_at'])) : ?>
                  <strong>更新日時: </strong><?php echo h($post['updated_at']); ?><br>
                <?php endif; ?>
              </div>
            </label>
          <?php endforeach; ?>
        </ul>
      </form>
    </div>
  </div>
  <!-- Display area[End] -->
</div>
<!-- Main[End] -->


<script>
  // 登録者一覧と投稿一覧の表示をクリックで切り替える処理
  function showUsers() {
    document.getElementById('userList').classList.remove('hidden');
    document.getElementById('postList').classList.add('hidden');
  }

  function showPosts() {
    document.getElementById('userList').classList.add('hidden');
    document.getElementById('postList').classList.remove('hidden');
  }

  // ハンバーガーメニューの切り替え処理
  button.addEventListener('click', event => {
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
</script>

<!-- Footer -->
<?php include 'foot.php'; ?>