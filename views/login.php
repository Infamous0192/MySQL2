<div class="container">
  <?php

  if (isset($_SESSION['username'])) {
    header('Location: home');
  }

  if (isset($_POST['submit'])) {
    $db->login($_POST);
  }

  ?>
  <div class="row">
    <div class="col-6 mx-auto">
      <h1>Login</h1>
      <form method="POST" action="">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" name="username" id="username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" name="remember" value="remember" id="remember">
          <label class="form-check-label" for="remember">Remember Me</label>
        </div>
        <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
  </div>
</div>