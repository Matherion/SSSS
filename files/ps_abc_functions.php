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
  
    global $infoBlock, $errorBlock, $teachers, $begeleider, $log;

    // Verification of general fields
    if (isset($_POST['ps_abc_name'])       &&
        isset($_POST['ps_abc_idnr'])       &&
        isset($_POST['ps_abc_email'])      ) {

      $name=trim($_POST['ps_abc_name']);
      $idnr=trim($_POST['ps_abc_idnr']);
      $email=trim($_POST['ps_abc_email']);

      $infoBlock .= "<li>Naam: $name</li>
                     <li>Id-nummer: $idnr</li>
                     <li>E-mail adres: $email</li>";

      if (strlen($_POST['ps_abc_name']) == 0) {
        $errorBlock .= "<li>Er is geen naam ingevuld.</li>";
      }

      if (strlen($_POST['ps_abc_idnr']) == 0) {
        $errorBlock .= "<li>Er is geen ID-nummer ingevuld.</li>";
      }
      else if (!is_numeric($idnr))
      {
        $errorBlock .= "<li>Je id-nummer bestaat niet uit alleen maar cijfers.</li>";
      }

      if (strlen($_POST['ps_abc_email']) == 0) {
        $errorBlock .= "<li>Er is geen E-mail adres ingevuld.</li>";
      }
      
    }
    else {
      $errorBlock .= "<li>Een of meerdere van de basisvelden (naam, id-nummer, email-adres en cursus) is leeg.</li>";
    }
    
    if (!isset($_POST['ps_abc_supervisor'])) {
      $errorBlock .= "<li>Fout: er is geen begeleider geselecteerd (let op: als je geen begeleider had, selecteer dan 'Ik had geen begeleider'!).</li>";
    }
    else {
      $begeleider=trim($_POST['ps_abc_supervisor']);    
      if (!($begeleider == "No supervisor") && !array_key_exists($begeleider, $teachers)) {
        $errorBlock .= "<li>Er geen begeleider geselecteerd (let op: als je geen begeleider had, selecteer dan 'Ik had geen begeleider'!).</li>";
      }
    }
    
    if (!isset($_POST['ps_abc_datanr'])) {
      $errorBlock .= "<li>Er is geen nummer van de dataset ingevuld.</li>";
    }
    
    $ps_abc_datanr = $_POST['ps_abc_datanr'];
    
    if (!is_numeric($ps_abc_datanr)) {
      $errorBlock .= "<li>In het veld voor het nummer van de dataset staan niet alleen cijfers.</li>";
    }

    if ($ps_abc_datanr == 0) {
      $errorBlock .= "<li>Het nummer van de dataset is niet geldig.</li>";
    }
    
    $infoBlock .= "<li>Nummer dataset: $ps_abc_datanr</li>";

    if ($_FILES["ps_abc_file"]["error"] > 0) {
      if ($_FILES["ps_abc_file"]["error"] == 4)
      {
        $errorBlock .= "<li>Je hebt geen bestand geselecteerd.</li>";
      }
      else
      {
        $errorBlock .= "<li>Er ging iets fout met het bestand. Probeer het nogmaals. De foutcode is {$_FILES["ps_abc_file"]["error"]}.";
      }
    }
    else {

      $infoBlock .= "<li>Meegestuurd bestand:<ul>
                       <li>Naam: {$_FILES["ps_abc_file"]["name"]}</li>
                       <li>Type: {$_FILES["ps_abc_file"]["type"]}</li>
                       <li>Grootte: {$_FILES["ps_abc_file"]["size"]}</li>
                     </ul></li>";
      
      if ($_FILES["ps_abc_file"]["size"] > MAX_FILE_SIZE_BYTES) {
        $errorBlock .= "<li>Het bestand is groter dan {MAX_FILE_SIZE_TEXT}.</li>";
      }
      
      if ((strtolower(substr($_FILES["ps_abc_file"]["name"], -4)) != ".pdf") &&
          (strtolower(substr($_FILES["ps_abc_file"]["name"], -4)) != ".odf") &&
          (strtolower(substr($_FILES["ps_abc_file"]["name"], -4)) != ".rtf") &&
          (strtolower(substr($_FILES["ps_abc_file"]["name"], -4)) != ".doc") &&
          (strtolower(substr($_FILES["ps_abc_file"]["name"], -5)) != ".docx") ) {
        $errorBlock .= "<li>Het bestand heeft een andere extensie dan .pdf, .odf, .rtf, doc of .docx.</li>";
      }
    
      if ($errorBlock == "") {
        $log .= "Received a submission by $name (id $idnr), $email.\n";
        $log .= "File: {$_FILES["ps_abc_file"]["name"]} (type: {$_FILES["ps_abc_file"]["type"]}, size: {$_FILES["ps_abc_file"]["size"]}).\n";
      }

    }
    
  }

  function ps_abc_send() {

    global $dbHandle, $setSubmission, $setPaper, $infoBlock, $errorBlock, $courses, $course, $designatedTeacher, $log;

    // Store whether a supervisor was selected or randomly designated
    if (is_numeric($_POST['ps_abc_supervisor']) && $_POST['ps_abc_supervisor'] > 0) {
      $teacherSelection = 0;
    }
    else {
      $teacherSelection = 1;
    }

    $name=trim($_POST['ps_abc_name']);
    $idnr=trim($_POST['ps_abc_idnr']);
    $email=trim($_POST['ps_abc_email']);
    $fileNameComponent = $name;
    $fileIdComponent = $idnr;
    $ps_abc_datanr = $_POST['ps_abc_datanr'];
    if (strlen(trim($_POST['message'])) > 0) {
      $userMessage = "
Als boodschap heb ik het volgende ingevoerd: \"{$_POST['message']}\".
";
      $infoBlock .= "<li>Bericht: ".$_POST['message'];
    }    
     
    $ps_abc_tempfile = prepareFile("PS ABC", $_FILES["ps_abc_file"], $fileNameComponent, $fileIdComponent);

    $mail = new PHPMailer(true);
    try {
      $mail->SetFrom($email, $name);
      $mail->AddReplyTo($email, $name);
      $mail->AddAddress($designatedTeacher->email, $designatedTeacher->name);
      $mail->AddCC($email, $name);
      $mail->AddCC("abchuiswerkopdracht.survey@ou.nl");
      $mail->Subject = "De ABC huiswerkopdracht van $name ($idnr)";
      $mail->AddAttachment($ps_abc_tempfile);
      $mail->Body = "Beste {$designatedTeacher->name},

Bij deze stuur ik mijn ABC huiswerkopdracht voor Psychologisch Survey in. Het nummer van het databestand dat ik heb gebruikt is $ps_abc_datanr.
$userMessage
Met vriendelijke groet,

$name
$idnr";
      
      if (!(isset($_GET['debug']))) {
        $mail->Send();
        $log .= "Mail sent.\n";
      }
      else {
        $log .= "Mail NOT sent because \$_GET['debug'] was set.\n";
      }
      
      $infoBlock .= "<li>De mail is succesvol verzonden!</li>";
      // Save submission to database
      try {
        // First save paper to papers table
        $data = array('course'=>$courses[$course]->id, 'teacher'=>$designatedTeacher->id, 'teacherSelection'=>$teacherSelection , 'nrOfStudents'=>1);
        $setPaper->execute($data);
        if(!($setPaper->errorCode() == 0)) {
          $errors = $setPaper->errorInfo();
          $infoBlock .= "<li>Fout in de opslag in de database: {$errors[2]}</li>";
          $log .= "Error while saving submission in the database! Error: '{$errors[2]}'.\n";
        }
        else {
          // We can go on and store the student(s). First get the paper id
          $papers_id = $dbHandle->lastInsertId();
          // Then save submission for student
          $data = array('name'=>$name, 'nr'=>$idnr, 'email'=>$email, 'course'=>$courses[$course]->id, 'teacher'=>$designatedTeacher->id,
                        'papers_id'=>$papers_id, 'papers_teachers_id'=>$designatedTeacher->id, 'papers_courses_id'=>$courses[$course]->id);
          $setSubmission->execute($data);
          if(!($setSubmission->errorCode() == 0)) {
            $errors = $setSubmission->errorInfo();
            $infoBlock .= "<li>Fout in de opslag in de database: {$errors[2]}</li>";
            $log .= "Error while saving submission in the database! Error: '{$errors[2]}'.\n";
          }
          else {
            $infoBlock .= "<li>De submissie is succesvol opgeslagen in de database!</li>";
            $log .= "Submission succesfully saved in the database!\n";
          }
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
    
    unlink($ps_abc_tempfile);

  }

?>