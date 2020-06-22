<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<?php
$realdate = date("Y");
$realdatex1 = $realdate-1;
$realdatex2 = $realdate-2;
if(!isset($_GET['years'])) {
    $_GET['years'] = $realdate;
} ?>
<div class="text-center">
    <?php
    if(isset($_GET['years']) && $_GET['years'] == $realdatex1) {
		echo '<a class="btn btn-success" href="index.php?page=statistiques&years='.$realdatex2.'">'.$realdatex2.'</a>&nbsp;';
        echo '<a class="btn btn-success" href="index.php?page=statistiques&years='.$realdatex1.'">'.$realdatex1.'</a>&nbsp;';
        echo '<a class="btn btn-primary" href="index.php?page=statistiques&years='.$realdate.'">'.$realdate.'</a>';
    } elseif(isset($_GET['years']) && $_GET['years'] == $realdate) {
        echo '<a class="btn btn-success" href="index.php?page=statistiques&years='.$realdatex2.'">'.$realdatex2.'</a>&nbsp;';
		echo '<a class="btn btn-primary" href="index.php?page=statistiques&years='.$realdatex1.'">'.$realdatex1.'</a>&nbsp;';
        echo '<a class="btn btn-success" href="index.php?page=statistiques&years='.$realdate.'">'.$realdate.'</a>';
    } elseif(isset($_GET['years']) && $_GET['years'] == $realdatex2) {
        echo '<a class="btn btn-success" href="index.php?page=statistiques&years='.$realdatex2.'">'.$realdatex2.'</a>&nbsp;';
		echo '<a class="btn btn-primary" href="index.php?page=statistiques&years='.$realdatex1.'">'.$realdatex1.'</a>&nbsp;';
        echo '<a class="btn btn-success" href="index.php?page=statistiques&years='.$realdate.'">'.$realdate.'</a>';
	} ?>
    
</div>


<?php
if(isset($_GET['years'])) {
    $datevar = $_GET['years'];
} else {
    $datevar = $realdate;
} ?>

<br><br><br>

<!-- --------------------------------------------------------------------------- -->
<div class="row">
    <div class="col-md-offset-4 col-md-4 text-center">
        <legend>
            <h1>Nombre total d'heures</h1>
        </legend>
		<br>
    </div>
    <div class="text-center col-md-12">
        <canvas id="total" style="width: 98%; height: 600px;" height="600"></canvas>
    </div>
</div>

