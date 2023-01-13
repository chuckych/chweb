<?php
require_once "../sidebar/sidebar.php";?>
<!-- /#sidebar-wrapper -->
<!-- Page Content -->
<?php

/**
 * Pagina de Zonas
 */
?>
<style>
.map_canvas { 
  width  : 100%;
  height : 300px;
  border : 1px solid #cecece;
}
.pac-item{
   font-family : 'Montserrat';
   font-style  : normal;
   font-weight : 400;
   color       : #333333;
   padding     : 5px;
}
.pac-item-query{
   font-style  : normal;
   font-weight : 400;
   color       : #333333;
   font-size   : 1.3em;
}
.pac-matched{
   font-style     : normal;
   font-weight    : 400;
   background     : #efefef;
   padding-top    : 5px;
   padding-bottom : 5px;
   padding-left   : 5px;
   border         : 1px solid #cecece;
}
.pac-item:hover{
   background: #fafafa;
}
.pac-item-selected{
   background: #fafafa;
}
</style>
<div id="page-content-wrapper">
<?php // require_once "../sidebar/nav.php"; if(empty($_GET['q'])){$q="";}else{$q=$_GET['q'];} ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12 col-lg-10 col-xl-8">
				<div class="rounded card card-body">
					<div class="row mb-3">
						<div class="col-12">
                     <?php /** Menu Lateral */?>
                        <button title="Menu Lateral"class="float-right btn btn-sm pr-2" id="menu-toggle"><i class="fas fa-bars"></i></button>
                     <?php /** Fin Menu Lateral */?>
                        <span class="h4 card-title pr-3">Nueva Zona</span>
						</div>
					</div>
               <div class="row">
                  <div class="col-12">
                     <form action="" method="GET">
                        <div class="form-group">
                           <input id="geocomplete" type="text" class="form-control" placeholder="Ingrese un lugar o dirección" value="" />
                           <!-- <input id="find" type="button" value="Localizar" class="my-2 float-right btn btn-success btn-sm btn-round" /> -->
                        </div>
                        
                           <div class="pb-4"><input type="reset" value="Reset" class="float-right btn btn-outline-secondary btn-sm btn-round border"></div>
                        
                     </form>
                        <form action="insert-zone.php" method="POST" onsubmit="ShowLoading()">
                           <div class="mb-3">
                              <!-- <label><span class="p-2">Latitud</span></label> -->
                              <input name="lat" type="hidden" value="" class="form-control"  >
                              <!-- <label><span class="p-2">Longitud</span></label> -->
                              <input name="lng" type="hidden" value="" class="form-control"  >
                              
                           </div> 
                           <div class="border-bottom my-4"></div>
                              <div class="form-group row">
                                 <label for="nombre" class="col-sm-2 col-form-label text-nowrap">Nombre</label>
                                 <div class="col-sm-10">
                                 <input type="text" class="form-control" id="nombre" required name="nombre" placeholder="Nombre de la zona">
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label for="metros" class="col-sm-2 col-form-label text-nowrap">Radio</label>
                                 <div class="col-sm-10">
                                 <input type="number" class="form-control" id="metros" min="100" required name="metros" placeholder="Radio de la zona en metros">
                                 <span class="text-secondary fontp mt-1">Mínimo 100 metros.</span>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <div class="col-12">
                                 <button type="submit" class="float-right btn btn-info btn-sm btn-round" name="submit" >Guardar</button>
                                 
                                 </div>
                              </div>
                              <div name="formatted_address" value="" class="mb-2 text-secondary fontq border-0" readonly style="background:#ffffff"></div>
                              <!-- <div id="map_canvas" class="" style="height:400px"></div> -->
                              <div id="" class="map_canvas" style=""></div>
                            </form>
                       
                        <a id="reset" href="#" style="display:none;" class="my-2 btn btn-outline-primary border btn-sm btn-round">Resetar Marcador</a>
                     
                     
                  </div>
               </div>
               <div class="form-group row my-2">
                  <div class="col-12">
                  </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<!-- /cierra #page-content-wrapper -->
</div>
<!-- /#wrapper -->
<?php 
include "../js/jquery2.php"; 
?>
</body>
</html>