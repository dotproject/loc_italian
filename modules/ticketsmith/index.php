<?php

// check permissions
$denyRead = getDenyRead( $m );
$denyEdit = getDenyEdit( $m );

if ($denyRead) {
	echo '<script language="javascript">
	window.location="./index.php?m=help&a=access_denied";
	</script>
';
}
?>

<TABLE width="95%" border=0 cellpadding="0" cellspacing=1>
	<TR>
	<TD valign="top"><img src="./images/icons/ticketsmith.gif" alt="" border="0" width="42" height="42" /></td>
		<TD nowrap><h1><? echo $AppUI->_('Trouble Ticket Management');?></h1></td>
		<TD align="right" width="100%">
		<?php if (!$denyEdit) { ?>
			<input type="button" class=button value="<? echo $AppUI->_('new ticket');?>" onClick="javascript:window.location='./index.php?m=ticketsmith&a=post_ticket';">
		<?php } ?>
		</td>
	</tr>
</TABLE>
<?php

/* $Id: index.php,v 1.10 2003/03/31 21:41:55 korinek Exp $ */

require("modules/ticketsmith/config.inc.php");
require("modules/ticketsmith/common.inc.php");

/* expunge deleted tickets */
if (@$action == "expunge") {
    $deleted_parents = column2array("SELECT ticket FROM tickets WHERE type = 'Deleted'");
    for ($loop = 0; $loop < count($deleted_parents); $loop++) {
        do_query("DELETE FROM tickets WHERE ticket = '$deleted_parents[$loop]'");
        do_query("DELETE FROM tickets WHERE parent = '$deleted_parents[$loop]'");
    }
}

/* setup table & database field stuff */
$fields = array("headings" => array("Send", "Author", "Subject", "Date", 
                                    "Followup", "Status", "Priority", "Adressee"),

                "columns"  => array("ticket", "author", "subject", "timestamp", 
                                    "activity", "type", "priority", "assignment"),

                "types"    => array("view", "email", "normal", "open_date", 
                                    "activity_date", "normal", "priority_view", "user"),
                              
                "aligns"   => array("center", "left", "left", "left", "left", 
                                    "center", "center", "center"));

												
/* set up defaults for viewing */
$type = @$type ? $type : "Open";
if($type == "my"){
	$title = "My Tickets";
}
else{
	$title = "$type Tickets";
}
$column = @$column ? $column : "priority";
$direction = @$direction ? $direction : "DESC";
$offset = @$offset ? $offset : 0;
$limit = @$limit ? $limit : $CONFIG["view_rows"];


/* start page */
common_header($title);

/* count tickets */
$query = "SELECT COUNT(*) FROM tickets WHERE parent = '0'";
if ($type != 'All') {
    $query .= " AND type = '$type'";
}
$ticket_count = query2result($query);

/* paging controls */
if (($offset + $limit) < $ticket_count) {
    $page_string = ($offset + 1) . " to " . ($offset + $limit) . " of $ticket_count";
}
else {
    $page_string = ($offset + 1) . " to $ticket_count of $ticket_count";
}

/* start table */
print("<table class=maintable bgcolor=#eeeeee width=95%>\n");
print("<tr></TD>\n");
print("<td colspan=" . count($fields["headings"]) . " align=center bgcolor=#878676>");
print("<table width=100% border=0 cellspacing=1 cellpadding=1>\n");
print("<tr><td width=1%><br /></td><td width=34%><br /></td>\n");
print("<td width=32% align=center><div class=heading>".$AppUI->_($title)."</div></td>\n");
print("<td width=32% align=right valign=middle><div class=paging>");
if ($ticket_count > $limit) {
    if ($offset - $limit >= 0) {
        print("<a href=index.php?m=ticketsmith&type=$type&column=$column&direction=$direction&offset=" . ($offset - $limit) . "><img src=ltwt.gif border=0></a> | \n");
    }
    print($AppUI->_("$page_string")."\n");
    if ($offset + $limit < $ticket_count) {
        print(" | <a href=index.php?m=ticketsmith&type=$type&column=$column&direction=$direction&offset=" . ($offset + $limit) . "><img src=rtwt.gif border=0></a>\n");
    }
}
print("</div></td>\n");
print("<td width=1%><br /></td></tr></table>");
print("</td>");
print("</tr>\n");

/* form query */
$select_fields= join(", ", $fields["columns"]);
$query = "SELECT $select_fields FROM tickets WHERE ";
if ($type == "My") {
    $query .= "type = 'Open' AND (assignment = '$AppUI->user_id' OR assignment = '0') AND ";
}
elseif ($type != "All") {
    $query .= "type = '$type' AND ";
}
$query .= "parent = '0' ORDER BY " . urlencode($column) . " $direction LIMIT $offset, $limit";

/* do query */
$result = do_query($query);
$parent_count = number_rows($result);

/* output tickets */
if ($parent_count) {
    print("<tr>\n");
    for ($loop = 0; $loop < count($fields["headings"]); $loop++) {
        print("<td  align=" . $fields["aligns"][$loop] . ">");
        print("<a href=index.php?m=ticketsmith&type=$type");
        print("&column=" . $fields["columns"][$loop]);
        if ($column != $fields["columns"][$loop]) {
            $new_direction = "ASC";
        }
        else {
            if ($direction == "ASC") {
                $new_direction = "DESC";
            }
            else {
                $new_direction == "ASC";
            }
        }
        print("&direction=$new_direction");
        print("><strong>" . $AppUI->_($fields["headings"][$loop]) . "</strong></a></td>\n");
    }
    print("</tr>\n");
    while ($row = result2hash($result)) {
        print("<tr height=25>\n");
        for ($loop = 0; $loop < count($fields["headings"]); $loop++) {
            print("<td  bgcolor=white align=" . $fields["aligns"][$loop] . ">\n");
	        print(format_field($row[$fields["columns"][$loop]], $fields["types"][$loop], $row[$fields["columns"][0]]) . "\n");
            print("</td>\n");
        }
        print("</tr>\n");
    }
}
else {
    print("<tr height=25>\n");
    print("<td align=center colspan=" . count($fields["headings"]) . ">\n");
    print("There are no ");
    print($type == "All" ? "" : strtolower($type) . " ");
    print("tickets.\n");
    print("</td>\n");
    print("</tr>\n");
}

/* output action links */
print("<tr>\n");
print("<td><br /></td>\n");
print("<td colspan=" . (count($fields["headings"]) - 1) . " align=right>\n");
print("<table width=100% border=0 cellspacing=0 cellpadding=0>\n");
print("<tr height=25><td align=left>");
$types = array("My","Open","Processing","Closed","Deleted","All Tickets");
for ($loop = 0; $loop < count($types); $loop++) {
    $toggles[] = "<a href=index.php?m=ticketsmith&type=" . $types[$loop] . ">" . $AppUI->_($types[$loop]) . "</a>";
}
print(join(" | ", $toggles));
print(" </td>\n");
if ($type == "Deleted" && $parent_count) {
    print("<td align=center><a href=index.php?m=ticketsmith&type=Deleted&action=expunge>".$AppUI->_('Expunge Deleted')."</a></td>");
}
print("<td align=right><a href=index.php?m=ticketsmith&a=search>".$AppUI->_('Search')."</a> | 
<a href=index.php?m=ticketsmith&type=$type>".$AppUI->_('Back to top')."</a></td></tr>\n");
print("</table>\n");
print("</td>\n");
print("</tr>\n");    

/* end table */
print("</table>\n");

/* end page */
common_footer();

?>
