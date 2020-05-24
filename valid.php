<?php

function loadPos($pdo, $profile_id) {
  $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
  $stmt->execute(array(':prof' => $profile_id));
  $position = array();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $position[] = $row;
  }
  return $position;
}

function loadEduPos($pdo,$profile_id) {
  $stmt = $pdo->prepare('SELECT * FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id = :prof ORDER BY rank');
  $stmt->execute(array(':prof' => $profile_id));
  $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $educations;
}

function validateProfile() {

if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1 ) {
        //error message if fields not filled
        return "All fields are required";
    }

if(strpos($_POST['email'],'@') === false){
	return "Email address must contain @";
}
return true;
}

function validatePos() {
	                  for($i=1; $i<=9; $i++)
                  {
                    if( ! isset($_POST['year'.$i])) continue;
                    if( ! isset($_POST['desc'.$i])) continue;
                    $year = $_POST['year'.$i];
                    $desc = $_POST['desc'.$i];
                    if( strlen($year)==0 || strlen($desc) == 0  ){
                      return "All fields are required";
                    }
                    if( ! is_numeric($year)){
                      return "Position year must be numeric";
                    }
                  }
}


function validateEducation() {
                    for($i=1; $i<=9; $i++)
                  {
                    if( ! isset($_POST['edu_year'.$i])) continue;
                    if( ! isset($_POST['edu_school'.$i])) continue;
                    $edu_year = $_POST['edu_year'.$i];
                    $edu_school = $_POST['edu_school'.$i];
                    if( strlen($edu_year)==0 || strlen($edu_school)==0 ){
                      return "All fields are required";
                    }
                    if( ! is_numeric($edu_year)){
                      return "Education year must be numeric";
                    }
                  }
}

function insertEducation($pdo, $profile_id) {
  $rank = 1;
  for($i=1; $i<=9;$i++)
  {
    if(! isset($_POST['edu_year'.$i])) continue;
    if(! isset($_POST['edu_school'.$i])) continue;
    $edu_year = $_POST['edu_year'.$i];
    $edu_school = $_POST['edu_school'.$i];

    //Lookup the school if it is there
    $institution_id = false;
    $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
    $stmt->execute(array(':name' => $edu_school));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if( $row!== false) $institution_id = $row['institution_id'];

    //if thereis no Institution, insert it
    if( $institution_id === false){
      $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
      $stmt->execute(array(':name' => $edu_school));
      $institution_id = $pdo->lastInsertId();
    }

    $stmt = $pdo->prepare('INSERT INTO Education (profile_id,rank,year,institution_id) VALUES (:pid, :rank, :year, :iid)');
    $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $edu_year,
        ':iid' => $institution_id
    ));
    $rank++;
  }
}



function insertPosition($pdo,$profile_id) {

  //insering Data of Position Section
                $rank = 1; //rank is used just to maintain things in Order
                for($i=1; $i<=9; $i++)
                  {
                    if( ! isset($_POST['year'.$i])) continue;
                    if( ! isset($_POST['desc'.$i])) continue;
                    $year = $_POST['year'.$i];
                    $desc = $_POST['desc'.$i];
                 $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
                        $stmt->execute(array(
                          ':pid' => $profile_id,
                          ':rank' => $rank,
                          ':year' => $year,
                          ':desc' => $desc)
                        );                   
                        $rank++;
                  }

}


?>