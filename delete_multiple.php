<?php
session_start();  // セッション開始
require_once('funcs.php');  // 関数群の呼び出し
require_once('db_conn.php');
loginCheck();  // ログインチェック

// DB接続
$pdo = db_conn();

// POSTデータの確認→POSTの中にdelete_idsがあるかを確認→変数格納(中身は削除する投稿のID)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ids'])) {
  $delete_ids = $_POST['delete_ids'];

  // delete_idsが配列か確認＆配列の要素数が0より多い=1つ以上のデータがあるか確認

  if (is_array($delete_ids) && count($delete_ids) > 0) {
    try {
      // トランザクション開始 以降の処理が全て成功した場合のみコミットすることで整合性を保つ
      $pdo->beginTransaction();

      // 投稿の削除クエリ
      // INで複数削除可能 SQLインフェクション対策で?(プレースホルダ)配列作成→,区切りの文字列に変換
      $postStmt = $pdo->prepare("DELETE FROM kadai11_msgs_table WHERE id IN (" . implode(',', array_fill(0, count($delete_ids), '?')) . ")");
      
      // 各IDをkeyにプレースホルダに値をバインド
      foreach ($delete_ids as $k => $id) {
        $postStmt->bindValue(($k + 1), $id, PDO::PARAM_INT);
      }
      
      $postStmt->execute();

      // ユーザーの削除クエリ
      $userStmt = $pdo->prepare("DELETE FROM kadai11_users_table WHERE id IN (" . implode(',', array_fill(0, count($delete_ids), '?')) . ")");
      
      // 各IDをkeyにプレースホルダに値をバインド
      foreach ($delete_ids as $k => $id) {
        $userStmt->bindValue(($k + 1), $id, PDO::PARAM_INT);
      }
      
      $userStmt->execute();

      // トランザクションをコミットし、操作を確定→データベースに反映
      $pdo->commit();
    } catch (PDOException $e) {  // PDO操作中に発生する例外をキャッチした時に以下実行
      // ロールバック(変更を元に戻す)
      $pdo->rollBack();
      echo "処理中に問題が発生しました。もう一度お試しください。";
      error_log("トランザクションのロールバックが実行されました。");  //  エラーログ記録
    }
  }
}

// 管理者画面にリダイレクト
redirect('admin.php');
?>
