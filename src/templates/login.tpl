{include file="header.tpl"}

<form action="/loginUser" method="post">
  <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

  <div class="form-floating">
    <input type="text" name="username" required class="form-control" id="floatingInput" placeholder="username or email address">
    <label for="floatingInput">Username or Email address</label>
  </div>
  <div class="form-floating">
    <input type="password" name="password" required class="form-control" id="floatingPassword" placeholder="Password">
    <label for="floatingPassword">Password</label>
  </div>

  <p>&nbsp;</p>
  <button class="btn btn-primary w-100 py-2" type="submit">Sign in</button>
  <p><a href="/register">Need an account? Register here.</a>&nbsp;&nbsp;<a href="/forgot">Forgotten your password?</a></p>
</form>

{include file="footer.tpl"}