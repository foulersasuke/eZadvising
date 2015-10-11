<?php

require_once("config.php");
require_once("pe.php");

$token = $_GET['token'];
$studentId = $_GET['studentId'];
$courseId = $_GET['courseId'];

$result = "false";

try
{
    $conn = new PDO(DBCONNECTSTRING, DBUSER, DBPASSWORD);
    $sql = 'SELECT expression FROM prereqs WHERE prereqs.courseId=:courseId';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseId', $courseId);
    $stmt->execute();
    $prereqs = $stmt->fetchAll();

    foreach ($prereqs as $prereq) {
        $req = $prereq['expression'];
    }

    $len = strlen($req);

    if ($len > 1)
    {
        $req1 = substr($req, 0, 1);
        $req2 = substr($req, -1);

        $sql = 'SELECT *  FROM prereq_detail WHERE prereq_detail.id=:id OR prereq_detail.id=:id2 ';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $req1);
        $stmt->bindParam(':id2', $req2);
        $stmt->execute();
        $req_detail = $stmt->fetchAll();

        $count = 0;
        $passClass = [];
        foreach ($req_detail as $rd) {

            if ($rd['type'] == 2) {
                $sql = 'SELECT grade from course_records WHERE studentId=:stuId AND courseId=:courseId AND type=1';
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':stuId', $studentId);
                $stmt->bindParam(':courseId', $rd['courseId']);
                $stmt->execute();
                $taken = $stmt->fetchAll();

                foreach($taken as $t)
                {
                    $grade = $t['grade'];
                }

                if ($grade >= $rd['minGrade']) {
                    $passClass[$count] = true;
                }
            }
            $count = $count + 1;
        }
        $pass = $passClass[0] + $passClass[1];
        if ($pass == 2) {
            $result = "true";
        }
        echo($result);
    }
    else {
        $req1 = substr($req, 0, 1);

        $sql = 'SELECT *  FROM prereq_detail WHERE prereq_detail.id=:id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $req1);
        $stmt->execute();
        $req_detail = $stmt->fetchAll();

        foreach ($req_detail as $rd) {
            $type = $rd['type'];
            $cID = $rd['courseId'];
            $minG = $rd['minGrade'];
        }

        if ($type == 2) {
            $sql = 'SELECT grade from course_records WHERE studentId=:stuId AND courseId=:courseId AND type=1';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':stuId', $studentId);
            $stmt->bindParam(':courseId', $cID);
            $stmt->execute();
            $taken = $stmt->fetchAll();

            if ($taken['grade'] >= $minG) {
                $result = "true";
            }
        }
        echo($result);
    }
}
catch (PDOException $e)
{
    return 500;
}