[{if $oViewConf->mo_etracker__isConfigComplete()}]
    <script type="text/javascript">
        [{assign var="mo_etracker__config" value=$oViewConf->mo_etracker__getConfiguration()}]
        [{if $mo_etracker__config.mo_etracker__debug}]
        etCommerce.debugMode = true;
        [{/if}]
        var etCommercePrepareEvents = [{$oViewConf->mo_etracker__getEventCalls()}];
    </script>
[{/if}]
