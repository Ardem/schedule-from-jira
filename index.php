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
		$scheduleByUser .= $this->getStyled($printingStyle, "issue", $resultArray["issues"][$m]["fields"]);
		
		$storyPointsByUser = $storyPointsByUser + $resultArray["issues"][$m]["fields"]["customfield_10710"];
	    }

	    $schedule .= $this->getStyled($printingStyle, "user", $this->users[$i], $storyPointsByUser).$scheduleByUser;
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
    public function getStyled($printingStyle, $field, $data, $storyPointsByUser = 0) {
	if($printingStyle == "wikimarkup") {
	    if($field == "issue") {
		$result = $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"].$data["project"]["key"]." ".
			  $this->styles[$printingStyle]["td"].$data["summary"]." ".
			  $this->styles[$printingStyle]["td"].$data["customfield_10710"]." ".
			  $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"]." ".$this->styles[$printingStyle]["trc"];
	    }
	    elseif($field == "user") {
		$result = $this->styles[$printingStyle]["td"].$data[1]." ".
			  $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"]."∑".$storyPointsByUser." ".
			  $this->styles[$printingStyle]["td"]." ".
			  $this->styles[$printingStyle]["td"]."∑".
			  $this->styles[$printingStyle]["td"]." ".$this->styles[$printingStyle]["trc"];
	    }
	}
	elseif($printingStyle == "html") {
	    if($field == "issue") {
		$result = $this->styles[$printingStyle]["tr"].
			    $this->styles[$printingStyle]["td"].$this->styles[$printingStyle]["tdc"].
			    $this->styles[$printingStyle]["td"].$data["project"]["key"].$this->styles[$printingStyle]["tdc"].
			    $this->styles[$printingStyle]["td"].$data["summary"].$this->styles[$printingStyle]["tdc"].
			    $this->styles[$printingStyle]["td"].$data["customfield_10710"].$this->styles[$printingStyle]["tdc"].
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