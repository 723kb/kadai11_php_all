<?php
require_once('funcs.php');
require_once('db_conn.php');

// DB接続
$pdo = db_conn();

try {
    // データベースからすべての位置情報を取得
    $stmt = $pdo->prepare("SELECT * FROM kadai11_msgs_table WHERE latitude AND longitude");
    $stmt->execute();
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($locations) {
        // 結果をJSON形式で出力
        header('Content-Type: application/json');
        echo json_encode($locations);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(array('error' => 'No locations found'));
    }
} catch (PDOException $e) {
    // エラーが発生した場合の処理
    http_response_code(500); // Internal Server Error
    echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
}
?>