<script>
var barData = {
    labels: [<?php
        $query = mysql_query("
            SELECT c.name, i.category, SUM(i.time) AS time
            FROM tincidents i
            INNER JOIN tusers u ON u.id = i.user
            INNER JOIN tcompany c ON c.id = u.company
            WHERE date_create BETWEEN '$datevar-01-01' AND '$datevar-12-31'
			AND i.category NOT IN (9)
            GROUP BY u.company
			LIMIT 20
        ");
        while ($row = mysql_fetch_array($query)){
            echo "'".$row['name']."',";
        } ?>],
    datasets: [
        {
            label: '<?= $datevar ?>',
            fillColor: '#4400ff',
            data: [<?php
                $query = mysql_query("
                    SELECT c.name, i.category, SUM(i.time) AS time
                    FROM tincidents i
                    INNER JOIN tusers u ON u.id = i.user
                    INNER JOIN tcompany c ON c.id = u.company
                    WHERE date_create BETWEEN '$datevar-01-01' AND '$datevar-12-31'
					AND i.category NOT IN (9)
                    GROUP BY u.company
                ");
                while ($row = mysql_fetch_array($query)){
                    $time = $row['time'] / 60;
                    echo "'".$time."',";
                } ?>]
        }
    ]
};
    
var context = document.getElementById('total').getContext('2d');
var clientsChart = new Chart(context).Bar(barData);
</script>

<!-- --------------------------------------------------------------------------- --> <br>

<div class="row">
    <div class="col-md-offset-4 col-md-4 text-center">
        <legend>
            <h1>Nombre d'heures en hotline</h1>
        </legend>
        <br>
    </div>
	<br>
    <div class="text-center col-md-12">
        <canvas id="cattlm" style="width: 46%;height: 300px;"></canvas>
    </div>
</div>

<script>
var barData = {
    labels: [<?php
        $query = mysql_query("
            SELECT c.name, i.category, SUM(i.time) AS time
            FROM tincidents i
            INNER JOIN tusers u ON u.id = i.user
            INNER JOIN tcompany c ON c.id = u.company
            WHERE date_create BETWEEN '$datevar-01-01' AND '$datevar-12-31' AND i.category IN (5)
            GROUP BY u.company
        ");
        while ($row = mysql_fetch_array($query)){
            echo "'".$row['name']."',";
        } ?>],
    datasets: [
        {
            label: '<?= $datevar ?>',
            fillColor: '#00ff58',
            data: [<?php
                $query = mysql_query("
                    SELECT c.name, i.category, SUM(i.time) AS time
                    FROM tincidents i
                    INNER JOIN tusers u ON u.id = i.user
                    INNER JOIN tcompany c ON c.id = u.company
                    WHERE date_create BETWEEN '$datevar-01-01' AND '$datevar-12-31' AND i.category IN (5)
                    GROUP BY u.company
                ");
                while ($row = mysql_fetch_array($query)){
                    $time = $row['time'] / 60;
                    echo "'".$time."',";
                } ?>]
        }
    ]
};
    
var context = document.getElementById('cattlm').getContext('2d');
var clientsChart = new Chart(context).Bar(barData);
</script>

<!-- --------------------------------------------------------------------------- --> <br>

<div class="row">
    <div class="col-md-offset-4 col-md-4 text-center">
        <legend>
            <h1>Nombre d'heures en télémaintenance</h1>
        </legend>
        <br>
    </div>
	<br>
    <div class="text-center col-md-12">
        <canvas id="cathot" style="width: 46%;height: 300px;"></canvas>
    </div>
</div>

<script>
var barData = {
    labels: [<?php
        $query = mysql_query("
            SELECT c.name, i.category, SUM(i.time) AS time
            FROM tincidents i
            INNER JOIN tusers u ON u.id = i.user
            INNER JOIN tcompany c ON c.id = u.company
            WHERE date_create BETWEEN '$datevar-01-01' AND '$datevar-12-31' AND i.category IN (6)
            GROUP BY u.company
        ");
        while ($row = mysql_fetch_array($query)){
            echo "'".$row['name']."',";
        } ?>],
    datasets: [
        {
            label: '<?= $datevar ?>',
            fillColor: '#ff9d00',
            data: [<?php
                $query = mysql_query("
                    SELECT c.name, i.category, SUM(i.time) AS time
                    FROM tincidents i
                    INNER JOIN tusers u ON u.id = i.user
                    INNER JOIN tcompany c ON c.id = u.company
                    WHERE date_create BETWEEN '$datevar-01-01' AND '$datevar-12-31' AND i.category IN (6)
                    GROUP BY u.company
                ");
                while ($row = mysql_fetch_array($query)){
                    $time = $row['time'] / 60;
                    echo "'".$time."',";
                } ?>]
        }
    ]
};
    
var context = document.getElementById('cathot').getContext('2d');
var clientsChart = new Chart(context).Bar(barData);
</script>

<!-- --------------------------------------------------------------------------- --> <br>


<div class="row">
    <div class="col-md-offset-4 col-md-4 text-center">
        <legend>
            <h1>Nombre de tickets d'appels</h1>
        </legend>
        <br>
    </div>
	<br>
    <div class="text-center col-md-12">
        <canvas id="catcount" style="width: 46%;height: 300px;"></canvas>
    </div>
</div>

<script>
var barData = {
    labels: [<?php
        $query = mysql_query("
            SELECT c.name, i.category, SUM(i.time) AS time
            FROM tincidents i
            INNER JOIN tusers u ON u.id = i.user
            INNER JOIN tcompany c ON c.id = u.company
            WHERE date_create BETWEEN '$datevar-01-01' AND '$datevar-12-31' AND i.category IN (8)
            GROUP BY u.company
        ");
        while ($row = mysql_fetch_array($query)){
            echo "'".$row['name']."',";
        } ?>],
    datasets: [
        {
            label: '<?= $datevar ?>',
            fillColor: '#DB0073',
            data: [<?php
                $query = mysql_query("
                    SELECT c.name, i.category, COUNT(*) AS icount
                    FROM tincidents i
                    INNER JOIN tusers u ON u.id = i.user
                    INNER JOIN tcompany c ON c.id = u.company
                    WHERE date_create BETWEEN '$datevar-01-01' AND '$datevar-12-31' AND i.category IN (8)
                    GROUP BY u.company
                ");
                while ($row = mysql_fetch_array($query)){
                    $count = $row['icount'];
                    echo "'".$count."',";
                } ?>]
        }
    ]
};
    
var context = document.getElementById('catcount').getContext('2d');
var clientsChart = new Chart(context).Bar(barData);
</script>

<!-- --------------------------------------------------------------------------- --> <br>