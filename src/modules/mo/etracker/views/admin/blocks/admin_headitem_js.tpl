[{$smarty.block.parent}]
[{if $mo_etracker__include}]
    [{include file="mo_etracker__etracker_include.tpl"}]
    [{if $oViewConf->mo_etracker__isConfigComplete()}]
        <script type="text/javascript">
            [{include file="mo_etracker__eventhandler.tpl"}]
        </script>
    [{/if}]
[{/if}]