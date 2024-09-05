{include file="header.tpl"}

<h3>Add a new activity</h3>
<form role="form" action="/createActivity" method="post">

  <label class="col-lg-2 control-label">Activity name</label>
  <input type="text" name="activityName" placeholder="activity name" required autofocus maxlength="100" size="50">

  <p><input type="submit" value="Create activity"></p>

</form>


{include file="footer.tpl"}