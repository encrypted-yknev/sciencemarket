<?php

require 'vendor/autoload.php';

class ses_mail{

	private $username = '123';

	// Replae with actual user name and password.
	
	private $password = '123';

	private $host = "email-smtp.us-east-1.amazonaws.com";

	private $port = 587;

	private $from_email = "no-reply@sciencemarket.org";

	private $from_name = "no-reply";

	// public $to_name;
	//
	// public $to_email;
	//
	// public $subject;
	//
	// public $body;

	private $mail;

	public function __construct(){
		$this->mail = new PHPMailer;
		$this->mail->isSMTP();
		$this->mail->Username = $this->username;
		$this->mail->Password = $this->password;
		$this->mail->Host = $this->host;
		$this->mail->port = $this->port;
		$this->mail->SMTPAuth = true;
		$this->mail->SMTPSecue = 'tls';
		$this->mail->setFrom($this->from_email, $this->from_name);

	}

	public function addImage($img_path, $cid){
		$this->mail->AddEmbeddedImage($img_path, $cid);
	}

	public function sendEmail($to_email, $to_name, $subject, $body, $alt_body=NULL, $is_html=true){
		$this->mail->Subject = $subject;
		$this->mail->Body = $body;
		if($alt_body!=NULL) $this->mail->AltBody = $alt_body;
		$this->mail->addAddress($to_email, $to_name);
		if($is_html) $this->mail->isHTML(true);
		if(!$this->mail->send()){
			return $this->mail->ErrorInfo;
		}
		else{
			return "Success email sent";
		}

	}



}

 ?>
