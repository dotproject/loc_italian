<TABLE width="95%" border=0 cellpadding="0" cellspacing=1>
	<TR>
	<TD valign="top"><img src="./images/icons/ticketsmith.gif" alt="" border="0" width="42" height="42" /></td>
		<TD nowrap><h1><? echo $AppUI->_('Trouble Ticket Management');?></h1></td>
		<TD valign="top" align="right" width="100%">&nbsp;</td>
	</tr>
</TABLE>
<?php

/* $Id: view.php,v 1.10 2003/02/08 01:41:24 kobudo Exp $ */

require("./modules/ticketsmith/config.inc.php");
require("./modules/ticketsmith/common.inc.php");

/* Centralize references */
$app_root="http://www.intera.it/pla";

$ticket = $_REQUEST['ticket'];

/* initialize fields */
if ($ticket_type == "Staff Followup" || $ticket_type == "Client Followup") {
    
    $title = "" . $AppUI->_("$ticket_type to Ticket") . " #$ticket_parent";
    
    $fields = array("headings" => array("From", "To", "Subject", "Date", "Cc", "<br />"),
                    "columns"  => array("author", "recipient", "subject", "timestamp", "cc", "body"), 
                    "types"    => array("email", "original_author", "normal", "elapsed_date", "email", "body"));

}
elseif ($ticket_type == "Staff Comment") {

    $title = "" . $AppUI->_("$ticket_type to Ticket") . " #$ticket_parent";
    
    $fields = array("headings" => array("From", "Date", "<br />"),
                    "columns"  => array("author", "timestamp", "body"), 
                    "types"    => array("email", "elapsed_date", "body"));

}
else {
    
    $title = "" . $AppUI->_("Ticket") . " #$ticket";
    
    $fields = array("headings" => array("From", "Subject", "Date", "Cc", "Status", 
                                        "Priority", "Owner", "<br />"),
                    
                    "columns"  => array("author", "subject", "timestamp", "cc", 
                                        "type", "priority", "assignment", "body"),
                    
                    "types"    => array("email", "normal", "elapsed_date", "email", 
                                        "status", "priority_select", "assignment", "body"));
}

/* perform updates */
if (@$type_toggle || @$priority_toggle || @$assignment_toggle) {
    do_query("UPDATE tickets SET type = '$type_toggle', priority = '$priority_toggle', assignment = '$assignment_toggle' WHERE ticket = '$ticket'");
	if(@$assignment_toggle != @$orig_assignment)
	{
		$mailinfo = query2hash("SELECT user_first_name, user_last_name, user_email from users WHERE user_id = $assignment_toggle");
		
		$message .= "<html>";
		$message .= "<head>";
		$message .= "<style>";
		$message .= ".title {";
		$message .= "	FONT-SIZE: 18pt; SIZE: 18pt;";
		$message .= "}";
		$message .= "</style>";
		$message .= "<title>Segnalazione per te</title>"; //da mettere a posto
		$message .= "</head>";
		$message .= "<body>";
		$message .= "";
		$message .= "<TABLE border=0 cellpadding=4 cellspacing=1>";
		$message .= "	<TR>";
		$message .= "	<TD valign=top><img src=$app_root/images/icons/ticketsmith.gif alt= border=0 width=42 height=42></td>";
		$message .= "		<TD nowrap><span class=title>Segnalazione</span></td>";
		$message .= "		<TD valign=top align=right width=100%>&nbsp;</td>";
		$message .= "	</tr>";
		$message .= "</TABLE>";
		$message .= "<TABLE width=600 border=0 cellpadding=4 cellspacing=1 bgcolor=#878676>";
		$message .= "	<TR>";
		$message .= "		<TD colspan=2><font face=arial,san-serif size=2 color=white>Segnalazione per te</font></TD>";
		$message .= "	</tr>";
		$message .= "	<TR>";
		$message .= "		<TD bgcolor=white nowrap><font face=arial,san-serif size=2>Ticket ID:</font></TD>";
		$message .= "		<TD bgcolor=white nowrap><font face=arial,san-serif size=2>$ticket</font></TD>";
		$message .= "	</tr>";
		$message .= "	<TR>";
		$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>Author:</font></TD>";
		$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>" . str_replace(">", "&gt;", str_replace("<", "&lt;", str_replace('"', '', $author))) . "</font></TD>";
		$message .= "	</tr>";
		$message .= "	<TR>";
		$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>Oggetto:</font></TD>";
		$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>$subject</font></TD>";
		$message .= "	</tr>";
		$message .= "	<TR>";
		$message .= "		<TD bgcolor=white nowrap><font face=arial,san-serif size=2>link:</font></TD>";
		$message .= "		<TD bgcolor=white nowrap><a href=\"$app_root/index.php?m=ticketsmith&a=view&ticket=$ticket\"><font face=arial,san-serif size=2>$app_root/index.php?m=ticketsmith&a=view&ticket=$ticket</font></a></TD>";
		$message .= "	</tr>";
		$message .= "</TABLE>";
		$message .= "</body>";
		$message .= "</html>";


		mail($mailinfo["user_email"], "Segnalazione #$ticket per te", $message, "From: " . $CONFIG['reply_to'] . "\nContent-type: text/html\nMime-type: 1.0");
	}

}

