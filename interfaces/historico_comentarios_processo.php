<?php
/*
 * Copyright 2008 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuíção e/ou modifição dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuíção na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */

include("function/auto_load_statics.php");

$controller = Controlador::getInstance();
$auth = $controller->usuario;

$objeto = DaoProcesso::getProcesso($_GET['numero_processo']);
$controller->setContexto($objeto);
$controller->botoes = Util::getMenus($auth, $controller->recurso, $controller->acl);
foreach ($controller->recurso->dependencias as $arquivo) {
    include_once('interfaces/'.$arquivo);
}
?>

<html>
    <head>
        <style type="text/css">
            @import "plugins/datatable/media/css/demo_table_tabs.css";
            body{
                margin: 10px;
                background-color: #101c01;
                background-image: url('imagens/<?php print(__BACKGROUND__); ?>');
                background-position: bottom right;
                background-repeat: no-repeat;
            }
        </style>
        <script type="text/javascript" src="plugins/datatable/media/js/jquery.dataTables.js"></script>
        <script type="text/javascript">
            /*Variaveis globais*/
            var oTabelaHistoricos = null;

            $(document).ready(function() {

                $("#tabs").tabs();
                $(".cabecalho-caixas").tabs();

                /*DataTables*/
                oTabelaHistoricos = $('#TabelaHistoricos').dataTable({
                    aLengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    bStateSave: false,
                    bPaginate: true,
                    bProcessing: true,
                    bServerSide: true,
                    bJQueryUI: true,
                    sPaginationType: "full_numbers",
                    aaSorting: [[ 1, "desc" ]],
                    sAjaxSource: "modelos/historicos/listar_historicos_documentos_processos.php?usercase=historico-comentarios-processos&numero_processo=<?php echo $_GET['numero_processo'] ?>",
                    oLanguage: {
                        sProcessing: "Carregando...",
                        sLengthMenu: "_MENU_ por página",
                        sZeroRecords: "Nenhum comentário encontrado.",
                        sInfo: "_START_ a _END_ de _TOTAL_ comentarios",
                        sInfoEmpty: "Não foi possível localizar comentários com o parâmetros informados! ",
                        sInfoFiltered: "",
                        sInfoPostFix: "",
                        sSearch: "Pesquisar:",
                        oPaginate: {
                            sFirst: "Primeiro",
                            sPrevious: "Anterior",
                            sNext: "Próximo",
                            sLast: "Ultimo"
                        }
                    },
                    fnServerData: function ( sSource, aoData, fnCallback ) {
                        $.getJSON( sSource, aoData, function (json) {
                            fnCallback(json);
                        });
                    },
                    fnRowCallback: function( nRow, aData, iDisplayIndex ) {
                        /* Contador */
                        $('td:eq(0)', nRow).html(iDisplayIndex+1);
                        if(aData[3]==''){
                            $('td:eq(3)', nRow).html('<div title=""></div>');
                        }
                        $('td:eq(1)', nRow).html(convertDateToString(aData[1]));
                        /*Retorna a linha modificada*/
                        return nRow;
                    }
                });
            });
        </script>

        <title>Comentarios</title>
    </head>
    <body>
        <div class="cabecalho-caixas">
            <div class="logo-historico-comentarios"></div>
            <div class="titulo-historico">Comentários - <?php echo $_GET['numero_processo'] ?></div>
            <div class="menu-auxiliar">
                <?php Util::montaMenus($controller->botoes, array('class' => 'botao32')); ?>
            </div>
        </div>
        <div id="tabs">
            <table class="display" border="0" id="TabelaHistoricos">
                <thead>
                    <tr>
                        <th class="style13 column-checkbox">#</th>
                        <th class="style13 column-numero">Data</th>
                        <th class="style13 column-assunto">Comentario</th>
                        <th class="style13 column-numero">Usuario</th>
                        <th class="style13 column-tipo">Setor</th>
                    </tr>
                </thead>
            </table>
        </div>
    </body>
</html>
