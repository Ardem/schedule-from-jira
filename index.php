<?php
/**
 * ScheduleGenerator class file
 *
 * @author Artem Demchenkov <ardemchenkov@gmail.com>
 * @version 1.0
 */

require_once './config.php';

class ScheduleGenerator extends ScheduleConfig
{
    /*
     * get info from Jira
     * @param wikimarkup|html $printingStyle
     * @return string $result
     */
    public function getInfo($printingStyle) {
	
	$schedule = $this->styles[$printingStyle]["header"];

	$headers = array(
    	    'Accept: application/json',
	    'Accept-Charset: utf-8',
	    'Content-Type: application/json'
	);
	
	$count = sizeof($this->users);
	
	// take a list of issues for all user from the list
	for($i=0; $i<$count; $i++) {
	
	    $ch = curl_init();
	
	    $data = '
		{
		    "jql": "assignee = \''.$this->users[$i][0].'\' AND status=1 order by project",
		    "startAt": 0,
		    "fields": [
	    		'.self::FIELDS.'
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
	    
	    //use an every issue from the list for output generation
	    for($m=0; $m<$countArray; $m++) {
		$scheduleByUser .= $this->getView($printingStyle, "issue", $resultArray["issues"][$m]);
		
		$storyPointsByUser = $storyPointsByUser + $resultArray["issues"][$m]["fields"]["customfield_10710"];
	    }

	    $schedule .= $this->getView($printingStyle, "user", $this->users[$i], $storyPointsByUser).$scheduleByUser;
	}
	
	$schedule .= $this->styles[$printingStyle]["footer"];
	
	return $schedule;
    }
    
    /*
     * create a stylish fields
     * @param wikimarkup|html $printingStyle
     * @param user|issue $field
     * @param array() $data
     * @param int $storyPointsByUser
     * @return string $result
     */
    public function getView($printingStyle, $field, $data, $storyPointsByUser = 0) {
	if($printingStyle == "wikimarkup") {
	    if($field == "issue") {
		$result = $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"].$data["fields"]["project"]["key"]." ".
			  $this->styles[$printingStyle]["td"]."{JIRA: ".$data["key"]."} ".
			  $this->styles[$printingStyle]["td"].$data["fields"]["customfield_10710"]." ".
			  $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"]." ".$this->styles[$printingStyle]["trc"];
	    }
	    elseif($field == "user") {
		$result = $this->styles[$printingStyle]["td"]."*{color:green}".$data[1]."{color}* ".
			  $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"]."*{color:navy}∑".$storyPointsByUser."{color}* ".
			  $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"]."*{color:navy}∑{color}*".
			  $this->styles[$printingStyle]["td"]." ".$this->styles[$printingStyle]["trc"];
	    }
	}
	elseif($printingStyle == "html") {
	    if($field == "issue") {
		$result = $this->styles[$printingStyle]["tr"].
			    $this->styles[$printingStyle]["td"].$this->styles[$printingStyle]["tdc"].
			    $this->styles[$printingStyle]["td"].$data["fields"]["project"]["key"].$this->styles[$printingStyle]["tdc"].
			    $this->styles[$printingStyle]["td"].$data["fields"]["summary"]." (".$data["key"].") ".$this->styles[$printingStyle]["tdc"].
			    $this->styles[$printingStyle]["td"].$data["fields"]["customfield_10710"].$this->styles[$printingStyle]["tdc"].
			    $this->styles[$printingStyle]["td"].$this->styles[$printingStyle]["tdc"].
			    $this->styles[$printingStyle]["td"].$this->styles[$printingStyle]["tdc"].
			    $this->styles[$printingStyle]["td"].$this->styles[$printingStyle]["tdc"].
			    $this->styles[$printingStyle]["trc"];
	    }
	    elseif($field == "user") {
		$result = $this->styles[$printingStyle]["tr"].
			$this->styles[$printingStyle]["td"].$data[1].$this->styles[$printingStyle]["tdc"].
			$this->styles[$printingStyle]["td"].$this->styles[$printingStyle]["tdc"].
			$this->styles[$printingStyle]["td"].$this->styles[$printingStyle]["tdc"].
			$this->styles[$printingStyle]["td"]."∑".$storyPointsByUser.$this->styles[$printingStyle]["tdc"].
			$this->styles[$printingStyle]["td"].$this->styles[$printingStyle]["tdc"].
			$this->styles[$printingStyle]["td"]."∑".$this->styles[$printingStyle]["tdc"].
			$this->styles[$printingStyle]["td"].$this->styles[$printingStyle]["tdc"].
			$this->styles[$printingStyle]["trc"];
	    }
	}
	
	return $result;
    }
}

$generator = new ScheduleGenerator();
echo $generator->getInfo("wikimarkup");