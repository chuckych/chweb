$(function () {

    //"use strict"; // Start of use strict

    let proy_info = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_info"
    );
    proy_info = JSON.parse(proy_info);

    let p = '';
    p = get_proy_pasos();


    $('.ProyNom').html(p.ProyNom);
    $('.ProyDesc').html(p.ProyDesc);
    $('.ProcDesc').html(p.ProcDesc);
    $('.PlanoDesc').html(p.PlanoDesc);
    $('.PlanoCod').html(p.PlanoCod);

    // console.log(p);
    // console.log(proy_info);

    // sessionStorage.setItem(
    //     location.pathname.substring(1) + "proy_page",
    //     'Finalizar'
    // );
    $("#mainTitleBar").html(('Finalizar'));
    $(document).prop("title", ('Finalizar'));

    getPag("#tarCancelar", "inicio");

    $(document).on('click', "#tarSubmit", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        // form data 
        let datos = new FormData();
        datos.append('data', JSON.stringify(p));
        datos.append('tarSubmit', 'tarSubmit');
        axios({
            method: "post",
            url: 'finalizar/process.php',
            data: datos,
            headers: { "Content-Type": "multipart/form-data" },
        }).then(function (response) {
            $.notifyClose();
            let data = response.data;
            if (data.status == 'ok') {
                notify(data.Mensaje, 'success', 1000, 'right')
                datos.delete('data');
                // off click
                $(document).off('click', "#tarSubmit");
                getPag2("finTar", "Tarea Finalizada. Gracias");

            } else if (data.status == 'pendTar') {

                let d = data.Mensaje
                let item = (title, text) => {
                    let d = `
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col colItem">
                                <small class="text-body d-block">`+ title + `</small>
                                <span class="d-block mt-1 font08">`+ text + `</span>
                            </div>
                        </div>
                    </div>`
                    return d
                };

                let msg = `<div class="">
                    <div class="list-group list-group-flush font09">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="lh15 list-timeline-title text-info">${d.Text}</span>
                            </div>
                        </div>
                    </div>
                    ${item("Proyecto", d.Proy.nombre + ' (#' + d.Proy.ID + ')')}
                    ${item("Empresa", d.EmpDesc)}
                    ${item("Proceso", d.ProcDesc)}
                    ${item("Plano", d.PlanoDesc)}
                    ${item("Inicio Tarea", d.Inicio + ' | <span class="mt-1 font07">' + d.Duracion + '</span>')}
                        <div class="col mt-2">
                        <button class="btn btn-success px-4 completeTarNotif float-end" data-tareID="${d.TareID}">COMPLETAR</button>
                        </div>
                    </div>
                </div>`
                notify(msg, 'info', 0, 'right')
                let dataContainer = document.querySelectorAll("[data-notify=container]");
                console.log(dataContainer);
                $(dataContainer).addClass("shadow")
                setTimeout(() => {
                    $(".completeTarNotif").addClass("animate__animated animate__headShake")
                }, 500);
            } else {
                datos.delete('data');
                notify(data.Mensaje, 'danger', 0, 'right')
            }
            // sessionStorage.setItem(
            //     location.pathname.substring(1) + "proy_page",
            //     pag
            // );
            // $("#contenedor").html(response.data);
        }).then(() => {
            datos.delete('data');
            // $("#mainTitleBar").html(capitalize(pag));
            // const p = selector;
            // $("#mainTitleBar").addClass(p.replace('.', ''));
            // $(document).prop("title", capitalize(pag));
        }).catch(function (error) {
            alert(error);
        })
    });
    completeTar('.completeTarNotif')
});