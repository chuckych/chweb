<script>
    $('#LegTDoc').val(<?= $pers['LegTDoc']; ?>).trigger('change');
    $('#LegEsCi').val(<?= $pers['LegEsCi']; ?>).trigger('change');
    $('#LegSexo').val(<?= $pers['LegSexo']; ?>).trigger('change');
    $('#LegHoAl').val(<?= $pers['LegHoAl']; ?>).trigger('change');
    $('#LegTipo').val(<?= $pers['LegTipo']; ?>).trigger('change');
    $('#LegIncTi').val(<?= $pers['LegIncTi']; ?>).trigger('change');
    $('#SecCodi').val(<?= $pers['LegSect']; ?>).trigger('change');
    $("#SectorHelpBlock").html(`Sector: <?= $persSecDesc; ?>`);
</script>
<?php
if ($pers['LegSect'] > '0') {
?>
<script>
    $("#select_seccion").removeClass("d-none");
</script>
<?php } ?>
    <script>
        var newOption = new Option('<?= $persNacDesc; ?>', '<?= $pers['LegNaci']; ?>', true, true);
        $('.selectjs_naciones').append(newOption).trigger('change');
    </script>
<?php //} ?>
<?php
if ($pers['LegProv'] != '0') { ?>
    <script>
        var newOption = new Option('<?= $persProDesc; ?>', '<?= $pers['LegProv']; ?>', true, true);
        $('.selectjs_provincias').append(newOption).trigger('change');
    </script>
<?php } ?>
<?php
if ($pers['LegLoca'] != '0') { ?>
    <script>
        var newOption = new Option('<?= $persLocDesc; ?>', '<?= $pers['LegLoca']; ?>', true, true);
        $('.selectjs_localidad').append(newOption).trigger('change');
    </script>
<?php } ?>
<?php
if ($pers['LegEmpr'] != '0') { ?>
    <script>
        var newOption = new Option('<?= $persEmpRazon; ?>', '<?= $pers['LegEmpr']; ?>', true, true);
        $('.selectjs_empresas').append(newOption).trigger('change');
    </script>
<?php } ?>

<?php if ($pers['LegPlan'] != '0') { ?>
    <script>
        var newOption = new Option('<?= $persPlaDesc; ?>', '<?= $pers['LegPlan']; ?>', true, true);
        $('.selectjs_plantas').append(newOption).trigger('change');
    </script>
<?php } ?>

<?php //if ($pers['LegConv'] != '0') { ?>
    <script>
        var newOption = new Option('<?= $pers['ConDesc']; ?>', '<?= $pers['LegConv']; ?>', true, true);
        $('.selectjs_convenio').append(newOption).trigger('change');
    </script>
<?php //} ?>

<?php if ($pers['LegSect'] != '0') { ?>
    <script>
        var newOption = new Option('<?= $persSecDesc; ?>', '<?= $pers['LegSect']; ?>', true, true);
        $('.selectjs_sectores').append(newOption).trigger('change');
    </script>
<?php } ?>

<?php if ($pers['LegGrup'] != '0') { ?>
    <script>
        var newOption = new Option('<?= $persGruDesc; ?>', '<?= $pers['LegGrup']; ?>', true, true);
        $('.selectjs_grupos').append(newOption).trigger('change');
    </script>
<?php } ?>

<?php if ($pers['LegSucu'] != '0') { ?>
    <script>
        var newOption = new Option('<?= $persSucDesc; ?>', '<?= $pers['LegSucu']; ?>', true, true);
        $('.selectjs_sucursal').append(newOption).trigger('change');
    </script>
<?php } ?>

<?php if ($pers['LegTareProd'] != '0') { ?>
    <script>
        var newOption = new Option('<?= $persTareDesc; ?>', '<?= $pers['LegTareProd']; ?>', true, true);
        $('.selectjs_tarea').append(newOption).trigger('change');
    </script>
<?php } ?>

<?php if ($pers['LegSec2'] != '0') { ?>
    <script>
        var newOption = new Option('<?= $persSe2Desc; ?>', '<?= $pers['LegSec2']; ?>', true, true);
        $('.selectjs_secciones').append(newOption).trigger('change');
    </script>
<?php } ?>

<script>
    var newOption = new Option('<?= $persRCDesc; ?>', '<?= $pers['LegRegCH']; ?>', true, true);
    $('.selectjs_regla').append(newOption).trigger('change');

    var newOption = new Option('<?= $persGHaDesc; ?>', '<?= $pers['LegGrHa']; ?>', true, true);
    $('.selectjs_grupocapt').append(newOption).trigger('change');

    // $('#liquid-tab').tab('show')
</script>