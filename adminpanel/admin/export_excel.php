<?php
$host = "localhost";
$user = "root";
$pass = "admin";
$db   = "cee_db";
$conn = null;

try {
  $conn = new PDO("mysql:host={$host};dbname={$db};",$user,$pass);
} catch (Exception $e) {
  
}
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Mendapatkan data dari database (sesuaikan dengan query Anda)
@$exam_id = $_GET['exam_id'];
$selEx = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$exam_id' ")->fetch(PDO::FETCH_ASSOC);
$exam_course = $selEx['cou_id'];
$cou_name = $conn->query("SELECT cou_name FROM course_tbl WHERE cou_id='$exam_course' ")->fetch(PDO::FETCH_ASSOC);
$selcou=$cou_name['cou_name'];
$selExmne = $conn->query("SELECT * FROM examinee_tbl et  WHERE exmne_course='$exam_course' ");

// Membuat objek Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan header kolom
$sheet->setCellValue('A1', 'Examinee Fullname');
$sheet->setCellValue('B1', 'Score / Over');
$sheet->setCellValue('C1', 'Percentage');

// Menambahkan data dari database
$row = 2;
while ($selExmneRow = $selExmne->fetch(PDO::FETCH_ASSOC)) {
    $exmneId = $selExmneRow['exmne_id'];
    $selScore = $conn->query("SELECT * FROM exam_question_tbl eqt INNER JOIN exam_answers ea ON eqt.eqt_id = ea.quest_id AND eqt.exam_answer = ea.exans_answer  WHERE ea.axmne_id='$exmneId' AND ea.exam_id='$exam_id' AND ea.exans_status='new' ORDER BY ea.exans_id DESC");
    $selAttempt = $conn->query("SELECT * FROM exam_attempt WHERE exmne_id='$exmneId' AND exam_id='$exam_id' ");
    $over = $selEx['ex_questlimit_display'];

    $score = $selScore->rowCount();
    $ans = $score / $over * 100;

    $sheet->setCellValue('A' . $row, $selExmneRow['exmne_fullname']);
    $sheet->setCellValue('B' . $row, $score . ' / ' . $over);
    $sheet->setCellValue('C' . $row, number_format($ans, 2) . '%');

    $row++;
}

// Menyimpan file Excel
$writer = new Xlsx($spreadsheet);
$filename = 'ranking_exam '.$selcou.'.xlsx';
$writer->save($filename);


?>