/* start page */
common_header($title);


/* start table */
print("<table class=maintable bgcolor=\"#eeeeee\" cellpadding=5 width=95%>\n");
print("<tr>\n");
print("<td colspan=\"2\" align=\"center\"  bgcolor=#878676>\n");
print("<div class=\"heading\">" . $AppUI->_("$title") . "</div>\n");
print("</td>\n");
print("</tr>\n");
/* start form */
print("<form name=\"form\" action=index.php?m=ticketsmith&a=view method=\"post\">\n");
print("<input type=\"hidden\" name=\"ticket\" value=\"$ticket\">\n");

/* get ticket */
$ticket_info = query2hash("SELECT * FROM tickets WHERE ticket = $ticket");

print("<input type=\"hidden\" name=\"orig_assignment\" value='" . $ticket_info["assignment"] . "'>\n");
print("<input type=\"hidden\" name=\"author\" value='" . $ticket_info["author"] . "'>\n");
print("<input type=\"hidden\" name=\"priority\" value='" . $ticket_info["priority"] . "'>\n");
print("<input type=\"hidden\" name=\"subject\" value='" . $ticket_info["subject"] . "'>\n");

/* output ticket */
for ($loop = 0; $loop < count($fields["headings"]); $loop++) {
    print("<tr>\n");
    print("<td align=\"left\"><strong>" . $AppUI->_($fields["headings"][$loop]) . "</strong></td>");
    print("<td align=\"left\">" . format_field($ticket_info[$fields["columns"][$loop]], $fields["types"][$loop]) . "</td>\n");
    print("</tr>\n");
}
$ticket_info["assignment"];

/* output attachment indicator */
if (query2result("SELECT attachment FROM tickets WHERE ticket = '$ticket'")) {
    print("<tr>\n");
    print("<td align=\"left\"><strong>". $AppUI->_("Attachments") ."</strong></td>");
    print("<td align=\"left\">Gli allegati sono stati rimossi.</td>\n");
    print("</tr>\n");
}

/* output followup navigation */
if ($ticket_type != "Staff Followup" && $ticket_type != "Client Followup" && $ticket_type != "Staff Comment") {

    /* output followups */
    print("<tr>\n");
    print("<td align=\"left\" valign=\"top\"><strong>". $AppUI->_("Followup") ."</strong></td>\n");
    print("<td align=\"left\" valign=\"top\">\n");
   
    /* grab followups */
    $query = "SELECT ticket, type, timestamp, author FROM tickets WHERE parent = '$ticket' ORDER BY ticket " . $CONFIG["followup_order"];
    $result = do_query($query);
    
    if (number_rows($result)) {

        /* print followups */
        print("<table width=\"100%\" border=\"1\" cellspacing=\"5\" cellpadding=\"5\">\n");
        while ($row = result2hash($result)) {
            
            /* determine row color */
            $color = (@$number++ % 2 == 0) ? "#d3dce3" : "#dddddd";

            /* start row */
            print("<tr>\n");
            
            /* do number/author */
            print("<td bgcolor=\"$color\">\n");
            print("<strong>$number</strong> : \n");
            $row["author"] = ereg_replace("\"", "", $row["author"]);
            $row["author"] = htmlspecialchars($row["author"]);
            print($row["author"] . "\n");    
            print("</td>\n");
            
            /* do type */
            print("<td bgcolor=\"$color\"><a href=\"index.php?m=ticketsmith&a=view&ticket=" . $row["ticket"] . "\">" . $AppUI->_($row["type"]) . "</a></td>\n");
            
            /* do timestamp */
            print("<td bgcolor=\"$color\">\n");
            print(get_time_ago($row["timestamp"]));
            print("</td>\n");

            /* end row */
            print("</tr>\n");
        
        }
        print("</table>\n");
    
    }
    else {
        print("<em>none</em>\n");
    }
    
    print("</td>\n</tr>\n");
    
}

