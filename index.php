<?php 
session_start();
require_once 'header.php';
?>
<div class="container-fluid pt-3">
    <div class="row">
        <div class="col">

            <div class="row">
                <div class="col">
                    <video id="video" style="width:800px; height:480px;" autoplay></video>

                    <div class="alert alert-danger align-items-center" role="alert" id="videoNotify" style="display: none;">
                    กรุณาติดตั้งกล้อง Webcam ก่อนใช้งาน
                    </div>

                </div>
                <div class="col">

                    <div class="d-grid gap-2">
                        <button id="snap" type="button" class="btn btn-primary">ถ่ายรูป</button>
                    </div>

                </div>
            </div>

            <form action="save.php" method="post" enctype="multipart/form-data">

                <div class="form-group mb-3">
                    <label for="hn" class="form-label">HN:</label>
                    <input type="text" class="form-control" name="hn" id="hn"> 
                </div>

                <div class="form-group">
                    <label for="dateTreatment" class="form-label">วันที่ทำการรักษา:</label>
                    <?php 
                    $exDate = date('Y-m-d');
                    ?>
                    <input type="date" class="form-control" name="dateTreatment" id="dateTreatment" value="<?=$exDate;?>">
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col">
                            <div id="canvasContent" class="row row-cols-auto" style="background-color:purple"></div>
                            <!-- 
                                https://getbootstrap.com/docs/5.0/components/card/
                             -->
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col">
                            <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                        </div>
                    </div>
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
            document.getElementById('video').style.display = 'none';
            document.getElementById('videoNotify').style.display = '';
        }

        var i = 1;
        // Trigger photo take
        document.getElementById("snap").addEventListener("click", function() { 

            var testHTML = '<div class="canvas-contain" id="canvas-id-'+i+'" draggable="true" ondragstart="event.dataTransfer.setData(\'text/plain\',null)">';
            testHTML += '<div class="canvasCloseBtn" onclick="canvasCloseBtn(this)" data-parent="canvas-id-'+i+'"> [ ปิด ] </div>';
            // testHTML += '<div style="float: right;top: 0;right: 0;padding: 3px; cursor: pointer;" onclick="rotate(\'left\')">[LEFT]</div>';
            // testHTML += '<div style="float: right;top: 0;right: 0;padding: 3px; cursor: pointer;" onclick="rotate(\'right\')">[RIGHT]</div>';
            testHTML += '<div style="text-align: center;"><img src="" id="canvas-img-'+i+'"></div>';
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
                    if(data.resStatus === true)
                    {
                        document.getElementById('hn').value = data.hn;
                    }
                }
            );

        });
        
        /*
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
        */

        function canvasCloseBtn(test)
        {
            var getParent = test.getAttribute('data-parent');
            var element = document.getElementById(getParent);
            document.getElementById('canvasContent').removeChild(element);
        }

        jQuery.noConflict();
        (function( $ ) {
            $(function() {
                // More code using $ as alias to jQuery

                /*
                $('#canvasContent').sortable({
                    connectWith: ".canvas-contain",
                    handle: ".canvas-contain",
                    placeholder: "panel-placeholder",
                    start: function(e, ui){
                        ui.placeholder.width(ui.item.find('.canvas-contain').width());
                        ui.placeholder.height(ui.item.find('.canvas-contain').height());
                        ui.placeholder.addClass(ui.item.attr("class"));
                    }
                });
                */
               
                $('#canvasContent').sortable();

            });
        })(jQuery);
    </script>
<?php 
require_once 'footer.php';
?>