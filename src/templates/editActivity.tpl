{include file="header.tpl"}

<h3>Edit an activity</h3>
<form role="form" action="/updateActivity/{$id}" method="post">

    <label class="col-lg-2 control-label">Activity name</label>
    <input type="text" name="activityName" placeholder="activity name" value="{$activityName}" required autofocus maxlength="100" size="50">

    <p><input type="submit" value="Update activity"></p>

</form>

{include file="footer.tpl"}