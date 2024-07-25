<?php
session_start();  // セッション開始
require_once('funcs.php');  // 関数群の呼び出し
require_once('db_conn.php');
loginCheck();  // ログインチェック

// DB接続
$pdo = db_conn();

// ログインしているユーザーのIDを取得し、セッションがセットされているか確認
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // ユーザーを無効化
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
// 一旦データベースから削除せずlife_flg=0にして残しておく→再登録の処理は未
    $stmt = $pdo->prepare('UPDATE kadai11_users_table SET life_flg = 0 WHERE id = :id');
    $stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status) {
        // セッションをクリアしてログアウト状態にする
        session_unset();
        session_destroy();

        // ホームページにリダイレクト
        redirect('index.php');
        exit;
    } else {
        exit('退会処理に失敗しました。');
    }
}