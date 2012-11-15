<?php

  echo("<div class=\"subtitle\">Admin pagina van OU SSSS</div>
        <br />
        Huidig academisch jaar gestart op $startOfCurrentAcademicYear<br />");
  foreach($courses as $course) {
    $totalRestCapacity = $totalCapacity[$course->id] - $totalPapers[$course->id];
    echo("<br />
      <div class=\"headingBar\">{$course->name} (id {$course->id})</div>
      <strong>Cursusinformatie:</strong><br />
      Totale capaciteit: {$totalCapacity[$course->id]}<br />
      Aantal papers: {$totalPapers[$course->id]}<br />
      Resterende capaciteit: $totalRestCapacity<br />
      Aantal submissies (aantal studenten dat een paper heeft ingeleverd): {$totalSubmissions[$course->id]}<br />
      <br />
      <strong>Informatie per docent:</strong><br />
      <table border=\"0\">
      <tr style=\"background:#EEEEEE;\">
        <td style=\"font-weight:bold; text-align:center;\">
          id
        </td>
        <td style=\"font-weight:bold; text-align:center;\">
          Naam
        </td>
        <td style=\"font-weight:bold; text-align:center;\">
          Capaciteit
        </td>
        <td style=\"font-weight:bold; text-align:center;\">
          Papers
        </td>
        <td style=\"font-weight:bold; text-align:center;\">
          Selecties
        </td>
        <td style=\"font-weight:bold; text-align:center;\">
          Percentage selecties
        </td>
        <td style=\"font-weight:bold; text-align:center;\">
          Resterende capaciteit
        </td>
        <td style=\"font-weight:bold; text-align:center;\">
          Relatieve resterende capaciteit*
        </td>
        <td style=\"font-weight:bold; text-align:center;\">
          Submissies (studenten)
        </td>
      </tr>
      ");
    $total_capacity    = 0;
    $total_papers      = 0;
    $total_selections  = 0;
    $total_proportion  = 0;
    $total_submissions = 0;
    foreach($teachers as $teacher) {
      if ($teacher->id % 2 == 0) { //Even number, grey table background
        $tableRowStyle = "style=\"background:#EEEEEE;\"";
      }
      else {
        $tableRowStyle = "";
      }
      $restCapacity = $capacities[$course->id][$teacher->id] - $papers[$course->id][$teacher->id];
      $proportion = round(100 * $restCapacity / $totalRestCapacity, 2);
      if ($papers[$course->id][$teacher->id] == 0) {
        $percentageSelections = 0;
      }
      else {
        $percentageSelections = round(100 * $selections[$course->id][$teacher->id] / $papers[$course->id][$teacher->id], 2);
      }
      // Also calculate totals for bottom row of table
      $total_capacity += $capacities[$course->id][$teacher->id];
      $total_papers += $papers[$course->id][$teacher->id];
      $total_selections += $selections[$course->id][$teacher->id];
      $total_proportion += $proportion;
      $total_submissions += $submissions[$course->id][$teacher->id];
      echo("
        <tr $tableRowStyle>
          <td style=\"text-align:center;\">
            {$teacher->id}
          </td>
          <td style=\"text-align:center;\">
            {$teacher->name}
          </td>
          <td style=\"text-align:center;\">
            {$capacities[$course->id][$teacher->id]}
          </td>
          <td style=\"text-align:center;\">
            {$papers[$course->id][$teacher->id]}
          </td>
          <td style=\"text-align:center;\">
            {$selections[$course->id][$teacher->id]}
          </td>
          <td style=\"text-align:center;\">
            $percentageSelections %
          </td>
          <td style=\"text-align:center;\">
            $restCapacity
          </td>
          <td style=\"text-align:center;\">
            $proportion %
          </td>
          <td style=\"text-align:center;\">
            {$submissions[$course->id][$teacher->id]}
          </td>
        </tr>");
    }
    $total_percentageSelections = $capacities[$course->id][$teacher->id];
    echo("
      <tr style=\"background:#BBBBBB;\">
        <td style=\"text-align:center;\">
          
        </td>
        <td style=\"text-align:center;\">
          Totals:
        </td>
        <td style=\"text-align:center;\">
          $total_capacity
        </td>
        <td style=\"text-align:center;\">
          $total_papers
        </td>
        <td style=\"text-align:center;\">
          $total_selections
        </td>
        <td style=\"text-align:center;\">
          ".($total_papers==0?0:round($total_selections/$total_papers, 2))." %
        </td>
        <td style=\"text-align:center;\">
          ".round($total_capacity - $total_papers, 2)."
        </td>
        <td style=\"text-align:center;\">
          $total_proportion %
        </td>
        <td style=\"text-align:center;\">
          $total_submissions
        </td>
      </tr>
    </table>");
  }
  
  echo("<br />* Let op: bij de berekening van de relatieve resterende capaciteit (de kans dat de volgende ingeleverde paper aan een docent wordt toegewezen) wordt g&eacute;&eacute;n rekening gehouden met de status van docenten. Alle docenten worden als 'actief' beschouwd.<br />");
  
  echo("<br /><div class=\"headingBar\">Status docenten</div>
    <table border=\"0\">
    <tr>
      <td>
        id
      </td>
      <td>
        Naam
      </td>
      <td>
        Actief?
      </td>
  ");
  foreach($teachers as $teacher) {
    echo("
      <tr>
        <td>
          {$teacher->id}
        </td>
        <td>
          {$teacher->name}
        </td>
        <td>
          {$teacher->active}
        </td>
      </tr>");
  }
  echo("</table>");
  echo("<br /><div class=\"headingBar\">Verander status van een docent</div>
    <form action=\"index.php\" method=\"post\">
      <div class=\"formRow\">
        <label for=\"teacherName\">Acroniem van docent:</label>
        <input id = \"teacherName\" type=\"text\" value=\"\" name=\"teacherName\" />
       </div>
      <div class=\"formRow\">
        <label for=\"password\">Wachtwoord:</label>
        <input id = \"password\" type=\"password\" value=\"\" name=\"password\" />
       </div>
      <div class=\"formRow\">
        <input type=\"submit\" value=\"Verstuur\" name=\"submit\" />
      </div>
    </form>");

?>