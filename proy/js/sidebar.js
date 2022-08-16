$(function () {
    let sideData = new FormData();
    sideData.append('page', 'sidebar');
    setTimeout(() => {
        axios({
            method: "post",
            url: 'sidebar.php',
            data: sideData
        }).then(function (response) {
            let liIcon = '';
            let liId = '';
            // foreach para recorrer el array de modulos
            if (response.data) {
                response.data.forEach(function (item) {
                    let li = document.createElement('li'); // crear el li
                    liClassName = 'nav-item';
                    switch (item.modsrol) {
                        case '35':
                            liIcon = 'easel'
                            liId = 'sidebarProyectos'
                            break;
                        case '36':
                            liIcon = 'list-task'
                            liId = 'sidebarMisTareas'
                            break;
                        case '37':
                            liIcon = 'list-task'
                            liId = 'sidebarTareas'
                            break;
                        case '38':
                            liIcon = 'card-checklist'
                            liId = 'sidebarEstados'
                            break;
                        case '39':
                            liIcon = 'diagram-2'
                            liId = 'sidebarProcesos'
                            break;
                        case '40':
                            liIcon = 'diagram-3'
                            liId = 'sidebarPlantillas'
                            break;
                        case '41':
                            liIcon = 'map'
                            liId = 'sidebarPlanos'
                            break;
                        case '42':
                            liIcon = 'building'
                            liId = 'sidebarEmpresas'
                            break;
                        case '43':
                            liIcon = 'house'
                            liId = 'sidebarInicio'
                            break;
                        default:
                            liIcon = ''
                            liId = ''
                            break;
                    }
                    li.innerHTML = `
                <li class="${liClassName} ${liId}" id="${liId}">
                    <a class="nav-link" href="./#">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="bi bi-${liIcon}"></i>
                        </span>
                        <span class="nav-link-title">
                            ${item.modulo}
                        </span>
                    </a>
                </li>`;
                    // agregar el li al ul
                    document.getElementById('ulsidebar').appendChild(li);
                });
                let ch = document.createElement('li')
                ch.innerHTML = `
                    <li class="mt-2 nav-item sidebarSalir" id="sidebarSalir">
                        <a class="nav-link" href="./#">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="bi bi-power"></i>
                            </span>
                            <span class="nav-link-title">
                                Salir
                            </span>
                        </a>
                    </li>`;
                document.getElementById('ulsidebar').appendChild(ch);
            }

        }).catch(function (error) {
            console.log(error);
        })
    }, 500);
})