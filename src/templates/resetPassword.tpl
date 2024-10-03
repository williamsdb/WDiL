{include file="header.tpl"}

<form action="/changePassword" method="post">
  <h1 class="h3 mb-3 fw-normal">Reset your Password</h1>

  <p>Please enter your new password and confirm</p>
  <div class="form-floating">
    <input type="password" name="password" required class="form-control" id="floatingInput" placeholder="strong password">
    <label for="floatingInput">New Password</label>
  </div>

  <div class="form-floating" style="margin-top: 10px;">
    <input type="password" name="passwordConf" required class="form-control" id="floatingInput" placeholder="reenter your new password">
    <label for="floatingInput">Confirm Password</label>
  </div>

  <input type="hidden" value="{$id}" name="id">
  <input type="hidden" value="{$code}" name="code">
  <button class="btn btn-primary w-100 py-2" type="submit" style="margin-top: 10px;">Reset Password</button>
</form>

{include file="footer.tpl"}