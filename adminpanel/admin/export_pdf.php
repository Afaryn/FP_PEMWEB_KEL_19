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
require_once("dompdf/autoload.inc.php");
use Dompdf\Dompdf;
$dompdf = new Dompdf();

// Mendapatkan data dari database (sesuaikan dengan query Anda)
@$exam_id = $_GET['exam_id'];
$selEx = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$exam_id' ")->fetch(PDO::FETCH_ASSOC);
$exam_course = $selEx['cou_id'];
$cou_name = $conn->query("SELECT cou_name FROM course_tbl WHERE cou_id='$exam_course' ")->fetch(PDO::FETCH_ASSOC);
$selcou=$cou_name['cou_name'];
$selExmne = $conn->query("SELECT * FROM examinee_tbl et  WHERE exmne_course='$exam_course' ");

$html = '<center><h3>Exam Report '.$selcou.'</h3></center><hr/><br/>';
$html .= '<table border="1" width="100%">
 <tr>
 <th>no</th>
 <th>Examinee Fullname</th>
 <th>Score / Over</th>
 <th>Percentage</th>
 </tr>';
$no = 1;
// while($row = mysqli_fetch_array($query))
// {
//  $html .= "<tr>
//  <td>".$no."</td>
//  <td>".$row['nama']."</td>
//  <td>".$row['kelas']."</td>
//  <td>".$row['alamat']."</td>
//  </tr>";
//  $no++;
// }
while ($selExmneRow = $selExmne->fetch(PDO::FETCH_ASSOC)) {
    $exmneId = $selExmneRow['exmne_id'];
    $selScore = $conn->query("SELECT * FROM exam_question_tbl eqt INNER JOIN exam_answers ea ON eqt.eqt_id = ea.quest_id AND eqt.exam_answer = ea.exans_answer  WHERE ea.axmne_id='$exmneId' AND ea.exam_id='$exam_id' AND ea.exans_status='new' ORDER BY ea.exans_id DESC");
    $selAttempt = $conn->query("SELECT * FROM exam_attempt WHERE exmne_id='$exmneId' AND exam_id='$exam_id' ");
    $over = $selEx['ex_questlimit_display'];

    $score = $selScore->rowCount();
    $ans = $score / $over * 100;

    $html .= "<tr>
    <td>".$no."</td>
    <td>".$selExmneRow['exmne_fullname']."</td>
    <td>".$score . ' / ' . $over."</td>
    <td>".number_format($ans, 2) . '%'."</td>
    </tr>";

    

    $no++;
}
$html .= "</html>";
$dompdf->loadHtml($html);
// Setting ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'potrait');
// Rendering dari HTML Ke PDF
$dompdf->render();
// Melakukan output file Pdf
$dompdf->stream('ranking_exam '.$selcou.'.pdf');
?>