else {

    /* get peer followups */
    $results = do_query("SELECT ticket, type FROM tickets WHERE parent = '$ticket_parent' ORDER BY ticket " . $CONFIG["followup_order"]);

    /* parse followups */
    while ($row = result2hash($results)) {
        $peer_tickets[] = $row["ticket"];
    }
    
    /* count peers */
    $peer_count = count($peer_tickets);
    
    if ($peer_count > 1) {
    
        /* start row */
        print("<tr>\n");
        print("<td><strong>". $AppUI->_("Followup") ."</strong></td>\n");
    
        /* start cell */
        print("<td valign=\"middle\">");
        
        /* form peer links */
        for ($loop = 0; $loop < $peer_count; $loop++) {
            if ($peer_tickets[$loop] == $ticket) {
                $viewed_peer = $loop;
                $peer_strings[$loop] = "<strong>" . ($loop + 1) . "</strong>";
            }
            else {
                $peer_strings[$loop] = "<a href=\"index.php?m=ticketsmith&a=view&ticket=$peer_tickets[$loop]\">" . ($loop + 1) . "</a>";
            }
        }
            
        /* previous navigator */
        if ($viewed_peer > 0) {
            print("<a href=\"index.php?m=ticketsmith&a=view&ticket=" . $peer_tickets[$viewed_peer - 1] . "\">");
            print($CONFIG["followup_order"] == "ASC" ?  "&laquo;" : "&raquo;");
            print("</a> | ");
        }
        
        /* ticket list */
        print(join(" | ", $peer_strings));
        
        /* next navigator */
        if ($peer_count - $viewed_peer > 1) {
            print(" | <a href=\"index.php?m=ticketsmith&a=view&ticket=" . $peer_tickets[$viewed_peer + 1] . "\">");
            print($CONFIG["followup_order"] == "ASC" ?  "&raquo;" : "&laquo;");
            print("</a>");
        }

        /* end cell */
        print("</td>\n");

        /* end row */
        print("</tr>\n");
    
    }

}

/* output action links */
print("<tr>\n");
print("<td><br /></td>\n");
print("<td>\n");
print("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
if ($ticket_type == "Staff Followup" || $ticket_type == "Client Followup" || $ticket_type == "Staff Comment") {
    print("<tr><td align=\"left\"><a href=index.php?m=ticketsmith&a=followup&ticket=$ticket>". $AppUI->_("Post followup (email client)") ."</a> | ");
    print("<a href=index.php?m=ticketsmith&a=comment&ticket=$ticket>". $AppUI->_("Post internal comment") ."</a> | ");
    print("<a href=index.php?m=ticketsmith&a=view&ticket=$ticket_parent>". $AppUI->_("Return to parent") ."</a> | ");
}
else {
    print("<tr><td align=\"left\"><a href=index.php?m=ticketsmith&a=followup&ticket=$ticket>". $AppUI->_("Post followup (emails client)") ."</a> | ");
    print("<a href=index.php?m=ticketsmith&a=comment&ticket=$ticket>". $AppUI->_("Post internal comment") ."</a> | ");
}
print("<a href=index.php?m=ticketsmith>". $AppUI->_("Return to ticket list") ."</a></td>");
print("<td align=\"right\"><a href=\"index.php?m=ticketsmith&a=view&ticket=$ticket\">". $AppUI->_("Back to top") ."</a></td></tr>\n");
print("</table>\n");
print("</td>");
print("</tr>\n");

/* end table */
print("</table>\n");

/* end form */
print("</form>\n");

/* end page */
common_footer();

?>
