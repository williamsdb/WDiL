{include file="header.tpl"}

<h3>Statistics</h3>

{if $total ==0}
    <p>Not enough activities to produce any stats. Try <a href="/addActivity">adding one here</a>.</p>
{else}
    <table class="table table-striped">
        <tbody>
            <tr><td><strong>Total activities</strong></td><td align="left">{$total}</td></tr>
            <tr><td><strong>Triggered a total of</strong></td><td align="left">{$totTriggered} times</td></tr>
            <tr><td><strong>Never Triggered</strong></td><td>{$notTriggered}</td></tr>
            <tr><td><strong>Longest interval</strong></td><td>{$maxInterval}</td></tr>
            <tr><td><strong>Shortest interval</strong></td><td>{$minInterval}</td></tr>
            <tr><td><strong>Triggered most recently</strong></td><td>{$maxTimestamp}</td></tr>
            <tr><td><strong>Triggered longest ago</strong></td><td>{$minTimestamp}</td></tr>
        </tbody>
        </table>
    </table>
{/if}

{include file="footer.tpl"}