{include file="header.tpl"}

<h3>Edit an activity</h3>
<form role="form" action="/updateActivity/{$id}" method="post">
  <div class="mb-3">
    <label for="activityName" class="form-label">Activity name</label>
    <input type="text" class="form-control" id="activityName" name="activityName" placeholder="activity name" value="{$activityName}" required autofocus maxlength="100">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>

</form>

{include file="footer.tpl"}