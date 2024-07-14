<!-- Footer -->
<?php include 'head.php'; ?>

<!-- Main[Start] -->
<div class="min-h-screen w-5/6 flex flex-col items-center bg-[#F1F6F5] rounded-lg">
  <form action="user_confirm.php" method="post" class="w-full flex flex-col justify-center items-center m-2">
    <div class="w-full flex flex-col justify-center m-2">
      <div class="p-4">
        <label for="lid" class="text-sm sm:text-base md:text-lg lg:text-xl">ログインID：<br class="sm:hidden"><small class="text-slate-600"> (半角英数字で入力してください) </small></label>
        <input type="text" name="lid" id="lid" placeholder="example123" required class="w-full h-11 p-2 border rounded-md">
      </div>
      <div class="p-4">
        <label for="username" class="text-sm sm:text-base md:text-lg lg:text-xl">ユーザー名：<br class="sm:hidden"><small class="text-slate-600"> (絵文字は使用できません) </small></label>
        <input type="text" name="username" id="username" placeholder="テストユーザー1" required class="w-full h-11 p-2 border rounded-md">
      </div>
      <div class="p-4">
        <label for="email" class="text-sm sm:text-base md:text-lg lg:text-xl">EMAIL：</label>
        <input type="email" name="email" id="email" placeholder="example123@mail.com" required class="w-full h-11 p-2 border rounded-md">
      </div>
      <div class="p-4">
        <label for="password" class="text-sm sm:text-base md:text-lg lg:text-xl">PASSWORD：<br class="sm:hidden"><small class="text-slate-600"> (半角英数字で入力してください) </small></label>
        <input type="password" name="password" id="password" placeholder="pass01example" required class="w-full h-11 p-2 border rounded-md">
      </div>
      <div class="p-4">
        <label for="pass_confirm" class="text-sm sm:text-base md:text-lg lg:text-xl">PASSWORD(確認用)：</label>
        <input type="password" name="pass_confirm" id="pass_confirm" placeholder="pass01example" required class="w-full h-11 p-2 border rounded-md">
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
      <div class="sm:w-full flex flex-col-reverse sm:flex-row justify-center sm:justify-around items-center m-2 p-2">
        <button onclick="location.href='login.php'" class="w-2/3 sm:w-1/3 md:w-1/4 border-2 rounded-md border-[#B33030] text-white md:text-[#B33030] bg-[#B33030] md:bg-transparent md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 p-2 m-2">戻る</button>
        <button type="submit" class="w-2/3 sm:w-1/3 md:w-1/4 border-2 rounded-md border-[#8DB1CF] text-slate-800 md:text-slate-600 bg-[#8DB1CF] md:bg-transparent md:hover:bg-[#8DB1CF] md:hover:text-white transition-colors duration-300 p-2 m-2">次へ</button>
      </div>
    </div>
  </form>
</div>
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