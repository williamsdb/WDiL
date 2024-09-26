{include file="header.tpl"}

<form action="/forgotPass" method="post">
  <h1 class="h3 mb-3 fw-normal">Forgotten Password</h1>

  <p>Please enter your email address to reset password</p>
  <div class="form-floating">
    <input type="email" name="email" required class="form-control" id="floatingInput" placeholder="email address">
    <label for="floatingInput">Email address</label>
  </div>

  <p>&nbsp;</p>
  <button class="btn btn-primary w-100 py-2" type="submit">Reset Password</button>
  <p><a href="/login">Already have an account? Login here.</a></p>
</form>

{include file="footer.tpl"}