<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Leafletを使った地図に緯度経度を反映させる</title>
    <!-- LeafletのCSS（OpenStreetMapライブラリ） -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
      #map {
        height: 400px;
      }
    </style>
  </head>

  <body>
    <h1>Leafletを使った地図に緯度経度を反映させる</h1>
    <div id="map"></div>

    <!-- LeafletのJavaScript（OpenStreetMapライブラリ） -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
      // 地図の初期化
      var map = L.map("map").setView([35.681236, 139.767125], 13); // デフォルトの中心を東京駅に設定、ズームレベル13

      // OpenStreetMapのタイルレイヤーを追加
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution:
          'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      }).addTo(map);

      // データベースから緯度経度を取得して地図に表示する関数
      function getDatabaseLocations() {
        fetch("geolocation.php")
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.json();
          })
          .then((data) => {
            // 取得した位置情報を地図に表示
            data.forEach((location) => {
              let latitude = parseFloat(location.latitude);
              let longitude = parseFloat(location.longitude);
              let marker = L.marker([latitude, longitude]).addTo(map);

              // マーカーをクリックした際のポップアップを設定
              if (location.picture_path) {
              marker.bindPopup(`
                <strong>名前：</strong>${location.name}<br>
                <strong>内容：</strong>${location.message}<br>
                <img src="${location.picture_path}" class="object-contain" style="max-width: 100%; max-height: 200px;"><br>
                <a href="index.php?id=${location.id}" onclick="centerMap(${latitude}, ${longitude}); return false;">投稿を見る</a>
                `);
              } else {
                marker.bindPopup(`
                <strong>名前：</strong>${location.name}<br>
                <strong>内容：</strong>${location.message}<br>
                <a href="index.php?id=${location.id}" onclick="centerMap(${latitude}, ${longitude}); return false;">投稿を見る</a>
                `);
              }
            });
          })
          .catch((error) =>
            console.error("緯度経度の取得中にエラーが発生しました:", error)
          );
      }

      // ページが読み込まれた時に位置情報を取得して地図に表示
      document.addEventListener("DOMContentLoaded", getDatabaseLocations);
    </script>
  </body>
</html>
