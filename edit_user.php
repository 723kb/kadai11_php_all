<?php
session_start();  // セッション開始
require_once('funcs.php');  // 関数群の呼び出し
require_once('db_conn.php');
loginCheck();  // ログインチェック

// 管理者パスワードの定数を定義 普通はセキュリティ上こんな風にはしない
define('ADMIN_PASSWORD', '1111');

// DB接続
$pdo = db_conn();

// ログインしているユーザーのIDを取得し、セッションがセットされているか確認
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// ログインしているユーザーの情報を取得
if ($user_id) {
  $stmtUser = $pdo->prepare('SELECT * FROM kadai11_users_table WHERE id = :id');
  $stmtUser->bindValue(':id', $user_id, PDO::PARAM_INT);
  $stmtUser->execute();
  $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

  // ユーザー情報を変数に代入
  $lid = h($user['lid']);
  $username = h($user['username']);
  $email = h($user['email']);
  $user_type = $user['kanri_flg'] == 1 ? 'admin' : 'normal';
}

// フォーム送信があった場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $lid = h($_POST['lid']);
  $username = h($_POST['username']);
  $email = h($_POST['email']);
  $password = h($_POST['password']);
  $pass_confirm = h($_POST['pass_confirm']);
  $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
  $user_type = $_POST['user_type'];
  $admin_password = isset($_POST['admin_password']) ? h($_POST['admin_password']) : '';


  // パスワードと確認用パスワードが一致しているか確認
  if ($password !== $pass_confirm) {
    // 一致しない場合はエラーメッセージを表示して終了
    echo "パスワードと確認用パスワードが一致しません。";
    exit;
  }

  // 管理者フラグの設定
  $kanri_flg = 0; // デフォルトは一般ユーザー
  if ($user_type === 'admin' && $admin_password === ADMIN_PASSWORD) {
    $kanri_flg = 1; // 管理者として設定
  } elseif ($user_type === 'admin' && $admin_password !== ADMIN_PASSWORD) {
    // 管理者パスワードが間違っている場合
    echo "<p class='my-auto text-center'>管理者登録パスワードが正しくありません。</p>";
    exit;
  }

  // ユーザー情報更新クエリ
  $stmtUpdate = $pdo->prepare('UPDATE kadai11_users_table SET lid = :lid, username = :username, email = :email, kanri_flg = :kanri_flg, updated_at = now() WHERE id = :id');

  // バインドと実行
  $stmtUpdate->bindValue(':lid', $lid, PDO::PARAM_STR);
  $stmtUpdate->bindValue(':username', $username, PDO::PARAM_STR);
  $stmtUpdate->bindValue(':email', $email, PDO::PARAM_STR);
  $stmtUpdate->bindValue(':kanri_flg', $user_type === 'admin' ? 1 : 0, PDO::PARAM_INT);
  $stmtUpdate->bindValue(':id', $user_id, PDO::PARAM_INT);
  $stmtUpdate->execute();

// msgsテーブルの投稿者名の更新
$stmtUpdateMsgs = $pdo->prepare('UPDATE kadai11_msgs_table SET name = :new_name WHERE user_id = :user_id');
$stmtUpdateMsgs->bindValue(':new_name', $username, PDO::PARAM_STR); // 新しい名前
$stmtUpdateMsgs->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmtUpdateMsgs->execute();

  // パスワードの更新がある場合の処理
  if ($new_password) {
    $stmtUpdatePassword = $pdo->prepare('UPDATE kadai11_users_table SET password = :password WHERE id = :id');
    $stmtUpdatePassword->bindValue(':password', $new_password, PDO::PARAM_STR);
    $stmtUpdatePassword->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmtUpdatePassword->execute();
  }

  // 更新後のデータを再取得
  $stmtUser->execute();
  $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
  $lid = h($user['lid']);
  $username = h($user['username']);
  $email = h($user['email']);
  $user_type = $user['kanri_flg'] == 1 ? 'admin' : 'normal';
  $admin_password = $_POST['admin_password'];

  // リダイレクト
  redirect('my_page.php');
}
?>

