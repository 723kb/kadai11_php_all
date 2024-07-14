<?php
session_start();  // セッション開始
require_once('funcs.php');  // 関数群の呼び出し
require_once('db_conn.php');
loginCheck();  // ログインチェック

// DB接続
$pdo = db_conn();

// 1.データの取得と表示
// idの存在と数値であることの確認 SQLでも定義しているがコードでも書いていた方が良い
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  exit('IDが不正です');
}
// GETでidを取得
$id = $_GET['id'];

// テーブルからデータ取得
$stmt = $pdo->prepare('SELECT * FROM kadai11_msgs_table WHERE id = :id');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// クエリの結果を確認 データがなければメッセージ表示し処理終了
if (!$row) {
  exit('該当するデータがありません');
}

// 2.データの削除
// POSTデータを受け取った場合(削除ボタンが押された時)に実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 削除SQL作成
  $stmt = $pdo->prepare('DELETE FROM kadai11_msgs_table WHERE id = :id');
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $status = $stmt->execute();

  // データ削除処理後
  if ($status) {
    redirect('index.php');
  } else {
    exit('削除に失敗しました。');
  }
}
?>

<!-- 以下HTMLの表示 -->

<!-- Header -->
<?php include 'head.php'; ?>

<!-- Main[Start] -->
<div class="px-4 min-h-fit w-5/6 flex flex-col flex-1  items-center bg-[#F1F6F5] rounded-lg">
  <div class="w-full max-h-full p-4 m-2 border rounded-md bg-white">
    <h2 class="text-lg font-semibold mb-2">以下の内容を削除しますか？</h2>
    <p><strong class="text-base sm:text-lg lg:text-xl">名前：</strong><?= h($row['name']) ?></p>
    <p class="mt-2"><strong class="text-base sm:text-lg lg:text-xl">内容：</strong><?= nl2br(h($row['message'])) ?></p>
    <!-- データに画像があれば表示 -->
    <?php if (!empty($row['picture_path'])) : ?>
      <div class="mt-2">
        <img src="<?php echo h($row['picture_path']); ?>" alt="写真" class="w-full max-w-full max-h-[90vh] object-contain">
      </div>
    <?php endif; ?>
    <p class="mt-2"><strong class="text-base sm:text-lg lg:text-xl">投稿：</strong><?= h($row['date']) ?></p>
  </div>
  <!-- Form[Start] -->
  <!-- 削除確認アラート追加 -->
  <form action="" method="POST" class="w-full my-4 flex justify-around" onsubmit="return confirm('本当に削除しますか？');">
    <input type="hidden" name="id" value="<?= $id ?>">
    <button type="button" onclick="location.href='index.php'" class="w-1/4 border border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1] transition-colors duration-300 p-2 m-2"><i class="fas fa-long-arrow-alt-left"></i></button>
    <button type="submit" class="w-1/4 border border-slate-200 rounded-md py-3 px-6 bg-[#B33030] text-white md:bg-transparent md:text-inherit md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 p-2 m-2"><i class="fas fa-trash-alt"></i></button>
  </form>
  <!-- Form[End] -->
</div>
<!-- Main[End] -->

<!-- Footer -->
<?php include 'foot.php'; ?>