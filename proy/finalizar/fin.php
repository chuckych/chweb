<div class="animate__animated animate__fadeIn container">
    <div class="row">
        <div class="col-12">
            <div class="bg-white py-5">
                <div class="">
                    <p class="display-5 font-weight-bold text-tabler w-100 text-center">Tarea ingresada correctamente</p>
                    <p class="h1 w-100 text-center">Antes de retirarse, no olvide acercarse a finalizar esta tarea.</p>
                    <p class="h1 w-100 text-center mb-4">Buena Jornada !!!</p>
                </div>
                <div class="text-center">
                    <div class=""></div>
                    <button type="button" class="btn btn-lg font09 bg-blue-lt border-0" id="tarSalir">Salir</button>
                    <!-- <button type="button" class="btn btn-lg font09 btn-green border-0 ms-4" id="tarNew">Ingresar otra tarea</button> -->
                </div>
            </div>
            <?= progressBar(0) ?>
        </div>
    </div>
</div>
<script>
    getPag('#tarSalir', 'inicio')
    $("#mainTitleBar").html(("Tarea Finalizada. Gracias"));
    $(document).prop("title", ("Tarea Finalizada. Gracias"));
</script>