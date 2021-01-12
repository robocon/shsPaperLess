<?php 
session_start();
require_once 'connection.php';
require_once 'header.php';

?>

<div class="container-fluid pt-3">
    <div class="row">
        <div class="col">
            <h1>จัดการข้อมูล</h1>
        </div>
    </div>
    <div>
        <form action="pageManage.php" method="post">
            <div class="mb-3">
                <label for="dateTM" class="form-label">เลือกวันที่รักษา</label>
                <input type="date" name="dateTM" class="form-control" id="dateTM" aria-describedby="emailHelp">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">แสดงข้อมูล</button>
                <input type="hidden" name="action" value="show">
            </div>
        </form>
    </div>
    <?php 
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
    if ($action==='show') {
        
        $dateTM = filter_input(INPUT_POST, 'dateTM', FILTER_SANITIZE_STRING);
        $sql = "SELECT * FROM `pdfs` WHERE `dateTM` = '$dateTM' ORDER BY `id` LIMIT 0, 100";
        $q = $mysqli->query($sql);
        if ($q->num_rows > 0) {
            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">วันที่ทำการรักษา</th>
                        <th scope="col">วันที่บันทึก</th>
                        <th scope="col">HN</th>
                        <th scope="col">ไฟล์</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    while($item = $q->fetch_assoc())
                    {
                        ?>
                        <tr>
                            <td scope="row"><?=$i;?></td>
                            <td><?=$item['dateTM'];?></td>
                            <td><?=$item['dateSave'];?></td>
                            <td><?=$item['hn'];?></td>
                            <td>
                            <?php 
                            if( preg_match('/(\/\w+\.pdf)/', $item['file'], $matchs) > 0)
                            {
                                echo '<a href="'.HOST.$item['file'].'">'.$matchs[1].'</a>';
                            }
                            ?>
                            </td>
                            <td>
                                <a href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
        else
        {
            echo "ไม่พบข้อมูล";
        }
    }
    ?>
</div>
<?php
require_once 'footer.php';