{include file="header.tpl"}

{if $activities != ''}
<figure>
<table style="width: 100%">
<thead>
	<tr>
	<th>Activity</th>
	<th>Last Triggered</th>
	<th>Actions</th>
	</tr>
</thead>
<tbody>
	{section name=all loop=$activities}
	<tr>
		<td>{$activities[all].activityName}</td>
		{if $activities[all].triggers|count > 0}
			{assign var="latestTimestamp" value=$activities[all].triggers[$activities[all].triggers|count - 1].timestamp}
			<td>{$latestTimestamp|date_format_tz:"Y-m-d H:i:s":$smarty.const.TZ}</td>
		{else}
			<td>Not yet Triggered</td>
		{/if}
		<td>
			<a href="/triggerActivity/{$smarty.section.all.index}" class="button">Trigger</a><br>
			{if $activities[all].triggers|count > 1}<a href="/statsActivity/{$smarty.section.all.index}">Stats</a>{/if}
			<a href="/editActivity/{$smarty.section.all.index}">Edit</a>
			<a href="#" onclick="confirmRedirect('/deleteActivity/{$smarty.section.all.index}'); return false;">Delete</a>
		</td>
	</tr>
	{/section}
</tbody>
<tfoot>
	<tr>
		<th>Activity</th>
		<th>Last Triggered</th>
		<th>Actions</th>
	</tr>
</tfoot>
</table>
<figcaption>&nbsp;</figcaption>
</figure>
{else}
	<p>You haven't any activities setup.</p>
{/if}
<a class="button" href="/addActivity">Add a new activity</a>


{include file="footer.tpl"}