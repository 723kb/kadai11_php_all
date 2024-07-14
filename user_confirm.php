<?php
include 'head.php';  // Header
require_once('funcs.php');  // 関数群の呼び出し

// POSTデータの取得
$lid = isset($_POST['lid']) ? h($_POST['lid']) : '';
$username = isset($_POST['username']) ? h($_POST['username']) : '';
$email = isset($_POST['email']) ? h($_POST['email']) : '';
$password_display = '表示しておりません'; // パスワードは表示しないため、ダミーの文字列を表示する
$pass_confirm_display = '表示しておりません'; // 確認用パスワードも同様に表示しない
$user_type = isset($_POST['user_type']) ? h($_POST['user_type']) : 'normal'; // ユーザータイプのデフォルトは一般ユーザー
$admin_password = $_POST['admin_password'];  // 管理者登録の場合の専用パスワード
?>

<!-- Main[Start] -->
<div class="min-h-screen w-5/6 flex flex-col items-center bg-[#F1F6F5] rounded-lg p-4">
  <h2 class="text-lg md:text-xl lg:text-2xl mb-4">入力内容の確認</h2>
  <div class="w-full flex flex-col justify-center items-start m-2 p-4 rounded-lg bg-white">
    <div class="p-4">
      <label for="lid" class="text-sm sm:text-base md:text-lg lg:text-xl">ログインID：</label>
      <p class="w-full h-11 p-2 rounded-md"><?php echo $lid; ?></p>
    </div>
    <div class="p-4">
      <label for="username" class="text-sm sm:text-base md:text-lg lg:text-xl">ユーザー名：</label>
      <p class="w-full h-11 p-2 rounded-md"><?php echo $username; ?></p>
    </div>
    <div class="p-4">
      <label class="text-sm sm:text-base md:text-lg lg:text-xl">EMAIL：</label>
      <p class="w-full h-11 p-2 rounded-md"><?php echo $email; ?></p>
    </div>
    <div class="p-4">
      <label class="text-sm sm:text-base md:text-lg lg:text-xl">PASSWORD：</label>
      <p class="w-full h-11 p-2 rounded-md"><?php echo $password_display; ?></p>
    </div>
    <div class="p-4">
      <label class="text-sm sm:text-base md:text-lg lg:text-xl">PASSWORD(確認用)：</label>
      <p class="w-full h-11 p-2 rounded-md"><?php echo $pass_confirm_display; ?></p>
    </div>
    <div class="p-4">
      <label class="text-sm sm:text-base md:text-lg lg:text-xl">ユーザータイプ：</label>
      <p class="w-full h-11 p-2 rounded-md"><?php echo ($user_type === 'admin') ? '管理者' : '一般ユーザー'; ?></p>
    </div>
  </div>
  <!-- 隠しフィールドでデータを登録 -->
  <form action="user_submit.php" method="post" class="w-full flex flex-col justify-center sm:items-center m-2">
    <input type="hidden" name="lid" value="<?php echo h($_POST['lid']); ?>">
    <input type="hidden" name="username" value="<?php echo h($_POST['username']); ?>">
    <input type="hidden" name="email" value="<?php echo h($_POST['email']); ?>">
    <input type="hidden" name="password" value="<?php echo h($_POST['password']); ?>">
    <input type="hidden" name="pass_confirm" value="<?php echo h($_POST['pass_confirm']); ?>">
    <input type="hidden" name="user_type" value="<?php echo h($_POST['user_type']); ?>">
    <input type="hidden" name="admin_password" value="<?php echo h($_POST['admin_password']); ?>">
    <div class="sm:w-full flex flex-col-reverse sm:flex-row justify-center sm:justify-around items-center m-2 p-2">
      <button type="button" onclick="history.back()" class="w-2/3 sm:w-1/3 md:w-1/4 border-2 rounded-md border-[#B33030] text-white md:text-[#B33030] bg-[#B33030] md:bg-transparent md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 p-2 m-2">戻る</button>
      <button type="submit" class="w-2/3 sm:w-1/3 md:w-1/4 border-2 rounded-md border-[#8DB1CF] text-slate-800 md:text-slate-600 bg-[#8DB1CF] md:bg-transparent md:hover:bg-[#8DB1CF] md:hover:text-white transition-colors duration-300 p-2 m-2">登録</button>
    </div>
  </form>
</div>
  <!-- Main[End] -->

  <!-- Footer -->
  <?php include 'foot.php'; ?>