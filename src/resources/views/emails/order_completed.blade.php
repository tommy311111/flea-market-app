<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>取引完了のお知らせ</title>
</head>
<body>
    <h1>取引が完了しました</h1>

    <p>取引の商品: {{ $order->item->name }}</p>
    <p>購入者: {{ $order->buyer->name }}</p>
    <p>出品者: {{ $order->seller->name }}</p>
    <p>評価が送信されました。</p>

    <p>ご確認ください。</p>
</body>
</html>
