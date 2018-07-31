[{assign var="mo_etracker__config" value=$oViewConf->mo_etracker__getConfiguration()}]
[{if $mo_etracker__config.mo_etracker__debug}]
    etCommerce.debugMode = true;
[{/if}]
[{if !$mo_etracker__events}]
    [{assign var=mo_etracker__events value=$oViewConf->mo_etracker__getEventCalls()}]
[{/if}]
[{foreach from=$mo_etracker__events item=mo_etracker__event}]
    [{assign var=mo_etracker__eventjson value=$mo_etracker__event|json_encode}]
    etCommerce.sendEvent([{", "|implode:$mo_etracker__eventjson}]);
[{/foreach}]
