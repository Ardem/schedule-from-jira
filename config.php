<?php
/**
 * ScheduleConfig class file
 *
 * @author Artem Demchenkov <ardemchenkov@gmail.com>
 */

/*
 * jira)url, user, password and user's list for access to Jira
 */

class ScheduleConfig
{
    const JIRA_URL = "https://example.com/rest/api/latest/";
    const USER     = "name1.surname1";
    const PASSWORD = "password";
    
    protected $users = array(
	array("name1.surname1", "Name1 Surname1"),
	array("name2.surname2", "Name2 Surname2")
    );
}