<?php
session_start();  // セッション開始
include 'head.php';  // Header

require_once('db_conn.php');  // 関数群の呼び出し
require_once('funcs.php');
loginCheck();  // ログインチェック

// DB接続
$pdo = db_conn();

// ログインしているユーザーのIDを取得し、セッションがセットされているか確認
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// URLパラメータからIDを取得
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// 特定の投稿データを取得（IDが指定されている場合）
$targetPost = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM kadai11_msgs_table WHERE id = :id AND latitude IS NOT NULL AND longitude IS NOT NULL");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $targetPost = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!-- Hamburger menu -->
<nav>
  <button id="menuButton" type="button" class="fixed top-3 right-6 z-50 text-slate-600 hover:bg-white transition-colors duration-300 p-1 rounded-md">
    <i id="bars" class="fa-solid fa-bars fa-2x"></i>
  </button>
  <ul id="menu" class="fixed top-0 left-0 z-10 w-full h-screen md:h-20 translate-x-full bg-[#8DB1CF] bg-opacity-90 md:bg-opacity-100 text-center text-xl font-bold text-white transition-all ease-linear flex flex-col justify-center items-center md:flex-row md:px-10">
    <button onclick="location.href='my_page.php'" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1] text-slate-600 hover:text-slate-600 transition-colors duration-300 m-2">マイページ</button>
    <!-- 管理者だった場合に管理画面ボタンを表示 -->
    <?php if ($_SESSION['kanri'] === 1) : ?>
      <button onclick="location.href='admin.php'" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1]  text-slate-600 hover:text-slate-600 transition-colors duration-300 m-2">管理画面</button>
    <?php endif ?>
    <button onclick="location.href='index.php'" class="w-3/4 md:w-1/3 border-2 border-[#D1D1D1] md:border-slate-200 rounded-md py-3 px-6 bg-[#D1D1D1] md:bg-transparent md:hover:bg-[#D1D1D1]  text-slate-600 hover:text-slate-600 transition-colors duration-300 m-2">ホーム</button>
    <button onclick="location.href='logout.php'" class="w-3/4 md:w-1/3 border-2 border-[#B33030] rounded-md py-3 px-6 text-white md:text-[#B33030]  bg-[#B33030] md:bg-transparent md:hover:bg-[#B33030] md:hover:text-white transition-colors duration-300 m-2">ログアウト</button>
  </ul>
</nav>

<!-- Map -->
<div id="map" class="h-[600px] w-full"></div>

<!-- Post list -->
<div class="container mx-auto my-4 px-4">
  <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-bold mb-4 text-center font-mochiy-pop-one">posts w/ location info</h2>
  <div class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
    <?php
    // 投稿を取得するクエリ
    $stmt = $pdo->query("SELECT * FROM kadai11_msgs_table WHERE latitude IS NOT NULL AND longitude IS NOT NULL ORDER BY date DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 投稿を表示
    foreach ($posts as $post) {
      echo '<div class="bg-white shadow-md rounded-md p-4  flex flex-col cursor-pointer post-item" data-id="' . h($post['id']) . '" data-lat="' . h($post['latitude']) . '" data-lng="' . h($post['longitude']) . '">';
      echo '<h3 class="font-bold text-sm sm:text-base lg:text-lg  mb-2">' . h($post['name']) . '</h3>';
      echo '<p class="text-sm sm:text-base lg:text-lg  mb-2">' . h($post['message']) . '</p>';

      // 写真部分にクラスとデータ属性を設定
      echo '<div class="rounded-md overflow-hidden w-full h-auto max-w-full max-h-96 picture-modal-trigger"';
      if (!empty($post['picture_path'])) {
        echo ' data-img-src="' . h($post['picture_path']) . '"'; // モーダルに表示する画像データ
      }
      echo '>';

      // pictureが空でなければ画像データを表示
      if (!empty($post['picture_path'])) {
        echo '<img src="' . $post['picture_path'] . '" alt="写真" class="w-full h-auto max-w-full max-h-[90vh] object-contain mb-2">';
      }
      echo '</div>';
      echo '<div class="mt-auto">';
      echo '<p class="text-sm sm:text-base lg:text-lg">投稿: ' . h($post['date']) . '</p>';
      if ($post['updated_at']) {
        echo '<p class="text-sm sm:text-base lg:text-lg">更新：' . h($post['updated_at']) . '</p>';
      }
      echo '<a href="select.php?id=' . h($post['id']) . '" class="text-blue-500 hover:text-blue-700">投稿を見る</a>';
      echo '</div>';
      echo '</div>';
    }
    ?>
  </div>
</div>

<!-- LeafletのJavaScript（OpenStreetMapライブラリ） -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  // 地図の初期化
  var map = L.map("map").setView([35.681236, 139.767125], 13);

  // OpenStreetMapのタイルレイヤーを追加
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map);

  var markers = {}; // マーカーを保存するオブジェクト

