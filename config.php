<?php
/**
 * ScheduleConfig class file
 *
 * @author Artem Demchenkov <ardemchenkov@gmail.com>
 * @version 1.0
 */

/*
 * jira_url, fields for schedule, user for access to jira, password for access to jira, user's list for schedule and styles for view
 */

class ScheduleConfig
{
    const JIRA_URL  = "https://example.com/rest/api/latest/";
    const FIELDS    = '"project", "summary", "customfield_10710"';
    const USER     = "name1.surname1";
    const PASSWORD = "password";
    
    protected $users = array(
	array("name1.surname1", "Name1 Surname1"),
	array("name2.surname2", "Name2 Surname2")
    );
    
    // styles for view
    protected $styles = array(
	"html" => array(
	   "header" => "
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
		",
	    "tr"  => "<tr>",
	    "td"  => "<td>",
	    "trc" => "</td>",
	    "tdc" => "</td>",
	    "footer" => "</table>"
	),
	"wikimarkup" => array(
	   "header" => "
		||*Сотрудник*||*Проект*||*Описание задачи*||*Сложность*||*Результат*||*Затраченное время*||*Пояснения*||<br/>
		",
	    "td"  => "|",
	    "trc" => "|<br/>",
	    "footer" => ""
	),
    );
}
