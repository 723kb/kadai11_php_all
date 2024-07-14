<?php

session_start();
require_once('funcs.php'); 
require_once('db_conn.php');

// DB接続
$pdo = db_conn();

// ログインチェック
$is_logged_in = isset($_SESSION['chk_ssid']) && $_SESSION['chk_ssid'] === session_id();
$response = ['success' => false, 'message' => 'エラーが発生しました。'];

if ($is_logged_in && isset($_POST['post_id'])) {
    // 整数型にすることで意図しないクエリ実行を防ぐ＆整合性を保つ
    $post_id = (int)$_POST['post_id'];

    if (isset($_SESSION['user_id'])) {
        $user_id = (int)$_SESSION['user_id'];
    } else {
        echo json_encode(['success' => false, 'message' => 'ユーザーIDが取得できませんでした']);
        exit;
    }

    try {
        // ユーザーがすでにいいねしているかどうかをチェック
        $stmt = $pdo->prepare("SELECT * FROM kadai11_likes WHERE user_id = :user_id AND post_id = :post_id");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // すでにいいねしている場合は削除
            $stmt = $pdo->prepare("DELETE FROM kadai11_likes WHERE user_id = :user_id AND post_id = :post_id");
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
            $is_liked = false;
        } else {
            // いいねしていない場合は追加
            $stmt = $pdo->prepare("INSERT INTO kadai11_likes (user_id, post_id) VALUES (:user_id, :post_id)");
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
            $is_liked = true;
        }

        // 更新後のいいね数を取得
        $stmt = $pdo->prepare("SELECT COUNT(*) AS like_count FROM kadai11_likes WHERE post_id = :post_id");
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        $like_count = $stmt->fetchColumn();
        
        // レスポンスデータの設定(配列)
        $response = [
            'success' => true,  // 処理完了
            'like_count' => $like_count,  // 更新後のいいね数
            'is_liked' => $is_liked  // 現在のいいねの状態
        ];
    } catch (PDOException $e) {
        // データベースエラー時の処理
        error_log('PDOException - ' . $e->getMessage(), 0);
        $response = [
            'success' => false,
            'message' => 'データベースエラーが発生しました'
        ];
    }
} else {
    // ログインしていない場合や投稿IDがPOSTされていない場合の処理
    $response = [
        'success' => false,
        'message' => 'ログイン状態が無効です。'
    ];
}

// JSON形式でレスポンスを出力してスクリプトを終了
echo json_encode($response);
exit;
