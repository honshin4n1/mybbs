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

      // formから受け取ったデータをデータベースに書き込む
      if(!empty($_POST["replyer"]) && !empty($_POST["body"]) && !empty($_POST["post_id"])) {
        $replyer = $_POST["replyer"];
        $body = $_POST["body"];
        $post_id = $_POST["post_id"];
        $sql = "INSERT INTO mybbs_honda_comments (replyer, body, post_id, created_at) VALUES (:replyer, :body, :post_id, NOW());";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindValue(":replyer", $replyer, PDO::PARAM_STR);
        $stmt -> bindValue(":body", $body, PDO::PARAM_STR);
        $stmt -> bindValue(":post_id", $post_id, PDO::PARAM_INT);
        $stmt -> execute();
      }

      // index.phpの返信formから受け取ったidでpostのデータを表示する
       if(!empty($_POST["post_id"])) {
         $post_id = $_POST["post_id"];
         $post = $pdo->prepare("SELECT * FROM mybbs_honda_posts WHERE id = :post_id;");
         $comments = $pdo->prepare("SELECT * FROM mybbs_honda_comments WHERE post_id = :post_id;");
         $post -> bindValue(":post_id", $post_id, PDO::PARAM_INT);
         $comments -> bindValue(":post_id", $post_id, PDO::PARAM_INT);
         $post->execute();
         $comments->execute();
         $post = $post->fetch(PDO::FETCH_ASSOC);
         $comments = $comments->fetchAll(PDO::FETCH_ASSOC);
       }?>

      <div class="message">
        <div class="message-flex">
          <div class="message-info">
            <p class="contributor">［ <?= $post["contributor"] ?> ］/</p>
            <div>
              <p class="creat-datetime">［ <?= $post["created_at"] ?> 作成］</p>
              <p class="update-datetime">［ <?= $post["update_at"] ?> 更新］</p>
            </div>
          </div>
        </div>
        <div class="message-text">
          <p class="text"><?= $post["message"] ?></p>
        </div>
      </div>
      <div class=message>
      <?php
      foreach($comments as $comment) {?>
        <div class="comment">
          <p class="comment-text"><?= $comment["body"] ?></p>
          <div class="replyer-info">
            <p class="replyer"><?= $comment["replyer"] ?> /</p>
            <p class="comment-created"><?= $comment["created_at"] ?></p>
          </div>
        </div>
      <?php }?>
      </div>
    </div>
    <div class="form-content" id="newpost">
      <form method="post" action="/honda/mybbs/posts/show.php">
        <div class="field-box">
          <label>ニックネーム</label>
          <input type="text" name="replyer" class="field-user">
        </div>
        <div class="field-box">
          <label>コメント</label>
          <br>
          <textarea name="body" rows="4" cols="40" class="field-message"></textarea>
        </div>
        <div class="field-box">
          <div class="btn-box">
            <input type="submit" value="返信する" class="post-btn">
          </div>
        </div>
        <input type="hidden" name="post_id" value="<?= $post["id"] ?>">
      </form>
    </div>
  </div>
  <div class="toppage-link-box">
    <a href="/honda/mybbs/posts/index.php">トップページへ戻る</a>
  </div>
</body>
</html>