{include file="header.tpl"}

<h3>Login</h3>
<form role="form" action="/loginUser" method="post">

  <label class="col-lg-2 control-label">Username</label>
  <input type="text" name="username" placeholder="username" required autofocus maxlength="100" size="50">

  <label class="col-lg-2 control-label">Password</label>
  <input type="password" name="password" placeholder="password" required maxlength="100" size="50">

  <p><a href="/register">Need an account? Register here.</a></p>

  <p><input type="submit" value="Login"></p>

</form>


{include file="footer.tpl"}