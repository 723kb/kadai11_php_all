<?php
session_start();
require_once('funcs.php');
require_once('db_conn.php');

$is_logged_in = isset($_SESSION['chk_ssid']) && $_SESSION['chk_ssid'] === session_id();

// GET パラメータから投稿 ID を取得
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($post_id === 0) {
    header("Location: index.php");
    exit();
}

// データベースから投稿を取得
$pdo = db_conn();
$stmt = $pdo->prepare("
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
    WHERE m.id = :post_id
");
$stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $is_logged_in ? $_SESSION['lid'] : null, PDO::PARAM_STR);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header("Location: index.php");
    exit();
}
?>


    <?php include 'head.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4"><?= h($post['name']) ?>さんの投稿</h1>
            <p class="text-gray-700 mb-4"><?= nl2br(h($post['message'])) ?></p>
            
            <?php if (!empty($post['picture_path'])): ?>
                <div class="mb-4">
                    <img src="<?= h($post['picture_path']) ?>" alt="投稿画像" class="max-w-full h-auto rounded-lg">
                </div>
            <?php endif; ?>
            
            <div class="text-sm text-gray-500 mb-4">
                <p>投稿日時: <?= h($post['date']) ?></p>
                <?php if ($post['updated_at']): ?>
                    <p>更新日時: <?= h($post['updated_at']) ?></p>
                <?php endif; ?>
            </div>
            
            <?php if ($is_logged_in): ?>
                <div class="like-section" data-user-id="<?= h($_SESSION['lid']) ?>">
                    <?php
                    $like_class = $post['is_liked'] ? 'liked' : '';
                    $heart_icon = $post['is_liked'] ? 'fa-solid' : 'fa-regular';
                    ?>
                    <button class="like-button mr-2 <?= $like_class ?>" data-post-id="<?= $post['id'] ?>">
                        <i class="<?= $heart_icon ?> fa-heart"></i>
                    </button>
                    <span class="like-count-number"><?= $post['like_count'] ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($post['latitude']) && !empty($post['longitude'])): ?>
                <div class="mt-4">
                    <h2 class="text-xl font-bold mb-2">位置情報</h2>
                    <div id="map" class="h-64 rounded-lg"></div>
                </div>
                <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
                <script>
                    var map = L.map('map').setView([<?= $post['latitude'] ?>, <?= $post['longitude'] ?>], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                    L.marker([<?= $post['latitude'] ?>, <?= $post['longitude'] ?>]).addTo(map);
                </script>
            <?php endif; ?>
            
            <div class="flex justify-around mt-6">
                <a href="index.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    ホームへ
                </a>
                <a href="map.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    マップへ
                </a>
            </div>
            <div class="mt-6">

            </div>
        </div>
    </div>

    <script src="js/likes.js"></script>

    <?php include 'foot.php'; ?>