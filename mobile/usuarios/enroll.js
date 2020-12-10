
$(document).on("click", ".btn_enroll", function (e) {

    var data_uid = parseFloat($(this).attr('data'));
    var data_face = ($(this).attr('data1'));
    var data_id = ($(this).attr('data2'));

    function EnrollFace() {
       
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: "enroll.php",
            'data': {
                u_id: data_uid,
                face_url: data_face,
                _id: data_id,
            },
            beforeSend:function(){
                $('#'+data_id).html('Aguarde')
                $('#'+data_id).prop('disabled', true)
                $("#reset_face").prop('disabled', true)
            },
            success: function (respuesta) {
                if (respuesta.status == "ok") {
                    $("#reset_face").prop('disabled', false)
                    $('#'+data_id).prop('disabled', true)
                    $('#'+data_id).html('Listo')
                    $('#'+data_id).removeClass('btn-custom')
                    $('#'+data_id).addClass('btn-success opa5')
                    
                }else{
                    $("#reset_face").prop('disabled', false)
                    $('#'+data_id).prop('disabled', false)
                    $('#'+data_id).html('Error')
                    setTimeout(function(){
                        $('#'+data_id).html('Enrolar')
                        $('#'+data_id).removeClass('btn-warning')
                        $('#'+data_id).addClass('btn-custom')
                     }, 2000);
                    $('#'+data_id).removeClass('btn-custom')
                    $('#'+data_id).addClass('btn-warning')
                }
            },
            error: function () {
                $("#reset_face").prop('disabled', false)
            }
        });
    }
    EnrollFace()
}); 
$(document).on("click", "#reset_face", function (e) {
    var data_uid = parseFloat($(this).attr('data'));
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: "reset_face.php",
        'data': {
            u_id: data_uid,
        },
        beforeSend:function(){
            $('#rostros').addClass('bg-light')
            $('#reset_respuesta').html('<span class="px-3">Aguarde por favor..</span>')
            $('.btn_enroll').prop('disabled', true)
        },
        success: function (respuesta) {
            if (respuesta.status == "ok") {
                $('#rostros').removeClass('bg-light')
                $('#reset_respuesta').html('<span class="px-3"></span>')
                window.location.reload(1);
            } else {
                $('.btn_enroll').prop('disabled', false)
                $('#reset_respuesta').html('<span class="px-3">Error</span>')
                $('#rostros').removeClass('bg-light')
                $('#rostros').removeClass('d-none')
                // window.location.reload(1);
            }
        },
        error: function () {
            $('.btn_enroll').prop('disabled', false)
            $('#reset_respuesta').html('<span class="px-3">Error</span>')
            $('#rostros').removeClass('bg-light')
            $('#rostros').removeClass('d-none')
            // alert('Error..');   
        }
    });

});
