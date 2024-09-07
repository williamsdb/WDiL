{include file="header.tpl"}

<h3>Register</h3>
<form role="form" action="/registerUser" method="post">

  <label class="col-lg-2 control-label">Email</label>
  <input type="email" name="email" placeholder="email address" required autofocus maxlength="100" size="50">

  <label class="col-lg-2 control-label">Username</label>
  <input type="text" name="username" pattern="[A-Za-z0-9]+" title="Letters and numbers only"  placeholder="username" required maxlength="100" size="50">

  <label class="col-lg-2 control-label">Password</label>
  <input type="password" name="password" placeholder="password" required maxlength="100" size="50">

  <label class="col-lg-2 control-label">Registration Code</label>
  <input type="text" name="regcode" title="enter the code given to you"  placeholder="regcode" required maxlength="100" size="50">

  <p><input type="submit" value="Register"></p>

</form>

{include file="footer.tpl"}