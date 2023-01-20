
## セットアップ

```bash
git clone https://github.com/Tokio-svg/docusign_test
cd docusign_test
make init
make serve
```
## 連携手順
- 「連携する」ボタンをクリックして連携を承認
- index画面に「DocuSign Status」が表示されたら連携完了
- 「連携解除」ボタンをクリックすると連携が解除される

## 署名リクエスト手順
- 「API Menu」の「電子署名を依頼」をクリック
- 署名者とCCメールアドレスを入力
- 「送信」ボタンをクリック
- Envelope List画面で該当のenvelopeをクリックすると状態を確認できる

## 署名手順
- 署名者に送られたメールのリンクをクリック
- 署名する
- FINISHをクリックして終了する
- 署名者とCC宛に完了メールが送信される
- Envelope List画面で該当のenvelopeをクリックするとstatusが「completed」になったのを確認できる
- 「ダウンロード」ボタンをクリックするとドキュメント群をZIP形式でダウンロードできる

## アクセストークンの更新
- index画面の「トークン更新」ボタンをクリックするか、プロジェクト直下で以下のコマンドを入力するとリフレッシュトークンによってアクセストークンの更新ができる（トークンの期限が切れている場合のみ）
```bash
make token
```
## 注意事項
- makeコマンドが上手く動かなかったらお手数ですがMakefileの内容を見て適切な処置を取ってください
- DocuSignクライアントのパッケージを使用していない
- テンプレート機能を使用していない
- アクセストークンの有効期限は8時間（expires_in=28800）
- リフレッシュトークンの有効期限は30日