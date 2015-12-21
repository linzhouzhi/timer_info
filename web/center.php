<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="static/jquery.dataTables.min.css">
    <title>NEWE 定时任务系统</title>
<style>

</style>
</head>

<body style="background: gainsboro">
<div style="margin-top:0px;margin:0 auto;width: 80%">
    <div><img src="images/logo.png"></div>
    <table id="example" class="display" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>id</th>
            <th>url</th>
            <th>exe_time</th>
            <th>type</th>
            <th>interval_time</th>
            <th>retry</th>
        </tr>
        </thead>

        <tbody>

        <?php
        require_once("data_process.php");
        $datas = table_datas();
        foreach($datas as $data){
            echo '<tr>';
            echo '<td>'.$data['id'].'</td>';
            echo '<td>'.$data['url'].'</td>';
            echo '<td>'.$data['exe_time'].'</td>';
            echo '<td>'.$data['type'].'</td>';
            echo '<td>'.$data['interval_time'].'/s</td>';
            echo '<td>'.$data['retry'].'</td>';
            echo '</tr>';
        }
        ?>

        </tbody>
    </table>
</div>

<script type="text/javascript" language="javascript" src="static/jquery-1.11.3.min.js">
</script>
<script type="text/javascript" language="javascript" src="static/jquery.dataTables.min.js">
</script>
<script type="text/javascript" language="javascript" class="init">
    $(document).ready(function() {
        $('#example').DataTable({
            "displayLength": 25
        });
    } );
</script>
</body>
</html>
