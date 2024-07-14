<?php
session_start();  // セッション開始
require_once('funcs.php');  // 関数群の呼び出し

// ログインチェック loginCheck ();だとログインしてない人は閲覧できないのでここでは書かない
$is_logged_in = isset($_SESSION['chk_ssid']) && $_SESSION['chk_ssid'] === session_id();
?>

<!-- Header -->
<?php include 'head.php'; ?>

<!-- ログインチェックがtrueだった場合に入力検索エリアを表示 -->
<?php if ($is_logged_in) : ?>
    <?php
    // セッションIDの再生成
    session_regenerate_id(true);
    $_SESSION['chk_ssid'] = session_id();

    include 'post_form.php'; ?>
<?php endif; ?>

<!-- 表示エリア（全ユーザー） -->
<?php include 'post.php'; ?>

<!-- Footer -->
<?php include 'foot.php'; ?>

<!-- jsファイル読み込み -->
<script src="js/app.js"></script>
<script src="js/likes.js"></script>