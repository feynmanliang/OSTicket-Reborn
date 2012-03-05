			</div>
		</div>
		<div id="secondaryContent">
<?
if(!is_object($thisclient) || !$thisclient->isValid()) {
?>
			<div class="box boxA">

				<div class="boxContent">
					<form method="post" action="login.php">
						<div>
							<span><?=$ctlang->dot('foot_001')?></span>
							<input type="text" class="text" name="lemail" value="<?=$e?>" />
							<span><?=$ctlang->dot('foot_002')?></span>
							<input type="<?=(CTHEME_TICKETPWD)?'password':'text'?>" class="text" name="lticket" value="<?=$t?>" />
							<input type="submit" class="button" value="<?=$ctlang->dot('foot_003')?>" />

							<a href="open.php"><?=$ctlang->dot('foot_004')?></a>
						</div>
					</form>
				</div>
			</div>
<?
}
else {
?>
			<div class="box boxB">
				<div class="boxContent">
					<form method="get" action="view.php">

						<div>
							<span><?=$ctlang->dot('foot_005')?></span>
							<input type="text" class="text" maxlength="32" name="sstring" value="<?=$_REQUEST['sstring']?>" />
							<input type="submit" class="button" value="<?=$ctlang->dot('foot_006')?>" />
							<a href="view.php"><?=$ctlang->dot('foot_007')?></a>
						</div>
					</form>
				</div>
			</div>

                        <div class="box">
                                <h3><?=$ctlang->dot('foot_008')?></h3>
                                <div class="boxSubContent">
                                        <p><?=$thisclient->fullname?></p>
                                        <p><?=$thisclient->username?></p>
                                        <p><?=$thisclient->session->ip?></p>
                                </div>
                        </div>

<?
}
?>
		</div>
		<div class="clear"></div>
	</div>
	<div id="footer">
		<!-- show your gratitude by not removing all credits ;) -->
		<p style="font-size:11px;"><?=$ctlang->dot('foot_009')?></p>
	</div>
</div>
</body>
</html>
