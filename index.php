<?php
/**
 * ScheduleGenerator class file
 *
 * @author Artem Demchenkov <ardemchenkov@gmail.com>
 */

require_once './config.php';

class ScheduleGenerator extends ScheduleConfig
{
    /*
     * get info from Jira
     * @return string $result
     */
    public function getInfo() {
	
	$schedule = "
	    <table width='100%' border='1'>
		<tr>
		    <th>Сотрудник</th>
		    <th>Проект</th>
		    <th>Описание задачи</th>
		    <th>Сложность</th>
		    <th>Результат</th>
		    <th>Затраченное время</th>
		    <th>Пояснения</th>
		</tr>
	";

	$headers = array(
    	    'Accept: application/json',
	    'Accept-Charset: utf-8',
	    'Content-Type: application/json'
	);
	
	$count = sizeof($this->users);
	
	for($i=0; $i<$count; $i++) {
	
	    $ch = curl_init();
	
	    $data = '
		{
		    "jql": "assignee = \''.$this->users[$i][0].'\' AND status=1 order by project",
		    "startAt": 0,
		    "fields": [
	    		"project",
			"summary",
			"customfield_10710"
		    ]
		}';

	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_VERBOSE, 1);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_URL, self::JIRA_URL.'search');
	    curl_setopt($ch, CURLOPT_USERPWD, self::USER.":".self::PASSWORD);

	    $result      = curl_exec($ch);
	    $resultArray = json_decode($result, true);	    
	    $countArray  = sizeof($resultArray["issues"]);

	    curl_close($ch);
	    
	    $scheduleByUser    = "";
	    $storyPointsByUser = 0;
	    
	    for($m=0; $m<$countArray; $m++) {
		$scheduleByUser .= "
			<tr>
			    <td></td>
			    <td>".$resultArray["issues"][$m]["fields"]["project"]["key"]."</td>
			    <td>".$resultArray["issues"][$m]["fields"]["summary"]."</td>
			    <td>".$resultArray["issues"][$m]["fields"]["customfield_10710"]."</td>
			    <td></td>
			    <td></td>
			    <td></td>
			</tr>
		";
		
		$storyPointsByUser = $storyPointsByUser + $resultArray["issues"][$m]["fields"]["customfield_10710"];
	    }

	    $schedule .= "
		<tr>
		    <td>".$this->users[$i][1]."</td>
		    <td></td>
		    <td></td>
		    <td>∑".$storyPointsByUser."</td>
		    <td></td>
		    <td>∑</td>
		    <td></td>
		</tr>
	    ".$scheduleByUser;
	    
	    /*echo "<pre>";
	    print_r ($resultArray["issues"]);
	    echo "</pre>";*/
	}
	
	$schedule .= "</table>";
	
	return $schedule;
    }
}

$generator = new ScheduleGenerator();
echo $generator->getInfo();