{include file="header.tpl"}

<form action="/registerUser" method="post">
  <h1 class="h3 mb-3 fw-normal">Register</h1>

  <div class="form-floating">
    <input type="email" name="email" required class="form-control" id="floatingInput" placeholder="email address">
    <label for="floatingInput">Email address</label>
  </div>
  <div class="form-floating">
    <input type="text" name="username" required pattern="[A-Za-z0-9]+" title="Letters and numbers only" class="form-control" id="floatingInput" placeholder="username">
    <label for="floatingInput">Username</label>
  </div>
  <div class="form-floating">
    <input type="password" name="password" required class="form-control" id="floatingPassword" placeholder="Password">
    <label for="floatingPassword">Password</label>
  </div>
  <div class="form-floating">
    <input type="text" name="regcode" required class="form-control" id="floatingInput" placeholder="registration code">
    <label for="floatingInput">Registration Code</label>
  </div>

  <p>&nbsp;</p>
  <button class="btn btn-primary w-100 py-2" type="submit">Register</button>
  <p><a href="/login">Sign in</a></p>
</form>

{include file="footer.tpl"}