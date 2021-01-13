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
        <?php 
        $currDateTm = date('Y-m-d');
        if($_POST['dateTM'])
        {
            $currDateTm = $_POST['dateTM'];
        }
        
        ?>
        <form action="pageManage.php" method="post">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="dateTM" class="form-label">เลือกวันที่รักษา</label>
                    <input type="date" name="dateTM" class="form-control" id="dateTM" aria-describedby="emailHelp" value="<?=$currDateTm;?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">แสดงข้อมูล</button>
                    <input type="hidden" name="action" value="show">
                </div>
            </div>
            
        </form>
    </div>
    <?php 
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
    if ($action==='show')
    {
        $dateTM = filter_input(INPUT_POST, 'dateTM', FILTER_SANITIZE_STRING);
        $sql = "SELECT * FROM `pdfs` WHERE `dateTM` = '$dateTM' AND `status` = 1 ORDER BY `id` LIMIT 0, 100";
        $q = $mysqli->query($sql);
        if ($q->num_rows > 0)
        {
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
                        $id = $item['id'];
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
                                // echo '<a href="'.HOST.$item['file'].'">'.$matchs[1].'</a>';

                                

                                echo '<a href="javascript:void(0)" file-data="'.hostPdfFile.$item['file'].'" data-bs-toggle="modal" data-bs-target="#exampleModal">'.$matchs[1].'</a>';
                            }
                            ?>
                            </td>
                            <td>
                                <a href="delFile.php?id=<?=$id;?>" onclick="return notiConfirm()" title="ลบ">
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

            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">ตัวอย่างไฟล์</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            function notiConfirm()
            {
                return confirm("ยืนยันที่จะลบข้อมูลหรือไม่?");
            }

            var myModalEl = document.getElementById('exampleModal')
            myModalEl.addEventListener('show.bs.modal', function (event) {
                var mo = event.relatedTarget.getAttribute('file-data');
                var iframeData = '<iframe src="'+mo+'" style="width:100%; height:100%;" frameborder="0">กรุณาติดตั้ง Adobe PDF Reader ก่อนใช้งาน</iframe>';
                var modalBodyInput = myModalEl.querySelector('.modal-body');
                modalBodyInput.innerHTML = iframeData;
            })

            </script>
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