[{foreach from=$oViewConf->mo_etracker__getVars() key=varName item=value}]
    var [{$varName}] = '[{$value}]';
[{/foreach}]

var cc_attributes = new Object();
cc_attributes["language"] = ["[{$oViewConf->mo_etracker__getLanguageAbbr()}]", false];
[{if $oView->getClassName() === 'search'}]
    cc_attributes["etcc_cu"] = 'onsite';
    cc_attributes["etcc_med_onsite"] = 'Interne Suche';
    [{if $oView->getArticleCount() === 0}]
        cc_attributes["etcc_cmp_onsite"] = 'ohne Ergebnis';
    [{else}]
        cc_attributes["etcc_cmp_onsite"] = 'mit Ergebnis';
    [{/if}]
    cc_attributes["etcc_st_onsite"] = '[{$oView->getSearchParam()}]';
[{/if}]