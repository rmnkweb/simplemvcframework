var $actionsAvailable = true,
    canvas = document.getElementById("inquiryGeneratorCanvas"),
    ctx = canvas.getContext("2d"),
    $img;

$(document).ready(function() {
    $("#createImageButton").on("click", function (e) {
        e.preventDefault();
        if ($actionsAvailable) {
            $actionsAvailable = false;
            $("#createImageButton").addClass("processing");

            var $form = $("#inquiryForm");

            $("#inquiryGeneratorCanvas").attr("width", 1600).attr("height", 1600);
            $img = new Image();
            $img.onload = function() {
                // inquiry image generation
                ctx.drawImage($img, 0, 0, $img.width, $img.height);
                ctx.font = "bold 40px Arial";
                var $fio = $("#inquiryFormFioText").val();
                ctx.fillText($fio, 750, 620, 720); // render last line
                $("#inquiryFormImageText").text(canvas.toDataURL("image/jpeg"));

                // save image and create DB row
                $.ajax({
                    data: $form.serialize(),
                    type: $form.attr("method"),
                    url: $form.attr("action"),
                    success: function (result) {
                        console.log(result);
                        try {
                            var $response = JSON.parse(result);
                            console.log($response);
                            if ((typeof $response.values !== "undefined") && (typeof $response.values.guid !== "undefined") && (typeof $response.values.phone !== "undefined")) {
                                $("#newImageGuid").text($response.values.guid + ".jpg");
                                $("#newImageGuid").attr("href", ("/public/files/" + $response.values.guid + ".jpg"));
                                $("#step1Blocks").addClass("hidden");
                                $("#step2Blocks").removeClass("hidden");
                                $smsText = "Уважаемый пациент! В силу ограничений передвижения по Москве, мы позаботились, чтобы Вы добрались до нас без затруднений. Ваш документ пациента ООО \"Клиника Подологии\": " + document.location.origin + "/public/files/" + $response.values.guid + ".jpg";
                                $("#inquirySendSmsFormPhone").val($response.values.phone);
                                $("#inquirySendSmsFormText").val($smsText);
                            } else {
                                alert("Непредвиденная ошибка! Свяжитесь с администратором.");
                                console.log($response);
                            }
                        } catch($e) {
                            console.log($e);
                            alert("Непредвиденная ошибка! Свяжитесь с администратором.");
                        }
                        $actionsAvailable = true;
                        $("#createImageButton").removeClass("processing");
                    }
                });
            };
            $img.src = '/public/img/inquiry_template.jpg';
        }
    });
    $("#sendMessageButton").on("click", function (e) {
        e.preventDefault();
        if ($actionsAvailable) {
            $actionsAvailable = false;
            $("#sendMessageButton").addClass("processing");

            var $form = $("#inquirySendSmsForm");


            $.ajax({
                data: $form.serialize(),
                type: $form.attr("method"),
                url: $form.attr("action"),
                success: function (result) {
                    console.log(result);
                    try {
                        var $response = JSON.parse(result);
                        if ((typeof $response.status !== "undefined") && ($response.status === 1)) {
                            alert("Сообщение отправлено!");
                        } else {
                            alert("Непредвиденная ошибка! Свяжитесь с администратором.");
                            console.log($response);
                        }
                    } catch($e) {
                        console.log($e);
                        alert("Непредвиденная ошибка! Свяжитесь с администратором.");
                    }
                    $actionsAvailable = true;
                    $("#sendMessageButton").removeClass("processing");
                    $("#newImageGuid").text("");
                    $("#step1Blocks").removeClass("hidden");
                    $("#step2Blocks").addClass("hidden");
                }
            });

        }
    });
});

function download(canvas, filename) {
    var e;
    var lnk = document.createElement("a");

    lnk.download = filename;

    lnk.href = canvas.toDataURL("image/jpeg", 0.8);

    if (document.createEvent) {
        e = document.createEvent("MouseEvents");
        e.initMouseEvent(
            "click",
            true,
            true,
            window,
            0,
            0,
            0,
            0,
            0,
            false,
            false,
            false,
            false,
            0,
            null
        );
        lnk.dispatchEvent(e);
    } else if (lnk.fireEvent) {
        lnk.fireEvent("onclick");
    }
}



function onloadFileManagerStatusbarActionsButton() {
    $status = $(".fileManagerStatusbarActions").data("status");
    switch ($status) {
        case "green3" :
            $("#fileManagerStatusbarActionsToedit").addClass("active");
            $status = "Выложено в инстаграм";
            break;
        case "green2" :
            $("#fileManagerStatusbarActionsToedit").addClass("active");
            $status = "Выложено видео";
            break;
        case "web" :
            $("#fileManagerStatusbarActionsToedit").addClass("active");
            $status = "Отправлено вебмастеру";
            break;
        case "green" :
            $("#fileManagerStatusbarActionsToedit").addClass("active");
            $("#fileManagerStatusbarActionsSend").addClass("active");
            $("#fileManagerStatusbarActionsVideo").addClass("active");
            $("#fileManagerStatusbarActionsInstagram").addClass("active");
            $status = "Одобрено";
            break;
        case "red" :
            $("#fileManagerStatusbarActionsToverify").addClass("active");
            $status = "В работе";
            break;
        case "yellow" :
            $("#fileManagerStatusbarActionsVerify").addClass("active");
            $("#fileManagerStatusbarActionsToedit").addClass("active");
            $status = "На проверке";
            break;
        case "empty" :
        default:
            $("#fileManagerStatusbarActionsToedit").addClass("active");
            $status = "Не опредлено";
            break;
    }
}