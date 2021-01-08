<?php
/**
 * เหลืออะไรบ้าง ??
 * [] หน้าแสดงรายการไฟล์ที่อัพโหลด
 * [] หน้าของแพทย์ที่ดึงข้อมูลไปใช้งาน
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bootstrap 4 Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body {
            padding-top: 5rem;
        }
    </style>
</head>
<body>
    <style>
    .canvas-contain{
        position: relative;
        display: inline-block;
    }
    .canvasCloseBtn{
        /* position: absolute; */
        float: right;
        top: 0;
        right: 0;
        padding: 3px;
        background-color: gray;
    }
    .canvasCloseBtn:hover{
        cursor: pointer;
    }
    </style>

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">ระบบ Scan เอกสาร</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<div class="container-fluid pt-3">
    
    <?php 
    if ($_SESSION['notiMessage'])
    {
        ?>
        <div class="row">
            <div class="col">
                <div style="background-color: red;"><?=$_SESSION['notiMessage'];?></div>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="row">
        <div class="col">

            <div class="row">
                <div class="col">
                    <video id="video" style="width:800px; height:480px;" autoplay></video>
                </div>
                <div class="col">
                    <button id="snap" type="button" class="btn btn-primary btn-block">ถ่ายรูป</button>
                </div>
            </div>

            <form action="save.php" method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="hn">HN:</label>
                    <input type="text" class="form-control" name="hn" id="hn"> 
                </div>

                <div class="form-group">
                    <label for="dateTreatment">วันที่ทำการรักษา:</label>
                    <?php 
                    $exDate = date('Y-m-d');
                    ?>
                    <input type="date" class="form-control" name="dateTreatment" id="dateTreatment" value="<?=$exDate;?>">
                </div>

                <div class="form-group">
                    <div id="canvasContent" style="background-color:purple"></div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">บันทึกข้อมูล</button>
                </div>
            </form>

        </div>
    </div>

</div>

    <script>
        var SmHttp = function(){}
        SmHttp.prototype = {
            ajax: function(url, data, callback){
                try{
                    xHttp = new ActiveXObject("Msxml2.XMLHTTP");
                }catch(e){
                    try{
                        xHttp = new ActiveXObject("Microsoft.XMLHTTP");
                    }catch(e){
                        xHttp = false;
                    }
                }
                if(!xHttp && document.createElement){
                    xHttp = new XMLHttpRequest();
                }
                
                xHttp.onreadystatechange = function(){
                    if( xHttp.readyState == 4 && xHttp.status == 200 ){
                        callback(xHttp.responseText);
                    }
                };
                xHttp.open("POST", url, true);
                xHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                data = this.objToStr(data);
                xHttp.send(data);
            },
            objToStr: function(data){
                
                if( data === null ){
                    return null;
                }
                
                test_str = [];
                for(var p in data){
                    test_str.push(encodeURIComponent(p)+"="+encodeURIComponent(data[p]));
                }
                return test_str.join("&");
            }
        }

        var video = document.getElementById('video');
        // 720p
        var videoWidth = 1280;
        var videoHeight = 720;

        // // HD
        // var videoWidth = 1920;
        // var videoHeight = 1080;

        // // UXGA
        // var videoWidth = 1600;
        // var videoHeight = 1200;

        // // 2K
        // var videoWidth = 2048;
        // var videoHeight = 1080;

        var constraints = {
        video: { width: { exact: videoWidth }, height: { exact: videoHeight } },
        };

        // Get access to the camera!
        if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) { 
            // Not adding `{ audio: true }` since we only want video now
            navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
                //video.src = window.URL.createObjectURL(stream);
                // window.stream = stream;
                video.srcObject = stream;
                video.play();
            }).catch(handleError);
        }else{
            alert("getUserMedia() is not supported by your browser\nเบราเซอร์ไม่รองรับการทำงานกับเว็บแคมกรุณาเปลี่ยนไปใช้ Chrome หรือ Firefox");
        }

        function handleError(error) {
            console.error("Error: ", error);
        }

        var i = 1;
        // Trigger photo take
        document.getElementById("snap").addEventListener("click", function() { 

            var testHTML = '<div class="canvas-contain" id="canvas-id-'+i+'" draggable="true" ondragstart="event.dataTransfer.setData(\'text/plain\',null)">';
            testHTML += '<div class="canvasCloseBtn" onclick="canvasCloseBtn(this)" data-parent="canvas-id-'+i+'"> [ ปิด ] </div>';
            // testHTML += '<div style="float: right;top: 0;right: 0;padding: 3px; cursor: pointer;" onclick="rotate(\'left\')">[LEFT]</div>';
            // testHTML += '<div style="float: right;top: 0;right: 0;padding: 3px; cursor: pointer;" onclick="rotate(\'right\')">[RIGHT]</div>';
            testHTML += '<div><img src="" id="canvas-img-'+i+'"></div>';
            testHTML += '<input type="hidden" name="canvasValue[]" id="canvas-file-'+i+'" value="">';
            testHTML += '</div>';
            document.getElementById('canvasContent').innerHTML += testHTML;

            var canvas = document.createElement('canvas');
            canvas.width = videoWidth;
            canvas.height = videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, videoWidth, videoHeight);
            
            var dataURL = canvas.toDataURL();

            var img = document.getElementById('canvas-img-'+i);
            img.width = "320";
            img.height = "200";
            img.src = dataURL;


            document.getElementById('canvas-file-'+i).value = dataURL;
            ++i;
            
            // var cvClass = document.getElementsByClassName("canvas-contain");
            // cvClass.forEach(element => {
            //     // console.log(element);
            // });

            var newSm = new SmHttp();
            newSm.ajax(
                "findHn.php",
                {"dataIMG" : dataURL},
                function(responseText){
                    
                    var data = JSON.parse(responseText);
                    console.log(data);

                    if(data.resStatus === true)
                    {
                        document.getElementById('hn').value = data.hn;
                    }
                }
            );

        });
        
        
        var dragged;
        document.addEventListener("dragstart", function(event){
            console.log("dragstart");
            dragged = event.target;
            event.target.style.opacity = .5;
        });

        document.addEventListener("drop", function(event){
            console.log("drop");
            
            event.preventDefault();

            if ( event.target.id == "canvasContent" ) {
                // event.target.style.background = "";
                // dragged.parentNode.removeChild( dragged );
                event.target.appendChild( dragged );
                // console.log("DROPPPPPPP");
            }
        }, false);

        document.addEventListener('dragover', function(event){
            console.log("dragover");
        });


        document.addEventListener("dragend", function(event) {
            // reset the transparency
            console.log("dragend");
            event.target.style.opacity = "";
        }, false);


        



        function rotate(rotateTo)
        {
            // alert(rotateTo);
            if (rotateTo == "right") 
            { 
                /**
                 * @testing
                 * [] 
                 */
                var img = new Image();
                img.src = document.getElementById('canvas-file-1').value;
                img.style.transform = 'rotate(90deg)';

                // หมุนรูป dummy
                document.getElementById('canvas-img-1').style.transform = 'rotate(90deg)';

                var canvas = document.createElement('canvas');
                canvas.getContext('2d').drawImage(img, 0, 0, videoHeight, videoWidth);
                document.getElementById('canvas-file-1').src = canvas.toDataURL();
            }
            else if(rotateTo == "left")
            {
                document.getElementById('canvas-img-1').style.transform = 'rotate(-90deg)';
            }
        }

        function canvasCloseBtn(test)
        {
            var getParent = test.getAttribute('data-parent');
            var element = document.getElementById(getParent);
            document.getElementById('canvasContent').removeChild(element);
        }


    </script>
</body>
</html>