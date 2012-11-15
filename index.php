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

  // Use $maintenance to take the site offline
  $maintenance = false;

  define("MAX_FILE_SIZE_BYTES", 2097152);
  define("MAX_FILE_SIZE_TEXT", "twee megabyte");
  define("FILES_PATH", "files/");
  
  include('db.php'); /* Looks like this:
<?php
  define("MYSQL_HOST", "hostname");
  define("MYSQL_DB", "databasename");
  define("MYSQL_USER", "username");
  define("MYSQL_PASSWORD", "password");
?>*/

  include('admin_emails.php'); /* Looks like this:
<?php
  define("ERRORS_EMAIL", "email@address.com");
  define("REPORTS_EMAIL", "email@address.com");
?>*/

  require_once('class.phpmailer.php');
  
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
    mail(ERRORS_EMAIL, "Error in OU submit script", $error);
    echo($error);
  }
  
  function prepareFile ($prefix, $filearray, $name, $idnr) {
    global $log;
    $tmpFileName = "uploads/" . $prefix . " " . $name . " (" . $idnr . ") [" . pathinfo($filearray["name"], PATHINFO_FILENAME) . "]." . pathinfo($filearray["name"], PATHINFO_EXTENSION);
    move_uploaded_file($filearray["tmp_name"], $tmpFileName);
    $log .= "File '{$filearray["name"]}' renamed to '$tmpFileName'.\n";
    return $tmpFileName;
  }
  
  // Connect to database and load submissions per supervisor

  try {
    # MySQL with PDO_MYSQL
    $dbHandle = new PDO("mysql:host=".MYSQL_HOST.";dbname=".MYSQL_DB, MYSQL_USER, MYSQL_PASSWORD);

    $getTeachers = $dbHandle->query("SELECT * FROM `teachers`;");
    $getTeachers->setFetchMode(PDO::FETCH_CLASS, 'Teacher');

    $getCourses = $dbHandle->query("SELECT * FROM `courses`;");
    $getCourses->setFetchMode(PDO::FETCH_CLASS, 'Course');

    $getCapacity = $dbHandle->prepare("SELECT `capacity` FROM `capacities` WHERE `courses_id` = :course AND `teachers_id` = :teacher LIMIT 1;");
    $getCapacity->setFetchMode(PDO::FETCH_ASSOC);
    $getCapacity->bindParam(':course', $course_id, PDO::PARAM_INT);
    $getCapacity->bindParam(':teacher', $teacher_id, PDO::PARAM_INT);

    //$getSubmissions = $dbHandle->prepare("SELECT COUNT(*) FROM `submissions` WHERE `courses_id` = :course AND `teachers_id` = :teacher AND `timestamp` >= :startAcadYear;");

    $getPapers = $dbHandle->prepare("SELECT COUNT(*) FROM `papers` WHERE `courses_id` = :course AND `teachers_id` = :teacher AND `timestamp` >= :startAcadYear;");
    $getPapers->setFetchMode(PDO::FETCH_ASSOC);
    $getPapers->bindParam(':course', $course_id, PDO::PARAM_INT);
    $getPapers->bindParam(':teacher', $teacher_id, PDO::PARAM_INT);
    $getPapers->bindParam(':startAcadYear', $startOfCurrentAcademicYear, PDO::PARAM_STR);

    $setPaper = $dbHandle->prepare("INSERT INTO papers (courses_id, teachers_id, teacherSelection, nrOfStudents) VALUE (:course, :teacher, :teacherSelection, :nrOfStudents);");

    $setSubmission = $dbHandle->prepare("INSERT INTO submissions (name, nr, email, courses_id, teachers_id, papers_id, papers_teachers_id, papers_courses_id) VALUE (:name, :nr, :email, :course, :teacher, :papers_id, :papers_teachers_id, :papers_courses_id);");
    
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

  // Load course-specific functions
  foreach ($courses as $currentCourse) {
    include(FILES_PATH.$currentCourse->functionfile);
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
  // query to get the submitted papers.
  
  $log = "SSSS submission\n\nCurrent date is ".date("d-n-Y")."; current academic year started on ".$startOfCurrentAcademicYear.".\n\n";
  $errorBlock = "";

  if (isset($_GET['action']) && ($_GET['action']=="submit")) {

    if (isset($_POST['course'])) {
      $course=trim($_POST['course']);
      if (array_key_exists($course, $courses)) {
        $infoBlock = "<li>Cursus: {$courses[$course]->name}</li>";
        $log .= "Received submission for course: $course ({$courses[$course]->name}).\n";
        call_user_func($courses[$course]->verifyfunction);
      }
      else {
        $errorBlock .= "<li>Er is een cursus geselecteerd die niet bestaat (nummer: $course).</li>";
        errorHandler("Non-existant course POSTed (number: $course)!");
      }
    }
    else {
      $errorBlock .= "<li>Er is geen cursus geselecteerd.</li>";
    }
    
    if (!($errorBlock)) {
    
      // We have everything we need. Now we can determine whom to send the email to.
      // We only have to pick a random supervisor if the student didn't specify a
      // supervisor.
      
      if ($begeleider == "No supervisor") {
      
        // First, build an array of the teachers that are active.
        
        $log .= "No supervisor specified.\n\nBuilding an array of active teachers.\n";

        $course_id = $course; // Bound to the `course` MySQL column using PDO

        foreach ($teachers as $teacher) {
          if ($teacher->active) {
            $teacher_id = $teacher->id; // Bound to the `teacher` MySQL column using PDO
            try {
              // Read available capacity for this teacher (for this course)
              // from the database
              if (!$getCapacity->execute()) {
                errorHandler("Errorcode: {$getCapacity->errorCode()}, errorinfo: {$getCapacity->errorInfo ()}.");
              }
              $tempCapacityArray = $getCapacity->fetch();
              $capacity[$teacher_id] = $tempCapacityArray['capacity'];
              // Read number of supervised students for this teacher
              // (for this course) from the database
              if (!$getPapers->execute()) {
                errorHandler("Errorcode: {$getPapers->errorCode()}, errorinfo: {$getPapers->errorInfo ()}.");
              }
              $tempSupervisedStudentsArray = $getPapers->fetch();
              $supervisedStudents[$teacher_id] = $tempSupervisedStudentsArray['COUNT(*)'];
            }
            catch(PDOException $e) {
              errorHandler($e->getMessage());
            }

            // Calculate available capacity for this teacher
            $availableCapacity[$teacher_id] = $capacity[$teacher_id] - $supervisedStudents[$teacher_id];
            
            // If the available capacity is lower than 0, put it at 0.
            // Because we assign extra capacity when total available capacity <= 0,
            // this makes sure that we don't start assigning extra capacity until
            // all teachers have exhausted their capacity.
            if ($availableCapacity[$teacher_id] < 0) {
              $availableCapacity[$teacher_id] = 0;
            }
            
            $log .= "Teacher with id $teacher_id ({$teacher->name}) is active. Current capacity = {$availableCapacity[$teacher_id]} ({$capacity[$teacher_id]} - {$supervisedStudents[$teacher_id]}).\n";
            
          }
          else {
            $log .= "Teacher with id {$teacher->id} ({$teacher->name}) is not active.\n";
          }
        }
        
        $totalAvailableCapacity = array_sum($availableCapacity);
        $totalCapacity = array_sum($capacity);
        $totalSupervisedStudents = array_sum($supervisedStudents);

        $log .= "\nTotal available capacity = $totalAvailableCapacity (total capacity = $totalCapacity, total supervised students = $totalSupervisedStudents).\n";
        
        // Note that if somebody supervised more papers than their capacity, their available
        // capacity was put at 0. This means that $totalAvailableCapacity can never be lower
        // than 0 (but just in case).
        if ($totalAvailableCapacity <= 0) {
          // The maximum number of papers has been submitted. We have to calculate a new
          // capacity. We do this by simply doubling the capacity of each teacher.
          $log .= "Total available capacity <= 0, so we double everybody's capacity.\n";
          foreach ($capacity as $currentTeacherId => $currentCapacity) {
            // Double this teacher's capacity
            $capacity[$currentTeacherId] = $capacity[$currentTeacherId] * 2;
            // Calculate new available capacity for this teacher
            $availableCapacity[$currentTeacherId] = $capacity[$currentTeacherId] - $supervisedStudents[$currentTeacherId];
            $log .= "Teacher with id $currentTeacherId ({$teachers[$currentTeacherId]->name}) is active. Current capacity = {$availableCapacity[$currentTeacherId]} ({$capacity[$currentTeacherId]} - {$supervisedStudents[$currentTeacherId]}).\n";
          }
          $totalCapacity = array_sum($capacity);
          $totalAvailableCapacity = array_sum($availableCapacity);
          $log .= "New total capacity: $totalCapacity (this means the new total available capacity is $totalAvailableCapacity).\n";
        }
        
        // Choose a random number between 1 and the total capacity
        $randomNumber = mt_rand(1, $totalAvailableCapacity);
        $log .= "Random number chosen between 0 and $totalAvailableCapacity: $randomNumber.\n";
       
        // Now, we loop through the array with each teacher's available capacity.
        // The first teacher's capacity goes from to his capacity; the second teacher's
        // capacity runs from the first teacher's capacity + 1 to the sum of the
        // first and second teachers' capacities; the third teacher's capacity runs
        // from the sum of the first two teachers' capacities + 1 to the sum of the
        // the capacities of teachers 1-3, and so on. The random number is
        // somwhere in this list of numbers. So every loop, we check whether
        // the random number is smaller than our counter + the current teacher's
        // available capacity. If so, we have our teacher. If the random number is
        // higher, we keep going.
        $counter = 1;
        foreach ($availableCapacity as $currentTeacherId => $currentTeacherAvailableCapacity) {
          $log .= "Seeing whether random number $randomNumber is smaller than counter $counter + available capacity $currentTeacherAvailableCapacity of teacher $currentTeacherId ({$teachers[$currentTeacherId]->name}):";
          if ($randomNumber < $counter + $currentTeacherAvailableCapacity) {
            $designatedTeacher  = $teachers[$currentTeacherId];
            $log .= "YES! {$designatedTeacher->name} chosen.\n";
            break;
          }
          else {
            $counter += $currentTeacherAvailableCapacity;
            $log .= "NO! Adding capacity to counter, new value = $counter.\n";
          }
        }
      }
      else {
        // A supervisor was chosen, so store the supervisor id in our designated teacher variable.
        $designatedTeacher = $teachers[$begeleider];
        $log .= "Supervisor specified. Setting teacher {$designatedTeacher->id} ({$designatedTeacher->name}) as supervisor.\n";
      }

      $log .= "\n";
      
      $infoBlock .= "<li>Verslag zal worden nagekeken door: {$designatedTeacher->name}</li>";
      
      call_user_func($courses[$course]->sendfunction);

      // This statement shows the log on the screen.
      if (isset($_GET['debug'])) {
        echo(nl2br($log));
      }
      // This statement emails the log.
      mail(REPORTS_EMAIL, "Report of OU submit script", $log);

    }
    
    // Generate html page

    include("view_header.php");
    
    if ($infoBlock) {
      echo("<div class=\"infoblock\"><strong>Ontvangen informatie:</strong><ul>".$infoBlock."</ul></div>");
    }
    if ($errorBlock) {
      echo("<div class=\"errorblock\">FOUT:<ul>".$errorBlock."</ul></div>");
      include("view_intro.php");
      include("view_form.php");
    }

  }
  else if (($maintenance) && !(isset($_GET['admin']))) {
    include("view_header.php");
    include("view_maintenance.php");
  }
  else {
    include("view_header.php");
    include("view_intro.php");
    include("view_form.php");
  }

  if (isset($_GET['debug'])) {
    echo("NOTE: IN DEBUG MODE!");
  }

  include("view_footer.php");

  // Close connection with database
  $dbHandle = null;
  
?>