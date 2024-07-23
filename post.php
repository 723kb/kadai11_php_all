<?php
// セッションがまだ開始されていない場合にのみ session_start() を呼び出す
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// ログインチェック
$is_logged_in = isset($_SESSION['chk_ssid']) && $_SESSION['chk_ssid'] === session_id();
?>

<!-- is_logged_inがfalseの表示[start] -->
<?php if (!$is_logged_in) : ?>

  <div class="h-[90vh] w-5/6 flex flex-col flex-1 items-center bg-[#F1F6F5] rounded-lg">
    <div class="w-full flex flex-col justify-center">
      <p class="text-center m-2 p-2">ログインすると投稿できます。<br class="sm:hidden">アカウントの作成は新規登録から！</p>
      <div class="sm:w-full flex flex-col-reverse sm:flex-row justify-center sm:justify-around items-center m-2 p-2 sm:pb-4">
        <button onclick="location.href='user.php'" class="w-2/3 sm:w-1/3 md:w-1/4 border-2 rounded-md border-[#8DB1CF] text-slate-800 md:text-slate-600 bg-[#8DB1CF] md:bg-transparent md:hover:bg-[#8DB1CF] md:hover:text-white transition-colors duration-300 p-2 m-2">新規登録</button>
        <button onclick="location.href='login.php'" class="w-2/3 sm:w-1/3 md:w-1/4 border-2 rounded-md border-[#4CAF50] text-white md:text-[#4CAF50] bg-[#4CAF50] md:bg-transparent md:hover:bg-[#4CAF50] md:hover:text-white transition-colors duration-300 p-2 m-2">ログイン</button>
      </div>
    </div>
  <?php endif ?>
  <!-- is_logged_inがfalseの表示[end] -->

  <!-- Posts[start] -->
  <div class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
    <!-- 以下に投稿内容が表示される -->

    <?php
    require_once('funcs.php');
    require_once('db_conn.php');

    // DB接続
    $pdo = db_conn();

    // ログインしているユーザーの情報を取得
    $username = '';  // usernameの初期化
    if ($is_logged_in && isset($_SESSION['lid'])) {  // ログインしていてlidがある場合
      $stmt = $pdo->prepare("SELECT username FROM kadai11_users_table WHERE lid = :lid");
      $stmt->bindValue(':lid', $_SESSION['lid'], PDO::PARAM_STR);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($user && isset($user['username'])) {  // $userが存在してusernameキーがあれば変数に格納
        $username = $user['username'];
      }
    }

    // データ登録処理
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {  // POSTで送信されたか確認
      if (
        // $_POST['message']がセットされていないor空文字(=未入力)ならtrue
        !isset($_POST['message']) || $_POST['message'] === ''
      ) { // 上記がtrueならエラーを出力
        exit('内容が入力されていません');
      }

      // メッセージが140文字を超えている場合はエラーとして処理を中断する
      if (mb_strlen($_POST['message']) > 140) {
        exit('内容は140文字以内で入力してください');
      }

      // セッションから名前を取得、未設定の場合はデフォルトで「名無しさん」
      $name = isset($_SESSION['username']) ? $_SESSION['username'] : '名無しさん';
      $message = $_POST['message'];
      // フォームから送られた位置情報を受け取る
      $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null; // 緯度を取得（拒否時はnull）
      $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null; // 経度を取得（拒否時はnull）
      $picturePath = null;

      // ファイルアップロード処理
      if (isset($_FILES['picture'])) {
        try {
          $picturePath = uploadFile($_FILES['picture']);
        } catch (Exception $e) {
          exit($e->getMessage());
        }
      }

      // データベースに保存
      if ($picturePath !== null) {
        // 写真がある場合
        $stmt = $pdo->prepare('INSERT INTO kadai11_msgs_table(id, name, message, picture_path, latitude, longitude, date) VALUES(NULL, :name, :message, :picture_path, :latitude , :longitude, now())');
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':message', $message, PDO::PARAM_STR);
        $stmt->bindValue(':picture_path', $picturePath, PDO::PARAM_STR);
        $stmt->bindValue(':latitude', $latitude !== '' ? $latitude : null, $latitude !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':longitude', $longitude !== '' ? $longitude : null, $longitude !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
      } else {
        // 写真がない場合
        $stmt = $pdo->prepare('INSERT INTO kadai11_msgs_table(id, name, message, latitude, longitude, date) VALUES(NULL, :name, :message, :latitude , :longitude, now())');
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':message', $message, PDO::PARAM_STR);
        $stmt->bindValue(':latitude', $latitude !== '' ? $latitude : null, $latitude !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':longitude', $longitude !== '' ? $longitude : null, $longitude !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
      }

      $status = $stmt->execute();

      // リダイレクトなどの処理
      redirect('index.php');
    }

    // データ取得処理
    $searchWord = isset($_GET['search']) ? $_GET['search'] : '';
    $order = isset($_GET['order']) ? $_GET['order'] : 'desc';

    $sql = "
    SELECT m.*, u.username, 
            COALESCE(l.like_count, 0) AS like_count,
            CASE WHEN ul.user_id IS NOT NULL THEN 1 ELSE 0 END AS is_liked
    FROM kadai11_msgs_table m
    LEFT JOIN kadai11_users_table u ON m.name = u.username
    LEFT JOIN (
        SELECT post_id, COUNT(*) AS like_count
        FROM kadai11_likes
        GROUP BY post_id
    ) l ON m.id = l.post_id
    LEFT JOIN kadai11_likes ul ON m.id = ul.post_id AND ul.user_id = :user_id
    ";

    if ($searchWord) {
      $sql .= " WHERE m.message LIKE :searchWord";
    }
    $sql .= " ORDER BY m.date $order";

    $stmt = $pdo->prepare($sql);
    if ($searchWord) {
      $stmt->bindValue(':searchWord', '%' . $searchWord . '%', PDO::PARAM_STR);
    }
    $stmt->bindValue(':user_id', $is_logged_in ? $_SESSION['lid'] : null, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 投稿内容の表示
    foreach ($results as $row) {
      echo '<div class="border shadow-md rounded-md p-2 m-2 bg-white flex flex-col">';
      echo '<h3 class="font-bold text-sm sm:text-base lg:text-lg  mb-2">' . h($row['name']) . '</h3>';
      echo '<p class="text-sm sm:text-base lg:text-lg  mb-2">' . nl2br(h($row['message'])) . '</p>';


      // 写真部分にクラスとデータ属性を設定
      echo '<div class="rounded-md overflow-hidden w-full h-auto max-w-full max-h-96 picture-modal-trigger"';
      if (!empty($row['picture_path'])) {
        echo ' data-img-src="' . h($row['picture_path']) . '"'; // モーダルに表示する画像データ
      }
      echo '>';

      // pictureが空でなければ画像データを表示
      if (!empty($row['picture_path'])) {
        echo '<img src="' . $row['picture_path'] . '" alt="写真" class="w-full h-auto max-w-full max-h-[90vh] object-contain mb-2">';
      }
      echo '</div>';
      echo '<div class="mt-auto">';
      echo '<p class="text-sm sm:text-base lg:text-lg">投稿：' . h($row['date']) . '</p>';
      if ($row['updated_at']) {
        echo '<p class="text-sm sm:text-base lg:text-lg">更新：' . h($row['updated_at']) . '</p>';
      }
      echo '</div>';

      // いいねボタンといいね数の表示
      if ($is_logged_in) {
        echo '<div class="like-section" data-user-id="' . h($_SESSION['lid']) . '">';
        $like_class = $row['is_liked'] ? 'liked' : '';
        $heart_icon = $row['is_liked'] ? 'fa-solid' : 'fa-regular';
        echo '<button class="like-button mr-2' . $like_class . '" data-post-id="' . $row['id'] . '"><i class="' . $heart_icon . ' fa-heart"></i></button>';
        echo '<span class="like-count-number">' . $row['like_count'] . '</span>';
        echo '</div>';

        // 位置情報を投稿時に取得している場合
        if (!empty($row['latitude']) && !empty($row['longitude'])) {
          echo '<a href="map.php?id=' . $row['id'] . '">位置情報あり</a>';
        }

        echo '<div class="mt-2">';
        echo '<a href="select.php?id=' . $row['id'] . '" class="text-blue-500 hover:text-blue-700">投稿を見る</a>';
        echo '</div>';

        echo '<div class="flex justify-center">';
        // ログインしているユーザーが投稿者である場合に編集ボタンを表示
        if ($_SESSION['username'] === $row['name']) {
          echo '<button type="button" onclick="location.href=\'edit.php?id=' . $row['id'] . '\'" class="w-1/4 border-2 rounded-md border-[#93CCCA] md:border md:border-slate-200  text-[#93CCCA] md:bg-transparent md:text-inherit md:hover:bg-[#93CCCA] transition-colors duration-300 p-2 m-2"><i class="fas fa-edit"></i></button>';
        }

        // ログインしているユーザーが投稿者である場合、または管理者の場合に削除ボタンを表示
        if ($_SESSION['username'] === $row['name'] || $_SESSION['kanri'] === 1) {
          echo '<button type="button" onclick="location.href=\'delete.php?id=' . $row['id'] . '\'" class="w-1/4 border-2 rounded-md border-[#B33030] md:border md:border-slate-200  text-[#B33030] md:bg-transparent md:text-inherit md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 p-2 m-2"><i class="fas fa-trash-alt"></i></button>';
        }
        echo '</div>';
      }
      echo '</div>';
    }
    ?>
  </div>
  <!-- Posts[End] -->
  </div>
  <!-- Display area[End] -->
  </div>
  <!-- Main[End] -->