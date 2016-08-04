<?php
 
$string = file_get_contents("../../Controller/issue.json");
$issue= json_decode($string, true);


	$html='<pre>';
		
	if(count($issue)>0){

		for ($i=1; $i <=count($issue); $i++) { 
					$TicketNumber = $issue[$i]['TicketNumber'][0];
					$CommissionAmount =  $issue[$i]['CommissionAmount'][0];
					$TotalAmount =  $issue[$i]['TotalAmount'][0];
					$GivenName =  $issue[$i]['GivenName'][0];
					$Surname =  $issue[$i]['Surname'][0];
					$html.= '<hr/>	
						<br/><b>Ticket:</b>'. $TicketNumber
						.'<br/><b>Amount:</b>'.   $TotalAmount
						.'<br/><b>Commision:</b>'. $CommissionAmount
						.'<br/><b>GivenName:</b>'. $GivenName
						.'<br/><b>Surname:</b>'. $Surname
					.'<br/><br/>';
		}
		$html.='</pre>';
		echo $html;
		
	}

	echo '<input type="button" value="Nueva Reservación" class="btn btn-primary" onclick="window.location=\'../Avail/index.php\'"/>';



?>