// 地図を特定の位置に移動させ、対応するマーカーを表示する関数
function centerMapAndShowPost(lat, lng, id) {
  // まず地図要素までスクロール
  const mapElement = document.getElementById('map');
  mapElement.scrollIntoView({ behavior: 'smooth', block: 'start' });

  // スクロールが完了したら地図を更新
  setTimeout(() => {
    map.setView([lat, lng], 15);
    if (markers[id]) {
      markers[id].openPopup();
    }
  }, 500); // スクロールにかかる時間に応じて調整may be needed
}

// 投稿をクリックしたときの処理
document.addEventListener('click', function(e) {
  if (e.target.closest('.post-item')) {
    const post = e.target.closest('.post-item');
    const lat = parseFloat(post.dataset.lat);
    const lng = parseFloat(post.dataset.lng);
    const id = post.dataset.id;

    centerMapAndShowPost(lat, lng, id);
  }
});

  // データベースから緯度経度を取得して地図に表示する関数
  function getDatabaseLocations() {
    fetch("geolocation.php")
      .then((response) => response.json())
      .then((data) => {
        data.forEach((location) => {
          let latitude = parseFloat(location.latitude);
          let longitude = parseFloat(location.longitude);
          let marker = L.marker([latitude, longitude]).addTo(map);

          // マーカーをIDと関連付けて保存
          markers[location.id] = marker;

          // マーカーをクリックした際のポップアップを設定
          marker.bindPopup(`
            <strong>名前：</strong>${location.name}<br>
            <strong>内容：</strong>${location.message}<br>
            ${location.picture_path ? `<img src="${location.picture_path}" class="object-contain" style="max-width: 100%; max-height: 200px;"><br>` : ''}
            <a href="select.php?id=${location.id}" class="text-blue-500 hover:text-blue-700">投稿を見る</a>
          `);
        });

        // 特定の投稿が指定されている場合、その位置にマップを中心化し、ポップアップを表示
        <?php if ($targetPost): ?>
        let targetLat = <?= $targetPost['latitude']; ?>;
        let targetLng = <?= $targetPost['longitude']; ?>;
        let targetId = <?= $targetPost['id']; ?>;
        
        map.setView([targetLat, targetLng], 15);
        if (markers[targetId]) {
          markers[targetId].openPopup();
        }
        <?php endif; ?>
      })
      .catch((error) => console.error("緯度経度の取得中にエラーが発生しました:", error));
  }

  // 位置情報を取得して地図に表示
  getDatabaseLocations();
});


  // モーダル表示のトリガー要素に対するクリックイベントを監視
  document.querySelectorAll('.picture-modal-trigger').forEach(trigger => {
    trigger.addEventListener('click', function() {
      // クリックされた要素から画像データを取得
      const imgSrc = this.getAttribute('data-img-src');

      // 新しいdiv要素を作成し、クラスとスタイルを設定
      const modalDiv = document.createElement('div');
      modalDiv.classList.add('rounded-md', 'overflow-hidden', 'w-full', 'h-auto', 'picture-modal-trigger');
      modalDiv.style.position = 'fixed';
      modalDiv.style.top = '50%';
      modalDiv.style.left = '50%';
      modalDiv.style.transform = 'translate(-50%, -50%)';
      modalDiv.style.zIndex = '9999';
      modalDiv.innerHTML = `
      <div class="modal-content relative w-full max-w-screen-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <span class="close absolute top-3 right-3 cursor-pointer w-10 h-10 flex items-center justify-center rounded-md text-2xl leading-none bg-gray-800  text-white hover:bg-white hover:text-gray-600">&times;</span>
        <img src="${imgSrc}" alt="写真" class="w-full h-auto max-w-full max-h-[80vh] object-contain p-4">
      </div>
    `;

      // モーダルをページに追加
      document.body.appendChild(modalDiv);

      // モーダルのクローズ処理（クリックイベント）
      modalDiv.querySelector('.close').addEventListener('click', function() {
        modalDiv.remove(); // モーダルを削除
      });
    });
  });
</script>
<script src="js/hamMenu.js"></script>


<!-- Footer -->
<?php include 'foot.php'; ?>