<div class="box_normal">
	<h3>{confirm_del}</h3>
	<div class="boxbox">
	{message}<br /><br />
	<form method="post" action="einstellungen.php" id="df">
		<input type="button" value="{nein}" onclick="location.href='einstellungen.php?modul=ansatz';" />
		<input type="button" value="{ja}" onclick="$('df').submit();"/>
		<input type="hidden" name="modul" value="ansatz_del" />
		<input type="hidden" name="bestaetigt" value="true" />
		<input type="hidden" name="id" value="{id}" />
	</form>
	</div>
</div>
