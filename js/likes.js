// ユーザーごとのキーを生成する関数
// これがないとユーザーを認識したいいねの状態が保存できない
function getLocalStorageKey(userId, postId) {
    return `liked_${userId}_${postId}`;
}

// 読み込み時にいいねボタンの取得と初期化
document.addEventListener('DOMContentLoaded', function () {
    // いいねボタンをすべて取得
    const likeButtons = document.querySelectorAll('.like-button');
    // 現在のユーザーIDを取得
    const userId = document.querySelector('.like-section').getAttribute('data-user-id');

    // 各いいねボタンに対しての処理
    likeButtons.forEach(button => {
         // 各ボタンの投稿IDを取得
        const postId = button.getAttribute('data-post-id');
        // ローカルストレージのキーを生成
        const localStorageKey = getLocalStorageKey(userId, postId);
        // ローカルストレージからいいね状態を取得
        let isLiked = localStorage.getItem(localStorageKey) === 'true';

        // 初期表示時にローカルストレージからいいね状態を取得してスタイルを設定
        updateButtonStyle(button, isLiked);

        // いいねボタンのクリックイベントリスナー
        button.addEventListener('click', function () {
            fetch('likes.php', {  // このサーバーに
                method: 'POST',   // リクエスト送信
                headers: {  // リクエストのボディ形式を指定
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `post_id=${postId}`,  // POSTリクエストのbodyとして投稿IDを送信
                cache: 'no-cache',  // キャッシュせず即時反映
            })
                .then(response => response.json())
                .then(data => {
                    // サーバーからのレスポンスが成功した場合
                    if (data.success) {
                        // いいね数を更新
                        const likeCountSpan = button.closest('.like-section').querySelector('.like-count-number');
                        likeCountSpan.textContent = data.like_count;

                        // いいね状態を反転
                        isLiked = !isLiked;
                        
                        // ローカルストレージにいいね状態を保存
                        localStorage.setItem(localStorageKey, isLiked);

                        // ボタンのスタイルを更新
                        updateButtonStyle(button, isLiked);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    // エラーが発生した場合の処理
                    console.error('Fetch error:', error);
                    alert('いいね処理中にエラーが発生しました。');
                });
        });
    });
});

// ボタンのスタイルを更新する関数
function updateButtonStyle(button, isLiked) {
    if (isLiked) {
        // いいねされている場合のボタンスタイル
        button.innerHTML = '<i class="fa-solid fa-heart" style="color: #f00528;"></i>';
        button.classList.add('liked');
    } else {
        // いいねされていない場合のボタンスタイル
        button.innerHTML = '<i class="fa-regular fa-heart"></i>';
        button.classList.remove('liked');
    }
}