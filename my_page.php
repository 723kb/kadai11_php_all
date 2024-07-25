<?php
session_start();  // セッション開始
include 'head.php';  // Header

require_once('db_conn.php');  // 関数群の呼び出し
require_once('funcs.php');
loginCheck();  // ログインチェック

// DB接続
$pdo = db_conn();

// ログインしているユーザーのIDを取得し、セッションがセットされているか確認
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
// $username = ($_SESSION['username'] === $row['name']);
// var_dump($user_id);

// ログインしているユーザーの情報を取得
if ($user_id) {
  $stmtUser = $pdo->prepare('SELECT * FROM kadai11_users_table WHERE id = :id');
  $stmtUser->bindValue(':id', $user_id, PDO::PARAM_INT);
  $stmtUser->execute();
  $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

  // データベースから投稿一覧を取得
  $stmtPosts = $pdo->prepare('SELECT m.*, u.username AS name FROM kadai11_msgs_table m JOIN kadai11_users_table u ON m.name = u.username WHERE u.id = :id');
  $stmtPosts->bindValue(':id', $user_id, PDO::PARAM_INT);
  $stmtPosts->execute();
  $posts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- Hamburger menu -->
<nav>
  <button id="menuButton" type="button" class="fixed top-3 right-6 z-10 text-slate-600 hover:bg-white transition-colors duration-300 p-1 rounded-md">
    <i id="bars" class="fa-solid fa-bars fa-2x"></i>
  </button>
  <ul id="menu" class="fixed top-0 left-0 z-0 w-full h-screen md:h-20 translate-x-full bg-[#8DB1CF] bg-opacity-90 md:bg-opacity-100 text-center text-xl font-bold text-white transition-all ease-linear flex flex-col justify-center items-center md:flex-row md:px-10">
    <!-- 管理者だった場合に管理画面ボタンを表示 -->
    <?php if ($_SESSION['kanri'] === 1) : ?>
      <button onclick="location.href='admin.php'" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1]  text-slate-600 hover:text-slate-600 transition-colors duration-300 m-2">管理画面</button>
    <?php endif ?>
    <button onclick="showUsers()" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1] text-slate-600 hover:text-slate-600 transition-colors duration-300 p-2 m-2">登録情報</button>
    <button onclick="showPosts()" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1] text-slate-600 hover:text-slate-600 transition-colors duration-300 p-2 m-2">投稿一覧</button>
    <button onclick="location.href='index.php'" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1]  text-slate-600 hover:text-slate-600 transition-colors duration-300 p-2 m-2">ホーム</button>
    <button onclick="location.href='logout.php'" class="w-3/4 md:w-1/3 border-2 border-[#B33030] rounded-md py-3 px-6 text-white md:text-[#B33030]  bg-[#B33030] md:bg-transparent md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 m-2">ログアウト</button>
  </ul>
</nav>

<!-- Main[Start] -->
<div class="w-[90vw] min-h-[70vh] flex flex-col bg-white rounded-lg">
  <!-- Display area[Start] -->
  <div id="contentArea" class="w-full h-full">
    <div id="userInfo">
      <h2 class="text-center text-xl mx-auto mt-4 sm:mb-4">登録情報</h2>
      <!-- 取得したユーザーの情報の表示 -->
      <div class="m-2 p-2">
        <strong>ログインID: </strong><?= h($user['lid']); ?><br>
        <strong>ユーザー名: </strong><?= h($user['username']); ?><br>
        <strong>EMAIL: </strong><?= h($user['email']); ?><br>
        <strong>ユーザータイプ: </strong><?= h($user['kanri_flg'] == 1 ? '管理者' : '一般ユーザー'); ?><br>
        <strong>登録日時: </strong><?= h($user['indate']); ?><br>
      </div>
      <div class="flex justify-center mb-auto">
        <!-- 編集ボタン -->
        <form method="get" action="edit_user.php" class="w-2/3 md:w-1/4 mb-4 sm:m-2">
          <input type="hidden" name="id" value="<?= $user['id'] ?>">
          <button type="submit" class="w-full border-2 rounded-md border-[#8DB1CF] text-slate-600 bg-[#8DB1CF] hover:bg-white hover:text-[#8DB1CF] transition-colors duration-300 p-2">
            <i class="fas fa-edit"></i> 編集
          </button>
        </form>

        <!-- 削除ボタン -->
        <form method="post" action="delete_user.php" class="w-2/3 md:w-1/4 mb-4 sm:m-2" onsubmit="return confirm('本当に退会しますか？');">
          <input type="hidden" name="id" value="<?= $user['id'] ?>">
          <button type="submit" class="w-full border-2 rounded-md border-[#B33030] text-white bg-[#B33030] hover:bg-transparent hover:text-[#B33030] transition-colors duration-300 p-2">
            <i class="fas fa-trash-alt"></i> 退会
          </button>
        </form>
      </div>
    </div>
    <!-- 投稿一覧 ボタンクリックで表示を切り替える -->
    <div id="postList" class="hidden">
      <h2 class="text-center text-xl mx-auto mt-4 sm:mb-4">投稿一覧</h2>
      <form id="deletePostForm" method="POST" action="delete_multiple.php" onsubmit="return confirm('選択した投稿を削除しますか？');">
        <div class="flex justify-center mt-4">
          <button id="delete" type="submit" class="w-2/3 md:w-1/4 border-2 rounded-md border-[#B33030] text-white md:text-[#B33030]  bg-[#B33030] md:bg-transparent md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 mb-4 p-2 sm:m-2"><i class="fas fa-trash-alt"></i> 選択した項目を削除</button>
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
                <?php if (isset($post['latitude']) && isset($post['longitude'])) : ?>
                  <strong><a href="map.php?id=' . $row['id'] . '">位置情報あり</a></strong><br>
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

<script src="js/hamMenu.js"></script>

<!-- Footer -->
<?php include 'foot.php'; ?>