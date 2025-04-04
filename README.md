![LOGO](https://merchant.bpmc.jp/img/color-logo.png)


# BpmLinkPayment

EC-CUBE4.0.x用及び4.2.x用のBPMクレジット決済プラグインです。  
EC-CUBE3.xをご利用の場合は、３系の専用プラグインをご利用ください。  
https://github.com/bpmc-tech/BpmLinkPaymentGateway3x

**このプラグインはEC-CUBE.4.1+での利用はできません。
EC-CUBE.4.1+では、Symfony4.4になっておりこのプラグインでは対応できません。**

# ダウンロード
事前に下記プラグインをダウンロードの上当ガイドをお読みください。

| バージョン | ダウンロード | EC-CUBE本体 |
|:-----------|:----------|:----------|
| 最新(v1.2.0) | [BpmLinkPayment.v_1_2_0.zip](https://github.com/bpmc-tech/BpmLinkPayment/releases/download/v1.2.0/BpmLinkPayment.v_1_2_0.zip)|`4.2.0` `4.2.1` `4.2.2` `4.2.3` |
| v1.1.0 | [BpmLinkPayment.v1_1_0.zip ](https://github.com/bpmc-tech/BpmLinkPayment/releases/tag/v1.1.0) |`4.2.0` `4.2.1` `4.2.2` `4.2.3` |
| v1.0.1 | [BpmLinkPayment.v1_0_1.zip ](https://github.com/bpmc-tech/BpmLinkPayment/releases/tag/v1.0.1) | `4.0.3` `4.0.4` `4.0.5` |


# はじめに

- 本決済モジュールはサイトとショップIDが１対１の場合を前提にご用意しております。
同じサイトで複数のショップIDを設定する場合は想定しておりません。
- 作業を行われる場合は、必ずEC-CUBEすべてのバックアップの取得をお願いします。
- 返金機能はついていません。返金については弊社管理システムより行ってください。
- カスタマイズに関するお問い合わせには対応しておりません。
- EC-CUBE(本体)に関するお問い合わせにつきましては、下記「開発コミュニティ」をご利用ください。
http://xoops.ec-cube.net/  
https://github.com/EC-CUBE/


## 動作環境

あらかじめ、以下URLにてご利用の環境が要件を満たしている事をご確認下さい。

http://www.ec-cube.net/product/system.php

決済モジュールとEC-CUBE本体のバージョンは以下になっています。

| 決済モジュール | EC-CUBE本体 |
|:----|:----|
| `1.0.1` | `4.0.3` `4.0.4` `4.0.5` |
| `1.1.0` `1.2.0` | `4.2.0` `4.2.1` `4.2.2` `4.2.3` |

# 決済モジュールの設定について

## 1.決済モジュール導入完了までの流れ

EC-CUBE決済モジュールの導入は下記５つの手順にて行います。

![flow.png (27.39KB)](https://merchant.bpmc.jp/document/assets/20200320/235c8fd7-c65e-469a-a9db-7adfc3a5f9ed)

## 2.決済モジュールをインストール

### インストール

- EC-CUBE管理画面にログインし、メニュー `オーナーズストア` > `プラグイン` > `プラグイン一覧`をクリックし`プラグイン一覧`のページへ遷移してください。
- 独自プラグインの`プラグインのアップロードはこちら`をクリックしてください。  
![001](https://user-images.githubusercontent.com/44288161/112596283-f5f50180-8e4e-11eb-83a4-a6b16e9787f5.png)

- プラグインのアップロードへ移動したら`ファイルを選択`ボタンをクリックし、弊社よりお送りしたファイル`BpmLinkPaymentGateway.vx.x.x.zip`ファイルを選択してください。  
ファイル選択ダイアログが表示されます。  
ファイル選択ができましたら`アップロード`ボタンをクリックしてください。  
![002](https://user-images.githubusercontent.com/44288161/112596305-fc837900-8e4e-11eb-9d07-562b6888a6ce.png)

プラグインページへ戻り、一覧に`BpmLinkPaymentGateway `が表示されていましたらインストールが成功です。

### プラグインのアップデート

- 既に弊社プラグインをインストールしており、新しいバージョンにアップデートする際には`ファイルを選択`ボタンをクリックし、弊社よりお送りしたファイル`BpmLinkPaymentGateway.vx.x.x.zip`ファイルを選択してください。  
ファイル選択ダイアログが表示されます。  
ファイル選択ができましたら`アップロード`ボタンをクリックしてください。  
![スクリーンショット 2025-03-31 14 15 49](https://github.com/user-attachments/assets/00cf1340-8d16-4a97-8a83-b6bcf192a35a)



### 決済モジュールの有効化

アップロードした段階では、決済モジュールは<b style="color:red">停止中</b>です。  
一覧にある`▶`をクリックしてください。  
![003](https://user-images.githubusercontent.com/44288161/112596370-10c77600-8e4f-11eb-90de-9a1279ed343d.png)


`▶`が消え、設定ボタンが表示されましたら、決済モジュールは有効になりました。  
次は決済モジュールの設定を行います。

## 3.決済モジュールの設定を行う

### (1) EC-CUBE側の設定

- 設定`歯車`ボタンをクリックしてください。

![004](https://user-images.githubusercontent.com/44288161/112596416-2472dc80-8e4f-11eb-83b1-7c4f6ed4790c.png)

- 設定画面に移動したら、契約した際に弊社が発行した`API TOKEN`を入力してください。
APIドメインには`payment.bpmc.jp`を入力してください。
3Dセキュアを利用の有無を設定してください。
入力が完了したら`設定`ボタンをクリックしてください。

![スクリーンショット 2025-03-31 14 17 54](https://github.com/user-attachments/assets/8bf90970-0c05-4429-aabe-c4bc428133ba)

<b style="color:red;">API TOKENは店舗管理システムにログイン後、`利用内容` => `システム利用内容`のページからご確認いただけます。</b>

### (2) 決済通知URLの設定

EC-CUBEが決済通知を受取るために、弊社管理システム側で決済通知URLの設定が必要です。

https://merchant.bpmc.jp/

ログイン後、メニュー `決済システム管理` > `リンク決済` > `結果通知設定(HTTP)` をクリックし、結果通知設定(HTTP)画面に遷移してください。  
**ログインは店舗様向けログイン情報にてログインしてください**

![006](https://user-images.githubusercontent.com/44288161/112596609-63089700-8e4f-11eb-8ea5-5728f11d6184.png)

下記内容を設定してください。
- **送信状態**  
送信する(GET)
- **HTTP結果通知**

```
http(s)://<あなたのEC-CUBEサイトドメイン>/bpm_link_payment/receive_complete
```

入力が完了したら`保存`ボタンをクリックしてください。


## 4.支払い方法設定を行う

EC-CUBE管理画面のメニュー `設定` > `店舗設定` > `支払方法設定`をクリックし、支払方法管理ページへ遷移してください。

- `BPMクレジットカード決済`が追加されています。
- 一覧の`支払い名`をクリックすると`編集`ページに移動します。
- 支払い方法について、名称や手数料はご自由に設定してください。  
利用条件は弊社と契約した際に発行された、決済可能金額を設定してください。  
弊社の管理システムからご確認いただけます。

## 5. 配送方法設定を行う
EC-CUBE管理画面のメニュー `設定` > `店舗設定` > `配送方法設定`をクリックし、配送方法管理ページへ遷移してください。

- 対象の配送方法の`タイトル`をクリックすると`編集`ページへ移動します。
- 支払い方法設定で`BPMクレジットカード決済`にチェックをいれて、`登録`してください。

## 6. 動作確認を行う

実際にEC-CUBEにて商品をカートに入れてレジにすすみます。  
支払い方法を`BPMクレジットカード決済`に変更し、`BPMクレジットカード決済へ`ボタンをクリックしてください。

![007](https://user-images.githubusercontent.com/44288161/112600612-c77a2500-8e54-11eb-96ac-b4e5c898be7c.png)

弊社の決済ページへ遷移すれば正常に動作しております。

![008](https://user-images.githubusercontent.com/44288161/112600629-cba64280-8e54-11eb-9765-a35065c02162.png)


# 7. 受注内容確認＆対応状況の更新

## 7.1. 受注内容確認
EC-CUBE管理画面にログインし、メニュー `受注管理` > `受注マスター`をクリックし`受注マスター`のページへ遷移してください。  
対象の受注編集ページを開きます。
赤枠の様に`決済状況` `決済承認番号` `決済明細表記名`が表示されます。

![009](https://user-images.githubusercontent.com/44288161/112783163-a51d1d00-9089-11eb-84a0-601d99c68c85.png)

EC-CUBEの仕様にて、`対応状況`は**新規受付**となっております。


## 7.2. 受注内容確認

BPM店舗様向け管理画面にログインしてください。
https://merchant.bpmc.jp/

- ログイン後 `決済一覧` をクリックしてください。
- 検索画面の`承認番号`に　EC-CUBEに記載されている`決済承認番号`にて検索をしてください。

![010](https://user-images.githubusercontent.com/44288161/112783652-c29eb680-908a-11eb-882d-a8d0e344bb6c.png)

## 7.3. 対応状況更新

EC-CUBEの受注編集ページにて`対応状況`を**入金済み**に更新してください。


# その他

### 決済が完了したタイミングで入金済みしたい場合。

EC-CUBEのデフォルトのステータス遷移設定では、**入金済み**にすることはできません。
受注ステータスを入金済みにする場合「app/config/eccube/packages/order_state_machine.php」を変更してください。

参考URL: https://doc4.ec-cube.net/customize_order_state_machine
