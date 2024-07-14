<!-- Header -->
<?php include 'head.php'; ?>

<!-- Main[Start] -->
<div class="h-[60vh] w-[60vw] flex flex-col justify-center items-center bg-[#F1F6F5] rounded-lg mt-auto p-4">

    <?php
    session_start();  // セッション開始
    require_once('funcs.php');  // 関数群の呼び出し
    require_once('db_conn.php');

    // 管理者パスワードの定数を定義 普通はセキュリティ上こんな風にはしない
    define('ADMIN_PASSWORD', '1111');

    // DB接続
    $pdo = db_conn();

    // POSTデータの取得
    $lid = isset($_POST['lid']) ? h($_POST['lid']) : '';
    $username = isset($_POST['username']) ? h($_POST['username']) : '';
    $email = isset($_POST['email']) ? h($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $pass_confirm = isset($_POST['pass_confirm']) ? $_POST['pass_confirm'] : '';
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : 'normal'; // ユーザータイプのデフォルトは一般ユーザー
    $admin_password = isset($_POST['admin_password']) ? $_POST['admin_password'] : '';

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
    }

    // パスワードのハッシュ化
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // データベースにユーザー情報登録
    $stmt = $pdo->prepare("INSERT INTO kadai11_users_table (lid,  username, email, password, kanri_flg, life_flg) VALUES (:lid, :username, :email, :password, :kanri_flg, :life_flg)");
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
    $stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT); // 管理者フラグを設定
    $stmt->bindValue(':life_flg', 1, PDO::PARAM_INT); // life_flgはデフォルトで1を設定
    $status = $stmt->execute();

    if ($status === true) {
        if ($kanri_flg === 1) {
            $message = "管理者として登録しました。<br>3秒後に遷移します。";
        } else {
            $message = "一般ユーザーとして登録しました。<br>3秒後に遷移します。";
        }

        // メッセージを表示し、3秒後にリダイレクトする
        echo "<p class='my-auto text-center'>" . $message . "</p>";
        echo "<script>
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 3000);
            </script>";
    } else {
        // 登録失敗時の処理
        echo "<p class='my-auto text-center>ユーザー登録に失敗しました。</p>";
        echo "<script>
            setTimeout(function() {
                window.location.href = 'user.php';
            }, 3000);
            </script>";
    }
    ?>
</div>

<!-- Footer -->
<?php include 'foot.php'; ?>