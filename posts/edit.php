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

      // index.phpの編集formから受け取ったデータをinputのvalueにする
       if(!empty($_POST["update_id"])) {
         $update_id = $_POST["update_id"];
         $sql = "SELECT * FROM mybbs_honda_posts WHERE id = :update_id;";
         $stmt = $pdo->prepare($sql);
         $stmt -> bindValue(":update_id", $update_id, PDO::PARAM_INT);
         $stmt -> execute(); 
       }?>

      <div class="message">
      </div>
      <?php
      $post = $stmt -> fetch(PDO::FETCH_ASSOC)?>
      <div class="form-content" id="newpost">
        <form method="post" action="/honda/mybbs/posts/update.php">
          <div class="field-box">
            <input type="hidden" name="id" value="<?= $post["id"] ?>">
            <label>投稿者</label>
            <input type="text" name="contributor" value="<?= $post["contributor"] ?>" class="field-user">
          </div>
          <div class="field-box">
            <label>本文</label>
            <br>
            <textarea name="message" rows="4" cols="40" class="field-message"><?= $post["message"] ?></textarea>
          </div>
          <div class="field-box">
            <div class="btn-box">
              <input type="submit" value="編集" class="post-btn">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="toppage-link-box">
    <a href="/honda/mybbs/posts/index.php">トップページへ戻る</a>
  </div>
</body>
</html>