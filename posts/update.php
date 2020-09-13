<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>MyBBS</title>
  <link rel="stylesheet" href="/honda/mybbs/posts/style.css">
</head>
<body>
  <header class="header">
    <div class="title">
      <a href="/honda/mybbs/posts/index.php" class="title-link">My BBS</a>
    </div>
  </header>
  <div class="main-content">
    <div class="messages">
      <?php
      // データベースに接続
      $dsn = "mysql:host=localhost;dbname=mybbs;charset=utf8";
      $user = "root";
      $password = "";
      $pdo = new PDO($dsn, $user, $password);

      // edit.phpの編集formから受け取ったデータをデータベースにupdateする
      if(!empty($_POST["id"]) && !empty($_POST["contributor"]) && !empty($_POST["message"])) {
        $id = $_POST["id"];
        $contributor = $_POST["contributor"];
        $message = $_POST["message"];
        $sql = "UPDATE mybbs_honda_posts SET contributor = :contributor, message = :message WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindValue(":id", $id, PDO::PARAM_INT);
        $stmt -> bindValue(":contributor", $contributor, PDO::PARAM_STR);
        $stmt -> bindValue(":message", $message, PDO::PARAM_STR);
        $stmt -> execute();
      }?>

      <div class="message">
        <p class="update-info">更新が完了しました</p>
      </div>
    </div>
  </div>
  <div class="toppage-link-box">
    <a href="/honda/mybbs/posts/index.php">トップページへ戻る</a>
  </div>
</body>
</html>