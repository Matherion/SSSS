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

  function pe_verify() {
  
    global $infoBlock, $errorBlock, $teachers, $begeleider, $log;

    // Verification of general fields for first student
    if (isset($_POST['pe_name_1'])       &&
        isset($_POST['pe_idnr_1'])       &&
        isset($_POST['pe_email_1'])      ) {

      $name_1=trim($_POST['pe_name_1']);
      $idnr_1=trim($_POST['pe_idnr_1']);
      $email_1=trim($_POST['pe_email_1']);
      
      $infoBlock .= "<li>Naam: $name_1</li>
                     <li>Id-nummer: $idnr_1</li>
                     <li>E-mail adres: $email_1</li>";

      if (strlen($_POST['pe_name_1']) == 0) {
        $errorBlock .= "<li>Er is geen naam ingevuld voor student 1.</li>";
      }

      if (strlen($_POST['pe_idnr_1']) == 0) {
        $errorBlock .= "<li>Er is geen ID-nummer ingevuld voor student 1.</li>";
      }
      else if (!is_numeric($idnr_1))
      {
        $errorBlock .= "<li>Het id-nummer van student 1 bestaat niet uit alleen maar cijfers.</li>";
      }

      if (strlen($_POST['pe_email_1']) == 0) {
        $errorBlock .= "<li>Er is geen E-mail adres ingevuld voor student 1.</li>";
      }
      
    }
    else {
      $errorBlock .= "<li>Een of meerdere van de basisvelden voor student 1 (naam, id-nummer, email-adres) is leeg.</li>";
    }

    // Verification of general fields for second student
    if ($_POST['pe_nrOfPeople'] == 2) {
      if (isset($_POST['pe_name_2'])       &&
          isset($_POST['pe_idnr_2'])       &&
          isset($_POST['pe_email_2'])      ) {

        $name_2=trim($_POST['pe_name_2']);
        $idnr_2=trim($_POST['pe_idnr_2']);
        $email_2=trim($_POST['pe_email_2']);
        
        $infoBlock .= "<li>Naam: $name_2</li>
                       <li>Id-nummer: $idnr_2</li>
                       <li>E-mail adres: $email_2</li>";

        if (strlen($_POST['pe_name_2']) == 0) {
          $errorBlock .= "<li>Er is geen naam ingevuld voor student 2.</li>";
        }

        if (strlen($_POST['pe_idnr_2']) == 0) {
          $errorBlock .= "<li>Er is geen ID-nummer ingevuld voor student 2.</li>";
        }
        else if (!is_numeric($idnr_2))
        {
          $errorBlock .= "<li>Het id-nummer van student 2 bestaat niet uit alleen maar cijfers.</li>";
        }

        if (strlen($_POST['pe_email_2']) == 0) {
          $errorBlock .= "<li>Er is geen E-mail adres ingevuld voor student 2.</li>";
        }
        
      }
      else {
        $errorBlock .= "<li>Een of meerdere van de basisvelden voor student 2 (naam, id-nummer, email-adres) is leeg.</li>";
      }
    }
    
    if (!isset($_POST['pe_supervisor'])) {
      $errorBlock .= "<li>Fout: er is geen begeleider geselecteerd (let op: als je geen begeleider had, selecteer dan 'Ik had geen begeleider'!).</li>";
    }
    else {
      $begeleider=trim($_POST['pe_supervisor']);    
      if (!($begeleider == "No supervisor") && !array_key_exists($begeleider, $teachers)) {
        $errorBlock .= "<li>Er geen begeleider geselecteerd (let op: als je geen begeleider had, selecteer dan 'Ik had geen begeleider'!).</li>";
      }
    }

    if ($errorBlock == "") {
      if ($_POST['pe_nrOfPeople'] == 1) {
        $log .= "Submission by $name_1 (id $idnr_1), $email_1.\n";
      }
      else if ($_POST['pe_nrOfPeople'] == 2) {
        $log .= "Submission by $name_1 (id $idnr_1), $email_1, and $name_2 (id $idnr_2), $email_2.\n";
      }
    }
    
    if ($_FILES["pe_paper"]["error"] > 0) {
      if ($_FILES["pe_paper"]["error"] == 4)
      {
        $errorBlock .= "<li>Fout in verslag: je hebt geen bestand geselecteerd.</li>";
      }
      else
      {
        $errorBlock .= "<li>Fout in verslag: er ging iets fout met het bestand. Probeer het nogmaals. De foutcode is {$_FILES["pe_paper"]["error"]}.";
      }
    }
    else {

      $infoBlock .= "<li>Meegestuurd bestand voor je verslag:<ul>
                       <li>Naam: {$_FILES["pe_paper"]["name"]}</li>
                       <li>Type: {$_FILES["pe_paper"]["type"]}</li>
                       <li>Grootte: {$_FILES["pe_paper"]["size"]}</li>
                     </ul></li>";
      
      if ($_FILES["pe_paper"]["size"] > MAX_FILE_SIZE_BYTES) {
        $errorBlock .= "<li>Fout in verslag: het bestand is groter dan {MAX_FILE_SIZE_TEXT}.</li>";
      }
      
      if ((strtolower(substr($_FILES["pe_paper"]["name"], -4)) != ".pdf") &&
          (strtolower(substr($_FILES["pe_paper"]["name"], -4)) != ".odf") &&
          (strtolower(substr($_FILES["pe_paper"]["name"], -4)) != ".rtf") &&
          (strtolower(substr($_FILES["pe_paper"]["name"], -4)) != ".doc") &&
          (strtolower(substr($_FILES["pe_paper"]["name"], -5)) != ".docx") ) {
        $errorBlock .= "<li>Fout in verslag: het bestand heeft een andere extensie dan .pdf, .odf, .rtf, .doc of .docx.</li>";
      }
    
      if ($errorBlock == "") {
        $log .= "File: {$_FILES["pe_paper"]["name"]} (type: {$_FILES["pe_paper"]["type"]}, size: {$_FILES["pe_paper"]["size"]}).\n";
      }

    }
    
    if ($_FILES["pe_data"]["error"] > 0) {
      if ($_FILES["pe_data"]["error"] == 4)
      {
        $errorBlock .= "<li>Fout in data: je hebt geen bestand geselecteerd.</li>";
      }
      else
      {
        $errorBlock .= "<li>Fout in data: er ging iets fout met het bestand. Probeer het nogmaals. De foutcode is {$_FILES["pe_data"]["error"]}.";
      }
    }
    else {

      $infoBlock .= "<li>Meegestuurd bestand voor je data:<ul>
                       <li>Naam: {$_FILES["pe_data"]["name"]}</li>
                       <li>Type: {$_FILES["pe_data"]["type"]}</li>
                       <li>Grootte: {$_FILES["pe_data"]["size"]}</li>
                     </ul></li>";
      
      if ($_FILES["pe_data"]["size"] > MAX_FILE_SIZE_BYTES) {
        $errorBlock .= "<li>Fout in data: het bestand is groter dan {MAX_FILE_SIZE_TEXT}.</li>";
      }
      
      if (strtolower(substr($_FILES["pe_data"]["name"], -4)) != ".sav") {
        $errorBlock .= "<li>Fout in data: het bestand heeft een andere extensie dan .sav.</li>";
      }
    
      if ($errorBlock == "") {
        $log .= "File: {$_FILES["pe_data"]["name"]} (type: {$_FILES["pe_data"]["type"]}, size: {$_FILES["pe_data"]["size"]}).\n";
      }

    }

    if ($_FILES["pe_ownwork_1"]["error"] > 0) {
      if ($_FILES["pe_ownwork_1"]["error"] == 4)
      {
        $errorBlock .= "<li>Fout in verklaring eigen werk 1: er is geen bestand geselecteerd.</li>";
      }
      else
      {
        $errorBlock .= "<li>Fout in verklaring eigen werk 1: er ging iets fout met het bestand. Probeer het nogmaals. De foutcode is {$_FILES["pe_ownwork_1"]["error"]}.";
      }
    }
    else {

      $infoBlock .= "<li>Meegestuurd bestand voor verklaring eigen werk 1:<ul>
                       <li>Naam: {$_FILES["pe_ownwork_1"]["name"]}</li>
                       <li>Type: {$_FILES["pe_ownwork_1"]["type"]}</li>
                       <li>Grootte: {$_FILES["pe_ownwork_1"]["size"]}</li>
                     </ul></li>";
      
      if ($_FILES["pe_ownwork_1"]["size"] > MAX_FILE_SIZE_BYTES) {
        $errorBlock .= "<li>Fout in verklaring eigen werk 1: het bestand is groter dan {MAX_FILE_SIZE_TEXT}.</li>";
      }
      
      if ((strtolower(substr($_FILES["pe_ownwork_1"]["name"], -4)) != ".pdf") &&
          (strtolower(substr($_FILES["pe_ownwork_1"]["name"], -4)) != ".jpg") &&
          (strtolower(substr($_FILES["pe_ownwork_1"]["name"], -5)) != ".jpeg") &&
          (strtolower(substr($_FILES["pe_ownwork_1"]["name"], -4)) != ".tif") &&
          (strtolower(substr($_FILES["pe_ownwork_1"]["name"], -5)) != ".tiff") ) {
        $errorBlock .= "<li>Fout in verklaring eigen werk 1: het bestand heeft een andere extensie dan .pdf, .jpg, .jpeg, .tif of .tiff.</li>";
      }
    
      if ($errorBlock == "") {
        $log .= "File: {$_FILES["pe_ownwork_1"]["name"]} (type: {$_FILES["pe_ownwork_1"]["type"]}, size: {$_FILES["pe_ownwork_1"]["size"]}).\n";
      }

    }
    
    if ($_POST['pe_nrOfPeople'] == 2) {
      if ($_FILES["pe_ownwork_2"]["error"] > 0) {
        if ($_FILES["pe_ownwork_2"]["error"] == 4)
        {
          $errorBlock .= "<li>Fout in verklaring eigen werk 2: er is geen bestand geselecteerd.</li>";
        }
        else
        {
          $errorBlock .= "<li>Fout in verklaring eigen werk 2: er ging iets fout met het bestand. Probeer het nogmaals. De foutcode is {$_FILES["pe_ownwork_2"]["error"]}.";
        }
      }
      else {

        $infoBlock .= "<li>Meegestuurd bestand voor verklaring eigen werk 2:<ul>
                         <li>Naam: {$_FILES["pe_ownwork_2"]["name"]}</li>
                         <li>Type: {$_FILES["pe_ownwork_2"]["type"]}</li>
                         <li>Grootte: {$_FILES["pe_ownwork_2"]["size"]}</li>
                       </ul></li>";
        
        if ($_FILES["pe_ownwork_2"]["size"] > MAX_FILE_SIZE_BYTES) {
          $errorBlock .= "<li>Fout in verklaring eigen werk 2: het bestand is groter dan {MAX_FILE_SIZE_TEXT}.</li>";
        }
        
        if ((strtolower(substr($_FILES["pe_ownwork_2"]["name"], -4)) != ".pdf") &&
            (strtolower(substr($_FILES["pe_ownwork_2"]["name"], -4)) != ".jpg") &&
            (strtolower(substr($_FILES["pe_ownwork_2"]["name"], -5)) != ".jpeg") &&
            (strtolower(substr($_FILES["pe_ownwork_2"]["name"], -4)) != ".tif") &&
            (strtolower(substr($_FILES["pe_ownwork_2"]["name"], -5)) != ".tiff") ) {
          $errorBlock .= "<li>Fout in verklaring eigen werk 2: het bestand heeft een andere extensie dan .pdf, .jpg, .jpeg, .tif of .tiff.</li>";
        }
      
        if ($errorBlock == "") {
          $log .= "File: {$_FILES["pe_ownwork_2"]["name"]} (type: {$_FILES["pe_ownwork_2"]["type"]}, size: {$_FILES["pe_ownwork_2"]["size"]}).\n";
        }
      }
    }
    
  }

  function pe_send() {

    global $dbHandle, $setSubmission, $setPaper, $infoBlock, $errorBlock, $courses, $course, $designatedTeacher, $log;
    
    // Store whether a supervisor was selected or randomly designated
    if (is_numeric($_POST['ps_abc_supervisor']) && $_POST['ps_abc_supervisor'] > 0) {
      $teacherSelection = 0;
    }
    else {
      $teacherSelection = 1;
    }

    $name_1=trim($_POST['pe_name_1']);
    $idnr_1=trim($_POST['pe_idnr_1']);
    $email_1=trim($_POST['pe_email_1']);
    $fileNameComponent = $name_1;
    $fileIdComponent = $idnr_1;
    $msgPreText = "Als boodschap heb ik het volgende ingevoerd:";
    $signOff = "$name_1
$idnr_1";
    $mailSubject = "Het OPE verslag van $name_1, id $idnr_1";
    $mailIntro = "Bij deze stuur ik mijn verslag voor Psychologisch Experiment in.";
    $successMessage = "<li>De mail is succesvol verzonden naar $designatedTeacher->email, met een cc naar uzelf ($email_1)!</li>";
    
    if ($_POST['pe_nrOfPeople'] == 2) {
      $name_2=trim($_POST['pe_name_2']);
      $idnr_2=trim($_POST['pe_idnr_2']);
      $email_2=trim($_POST['pe_email_2']);
      $fileNameComponent = $name_1." en ".$name_2;
      $fileIdComponent = $idnr_1." en ".$idnr_2;
      $msgPreText = "Als boodschap hebben we het volgende ingevoerd:";
      $signOff = "$name_1 en $name_2
$idnr_1 en $idnr_2";
      $mailSubject = "Het OPE verslag van $name_1 en $name_2, id $idnr_1 en $idnr_2";
      $mailIntro = "Bij deze sturen wij ons verslag voor Psychologisch Experiment in.";
      $successMessage = "<li>De mail is succesvol verzonden naar $designatedTeacher->email, met een cc naar uzelf ($email_1 en $email_2)!</li>";
    }

    if (strlen(trim($_POST['message'])) > 0) {
      $userMessage = "
$msgPreText \"{$_POST['message']}\".
";
      $infoBlock .= "<li>Bericht: ".$_POST['message'];
    }
    
    $pe_paper_tempfile = prepareFile("PE verslag", $_FILES["pe_paper"], $fileNameComponent, $fileIdComponent);
    $pe_data_tempfile = prepareFile("PE data", $_FILES["pe_data"], $fileNameComponent, $fileIdComponent);
    $pe_ownwork_1_tempfile = prepareFile("PE VEW", $_FILES["pe_ownwork_1"], $name_1, $idnr_1);
    if ($_POST['pe_nrOfPeople'] == 2) {
      $pe_ownwork_2_tempfile = prepareFile("PE VEW", $_FILES["pe_ownwork_2"], $name_2, $idnr_2);
    }

    $mail = new PHPMailer(true);
    try {
      $mail->SetFrom($email_1, $name_1);
      $mail->AddReplyTo($email_1, $name_1);
      $mail->AddAddress($designatedTeacher->email, $designatedTeacher->name);
      $mail->AddCC($email_1, $name_1);
      if ($_POST['pe_nrOfPeople'] == 2) {
        $mail->AddCC($email_2, $name_2);
      }
//      $mail->AddCC("abchuiswerkopdracht.survey@ou.nl");
      $mail->Subject = $mailSubject;
      $mail->AddAttachment($pe_paper_tempfile);
      $mail->AddAttachment($pe_data_tempfile);
      $mail->AddAttachment($pe_ownwork_1_tempfile);
      if ($_POST['pe_nrOfPeople'] == 2) {
        $mail->AddAttachment($pe_ownwork_2_tempfile);
      }
      $mail->Body = "Beste {$designatedTeacher->name},

$mailIntro
$userMessage
Met vriendelijke groet,

$signOff";
      
      if (!(isset($_GET['debug']))) {
        $mail->Send();
        $log .= "Mail sent.\n";
      }
      else {
        $log .= "Mail NOT sent because \$_GET['debug'] was set.\n";
      }
      
      $infoBlock .= $successMessage;

      // Save submission to database
      try {
        // First save paper to papers table
        $data = array('course'=>$courses[$course]->id, 'teacher'=>$designatedTeacher->id, 'teacherSelection'=>$teacherSelection , 'nrOfStudents'=>$_POST['pe_nrOfPeople']);
        $setPaper->execute($data);
        if(!($setPaper->errorCode() == 0)) {
          $errors = $setPaper->errorInfo();
          $infoBlock .= "<li>Fout in de opslag in de database: {$errors[2]}</li>";
          $log .= "Error while saving submission in the database! Error: '{$errors[2]}'.\n";
        }
        else {
          // We can go on and store the student(s). First get the paper id
          $papers_id = $dbHandle->lastInsertId();
          // Then save submission for the first student
          $data = array('name'=>$name_1, 'nr'=>$idnr_1, 'email'=>$email_1, 'course'=>$courses[$course]->id, 'teacher'=>$designatedTeacher->id,
                        'papers_id'=>$papers_id, 'papers_teachers_id'=>$designatedTeacher->id, 'papers_courses_id'=>$courses[$course]->id);
          $setSubmission->execute($data);
          // If two students worked together, also store second student
          if ($_POST['pe_nrOfPeople'] == 2) {
            $data = array('name'=>$name_2, 'nr'=>$idnr_2, 'email'=>$email_2, 'course'=>$courses[$course]->id, 'teacher'=>$designatedTeacher->id,
                          'papers_id'=>$papers_id, 'papers_teachers_id'=>$designatedTeacher->id, 'papers_courses_id'=>$courses[$course]->id);
            $setSubmission->execute($data);
          }
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
    
    unlink($pe_paper_tempfile);
    unlink($pe_data_tempfile);
    unlink($pe_ownwork_1_tempfile);
    if ($_POST['pe_nrOfPeople'] == 2) {
      unlink($pe_ownwork_2_tempfile);
    }

  }

?>