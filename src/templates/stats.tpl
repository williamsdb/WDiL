{include file="header.tpl"}

<h3>Statistics</h3>

<table class="table table-striped">
    <tbody>
        <tr><td><strong>Total activities</strong></td><td align="left">{$total}</td></tr>
        <tr><td><strong>Triggered a total</strong></td><td align="left">{$totTriggered} times</td></tr>
        <tr><td><strong>Longest interval</strong></td><td>{$maxInterval}</td></tr>
        <tr><td><strong>Shortest interval</strong></td><td>{$minInterval}</td></tr>
        <tr><td><strong>Triggered most recently</strong></td><td>{$maxTimestamp}</td></tr>
        <tr><td><strong>Triggered longest ago</strong></td><td>{$minTimestamp}</td></tr>
    </tbody>
    </table>
</table>

{include file="footer.tpl"}