<!-- 以下HTMLの表示 -->

<!-- Header -->
<?php include 'head.php'; ?>

<!-- Main[Start] -->
<form method="POST" action="" onsubmit="return confirm('変更を登録しますか？');" class="min-h-screen w-5/6 flex flex-col items-center bg-[#F1F6F5] rounded-lg p-4">
  <h2 class="text-lg md:text-xl lg:text-2xl mb-4">登録情報の変更</h2>
  <div class="w-full flex flex-col justify-center items-start m-2 p-4 rounded-lg bg-white">
    <div class="p-4">
      <label for="lid" class="text-sm sm:text-base md:text-lg lg:text-xl">ログインID：</label>
      <input type="text" name="lid" value="<?= $lid; ?>" class="w-full h-11 p-2 rounded-md">
    </div>
    <div class="p-4">
      <label for="username" class="text-sm sm:text-base md:text-lg lg:text-xl">ユーザー名：</label>
      <input type="text" name="username" value="<?= $username; ?>" class="w-full h-11 p-2 rounded-md">
    </div>
    <div class="p-4">
      <label class="text-sm sm:text-base md:text-lg lg:text-xl">EMAIL：</label>
      <input type="email" name="email" value="<?= $email; ?>" class="w-full h-11 p-2 rounded-md">
    </div>
    <div class="p-4">
      <label class="text-sm sm:text-base md:text-lg lg:text-xl">PASSWORD：</label>
      <input type="password" name="password" class="w-full h-11 p-2 rounded-md" placeholder="新しいパスワードを入力してください">
    </div>
    <div class="p-4">
      <label class="text-sm sm:text-base md:text-lg lg:text-xl">PASSWORD(確認用)：</label>
      <input type="password" name="pass_confirm" class="w-full h-11 p-2 rounded-md" placeholder="新しいパスワードを再入力してください">
    </div>
    <div class="p-4">
      <label class="text-sm sm:text-base md:text-lg lg:text-xl">ユーザータイプ：<br class="sm:hidden"><small class="text-slate-600"> (管理者登録にはパスワードが必要です) </small></label><br>
      <input type="radio" id="normal_user" name="user_type" value="normal" checked>
      <label for="normal_user">一般ユーザー</label><br>
      <input type="radio" id="admin_user" name="user_type" value="admin">
      <label for="admin_user">管理者</label>
    </div>
    <!-- 管理者用のパスワード入力 -->
    <div id="admin_password_section" class="p-4 hidden">
      <label for="admin_password" class="text-sm sm:text-base md:text-lg lg:text-xl">管理者登録パスワード：</label>
      <input type="password" name="admin_password" id="admin_password" placeholder="管理者登録パスワードを入力してください" class="w-full h-11 p-2 border rounded-md">
    </div>
  </div>
  <div class="sm:w-full flex flex-col-reverse sm:flex-row justify-center sm:justify-around items-center m-2 p-2">
    <button type="button" onclick="history.back()" class="w-2/3 sm:w-1/3 md:w-1/4 border-2 rounded-md border-[#B33030] text-white md:text-[#B33030] bg-[#B33030] md:bg-transparent md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 p-2 m-2">戻る</button>
    <button type="submit" class="w-2/3 sm:w-1/3 md:w-1/4 border-2 rounded-md border-[#8DB1CF] text-slate-800 md:text-slate-600 bg-[#8DB1CF] md:bg-transparent md:hover:bg-[#8DB1CF] md:hover:text-white transition-colors duration-300 p-2 m-2">登録</button>
  </div>
</form>

<!-- Main[End] -->

<script>
  // 管理者ラジオボタンが選択された時の処理
  document.getElementById('admin_user').addEventListener('change', function() {
    document.getElementById('admin_password_section').classList.remove('hidden');
  });

  // 一般ユーザーラジオボタンが選択された時の処理
  document.getElementById('normal_user').addEventListener('change', function() {
    document.getElementById('admin_password_section').classList.add('hidden');
  });
</script>

<!-- Footer -->
<?php include 'foot.php'; ?>