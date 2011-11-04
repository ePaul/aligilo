<?php
/*
 * sendmail_message.php
 *
 * @(#) $Header: /home/mlemos/cvsroot/PHPlibrary/sendmail_message.php,v 1.5 2002/08/28 06:31:01 mlemos Exp $
 *
 *
 */

class sendmail_message_class extends email_message_class
{
	var $sendmail_path="/usr/lib/sendmail";
	var $line_break="\n";
	var $sendmail_arguments="";

	Function SendMail($to,$subject,$body,$headers)
	{
		$command=$this->sendmail_path." -t";
		if(IsSet($this->delivery["Headers"]))
		{
			$headers_values=$this->delivery["Headers"];
			for($return_path="",$header=0,Reset($headers_values);$header<count($headers_values);$header++,Next($headers_values))
			{
				if(strtolower(Key($headers_values))=="return-path")
				{
					$return_path=$headers_values[Key($headers_values)];
					break;
				}
			}
			if(strlen($return_path))
				$command.=" -f $return_path";
		}
		if(strlen($this->sendmail_arguments))
			$command.=" ".$this->sendmail_arguments;
		if(!($pipe=popen($command,"w")))
			return($this->OutputError("it was not possible to open sendmail input pipe"));
		if(!fputs($pipe,"To: $to\n")
		|| !fputs($pipe,"Subject: $subject\n")
		|| ($headers!=""
		&& !fputs($pipe,"$headers\n"))
		|| !fputs($pipe,"\n$body"))
			return($this->OutputError("it was not possible to write sendmail input pipe"));
		pclose($pipe);
		return("");
	}
};

?>
