# ①課題番号-プロダクト名

kadai10_auth

## ②課題内容（どんな作品か）

- 名前、内容、写真を投稿＆表示できるアプリ。
- 前回の課題にログイン認証を追加しています。ユーザーの新規登録、ログインができます。
- 管理画面から投稿やユーザーを選択して削除することができます。

## ③DEMO

https://723kb.jp/kadai10_auth/

## ④作ったアプリケーション用のIDまたはPasswordがある場合

【一般ユーザー】
- ID: test1
- PW: 1111

【管理者】
- ID: admin1
- PW: 2222

- 新規登録から自由にユーザー登録できますが、ユーザーの削除は管理画面からしかできません。
  管理者として登録したい場合は、追加で管理者用パスワード1111を入力して登録してください。

## ⑤工夫した点・こだわった点

- ログインしていない場合でも全体投稿は閲覧できるようにした。
- ログインIDと投稿者のIDが一致するもののみ、編集ボタンと削除ボタンが表示されるようにした。
- 管理者としてログインしている時のみ、管理画面へアクセスできるボタンが表示されるようにした。
- 画像の読み込み速度改善のため、保存形式をbase64からimgディレクトリに保存し画像パスを呼び出す方法に変更した。
- いいね機能を追加した。
- 全てのページにおいてレスポンシブ対応とし、各デバイスで見やすい＆使いやすいようにした。デザインやレイアウトはテンプレートを使わず自分で考えた。

## ⑥難しかった点・次回トライしたいこと(又は機能)

- ログインしていない場合でも投稿の閲覧だけはできるようにしたかったが、なぜかどのページにアクセスしてもlogin.phpに戻され実装できなかった。→むやみやたらにloginCheck関数を使っており、ログイン失敗（そもそもしていないので当然）時のリダイレクトで戻されているだけだった。気づくのに時間がかかった。
- ユーザー登録やログイン処理におけるデータの受け渡し。管理者として登録したいのにラジオボタンの値を渡しておらず、このことに気づくまでにかなり時間を要した。
- 記事を参考にしていいね機能を実装したがかなり難しく、何度もエラー解消のためChatGPTとラリーをした。現在のいいねの状態を保持する挙動がおかしい時があるので、復讐を兼ねてブラッシュアップしたい。
- パスワードやIDを英数字、記号、大文字など含めたり、桁数の制限をつけたりしてみたい。各ユーザーがマイページで登録内容を修正できるようにしたい。
- phpのファイルだらけになりsrcフォルダにまとめようとしたが、パスの指定が変わりとんでもないことになり断念した。

## ⑦質問・疑問・感想、シェアしたいこと等なんでも

- [質問]実装したい機能を追加していくとphpのファイルだらけになってしまった。プロダクトを作る時は初めからディレクトリ構造をしっかり考えていないといけないですか？
- [感想]ログイン認証がつくと一気にそれらしいアプリになると感じた。やりたいことをやろうとした結果、今まで習ったこと全部入りの盛りだくさんになってしんどかった。作りたいものが以前よりも作れるようになり楽しい一方で、セキュリティ対策について考えるようになった。自分の作ったもので誰かが不利益を生じることがあってはいけない。
- [参考記事]
  - 1. トランザクション処理[https://www.happylifecreators.com/blog/20220610/]
  - 2. 条件で特定のデータを削除[https://lanchesters.site/sql-delete-where-in/]
  - 3. PHPでの画像アップロード[https://qiita.com/ryo-futebol/items/11dea44c6b68203228ff]
  - 4. Ajax通信を使ったいいね機能[https://zenn.dev/torihazi/articles/2fefc4b673aea7] [https://qiita.com/kanasann1106/items/4ea0675afde639e6d540]
