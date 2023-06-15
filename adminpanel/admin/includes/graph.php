<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row">
    <div class="col-md-12 col-lg-6">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Exam Report
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabs-eg-77">
                        <div class="card mb-3 widget-chart widget-chart2 text-left w-100">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 m-0">
                                    <canvas id="examChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
// Mengambil data dari database
$selCourse = $conn->query("SELECT cou_id, cou_name FROM course_tbl");
$courseData = [];
$labelData = [];

while ($courseRow = $selCourse->fetch(PDO::FETCH_ASSOC)) {
    $courseId = $courseRow['cou_id'];
    $selExmneCount = $conn->query("SELECT COUNT(*) AS exmne_count FROM examinee_tbl WHERE exmne_course='$courseId'")->fetch(PDO::FETCH_ASSOC);
    $courseData[] = $selExmneCount['exmne_count'];
    $labelData[] = $courseRow['cou_name'];
}

// Konversi data ke format JSON
$courseDataJSON = json_encode($courseData);
$labelDataJSON = json_encode($labelData);
?>

<script>
    
// Mendapatkan data chart dari PHP
var courseData = <?php echo $courseDataJSON; ?>;
var labelData = <?php echo $labelDataJSON; ?>;

// Membuat chart menggunakan Chart.js
var ctx = document.getElementById('examChart').getContext('2d');
var examChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labelData,
        datasets: [{
            label: 'Number of Examinees',
            data: courseData,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                precision: 0
            }
        }
    }
});
</script>
