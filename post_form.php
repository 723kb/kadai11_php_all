<?php
// session_star();だとセッションの重複エラーに 削除するとログインエラー if文で条件付きで呼び出したらいけた
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once('funcs.php');  // 関数群の呼び出し
require_once('db_conn.php');
loginCheck();  // ログインチェック

// DB接続
$pdo = db_conn();

// ログインしているユーザーの情報を取得
if ($is_logged_in && isset($_SESSION['lid'])) {
  $stmt = $pdo->prepare("SELECT username FROM kadai11_users_table WHERE lid = :lid");
  $stmt->bindValue(':lid', $_SESSION['lid'], PDO::PARAM_STR);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($user && isset($user['username'])) {
    $username = $user['username'];
    $_SESSION['username'] = $username; // セッションにユーザー名を保存
  }
}
?>

<!-- Hamburger menu -->
<nav>
  <button id="menuButton" type="button" class="fixed top-3 right-6 z-50 text-slate-600 hover:bg-white transition-colors duration-300 p-1 rounded-md">
    <i id="bars" class="fa-solid fa-bars fa-2x"></i>
  </button>
  <ul id="menu" class="fixed top-0 left-0 z-10 w-full h-screen md:h-20 translate-x-full bg-[#8DB1CF] bg-opacity-90 md:bg-opacity-100 text-center text-xl font-bold text-white transition-all ease-linear flex flex-col justify-center items-center md:flex-row md:px-10">
    <button id="showSearchButton" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1] text-slate-600 hover:text-slate-600 transition-colors duration-300 m-2">
      <i class="fas fa-search"></i>
    </button>
    <button onclick="location.href='my_page.php'" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1] text-slate-600 hover:text-slate-600 transition-colors duration-300 m-2">マイページ</button>
    <button onclick="location.href='map.php'" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1] text-slate-600 hover:text-slate-600 transition-colors duration-300 m-2">マップ</button>
    <!-- 管理者だった場合に管理画面ボタンを表示 -->
    <?php if ($_SESSION['kanri'] === 1) : ?>
      <button onclick="location.href='admin.php'" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1]  text-slate-600 hover:text-slate-600 transition-colors duration-300 m-2">管理画面</button>
    <?php endif ?>
    <button onclick="location.href='logout.php'" class="w-3/4 md:w-1/3 border-2 border-[#B33030] rounded-md py-3 px-6 text-white md:text-[#B33030]  bg-[#B33030] md:bg-transparent md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 m-2">ログアウト</button>
  </ul>
</nav>

