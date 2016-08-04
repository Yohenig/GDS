<?php
 
$string = file_get_contents("../../Controller/cancel.json");
$cancel= json_decode($string, true);


	$html='';
		
	if(count($cancel)>0){

		if ($cancel[1]['xpath'] != array()) {
					$html.= $cancel[1]['TicketTimeLimit'];
					echo '<pre><b>TICKET</b>'.$cancel[1]['Ticket'].' HAS BEEN VOIDED.<br/><b>NEW RESERVATION TIMELIMIT:</b> $tl.</pre>';
				} else {
					$html.= '<pre><b>RESERVATION</b> '.$cancel[1]['Pnr'].' HAS BEEN CANCELLED.<br></pre>';
		}

		echo $html;
	}



?>