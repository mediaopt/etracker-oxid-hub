[{if $oViewConf->moetIsConfigComplete()}]
    <script type="text/javascript">
        [{assign var=mo_etracker__config value=$oViewConf->mo_etracker__getConfiguration()}]
        [{if $mo_etracker__config.moetdebug}]
        etCommerce.debugMode = true;
        [{/if}]
        var etCommercePrepareEvents = [{$oViewConf->mo_etracker__getEventCalls()}];
    </script>
[{/if}]
