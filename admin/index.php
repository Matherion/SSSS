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
 
****************************************************

 This file is user as an administrative interface
 to get an overview of the database and to set
 teachers as 'active' or 'inactive'.
 
***************************************************/

  include('../db.php'); /* Looks like this:
<?php
  define("MYSQL_HOST", "hostname");
  define("MYSQL_DB", "databasename");
  define("MYSQL_USER", "username");
  define("MYSQL_PASSWORD", "password");
?>*/

  include('../admin_emails.php'); /* Looks like this:
<?php
  define("ERRORS_EMAIL", "email@address.com");
  define("REPORTS_EMAIL", "email@address.com");
?>*/

  include('admin_password.php'); /* Looks like this:
<?php
  define("ADMIN_PASSWORD", "password");
?>*/
  

  require_once('../class.phpmailer.php');
  
  class Teacher {
    public $id;
    public $name;
    public $email;
    public $active;
    public $acronym;
  }

  class Course {
    public $id;
    public $name;
    public $formfile;
    public $functionfile;
    public $sendfunction;
    public $verifyfunction;
  }

  function ErrorHandler ($error) {
    mail(ERRORS_EMAIL, "Error in OU SSSS admin script", $error);
    echo($error);
  }
  
  // Prepare database statements

  try {
    # MySQL with PDO_MYSQL
    $dbHandle = new PDO("mysql:host=".MYSQL_HOST.";dbname=".MYSQL_DB, MYSQL_USER, MYSQL_PASSWORD);
    $dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $getTeachers = $dbHandle->query("SELECT * FROM `teachers`;");
    $getTeachers->setFetchMode(PDO::FETCH_CLASS, 'Teacher');
    
    $getCourses = $dbHandle->query("SELECT * FROM `courses`;");
    $getCourses->setFetchMode(PDO::FETCH_CLASS, 'Course');
    
    $getCapacity = $dbHandle->prepare("SELECT `capacity` FROM `capacities` WHERE `courses_id` = :course AND `teachers_id` = :teacher LIMIT 1;");
    $getCapacity->setFetchMode(PDO::FETCH_ASSOC);
    $getCapacity->bindParam(':course', $course_id, PDO::PARAM_INT);
    $getCapacity->bindParam(':teacher', $teacher_id, PDO::PARAM_INT);
    
    $getPapers = $dbHandle->prepare("SELECT COUNT(*) FROM `papers` WHERE `courses_id` = :course AND `teachers_id` = :teacher AND `timestamp` >= :startAcadYear;");
    $getPapers->setFetchMode(PDO::FETCH_ASSOC);
    $getPapers->bindParam(':course', $course_id, PDO::PARAM_INT);
    $getPapers->bindParam(':teacher', $teacher_id, PDO::PARAM_INT);
    $getPapers->bindParam(':startAcadYear', $startOfCurrentAcademicYear, PDO::PARAM_STR);

    $getSelections = $dbHandle->prepare("SELECT COUNT(*) FROM `papers` WHERE `courses_id` = :course AND `teachers_id` = :teacher AND `teacherSelection` = 1 AND `timestamp` >= :startAcadYear;");
    $getSelections->setFetchMode(PDO::FETCH_ASSOC);
    $getSelections->bindParam(':course', $course_id, PDO::PARAM_INT);
    $getSelections->bindParam(':teacher', $teacher_id, PDO::PARAM_INT);
    $getSelections->bindParam(':startAcadYear', $startOfCurrentAcademicYear, PDO::PARAM_STR);
    
    $getSubmissions = $dbHandle->prepare("SELECT COUNT(*) FROM `submissions` WHERE `courses_id` = :course AND `teachers_id` = :teacher AND `timestamp` >= :startAcadYear;");
    $getSubmissions->setFetchMode(PDO::FETCH_ASSOC);
    $getSubmissions->bindParam(':course', $course_id, PDO::PARAM_INT);
    $getSubmissions->bindParam(':teacher', $teacher_id, PDO::PARAM_INT);
    $getSubmissions->bindParam(':startAcadYear', $startOfCurrentAcademicYear, PDO::PARAM_STR);

    $getStatus = $dbHandle->prepare("SELECT `active` FROM teachers WHERE `id` = :teacher LIMIT 1;");
    $getStatus->setFetchMode(PDO::FETCH_ASSOC);
    $getStatus->bindParam(':teacher', $teacher_id, PDO::PARAM_INT);
    
    $setStatus = $dbHandle->prepare("UPDATE teachers SET `active`=IF(`active` = 1, 0, 1) WHERE `id` = :teacher LIMIT 1;");
    $setStatus->bindParam(':teacher', $teacher_id, PDO::PARAM_INT);

    $setStatusSwitch = $dbHandle->prepare("INSERT INTO teacher_statusSwitches (`setStatus`, `teachers_id`) VALUES(:active, :teacher);");
    $setStatusSwitch->bindParam(':teacher', $teacher_id, PDO::PARAM_INT);
    $setStatusSwitch->bindParam(':active', $newStatus, PDO::PARAM_INT);

  }
  catch(PDOException $e) {
    errorHandler($e->getMessage());
  }

  // Connect to database and load submissions per supervisor
  try {
    // Get the teachers and the courses
    while($obj = $getTeachers->fetch()) {
      $teachers[$obj->id] = $obj;
    }
    while($obj = $getCourses->fetch()) {
      $courses[$obj->id] = $obj;
    }
  }
  catch(PDOException $e) {
    errorHandler($e->getMessage());
  }
  
  // Find when the last first of september was (the start of the academic year:
  // we only retrieve submitted papers after this date)
  if (date('n') < 9) {
    // So it's january to august, which means the 'last september' was last year.
    $startOfCurrentAcademicYear = date('Y') - 1 . "-09-01";
  }
  else {
    // It's september to december, so 'last september; was this year
    $startOfCurrentAcademicYear = date('Y') . "-09-01";
  }
  // Note that $startOfCurrentAcademicYear is bound to the timestamp field using PDO in the
  // query to get the submitted papers and submissions.

  foreach($courses as $course) {
  
    $course_id = $course->id; // Bound to the `course` MySQL columns using PDO

    foreach ($teachers as $teacher) {
      $teacher_id = $teacher->id; // Bound to the `teacher` MySQL column using PDO
      try {
        // Read available capacity for this teacher (for this course) from the database
        if (!$getCapacity->execute()) {
          errorHandler("Errorcode: {$getCapacity->errorCode()}, errorinfo: {$getCapacity->errorInfo ()}.");
        }
        $tempCapacityArray = $getCapacity->fetch();
        $capacities[$course_id][$teacher_id] = $tempCapacityArray['capacity'];

        // Read number of graded papers for this teacher (for this course) from the database
        if (!$getPapers->execute()) {
          errorHandler("Errorcode: {$getPapers->errorCode()}, errorinfo: {$getPapers->errorInfo ()}.");
        }
        $tempPapersArray = $getPapers->fetch();
        $papers[$course_id][$teacher_id] = $tempPapersArray['COUNT(*)'];

        // Read number of papers where this teacher was selected deliberately (for this course) from the database
        if (!$getSelections->execute()) {
          errorHandler("Errorcode: {$getSelections->errorCode()}, errorinfo: {$getSelections->errorInfo ()}.");
        }
        $tempSelectionsArray = $getSelections->fetch();
        $selections[$course_id][$teacher_id] = $tempSelectionsArray['COUNT(*)'];
        
        // Read number of supervised students for this teacher (for this course) from the database
        if (!$getSubmissions->execute()) {
          errorHandler("Errorcode: {$getSubmissions->errorCode()}, errorinfo: {$getSubmissions->errorInfo ()}.");
        }
        $tempSubmissionsArray = $getSubmissions->fetch();
        $submissions[$course_id][$teacher_id] = $tempSubmissionsArray['COUNT(*)'];
      }
      catch(PDOException $e) {
        errorHandler($e->getMessage());
      }
      // We also build an array of acronyms, which is convenient for checking
      // whether a submitted acronym (when changing teacher status from active
      // to inactive or nice versa) is valid
      $acronyms[$teacher_id] = $teacher->acronym;
    }

    $totalCapacity[$course_id] = array_sum($capacities[$course_id]);
    $totalPapers[$course_id] = array_sum($papers[$course_id]);
    $totalSubmissions[$course_id] = array_sum($submissions[$course_id]);
    
  }

  // If form information was posted, we have to switch a teacher's status
  $errorBlock = "";
  $infoBlock = "";
  
  if (isset($_POST['teacherName']) && isset($_POST['password'])) {
    if ($_POST['password'] == ADMIN_PASSWORD) {
      $teacher_id = array_search($_POST['teacherName'], $acronyms);
      if ($teacher_id === false) {
        // Submitted Acronym not in database
        $errorBlock .= "<li>Acroniem niet in database!</li>";
        mail(REPORTS_EMAIL, "Error in status change through OU SSSS script", "Acronym {$_POST['teacherName']} does not exist!");
      }
      else {
        $setStatus->execute();
        if(!($setStatus->errorCode() == 0)) {
          $errors = $setStatus->errorInfo();
          $errorBlock .= "<li>Fout in de opslag in de database: {$errors[2]}</li>";
        }
        else {
          // We succesfully switched the status; now get the new status
          if (!$getStatus->execute()) {
            errorHandler("Errorcode: {$getStatus->errorCode()}, errorinfo: {$getStatus->errorInfo ()}.");
          }
          $newStatusArray = $getStatus->fetch();
          $newStatus = $newStatusArray['active'];

          // Update teacher's status
          $teachers[$teacher_id]->active = $newStatus;
          
          // Store switch in database
          // Note: 'active' value is bound to $newStatus;
          if (!$setStatusSwitch->execute()) {
            errorHandler("Errorcode: {$setStatusSwitch->errorCode()}, errorinfo: {$setStatusSwitch->errorInfo ()}.");
          }
          
          mail($teachers[$teacher_id]->email, "Status change through OU SSSS script", "Status {$teachers[$teacher_id]->name} ($teacher_id) changed to $newStatus.");
          mail(REPORTS_EMAIL, "Status change through OU SSSS script", "Status {$teachers[$teacher_id]->name} ($teacher_id) changed to $newStatus.");
          $infoBlock .= "<li>Status van docent {$teachers[$teacher_id]->name} succesvol ingesteld op $newStatus.</li>";
        }
      }
    }
    else {
      // wrong password
      $errorBlock .= "<li>Verkeerd wachtwoord!</li>";
      mail(REPORTS_EMAIL, "Error in status change through OU SSSS script", "Word password ({$_POST['password']}), acronym {$_POST['teacherName']}!");
    }
  }
  
  
  include("../view_header.php");

    if ($infoBlock) {
      echo("<div class=\"infoblock\"><strong>Info:</strong><ul>".$infoBlock."</ul></div>");
    }
    if ($errorBlock) {
      echo("<div class=\"errorblock\">Fout(en):<ul>".$errorBlock."</ul></div>");
    }
    
  include("view_admin.php");
  include("../view_footer.php");

  // Close connection with database
  $dbHandle = null;
  
?>