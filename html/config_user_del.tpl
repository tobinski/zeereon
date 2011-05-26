<div class="box_normal">
	<h3>{confirm_del}</h3>
	<div class="boxbox">
	{message}<br /><br />
	<form method="get" action="index.php" id="df">
		<input type="button" value="{nein}" onclick="location.href='?section=einstellungen&function=build&modul=user';" />
		<input type="button" value="{ja}" onclick="$('#df').submit();"/>
		<input type="hidden" name="section" value="einstellungen" />
		<input type="hidden" name="modul" value="user_del" />
		<input type="hidden" name="function" value="build" />
		<input type="hidden" name="bestaetigt" value="true" />
		<input type="hidden" name="id" value="{id}" />
	</form>
	</div>
</div>
