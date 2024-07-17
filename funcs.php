<?php
//共通に使う関数を記述
//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//リダイレクト関数: redirect($file_name)
function redirect($file_name)
{
    header('Location: ' . $file_name);
    exit();
}

// ファイルアップロード関数
function uploadFile($file, $uploadDir = 'img/upload/')
{
    // ファイルがアップロードされていない場合は null を返す
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('ファイルアップロードエラー: ' . $file['error']);
    }

    // ファイルサイズの制限 (例: 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('ファイルサイズが大きすぎます');
    }

    // 許可する拡張子
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        throw new Exception('許可されていないファイル形式です');
    }

    // MIMEタイプのチェック
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mimeType, $allowedMimeTypes)) {
        throw new Exception('許可されていないファイル形式です');
    }

    // ユニークなファイル名を生成
    $fileName = bin2hex(random_bytes(16)) . '.' . $extension;
    $uploadFile = $uploadDir . $fileName;

    // ファイルを移動
    if (!move_uploaded_file($file['tmp_name'], $uploadFile)) {
        throw new Exception('ファイルの移動に失敗しました');
    }

    return $uploadFile;
}


//SQLエラー
function sql_error($stmt)
{
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit('SQLError:' . $error[2]);
}

// ログインチェク処理 loginCheck()
// 以下、セッションID持ってたら、ok
// 持ってなければ、閲覧できない処理にする。
// セッションID持っていない or 持っててもサーバーの値と違う場合
function loginCheck()
{
    if (!isset($_SESSION['chk_ssid']) || $_SESSION['chk_ssid'] !== session_id()) {
        exit('LOGIN ERROR');
    }
    session_regenerate_id(true);
    $_SESSION['chk_ssid'] = session_id();
}

// いいね数を取得する関数
function getLikeCount($pdo, $post_id)
{
    // テーブルからpost_idのレコード数(いいね数)を取得 COUNT(*)集計関数
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM kadai11_likes WHERE post_id = :post_id");
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);  // 整数型でバインド
    $stmt->execute();
    // 1つのカラムの値(COUNT(*)の結果)を取得
    return (int)$stmt->fetchColumn();  // デフォは文字列なので、取得した値を整数型にする
}

// いいねされた投稿にユーザーがいいねをしているか確認する関数
function checkUserLike($pdo, $user_id, $post_id)
{
    // WHERE以降の条件でテーブルからデータ取得
    $stmt = $pdo->prepare("SELECT * FROM kadai11_likes WHERE user_id = :user_id AND post_id = :post_id");
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    // fetchで取得した行があればtrue 否定条件で書く方がいいらしい
    return $stmt->fetch() !== false;  // 真偽判定なのでデータは1つでいい
}