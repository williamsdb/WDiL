{include file="header.tpl"}

{if $activities != ''}

	<div class="container">
	<p><input type="checkbox" name="archived" id="showArchived" {if $archived ==1}checked{/if}> Show archived activites?<p>
	<div class="color-container">
        <div class="color-option">
            <input type="radio" id="color0" name="color" value="default" class="color-input" {if $activityColour == '' || $activityColour == 'default'}checked{/if} required>
            <label for="color0" class="color-label" style="background-color: #f5f5f5;" title="Light grey"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color1" name="color" value="pink" class="color-input" {if $activityColour == 'pink'}checked{/if}>
            <label for="color1" class="color-label" style="background-color: #f5e6e8;" title="Pink"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color2" name="color" value="peach" class="color-input" {if $activityColour == 'peach'}checked{/if}>
            <label for="color2" class="color-label" style="background-color: #f5edd6;" title="Peach"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color3" name="color" value="mint" class="color-input" {if $activityColour == 'mint'}checked{/if}>
            <label for="color3" class="color-label" style="background-color: #e9f5e6;" title="Mint"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color4" name="color" value="light-blue" class="color-input" {if $activityColour == 'light-blue'}checked{/if}>
            <label for="color4" class="color-label" style="background-color: #d6f0f5;" title="Light Blue"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color5" name="color" value="lavender" class="color-input" {if $activityColour == 'lavender'}checked{/if}>
            <label for="color5" class="color-label" style="background-color: #f0e6f5;" title="Lavender"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color6" name="color" value="lemon" class="color-input" {if $activityColour == 'lemon'}checked{/if}>
            <label for="color6" class="color-label" style="background-color: #f5f0d6;" title="Lemon"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color7" name="color" value="aqua" class="color-input" {if $activityColour == 'aqua'}checked{/if}>
            <label for="color7" class="color-label" style="background-color: #e6f2f5;" title="Aqua"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color8" name="color" value="beige" class="color-input" {if $activityColour == 'beige'}checked{/if}>
            <label for="color8" class="color-label" style="background-color: #f5e6d6;" title="Beige"></label>
        </div>
    </div>
	<div class="row">
	  {assign var="count" value=0}
	  {section name=all loop=$activities}
		{if (($archived == 1) || ((!$activities[all].archived|isset || $activities[all].archived ==0) && ($archived == 0)))}
			{if ($activities[all].activityColour|isset && $colour != '' && $colour == $activities[all].activityColour) || $colour == 'default'}
				{assign var="count" value=$count+1}
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
					<div class="card-footer {if $activities[all].activityColour|isset && $activities[all].activityColour != 'default'} footer-{$activities[all].activityColour}{/if}">
						<button type="button" class="btn btn-primary" data-wdil="{$smarty.section.all.index}" data-bs-toggle="modal" data-bs-target="#triggerModal" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Trigger</button>
						<br>
						{if $activities[all].triggers|count > 0}
						<a href="/statsActivity/{$smarty.section.all.index}">Stats</a>
						{/if}
						<a href="/editActivity/{$smarty.section.all.index}">Edit</a>
						{if $activities[all].archived|isset && $activities[all].archived == 1}
							<a href="/archiveActivity/{$smarty.section.all.index}">Restore</a>
						{else}
							<a href="/archiveActivity/{$smarty.section.all.index}">Archive</a>
						{/if}
						<a href="#" onclick="confirmRedirect('/deleteActivity/{$smarty.section.all.index}'); return false;">Delete</a>
					</div>
					</div>
				</div>
			{/if}
		{/if}
	  {/section}
	  {if $count == 0}
		<p>No activities match this selection.</p>
	  {/if}
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