<?php
session_start();  // セッション開始
require_once('funcs.php');  // 関数群の呼び出し
require_once('db_conn.php');
loginCheck();  // ログインチェック

// DB接続
$pdo = db_conn();

// ユーザーが編集ページにアクセスした時にGETでidを取得
$id = isset($_GET['id']) ? $_GET['id'] : null;

// idが指定されていない場合のエラーハンドリング
if ($id === null) {
  exit('編集対象のIDが指定されていません。');
}
// 編集したい内容をデータベースから取得
$stmt = $pdo->prepare('SELECT * FROM kadai11_msgs_table WHERE id = :id');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();

// 編集対象のデータが見つからない場合のエラーハンドリング
if (!$row) {
  exit('指定されたIDのデータが見つかりません。'); // または他の適切なエラーメッセージを表示
}

$error_message = ''; // エラーメッセージ初期化

// POSTリクエスト処理 ユーザーが編集フォームを送信した時に実行
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!isset($_POST['message']) || $_POST['message'] === '') {
    $error_message = '内容が入力されていません';
  } elseif (mb_strlen($_POST['message']) > 140) {
    $error_message = '内容は140文字以内で入力してください';
  }

  // エラーがなければ更新処理を実行する
  if (empty($error_message)) {
    // POSTデータを取得
    $id = $_POST['id'];
    $message = $_POST['message'];
    $oldPicturePath = $row['picture_path'];
    $picturePath = $oldPicturePath;  // デフォルトで既存の画像パスを使用

    // ファイルアップロード処理
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] !== UPLOAD_ERR_NO_FILE) {
      try {
        $newPicturePath = uploadFile($_FILES['picture']);
        if ($newPicturePath !== null) {
          // 新しい画像がアップロードされた場合、古い画像を削除
          if ($oldPicturePath && file_exists($oldPicturePath)) {
            unlink($oldPicturePath);
          }
          $picturePath = $newPicturePath;
        }
      } catch (Exception $e) {
        $error_message = $e->getMessage();
      }
    }

    // エラーがなければ更新処理を実行
    if (empty($error_message)) {
      // 更新SQL作成
      $stmt = $pdo->prepare('UPDATE kadai11_msgs_table SET message = :message, picture_path = :picture_path, updated_at = now() WHERE id = :id');
      $stmt->bindValue(':message', $message, PDO::PARAM_STR);
      $stmt->bindValue(':picture_path', $picturePath, PDO::PARAM_STR);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      // リダイレクト
      redirect('index.php');
    }
  }
}

// ユーザー名の取得(アカウントに登録された名前を使うので、セッションから取得する処理が必要)
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!-- 以下HTMLの表示 -->

<!-- Header -->
<?php include 'head.php'; ?>

<!-- Main[Start] -->
<div class="min-h-fit w-5/6 flex flex-col flex-1 items-center bg-[#F1F6F5] rounded-lg">
  <!-- Form[Start] -->
  <form method="POST" action="" enctype="multipart/form-data" class="w-full flex flex-col justify-center items-center m-2">
    <input type="hidden" name="id" value="<?= h($row['id']) ?>">
    <div class="w-full flex flex-col justify-center m-2">
      <div class="p-4">
        <label for="username" class="text-sm sm:text-base md:text-lg lg:text-xl">名前：</label>
        <!-- 取得したユーザー名を表示 -->
        <p id="username" class="w-full h-11 p-2 border rounded-md"><?= h($username) ?></p>
      </div>
      <div class="p-4">
        <label for="message" class="text-sm sm:text-base md:text-lg lg:text-xl">内容：</label>
        <textArea name="message" id="message" rows="4" cols="40" class="w-full p-2 border rounded-md"><?= h($row['message']) ?></textArea>
        <div id="messageError" class="text-red-500 mt-1 hidden">内容は140文字以内で入力してください</div>
        <!-- エラーメッセージ表示 -->
        <?php if (isset($error_message)) : ?>
          <div class="error-message mt-auto text-red-500">
            <?= h($error_message) ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="pb-4 px-4">
        <label for="picture" class="text-sm sm:text-base md:text-lg lg:text-xl">写真：</label>
        <div class="flex flex-col sm:flex-row justify-center items-center">
          <input type="file" name="picture" id="picture" accept="image/*" class="w-full h-11 py-2 my-2">
        </div>
      </div>
      <div class="flex justify-center">
        <!-- データに画像があれば表示 -->
        <?php if (!empty($row['picture_path'])) : ?>
          <img src="<?php echo h($row['picture_path']); ?>" alt="写真" id="preview" class="max-w-100% max-h-[300px]">
          <!-- データに画像がなければ空のsrcを持つimg要素を作成 -->
        <?php else : ?>
          <img src="" id="preview" class="hidden max-w-100% max-h-[300px]" alt="選択した画像のプレビュー">
        <?php endif; ?>
      </div>
      <div class="w-full mt-4 flex justify-around">
        <button type="button" onclick="location.href='index.php'" class="w-1/4 border border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1] transition-colors duration-300 p-2 m-2"><i class="fas fa-long-arrow-alt-left"></i></button>
        <button type="submit" class="w-1/4 border border-slate-200 rounded-md py-3 px-6 bg-[#93CCCA] md:bg-transparent md:hover:bg-[#93CCCA] transition-colors duration-300 p-2 m-2"><i class="fas fa-check-circle"></i></button>
      </div>
    </div>
  </form>
  <!-- Form[End] -->
</div>
<!-- Main[End] -->
</body>

<!-- edit.php用のjsファイルを読み込み -->
<script src="js/edit.js"></script>

<!-- Footer -->
<?php include 'foot.php'; ?>