<!-- Main[Start] -->
<div id="main" class="min-h-screen w-5/6 flex flex-col flex-1 items-center bg-[#F1F6F5] rounded-lg">
  <!-- Login status -->
  <div class="w-full flex justify-center items-center p-2 m-2">
    <p id="username" class="text-center">ユーザー名： <?= h($username) ?> で<br class="sm:hidden">ログイン中</p>
  </div>

  <!-- Show search button -->
  <!-- <button id="showSearchButton" class="fixed top-6 right-4 bg-[#7895B2] hover:bg-[#AAC4FF] text-white hover:text-slate-700 transition-colors duration-300 py-2 px-4 rounded-full shadow-md">
    <i class="fas fa-search"></i>
  </button> -->

  <!-- Posting area[Start] -->
  <form method="POST" action="post.php" enctype="multipart/form-data" id="myForm" class="w-full flex flex-col justify-center items-center m-2">
    <div class="w-full flex flex-col justify-center m-2">
      <!-- Textarea -->
      <div class="p-4">
        <label for="message" class="text-sm sm:text-base md:text-lg lg:text-xl">内容：</label>
        <textArea name="message" id="message" placeholder="140字以内で内容を入力してください。" rows="4" cols="40" class="w-full p-2 border rounded-md"></textArea>
        <div id="messageError" class="text-red-500 text-lg mt-1 hidden">内容は140文字以内で入力してください</div>
      </div>
      <!-- Get location button -->
      <label class="pb-4 px-4 flex items-center cursor-pointer">
        <span class="text-sm sm:text-base md:text-lg lg:text-xl">現在地の取得：</span>
        <div class="relative w-14 h-7 bg-gray-200 rounded-full border-2 border-slate-200">
          <input type="checkbox" class="sr-only peer" onclick="getLocation(); toggleButton(this);">
          <span class="absolute inset-0 bg-gray-200 rounded-full transition peer-checked:bg-[#93CCCA]"></span>
          <span class="absolute inset-y-0 left-0 w-6 h-6 bg-white rounded-full transition-all peer-checked:translate-x-full shadow-md"></span>
        </div>
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
      </label>
      <!-- Photo selection -->
      <div class="pb-4 px-4">
        <label for="picture" class="text-sm sm:text-base md:text-lg lg:text-xl">写真：</label>
        <div class="flex flex-col sm:flex-row justify-center items-center">
          <input type="file" name="picture" id="picture" accept="image/*" onchange="previewFile()" class="w-full h-11 py-2 my-2"> <!-- accept="image/*" 画像ファイルのみを許可 -->
          <!-- Submit button -->
          <button type="submit" class="w-1/3 sm:w-1/4  border-2 rounded-md border-[#93CCCA] md:border md:border-slate-200 md:bg-transparent md:hover:bg-[#93CCCA] transition-colors duration-300 p-2 my-2"><i class="fas fa-paper-plane"></i></button>
        </div>
      </div>

      <!-- Photo preview area -->
      <div class="flex justify-center">
        <img src="" id="preview" class="hidden max-w-100% max-h-[300px]" alt="選択した画像のプレビュー">
      </div>
    </div>
  </form>
  <!-- Posting area[End] -->

  <!-- Search area[Start] -->
  <form method="GET" action="post.php" id="searchForm" class="w-full flex flex-col sm:flex-row items-center p-4  hidden">
    <div class="w-full sm:w-3/4  py-auto sm:ml-2">
      <label for="search" class="text-sm sm:text-base md:text-lg lg:text-xl">内容検索:</label>
      <input type="text" name="search" placeholder="キーワードで内容を検索" class="w-full h-11 p-2 border rounded-md sm:rounded-r-none" id="search" value="<?= h(isset($_GET['search']) ? $_GET['search'] : '') ?>">
    </div>
    <div class="w-1/2 sm:w-1/4 flex justify-around items-end sm:items-stretch mt-2 sm:pt-6 sm:mt-0 sm:mr-2">
      <button type="submit" id="searchButton" class="w-1/3 sm:w-1/2 border-2 rounded-md border-[#FAEAB1] sm:rounded-none md:border md:border-slate-200 md:bg-transparent md:hover:bg-[#FAEAB1] transition-colors duration-300 p-2 m-2 sm:mx-0 md:mt-3 md:py-2">
        <i class="fas fa-search"></i>
      </button>
      <button type="button" class="w-1/3 sm:w-1/2 border-2 rounded-md border-[#D1D1D1] sm:rounded-l-none md:border md:border-slate-200 md:bg-transparent md:hover:bg-[#D1D1D1] transition-colors duration-300 p-2 m-2 sm:mx-0 md:mt-3" onclick="clearSearch()">
        <i class="fas fa-times-circle"></i>
      </button>
    </div>
  </form>
  <!-- Search area[End] -->

  <!-- Display area[Start] -->
  <div class="w-full m-4 border-t flex flex-col items-center">
    <h2 class="text-md sm:text-lg md:text-xl lg:text-2xl text-center my-4 font-mochiy-pop-one">Posts</h2>
    <!-- SortButton -->
    <div class="w-1/2 flex justify-around mb-4">
      <button type="button" name="order" id="ascButton" value="asc" class="w-1/3 sm:w-1/4 border-2 rounded-md border-[#FFC4C4] md:border md:border-slate-200 md:bg-transparent md:hover:bg-[#FFC4C4] transition-colors duration-300 p-2 m-2">
        <i class="fas fa-sort-amount-up"></i>
      </button>
      <button type="button" name="order" id="descButton" value="desc" class="w-1/3 sm:w-1/4 border-2 rounded-md border-[#AAC4FF] md:border md:border-slate-200 md:bg-transparent md:hover:bg-[#AAC4FF] transition-colors duration-300 p-2 m-2">
        <i class="fas fa-sort-amount-down"></i>
      </button>
    </div>