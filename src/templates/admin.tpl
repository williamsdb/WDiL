{include file="header.tpl"}

<h3>Admin</h3>

<table class="table table-striped">
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

{include file="footer.tpl"}