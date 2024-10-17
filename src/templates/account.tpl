{include file="header.tpl"}

<h3>Account</h3>

<form role="form" action="/updateAccount" method="post">
  <div class="mb-3">
    <label for="email" class="form-label">Email address</label>
    <input type="text" class="form-control" disabled id="email" name="email" value={$email}><br>
    <label for="username" class="form-label">Username</label>
    <input type="text" class="form-control" disabled id="username" name="username" value={$username}><br>
    <label for="token" class="form-label">Pushover Token</label>
    <input type="text" class="form-control" id="token" name="token" value={$token}><br>
    <label for="user" class="form-label">Pushover User</label>
    <input type="text" class="form-control" id="user" name="user" value={$user}><br>
  <button type="submit" class="btn btn-primary">Submit</button>

{include file="footer.tpl"}