<?php
/***************************************************

 Smooth Student Submission System (SSSS)

 This file is part of SSSS, which is licensed
 under the Creative Commons: Attribution,
 Non-Commercial, Share Alike license (see
 http://creativecommons.org/licenses/by-nc-sa/3.0/)
 
 The first version was developed by
 Gjalt-Jorn Peters for the Dutch Open University
 in September 2012.
 
***************************************************/

  function ps_abc_verify() {
  
    global $infoBlock, $errorBlock, $abc_datanr, $userMessage, $log;
    
    if (!isset($_POST['abc_datanr'])) {
      $errorBlock .= "<li>Er is geen nummer van de dataset ingevuld.</li>";
    }
    
    $abc_datanr = $_POST['abc_datanr'];
    
    if (!is_numeric($abc_datanr)) {
      $errorBlock .= "<li>In het veld voor het nummer van de dataset staan niet alleen cijfers.</li>";
    }

    if ($abc_datanr == 0) {
      $errorBlock .= "<li>Het nummer van de dataset is niet geldig.</li>";
    }
    
    $infoBlock .= "<li>Nummer dataset: $abc_datanr</li>";

    if ($_FILES["abc_file"]["error"] > 0) {
      if ($_FILES["abc_file"]["error"] == 4)
      {
        $errorBlock .= "<li>Je hebt geen bestand geselecteerd.</li>";
      }
      else
      {
        $errorBlock .= "<li>Er ging iets fout met het bestand. Probeer het nogmaals. De foutcode is {$_FILES["abc_file"]["error"]}.";
      }
    }
    else {

      $infoBlock .= "<li>Meegestuurd bestand:<ul>
                       <li>Naam: {$_FILES["abc_file"]["name"]}</li>
                       <li>Type: {$_FILES["abc_file"]["type"]}</li>
                       <li>Grootte: {$_FILES["abc_file"]["size"]}</li>
                     </ul></li>";
      
      if ($_FILES["abc_file"]["size"] > MAX_FILE_SIZE_BYTES) {
        $errorBlock .= "<li>Het bestand is groter dan {MAX_FILE_SIZE_TEXT}.</li>";
      }
      
      if ((strtolower(substr($_FILES["abc_file"]["name"], -4)) != ".pdf") &&
          (strtolower(substr($_FILES["abc_file"]["name"], -4)) != ".doc") &&
          (strtolower(substr($_FILES["abc_file"]["name"], -4)) != ".odf") &&
          (strtolower(substr($_FILES["abc_file"]["name"], -4)) != ".rtf") &&
          (strtolower(substr($_FILES["abc_file"]["name"], -5)) != ".docx") ) {
        $errorBlock .= "<li>Het bestand heeft een andere extensie dan .pdf, .doc, .odf, .rtf of .docx.</li>";
      }
    
      if ($errorBlock == "") {
        $log .= "File: {$_FILES["abc_file"]["name"]} (type: {$_FILES["abc_file"]["type"]}, size: {$_FILES["abc_file"]["size"]}).\n";
      }

    }
    
    if (strlen(trim($_POST['message'])) > 0) {
      $userMessage = "
Als boodschap heb ik het volgende ingevoerd: \"{$_POST['message']}\".
";
      $infoBlock .= "<li>Bericht: ".$_POST['message'];
    }
    
  }

  function ps_abc_send() {

    global $dbHandle, $setSubmission, $infoBlock, $errorBlock, $name, $idnr, $email, $abc_datanr, $userMessage, $courses, $course, $designatedTeacher, $log;

    $filepathname = $_FILES["abc_file"]["name"];
    $tempfilename = "uploads/" . $_FILES["abc_file"]["name"];
    move_uploaded_file($_FILES["abc_file"]["tmp_name"], $tempfilename);

    $mail = new PHPMailer(true);
    try {
      $mail->SetFrom($email, $name);
      $mail->AddReplyTo($email, $name);
      $mail->AddAddress($designatedTeacher->email, $designatedTeacher->name);
      $mail->AddCC($email, $name);
      $mail->AddCC("abchuiswerkopdracht.survey@ou.nl");
      $mail->Subject = "De ABC huiswerkopdracht van $name, id $idnr";
      $mail->AddAttachment($tempfilename);
      $mail->Body = "Beste {$designatedTeacher->name},

Bij deze stuur ik mijn ABC huiswerkopdracht voor Psychologisch Survey in. Het nummer van het databestand dat ik heb gebruikt is $abc_datanr.
$userMessage
Met vriendelijke groet,

$name
$idnr";
      
      if (!(isset($_GET['debug']))) {
        $mail->Send();
        $log .= "Mail sent.\n";
      }
      else {
        $log .= "Mail NOT sent becausing \$_GET['debug'] was set.\n";
      }
      
      $infoBlock .= "<li>De mail is succesvol verzonden!</li>";
      // Save submission to database
      try {
        $data = array('name'=>$name, 'nr'=>$idnr, 'email'=>$email, 'course'=>$courses[$course]->id, 'teacher'=>$designatedTeacher->id);
        $setSubmission->execute($data);
        if($setSubmission->errorCode() == 0) {
          $infoBlock .= "<li>Je submissie is succesvol opgeslagen in de database!</li>";
          $log .= "Submission succesfully saved in the database!\n";
        }
        else {
          $errors = $setSubmission->errorInfo();
          $infoBlock .= "<li>Fout in de opslag in de database: {$errors[2]}</li>";
          $log .= "Error while saving submission in the database! Error: '{$errors[2]}'.\n";
        }
        
      }
      catch(PDOException $e) {
        errorHandler($e->getMessage());
      }
    }
    catch (phpmailerException $e) {
      $errorBlock .= "<li>Het versturen van de email is mislukt! De fout die de server gaf was: '".$e->errorMessage()."'</li>";
      $log .= "Error while sending mail! Error message: '".$e->errorMessage()."'.\n";
    }
    catch (Exception $e) {
      $errorBlock .= "<li>Het versturen van de email is mislukt! De fout die de server gaf was: '".$e->getMessage()."'</li>";
      $log .= "Error while sending mail! Error message: '".$e->getMessage()."'.\n";
    }
    
    unlink($tempfilename);

  }

?>