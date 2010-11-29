<div class="question">
<div class="box_normal">
	<h3>{titel}</h3>
	<div class="boxbox">
	{nachricht}<br /><br />
	<form method="post" action="zeit.php" id="df">
		<input type="button" value="{nein}" onclick="location.href='{location}'" />
		<input type="submit" value="{ja}" />
		<input type="hidden" name="modul" value="del" />
		<input type="hidden" name="step" value="2" />
		<input type="hidden" name="id" value="{id}" />
	</form>
	</div>
</div>
</div>
