{include file="header.tpl"}

<figure>
<table style="width: 100%">
<thead>
	<tr>
	<th>Username</th>
	<th>Email</th>
	<th>Actions</th>
	</tr>
</thead>
<tbody>
	{section name=all loop=$users}
	<tr>
        <td>{$users[all].username}</td>
		<td>{$users[all].email}</td>
        <td> &nbsp;
		</td>
	</tr>
	{/section}
</tbody>
<tfoot>
	<tr>
	<th>Username</th>
	<th>Email</th>
		<th>Actions</th>
	</tr>
</tfoot>
</table>
</figure>

{include file="footer.tpl"}