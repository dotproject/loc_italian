<?php /* TICKETSMITH $Id: post_ticket.php,v 1.7 2003/03/19 23:31:06 eddieajau Exp $ */
##
##	Ticketsmith Post Ticket
##
?>

<SCRIPT language="javascript">
function submitIt() {
	var f = document.ticket;
	var msg = '';
	if (f.name.value.length < 3) {
		msg += "\n- a valid name";
	}
	if (f.email.value.length < 3) {
		msg += "\n- a valid email";
	}
	if (f.subject.value.length < 3) {
		msg += "\n- a valid subject";
	}
	if (f.description.value.length < 3) {
		msg += "\n- a valid desciption";
	}
	
	if (msg.length < 1) {
		f.submit();
	} else {
		alert( "Please provide the following detail before submitting:" + msg );
	}
}
</script>

<TABLE width="95%" border=0 cellpadding="0" cellspacing=1>
<TR>
	<TD valign="top"><img src="./images/icons/ticketsmith.gif" alt="" border="0" width="42" height="42" /></td>
	<TD nowrap><h1>Segnala un problema o errore</h1></td>
	<TD align="right" width="100%"></td>
</tr>
</TABLE>

<TABLE width="90%" border=0 bgcolor="#f4efe3" cellpadding="0" cellspacing=1>
<form name="ticket" action="?m=ticketsmith" method="post">
<input type="hidden" name="dosql" value="do_ticket_aed">

<TR height="20">
	<TD bgcolor="#878676" colspan=2>
		&nbsp;<font face="verdana,helveitica,arial,sans-serif" color=#ffffff><strong>Dettagli</strong></font>
	</td>
</tr>
<tr>
	<TD align="right">Nome mitt:</td>
	<TD><input type="text" class="text" name="name" value="<?php echo "$AppUI->user_first_name $AppUI->user_last_name";?>" size=50 maxlength="255"> <span class="smallNorm">(richiesto)</span></td>
</tr>
<tr>
	<TD align="right">E-Mail mitt:</td>
	<TD><input type="text" class="text" name="email" value="<?php  echo "$AppUI->user_email";?>" size=50 maxlength="50"> <span class="smallNorm">(richiesto)</span></td>
</tr>
<tr>
	<TD align="right">Oggetto:</td>
	<TD><input type="text" class="text" name="subject" value="" size=50 maxlength="50"> <span class="smallNorm">(richiesto)</span></td>
</tr>
<tr>
	<TD align="right">Priorità:</td>
	<TD>
		<select name="priority">
			<option value="0">bassa
			<option value="1" selected>normale
			<option value="2">alta
			<option value="3">altissima
			<option value="4"><strong>112 (Chiamate i pompieri!!!)</strong>
		</select>
	</td>
</tr>
<TR>
	<TD align="right">Descrizione del problema: </td>
	<td><span class="smallNorm">(richiesto)</span></td>
</tr>
<TR>
	<TD colspan=2 align="center">
		<textarea cols="70" rows="10" class="textarea" name="description"><?php echo @$crow["description"];?></textarea>
	</td>
</tr>
<TR>
	<TD><input type="button" value="back" class="button" onClick="javascript:history.back(-1);"></td>
	<TD align="right"><input type="button" value="submit" class="button" onClick="submitIt()"></td>
</tr>
</form>
</TABLE>
&nbsp;<br />&nbsp;<br />&nbsp;
