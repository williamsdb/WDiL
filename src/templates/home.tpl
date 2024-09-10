{include file="header.tpl"}

{if $activities != ''}

	<div class="container">
	<div class="row">
	  {section name=all loop=$activities}
	  <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex align-items-stretch">
		<div class="card" style="width: 100%;">
		  <div class="card-body">
			<h5 class="card-title">{$activities[all].activityName}</h5>
			{if $activities[all].triggers|count > 0}
			  {assign var="latestTimestamp" value=$activities[all].triggers[$activities[all].triggers|count - 1].timestamp}
			  <h6 class="card-subtitle mb-2 text-body-secondary">{$latestTimestamp|date_format_tz:"Y-m-d H:i:s":$smarty.const.TZ}</h6>
			{else}
			  <h6 class="card-subtitle mb-2 text-body-secondary">Not yet Triggered</h6>
			{/if}
		  </div>
		  <div class="card-footer">
			<button type="button" class="btn btn-primary" data-wdil="{$smarty.section.all.index}" data-bs-toggle="modal" data-bs-target="#triggerModal" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Trigger</button>
			<br>
			{if $activities[all].triggers|count > 0}
			  <a href="/statsActivity/{$smarty.section.all.index}">Stats</a>
			{/if}
			<a href="/editActivity/{$smarty.section.all.index}">Edit</a>
			<a href="#" onclick="confirmRedirect('/deleteActivity/{$smarty.section.all.index}'); return false;">Delete</a>
		  </div>
		</div>
	  </div>
	  {/section}
	</div>
  </div>
  
{else}
	<p>You haven't any activities setup.</p>
{/if}

<p><a class="btn btn-primary" href="/addActivity" role="button">Add a new activity</a></p>

<!-- Bootstrap Modal -->
<div class="modal fade" id="triggerModal" tabindex="-1" aria-labelledby="triggerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="triggerModalLabel">Trigger activity</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
		<form role="form" action="/triggerActivity" method="post" id="triggerForm">
			<div class="mb-3">
				<label for="datetimePicker" class="form-label">Select Date and Time</label>
				<input type="datetime-local" class="form-control" name="dateTime" step="1" id="datetimePicker" required>
				<div id="error-message" style="color: red; display: none;">The date must be in the past.</div>
				<input type="hidden" class="form-control" name="activityId" id="activityId" value="">
				<input type="hidden" class="form-control" name="redirectTo" id="redirectTo" value="home">
			</div>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="triggerButton">Save</button>
      </div>
    </div>
  </div>
</div>

{include file="footer.tpl"}