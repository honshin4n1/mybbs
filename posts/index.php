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

  <?php
  // 1ページの表示数
  define("max_view", 5);

  // データベースに接続
  $dsn = "mysql:host=localhost;dbname=mybbs;charset=utf8";
  $user = "root";
  $password = "";
  $pdo = new PDO($dsn, $user, $password);

  // formから受け取ったデータをデータベースに書き込む
  if(!empty($_POST["contributor"]) && !empty($_POST["message"])) {
    $contributor = $_POST["contributor"];
    $message = $_POST["message"];
    $sql = "INSERT INTO mybbs_honda_posts (contributor, message, created_at, update_at) VALUES (:contributor, :message, NOW(),NOW());";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindValue(":contributor", $contributor, PDO::PARAM_STR);
    $stmt -> bindValue(":message", $message, PDO::PARAM_STR);
    $stmt -> execute();
  }

   // 削除機能 // 受け取ったidのレコードを削除
   if(!empty($_POST["delete_id"])) {
    $delete_id = $_POST["delete_id"];
    $sql = "DELETE FROM mybbs_honda_posts WHERE id = :delete_id;";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindValue(":delete_id", $delete_id, PDO::PARAM_INT);
    $stmt -> execute(); 
  }

  // ページネーション//

  //必要なページ数を求める
  $count = $pdo->prepare('SELECT COUNT(*) AS count FROM mybbs_honda_posts');
  $count->execute();
  $total_count = $count->fetch(PDO::FETCH_ASSOC);
  $max_page = ceil($total_count["count"] / max_view);

  //現在いるページのページ番号を取得
  if(!isset($_GET["page_id"])) { 
    $now = 1;
  } else {
    $now = $_GET["page_id"];
  }

  //表示するデータを取得するSQLを準備
  $select = $pdo->prepare("SELECT * FROM mybbs_honda_posts ORDER BY id DESC LIMIT :start, :max ");

  if ($now == 1) {
  //1ページ目の処理
    $select->bindValue(":start", $now -1, PDO::PARAM_INT);
    $select->bindValue(":max", max_view, PDO::PARAM_INT);
  } else {
  //1ページ目以外の処理
    $select->bindValue(":start", ($now -1) * max_view, PDO::PARAM_INT);
    $select->bindValue(":max", max_view, PDO::PARAM_INT);
  }
  //実行し結果を取り出しておく
  $select->execute();
  $posts = $select->fetchAll(PDO::FETCH_ASSOC);

  ?>

  <div class="main-content">
    <div class="post-link-box">
      <a href="#newpost" class="post-link">新規投稿</a>
    </div>
    <div class="messages">
    <?php
    // データを表示
    foreach($posts as $post) {?>
      <div class="message">
        <div class="message-flex">
          <div class="message-info">
            <p class="contributor">［ <?= $post["contributor"] ?> ］/</p>
            <div>
              <p class="creat-datetime">［ <?= $post["created_at"] ?> 作成］</p>
              <p class="update-datetime">［ <?= $post["update_at"] ?> 更新］</p>
            </div>
          </div>
          <div>
            <form method="post" action="/honda/mybbs/posts/index.php">
            <input type="hidden" name="delete_id" value="<?= $post["id"] ?>">
            <button type="submit" class="delete-btn">削除</button>
            </form>
            <form method="post" action="/honda/mybbs/posts/edit.php">
            <input type="hidden" name="update_id" value="<?= $post["id"] ?>">
            <button type="sumit" class="edit-btn">編集</button>
            </form>
            <form method="post" action="/honda/mybbs/posts/show.php/<?= $post["id"] ?>">
            <input type="hidden" name="post_id" value="<?= $post["id"] ?>">
            <button type="sumit" class="reply-btn">返信</button>
            </form>
          </div>
        </div>
        <div class="message-text">
          <p class="text"><?= $post["message"] ?></p>
        </div>
      </div>
  <?php }?>  
    </div>
    <div class="pagination">
    <?php
    // 最大ページ数分リンクを作成
    if($now > 1) {
      echo "<a href='/honda/mybbs/posts/index.php?page_id=".($now - 1)."' class='prev'>< 前へ</a>";
    }

    for($i = 1; $i <= $max_page; $i++) { 
      // 現在表示中のページ数の場合はリンクを貼らない
      if ($i == $now) { 
        echo "<span class='page'>$now</span>";
      } else {
          echo "<a href='/honda/mybbs/posts/index.php?page_id=$i' class='page'>$i</a>";
      }
    }
    
    if($now < $max_page) {
      echo "<a href='/honda/mybbs/posts/index.php?page_id=".($now + 1)."' class='next'>次へ ></a>";
    }
    ?>
    </div>
    <div class="form-content" id="newpost">
      <form method="post" action="/honda/mybbs/posts/index.php">
        <div class="field-box">
          <label>投稿者</label>
          <input type="text" name="contributor" class="field-user">
        </div>
        <div class="field-box">
          <label>本文</label>
          <br>
          <textarea name="message" rows="4" cols="40" class="field-message"></textarea>
        </div>
        <div class="field-box">
          <div class="btn-box">
            <input type="submit" value="投稿" class="post-btn">
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="toppage-link-box">
    <a href="#top">トップへ戻る</a>
  </div>
</body>
</html>