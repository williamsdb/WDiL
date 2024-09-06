{include file="header.tpl"}

<h3>Statistics for "{$activityName}"</h3>

<table style="width: 100%">
    <tbody>
        <tr><td><strong>Last triggered</strong></td><td>{$triggers[$triggers|count - 1].timestamp|date_format_tz:"Y-m-d H:i:s":$smarty.const.TZ}</td></tr>
        <tr><td><strong>Elapsed since last triggered</strong></td><td>{$elp}</td></tr>
        <tr><td><strong>Times triggered</strong></td><td>{$triggers|count}</td></tr>
        <tr><td><strong>Avg trigger interval</strong></td><td>{$avg}</td></tr>
        <tr><td><strong>Longest interval</strong></td><td>{$lrg}</td></tr>
    </tbody>
    </table>
</table>


<hr>
<h4>Last 10 trigger dates</h4>

<table style="width: 100%">
    <thead>
        <tr>
            <th>Date & time</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$triggers key=i item=trigger name=foo}
            {if $smarty.foreach.foo.index == 10}
            {break}
            {/if}
			<tr><td>{$trigger.timestamp|date_format_tz:"Y-m-d H:i:s":$smarty.const.TZ}</td></tr>
        {/foreach}
    </tbody>
    <tfoot>
        <tr>
            <th>Date & time</th>
        </tr>
    </tfoot>
    </table>
</table>

{include file="footer.tpl"}