<?php
require __DIR__ . "/vendor/autoload.php";
use PHPZxing\PHPZxingDecoder;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    <video id="video" style="width:640px; height:480px;" autoplay></video>

    <div>
        <button id="snap">ถ่ายรูป</button>
    </div>

    <form action="save.php" method="post" enctype="multipart/form-data">
        <div>
            HN : <input type="text" name="hn" id=""> 
        </div>
        <div>
            <?php 
            $exDate = date('Y-m-d');
            ?>
            วันที่ทำการรักษา : <input type="date" name="dateTreatment" id="" value="<?=$exDate;?>">
        </div>
        <div id="canvasContent" style="background-color:purple"></div>
        <div>
            <button type="submit">บันทึกข้อมูล</button>
        </div>
    </form>

    <script>
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
            
            var img = document.getElementById('canvas-img-'+i);
            img.width = "320";
            img.height = "200";
            img.src = canvas.toDataURL();

            document.getElementById('canvas-file-'+i).value = canvas.toDataURL();
            ++i;
            
            var cvClass = document.getElementsByClassName("canvas-contain");
            cvClass.forEach(element => {
                console.log(element);
            });
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