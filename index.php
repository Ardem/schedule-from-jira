<?php

require_once './config.php';

class ScheduleGenerator extends ScheduleConfig
{
    /*
     * get info from Jira
     * @param string $resource
     * @return answer from Jira
     */
    public function getInfo($resource) {
	$content  = file_get_contents(self::JIRA_URL.$resource);
	return $content;
    }
}

$generator = new ScheduleGenerator();

echo $generator->getInfo("serverInfo");